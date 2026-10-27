<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

function normalizeMandiriRate(mixed $value): float
{
    if (is_int($value) || is_float($value)) {
        $rate = (float) $value;
    } elseif (is_string($value)) {
        $clean = trim($value);
        $clean = preg_replace('/[^0-9.,-]/u', '', $clean) ?? '';

        if ($clean === '') {
            throw new RuntimeException('Mandiri returned an empty rate.');
        }

        if (str_contains($clean, '.') && str_contains($clean, ',')) {
            $clean = str_replace('.', '', $clean);
            $clean = str_replace(',', '.', $clean);
        } elseif (str_contains($clean, '.') && preg_match('/^\d{1,3}(?:\.\d{3})+$/', $clean) === 1) {
            $clean = str_replace('.', '', $clean);
        } elseif (str_contains($clean, ',')) {
            $clean = str_replace(',', '.', $clean);
        }

        if (!is_numeric($clean)) {
            throw new RuntimeException('Mandiri returned a non-numeric rate.');
        }

        $rate = (float) $clean;
    } else {
        throw new RuntimeException('Unexpected Mandiri rate format.');
    }

    if (!is_finite($rate) || $rate < 5000 || $rate > 50000) {
        throw new RuntimeException('Mandiri rate failed sanity validation.');
    }

    return $rate;
}

function fetchMandiriLiveRate(string $type = 'BUY'): float
{
    $type = strtoupper($type);
    if (!in_array($type, ['BUY', 'SELL'], true)) {
        throw new InvalidArgumentException('Rate type must be BUY or SELL.');
    }

    if (!function_exists('curl_init')) {
        throw new RuntimeException('PHP cURL extension is required.');
    }

    $body = http_build_query([
        MANDIRI_PREFIX . 'value' => '1',
        MANDIRI_PREFIX . 'from' => MANDIRI_USD_CODE,
        MANDIRI_PREFIX . 'to' => MANDIRI_IDR_CODE,
        MANDIRI_PREFIX . 'jenis' => $type,
    ], '', '&', PHP_QUERY_RFC3986);

    $ch = curl_init(MANDIRI_ENDPOINT);
    if ($ch === false) {
        throw new RuntimeException('Unable to initialize cURL.');
    }

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => MANDIRI_CONNECT_TIMEOUT,
        CURLOPT_TIMEOUT => MANDIRI_REQUEST_TIMEOUT,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_MAXREDIRS => 0,
        CURLOPT_ENCODING => '',
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json, text/javascript, */*; q=0.01',
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'X-Requested-With: XMLHttpRequest',
            'Referer: https://www.bankmandiri.co.id/kurs-display',
        ],
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/155.0 Safari/537.36',
    ]);

    $response = curl_exec($ch);
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new RuntimeException('Mandiri request failed: ' . $error);
    }

    $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);

    if ($status !== 200) {
        throw new RuntimeException('Mandiri returned HTTP ' . $status . '.');
    }

    if (strlen($response) > 100000) {
        throw new RuntimeException('Mandiri response is unexpectedly large.');
    }

    try {
        $json = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
        throw new RuntimeException('Mandiri returned invalid JSON.', 0, $e);
    }

    if (!is_array($json) || !array_key_exists('value', $json)) {
        throw new RuntimeException('Mandiri response does not contain value.');
    }

    return normalizeMandiriRate($json['value']);
}

function getMandiriRate(string $type = 'BUY', bool $forceRefresh = false): array
{
    $type = strtoupper($type);
    if (!in_array($type, ['BUY', 'SELL'], true)) {
        throw new InvalidArgumentException('Rate type must be BUY or SELL.');
    }

    $pdo = db();
    $stmt = $pdo->prepare('SELECT rate, fetched_at, updated_at FROM rates WHERE rate_type = :type LIMIT 1');
    $stmt->execute(['type' => $type]);
    $cached = $stmt->fetch();
    $now = time();

    if (!$forceRefresh && is_array($cached) && ($now - (int) $cached['fetched_at']) <= RATE_CACHE_TTL) {
        return [
            'rate' => (float) $cached['rate'],
            'cached' => true,
            'stale' => false,
            'updated_at' => (string) $cached['updated_at'],
        ];
    }

    try {
        $rate = fetchMandiriLiveRate($type);
        $updatedAt = date(DATE_ATOM);

        $stmt = $pdo->prepare(
            'INSERT INTO rates (rate_type, rate, fetched_at, updated_at)
             VALUES (:type, :rate, :fetched_at, :updated_at)
             ON CONFLICT(rate_type) DO UPDATE SET
                rate = excluded.rate,
                fetched_at = excluded.fetched_at,
                updated_at = excluded.updated_at'
        );
        $stmt->execute([
            'type' => $type,
            'rate' => $rate,
            'fetched_at' => $now,
            'updated_at' => $updatedAt,
        ]);

        return [
            'rate' => $rate,
            'cached' => false,
            'stale' => false,
            'updated_at' => $updatedAt,
        ];
    } catch (Throwable $e) {
        if (is_array($cached) && ($now - (int) $cached['fetched_at']) <= RATE_STALE_TTL) {
            return [
                'rate' => (float) $cached['rate'],
                'cached' => true,
                'stale' => true,
                'updated_at' => (string) $cached['updated_at'],
            ];
        }

        throw $e;
    }
}
