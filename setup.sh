#!/bin/bash

echo ""
echo "====================================================="
echo "  KUNNO TECH TEST - Setup Mac/Linux"
echo "  PHP requerido: 8.1 o superior"
echo "  Laravel: 10.x"
echo "====================================================="
echo ""

# Verificar PHP
if ! command -v php &> /dev/null; then
    echo "[ERROR] PHP no encontrado."
    echo ""
    echo "Instalacion:"
    echo "  Mac:   brew install php"
    echo "  Linux: sudo apt install php8.1 php8.1-cli php8.1-xml php8.1-mbstring php8.1-sqlite3"
    exit 1
fi

PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
echo "[OK] PHP $PHP_VERSION encontrado"

# Verificar Composer
if ! command -v composer &> /dev/null; then
    echo "[ERROR] Composer no encontrado."
    echo "Instala desde: https://getcomposer.org/download/"
    exit 1
fi
echo "[OK] Composer $(composer --version --no-ansi | head -1)"
echo ""

# Instalar dependencias
echo "Instalando dependencias de Laravel..."
composer install --no-interaction --prefer-dist --quiet
echo "[OK] Dependencias instaladas"
echo ""

# Configurar .env
if [ ! -f ".env" ]; then
    cp .env.example .env
    echo "[OK] Archivo .env creado"
else
    echo "[OK] Archivo .env ya existe"
fi

# Generar key
php artisan key:generate --ansi
echo ""

# Crear base de datos SQLite
if [ ! -f "database/kunno_test.sqlite" ]; then
    touch database/kunno_test.sqlite
    echo "[OK] Base de datos SQLite creada"
fi

# Storage
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache
chmod -R 775 storage bootstrap/cache
echo "[OK] Permisos de storage configurados"
echo ""

# Correr tests
echo "Verificando instalacion..."
php artisan test --filter CommissionTest
echo ""

echo "====================================================="
echo "  Setup completado."
echo ""
echo "  Comandos utiles:"
echo "    php artisan serve          - Levantar servidor"
echo "    php artisan test           - Correr todos los tests"
echo "    php artisan migrate        - Correr migraciones"
echo "    php artisan migrate:fresh --seed  - Reset BD"
echo ""
echo "  Tests en INCOMPLETE (rojo) = correcto, listo para empezar."
echo "====================================================="
