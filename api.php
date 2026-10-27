<?php

declare(strict_types=1);

session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true,
]);

require_once __DIR__ . '/mandiri.php';

header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store, max-age=0');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: no-referrer');

function jsonResponse(array $payload, int $status = 200): never
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION);
    exit;
}

function requestJson(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') {
        return [];
    }

    try {
        $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException) {
        jsonResponse(['ok' => false, 'error' => 'Invalid JSON body.'], 400);
    }

    return is_array($data) ? $data : [];
}

function validateAmount(mixed $value, string $name): float
{
    if (!is_int($value) && !is_float($value) && !is_string($value)) {
        jsonResponse(['ok' => false, 'error' => $name . ' is invalid.'], 422);
    }

    if (!is_numeric((string) $value)) {
        jsonResponse(['ok' => false, 'error' => $name . ' must be numeric.'], 422);
    }

    $number = (float) $value;
    if (!is_finite($number) || $number < 0 || $number > 1000000000) {
        jsonResponse(['ok' => false, 'error' => $name . ' is outside the permitted range.'], 422);
    }

    return $number;
}

function requireCsrf(array $data): void
{
    $sessionToken = $_SESSION['csrf'] ?? '';
    $requestToken = $data['csrf'] ?? '';

    if (!is_string($sessionToken) || !is_string($requestToken) || $sessionToken === '' || !hash_equals($sessionToken, $requestToken)) {
        jsonResponse(['ok' => false, 'error' => 'Invalid CSRF token.'], 403);
    }
}

$action = $_GET['action'] ?? 'rate';

try {
    if ($action === 'rate') {
        $force = isset($_GET['refresh']) && $_GET['refresh'] === '1';
        $result = getMandiriRate('BUY', $force);

        jsonResponse([
            'ok' => true,
            'source' => 'Bank Mandiri',
            'pair' => 'USD_IDR',
            'type' => 'BUY',
            ...$result,
        ]);
    }

    if ($action === 'save') {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse(['ok' => false, 'error' => 'Method not allowed.'], 405);
        }

        $data = requestJson();
        requireCsrf($data);

        $net1 = validateAmount($data['net1'] ?? null, 'net1');
        $net2 = validateAmount($data['net2'] ?? null, 'net2');
        $totalUsd = $net1 + $net2;

        if ($totalUsd <= 0) {
            jsonResponse(['ok' => false, 'error' => 'Total USD must be greater than 0.'], 422);
        }

        $rateData = getMandiriRate('BUY');
        $rate = (float) $rateData['rate'];
        $totalIdr = round($totalUsd * $rate, 2);
        $perPart = round($totalIdr / 4, 2);
        $createdAt = date(DATE_ATOM);

        $pdo = db();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO calculations (net1_usd, net2_usd, total_usd, rate, total_idr, per_part, created_at)
                 VALUES (:net1, :net2, :total_usd, :rate, :total_idr, :per_part, :created_at)'
            );
            $stmt->execute([
                'net1' => $net1,
                'net2' => $net2,
                'total_usd' => $totalUsd,
                'rate' => $rate,
                'total_idr' => $totalIdr,
                'per_part' => $perPart,
                'created_at' => $createdAt,
            ]);
            $id = (int) $pdo->lastInsertId();
            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }

        jsonResponse([
            'ok' => true,
            'item' => [
                'id' => $id,
                'net1_usd' => $net1,
                'net2_usd' => $net2,
                'total_usd' => $totalUsd,
                'rate' => $rate,
                'total_idr' => $totalIdr,
                'per_part' => $perPart,
                'created_at' => $createdAt,
            ],
        ]);
    }

    if ($action === 'history') {
        $pdo = db();
        $rows = $pdo->query(
            'SELECT id, net1_usd, net2_usd, total_usd, rate, total_idr, per_part, created_at
             FROM calculations
             ORDER BY id DESC
             LIMIT 100'
        )->fetchAll();

        $summary = $pdo->query(
            "SELECT
                strftime('%Y-W%W', created_at) AS week,
                COUNT(*) AS transactions,
                ROUND(SUM(total_usd), 2) AS total_usd,
                ROUND(SUM(total_idr), 2) AS total_idr,
                ROUND(SUM(per_part), 2) AS total_per_part
             FROM calculations
             GROUP BY strftime('%Y-W%W', created_at)
             ORDER BY week DESC
             LIMIT 12"
        )->fetchAll();

        jsonResponse([
            'ok' => true,
            'history' => $rows,
            'weekly' => $summary,
        ]);
    }

    if ($action === 'clear') {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse(['ok' => false, 'error' => 'Method not allowed.'], 405);
        }

        $data = requestJson();
        requireCsrf($data);
        db()->exec('DELETE FROM calculations');

        jsonResponse(['ok' => true]);
    }

    jsonResponse(['ok' => false, 'error' => 'Unknown action.'], 404);
} catch (Throwable $e) {
    error_log('[mandiri-finance] ' . $e->getMessage());
    jsonResponse(['ok' => false, 'error' => 'Server error.'], 500);
}
