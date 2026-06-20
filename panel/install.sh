#!/bin/bash
# ================================================
# AAPANEL MULTI-USER MANAGER — INSTALL SCRIPT
# Jalankan sebagai root di server Ubuntu/CentOS
# ================================================

set -e

INSTALL_DIR="/www/wwwroot/aapanel-manager"
PHP_BIN=$(which php || echo "php")
WEBUSER="www"

echo ""
echo "╔══════════════════════════════════════╗"
echo "║  AAPanel Manager — Installer         ║"
echo "╚══════════════════════════════════════╝"
echo ""

# Cek PHP
if ! command -v php &>/dev/null; then
    echo "[ERROR] PHP tidak ditemukan. Install PHP terlebih dahulu."
    exit 1
fi
PHP_VER=$($PHP_BIN -r "echo PHP_MAJOR_VERSION;")
if [ "$PHP_VER" -lt 7 ]; then
    echo "[ERROR] Butuh PHP 7.4+. Versi saat ini: $($PHP_BIN -r 'echo PHP_VERSION;')"
    exit 1
fi
echo "[OK] PHP $($PHP_BIN -r 'echo PHP_VERSION;') ditemukan"

# Cek ekstensi
for ext in pdo pdo_mysql curl json session; do
    if ! $PHP_BIN -m 2>/dev/null | grep -q "^$ext$"; then
        echo "[WARN] Ekstensi PHP '$ext' tidak aktif. Pasang: apt install php-$ext"
    fi
done

# Buat direktori
mkdir -p "$INSTALL_DIR"
cp -r ./* "$INSTALL_DIR/"
chown -R $WEBUSER:$WEBUSER "$INSTALL_DIR"
chmod -R 750 "$INSTALL_DIR"

echo "[OK] File disalin ke $INSTALL_DIR"

# Setup database
echo ""
echo "═══════════════════════════════════════"
echo "Setup Database MySQL"
echo "═══════════════════════════════════════"
read -p "MySQL root password: " -s MYSQL_ROOT_PASS
echo ""
read -p "Nama database [aapanel_manager]: " DB_NAME
DB_NAME=${DB_NAME:-aapanel_manager}
read -p "DB user [aapanel_mgr]: " DB_USER
DB_USER=${DB_USER:-aapanel_mgr}
DB_PASS=$(openssl rand -base64 20 | tr -d '=/+')
echo "Password DB: $DB_PASS"

mysql -u root -p"$MYSQL_ROOT_PASS" <<SQL
CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
SQL

echo "[OK] Database '$DB_NAME' siap"

# Buat config .env
SECRET_KEY=$(openssl rand -hex 32)
cat > "$INSTALL_DIR/.env" <<ENV
APP_ENV=production
AAPANEL_URL=http://127.0.0.1:8888
AAPANEL_KEY=YOUR_AAPANEL_API_KEY
DB_HOST=localhost
DB_NAME=$DB_NAME
DB_USER=$DB_USER
DB_PASS=$DB_PASS
SECRET_KEY=$SECRET_KEY
ENV
chmod 640 "$INSTALL_DIR/.env"
chown $WEBUSER:$WEBUSER "$INSTALL_DIR/.env"
echo "[OK] File .env dibuat"

# Buat tabel database
$PHP_BIN -r "
define('DB_HOST','localhost');
define('DB_NAME','$DB_NAME');
define('DB_USER','$DB_USER');
define('DB_PASS','$DB_PASS');
define('AAPANEL_URL','http://127.0.0.1:8888');
define('AAPANEL_KEY','dummy');
define('SESSION_LIFETIME',3600);
define('CSRF_TOKEN_NAME','_csrf');
define('SECRET_KEY','$SECRET_KEY');
define('TIMEZONE','Asia/Jakarta');
date_default_timezone_set('Asia/Jakarta');
session_start();
require '$INSTALL_DIR/includes/database.php';
UserManager::install();
echo '[OK] Tabel database dibuat';
"

# Nginx config
echo ""
echo "═══════════════════════════════════════"
echo "Konfigurasi Nginx"
echo "═══════════════════════════════════════"
read -p "Domain/IP untuk panel [localhost]: " PANEL_DOMAIN
PANEL_DOMAIN=${PANEL_DOMAIN:-localhost}
read -p "Port panel [9000]: " PANEL_PORT
PANEL_PORT=${PANEL_PORT:-9000}

cat > "/www/server/panel/vhost/nginx/aapanel-manager.conf" <<NGINX
server {
    listen $PANEL_PORT;
    server_name $PANEL_DOMAIN;
    root $INSTALL_DIR;
    index index.php;
    charset utf-8;
    client_max_body_size 100m;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    # Blok akses ke file sensitif
    location ~ /\.(env|git|htaccess) {
        deny all;
        return 404;
    }
    location ~ /includes/ { deny all; return 404; }

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }
    location ~ \.php$ {
        fastcgi_pass unix:/tmp/php-cgi-$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;").sock;
        fastcgi_index index.php;
        include fastcgi.conf;
    }
    location ~ .*\.(gif|jpg|jpeg|png|bmp|swf|js|css)$ {
        expires 30d;
    }
    access_log /www/wwwlogs/aapanel-manager.log;
    error_log /www/wwwlogs/aapanel-manager.error.log;
}
NGINX

# Reload nginx
/etc/init.d/nginx reload 2>/dev/null || nginx -s reload 2>/dev/null || echo "[WARN] Reload nginx manual: nginx -s reload"

echo ""
echo "╔══════════════════════════════════════════════════╗"
echo "║  Instalasi Selesai!                              ║"
echo "╠══════════════════════════════════════════════════╣"
echo "║  URL Panel : http://$PANEL_DOMAIN:$PANEL_PORT"
echo "║  Username  : admin"
echo "║  Password  : admin123"
echo "║"
echo "║  PENTING: Segera ubah password setelah login!"
echo "║  Edit konfigurasi AAPanel API di:"
echo "║  $INSTALL_DIR/.env"
echo "╚══════════════════════════════════════════════════╝"
