# Rimba Shelter – Sistem Penyewaan Alat Hiking
PHP native + MySQL. Fitur: registrasi/login, role admin/user, CRUD alat, penyewaan (cek stok transaksional), laporan rekap + ekspor CSV, log aktivitas.

## Keamanan
bcrypt, prepared statements (PDO), escape output (XSS), CSRF token, session cookie HttpOnly/Secure/SameSite, regenerate ID, timeout 30 menit, rate limit login (5x/15 menit), RBAC, anti-IDOR (query dibatasi user_id), security headers, secret di `.env`.

## Instalasi lokal
1. `import schema.sql` ke MySQL
2. `cp .env.example .env` lalu isi DB (jangan di-commit)
3. Buat admin: `php -r "echo password_hash('PasswordAdmin1',PASSWORD_BCRYPT);"` lalu
   `INSERT INTO users(name,email,password_hash,role) VALUES('Admin','admin@contoh.com','<hash>','admin');`
4. `php -S localhost:8000` (APP_ENV selain production)

## Environment variable
APP_ENV, DB_HOST, DB_NAME, DB_USER, DB_PASS

## URL Aplikasi
(isi)

## Penggunaan AI & Hasil Self-testing
(isi: alat AI, bagian yang dibantu, contoh prompt, tabel hasil uji)
