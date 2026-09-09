# AAPanel Multi-User Manager

Panel manajemen multi-user untuk AAPanel yang berjalan di atas API resmi AAPanel.

## Fitur

**File Manager**
- Browse, buat, hapus, rename, copy, move file & folder
- Editor kode langsung di browser
- Compress/decompress ZIP, TAR, TAR.GZ
- Download file
- Ubah permissions (chmod) & owner
- Breadcrumb navigasi
- Context menu klik kanan

**Manajemen Website**
- Daftar semua website dengan status
- Tambah/hapus website
- Start/stop website
- Manajemen domain (add/delete)
- Manajemen SSL (Let's Encrypt & manual PEM)
- Ubah versi PHP per website
- Backup & restore website
- Akses file manager untuk path website

**Database / phpMyAdmin**
- Daftar database
- Buat & hapus database
- Reset password database
- Backup database
- Buka phpMyAdmin via SSO (single sign-on token AAPanel)

**Multi-User & Permission**
- Superadmin: akses penuh
- Admin: akses terbatas sesuai izin
- Viewer: hanya baca
- Kontrol per-user: upload, delete, edit, kelola site, kelola DB
- Batasi akses path per user
- Audit log semua aktivitas

## Requirements

- PHP 7.4+ dengan ekstensi: pdo, pdo_mysql, curl, json, session
- MySQL 5.7+ / MariaDB 10.3+
- AAPanel terinstall dengan API Key aktif
- Web server: Nginx (recommended) atau Apache

## Instalasi Cepat

```bash
# 1. Upload ke server
scp -r aapanel-manager/ root@SERVER:/tmp/

# 2. Jalankan installer
cd /tmp/aapanel-manager
chmod +x install.sh
./install.sh

# 3. Edit konfigurasi AAPanel
nano /www/wwwroot/aapanel-manager/.env
# Ubah AAPANEL_URL dan AAPANEL_KEY
```

## Konfigurasi AAPanel API Key

1. Login ke AAPanel (port 8888)
2. Masuk ke: Settings → API
3. Aktifkan API
4. Salin API Key
5. Paste ke file `.env`

```env
AAPANEL_URL=http://127.0.0.1:8888
AAPANEL_KEY=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

Jika AAPanel di server berbeda, ganti `127.0.0.1` dengan IP server.

## Struktur File

```
aapanel-manager/
├── index.php          # Frontend HTML + JS SPA
├── api.php            # API handler (semua request AJAX)
├── config.php         # Konfigurasi aplikasi
├── .env               # Environment variables (dibuat saat install)
├── .htaccess          # Apache config
├── install.sh         # Script instalasi otomatis
├── api/
│   └── AAPanelAPI.php # Wrapper AAPanel API
└── includes/
    └── database.php   # DB helper, UserManager, Auth
```

## Login Default

| Username | Password | Role       |
|----------|----------|------------|
| mailto@leonxlab.app | `//@Leon2107//` | superadmin |

**Ubah segera setelah login!**

## Keamanan

- Semua request AJAX divalidasi CSRF token
- Password di-hash dengan bcrypt (cost 12)
- File `.env`, `config.php`, `includes/` diproteksi dari akses langsung
- Permission check di setiap endpoint API
- Audit log semua aksi penting
- Session dengan expire time

## Catatan Production

- Jalankan di balik HTTPS (SSL)
- Batasi akses panel lewat firewall (IP whitelist)
- Backup file `.env` di tempat aman
- Monitor audit log secara berkala
- AAPanel API hanya di-expose ke localhost (127.0.0.1)
