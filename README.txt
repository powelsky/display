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

Jalankan lokal di Windows (php -S 127.0.0.1:8080):
- Klik dua kali start.vbs, atau shortcut "Mandiri FX" di Desktop (memakai ikon icons/app.ico).
  Server PHP dijalankan tersembunyi di port 8080 hanya jika belum jalan, lalu browser dibuka.
- Hentikan server: jalankan stop.cmd.
- Uji tanpa membuka browser: wscript start.vbs noopen
- PWA: buka http://127.0.0.1:8080/ lalu klik "Pasang aplikasi"/ikon Install.
  start_url relatif, jadi ikon hasil install mengarah ke port yang dipakai saat install.
  Catatan: ikon PWA tidak dapat menyalakan server sendiri - gunakan shortcut start.vbs.
- Membutuhkan php.exe di PATH.

Install di mobile (Android/iOS):
- PWA hanya bisa di-install dari origin HTTPS (atau localhost di perangkat itu sendiri).
  127.0.0.1:8080 di HP menunjuk ke HP itu sendiri, bukan ke PC.
- Cara resmi: host aplikasi di domain HTTPS (cPanel/Cloudflare sesuai langkah Install di atas),
  buka di HP, lalu install.
  Android/Chrome: menu -> "Install app" / "Add to Home screen" (mode standalone).
  iOS/Safari   : tombol Bagikan -> "Tambahkan ke Layar Utama".
- Uji UI dari HP di jaringan yang sama tanpa HTTPS (hanya untuk melihat, bukan install):
  jalankan  wscript start.vbs lan   -> buka http://<IP-LAN>:8080/ dari HP.
  Chrome di origin http:// biasa hanya menawarkan shortcut biasa, bukan PWA standalone.
  Catatan: mode lan mem-bind 0.0.0.0 (terbuka ke seluruh LAN) dan aplikasi ini tanpa login.
- Ikon launcher di Android diambil dari manifest (icons/icon-192.png, icon-512.png);
  di iOS dari icons/apple-touch-icon.png (180x180).
- Service worker tidak meng-cache index.php/api.php (CSRF token + CSP nonce per response),
  jadi aplikasi tetap butuh koneksi ke server saat dipakai.

Fitur:
- Bank Mandiri USD -> IDR BUY via calculateCurrency resource endpoint.
- Cache kurs 60 detik, stale fallback 1 jam.
- NET1 + NET2 -> Total USD -> x rate -> Total IDR.
- Pembagian (satu rumus, berlaku untuk semua kombinasi):
  p1 = NET1 x rate / 4 ; p2 = NET2 x rate / 2
  M = Misc = p1 ; B = R = p1 + p2
  - hanya NET1 : M = B = R = Misc = NET1 x rate / 4
  - hanya NET2 : B = R = NET2 x rate / 2, M = Misc = 0
  - keduanya   : M = Misc = p1, B = R = p1 + p2
  Jumlah 4 bagian selalu = Total IDR.
- Pembagian tampil sebagai M / B / R / Misc.
- Histori menampilkan kolom M / Misc dan B / R.
- Histori SQLite, max 100 baris pada UI.
- Ringkasan mingguan 12 minggu.
- AJAX polling histori 5 detik.
- CSRF untuk operasi write.
- CSP nonce, fixed upstream URL, SSL verification, no SSRF user-controlled URL.

Catatan:
Endpoint Mandiri adalah endpoint internal halaman kurs (Liferay portlet). Jika instance ID
"m7rMvPoazf24" berubah, update MANDIRI_ENDPOINT dan MANDIRI_PREFIX di config.php.
