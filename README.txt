MANDIRI FINANCE APP
===================

Requirement:
- PHP 8.3+
- ext-pdo_sqlite
- ext-curl
- HTTPS recommended

Install:
1. Upload seluruh folder ke public_html/finance/ (atau nama folder lain).
2. Pastikan folder storage/ writable oleh user PHP. Umumnya chmod 700/750 sudah cukup.
3. Buka https://domain.com/finance/
4. Database SQLite dibuat otomatis di storage/app.sqlite.

Fitur:
- Bank Mandiri USD -> IDR BUY via calculateCurrency resource endpoint.
- Cache kurs 60 detik, stale fallback 1 jam.
- NET1 + NET2 -> Total USD -> x rate -> Total IDR -> /4.
- Pembagian tampil sebagai M / B / R / Misc.
- Histori SQLite, max 100 baris pada UI.
- Ringkasan mingguan 12 minggu.
- AJAX polling histori 5 detik.
- CSRF untuk operasi write.
- CSP nonce, fixed upstream URL, SSL verification, no SSRF user-controlled URL.

Catatan:
Endpoint Mandiri adalah endpoint internal halaman kurs (Liferay portlet). Jika instance ID
"m7rMvPoazf24" berubah, update MANDIRI_ENDPOINT dan MANDIRI_PREFIX di config.php.
