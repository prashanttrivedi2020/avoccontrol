#!/usr/bin/env bash
set -e

DATADIR="/home/runner/mysql-data"
SOCKET="/tmp/mysql.sock"
MYSQL_PORT=3306
APP_PORT="${PORT:-8000}"
APP_DIR="$(cd "$(dirname "$0")" && pwd)"

echo "==> FireKontrol 365 startup"
echo "    App dir: $APP_DIR"
echo "    App port: $APP_PORT"

# ── MySQL ──────────────────────────────────────────────────────
if [ ! -d "$DATADIR/mysql" ]; then
  echo "==> Initializing MySQL data directory..."
  mysqld --initialize-insecure --user="$(whoami)" --datadir="$DATADIR" 2>&1
fi

if ! mysqladmin --socket="$SOCKET" ping --silent 2>/dev/null; then
  echo "==> Starting MySQL..."
  nohup mysqld \
    --user="$(whoami)" \
    --datadir="$DATADIR" \
    --socket="$SOCKET" \
    --port="$MYSQL_PORT" \
    --skip-mysqlx \
    --log-error=/tmp/mysqld.log \
    > /tmp/mysqld-out.log 2>&1 &

  MYSQL_READY=0
  for i in $(seq 1 30); do
    if mysqladmin --socket="$SOCKET" ping --silent 2>/dev/null; then
      echo "==> MySQL ready!"
      MYSQL_READY=1
      break
    fi
    sleep 1
  done
  if [ "$MYSQL_READY" -eq 0 ]; then
    echo "ERROR: MySQL did not start within 30 seconds. Check /tmp/mysqld.log"
    cat /tmp/mysqld.log 2>/dev/null | tail -20
    exit 1
  fi
fi

# Create DB + user if needed
mysql -u root --socket="$SOCKET" <<SQL
CREATE DATABASE IF NOT EXISTS firekontrol CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'firekontrol'@'localhost' IDENTIFIED BY 'secret';
GRANT ALL ON firekontrol.* TO 'firekontrol'@'localhost';
FLUSH PRIVILEGES;
SQL

# ── Laravel ────────────────────────────────────────────────────
cd "$APP_DIR"

# Set up storage symlink
if [ ! -L "public/storage" ]; then
  echo "==> Creating storage symlink..."
  DB_PASSWORD=secret php artisan storage:link 2>/dev/null || true
fi

# Clear config cache and run migrations
echo "==> Running migrations..."
DB_PASSWORD=secret php artisan config:clear 2>/dev/null || true
DB_PASSWORD=secret php artisan migrate --force 2>&1

echo "==> Starting Laravel on port $APP_PORT..."
DB_PASSWORD=secret php artisan serve --host=0.0.0.0 --port="$APP_PORT"
