@echo off

echo.
echo =====================================================
echo   KUNNO TECH TEST - Setup Windows
echo   PHP requerido: 8.1 o superior
echo   Laravel: 10.x
echo =====================================================
echo.

REM Verificar PHP
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] PHP no encontrado en el PATH.
    echo.
    echo Opciones de instalacion recomendadas:
    echo   1. Laragon - https://laragon.org/download/  (RECOMENDADO)
    echo   2. XAMPP   - https://www.apachefriends.org/
    echo.
    echo Si ya tienes PHP instalado, agrega su carpeta al PATH de Windows.
    echo.
    pause
    exit /b 1
)

echo [OK] PHP encontrado:
php --version
echo.

REM Verificar Composer
composer --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Composer no encontrado.
    echo Descargalo en: https://getcomposer.org/download/
    echo.
    pause
    exit /b 1
)

echo [OK] Composer encontrado:
composer --version
echo.

REM Verificar extension SQLite (necesaria para los tests)
php -r "echo extension_loaded('pdo_sqlite') ? '[OK] PDO SQLite disponible' : '[AVISO] PDO SQLite no disponible - activa extension=pdo_sqlite en php.ini';" 
echo.

REM Instalar dependencias
echo Instalando dependencias de Laravel...
echo (Esto puede tardar 2-5 minutos la primera vez)
echo.
composer install --no-interaction --prefer-dist
if %errorlevel% neq 0 (
    echo [ERROR] composer install fallo.
    echo Verifica tu conexion a internet e intenta de nuevo.
    pause
    exit /b 1
)
echo.
echo [OK] Dependencias instaladas.
echo.

REM Configurar .env
if not exist ".env" (
    copy .env.example .env >nul
    echo [OK] Archivo .env creado desde .env.example
) else (
    echo [OK] Archivo .env ya existe - no se sobreescribio
)
echo.

REM Generar key
php artisan key:generate --ansi
echo.

REM Crear base de datos SQLite para los tests (no requiere MySQL)
if not exist "database\kunno_test.sqlite" (
    type nul > database\kunno_test.sqlite
    echo [OK] Base de datos SQLite creada en database\kunno_test.sqlite
)
echo.

REM Crear carpetas de storage necesarias
if not exist "storage\framework\cache\data" mkdir storage\framework\cache\data
if not exist "storage\framework\sessions" mkdir storage\framework\sessions
if not exist "storage\framework\views" mkdir storage\framework\views
if not exist "storage\logs" mkdir storage\logs
if not exist "bootstrap\cache" mkdir bootstrap\cache
echo [OK] Carpetas de storage listas
echo.

REM Correr tests para verificar que todo esta bien
echo Verificando instalacion con los tests...
echo.
php artisan test --filter CommissionTest
echo.

echo =====================================================
echo   Setup completado.
echo.
echo   Comandos utiles:
echo     php artisan serve          - Levantar servidor
echo     php artisan test           - Correr todos los tests
echo     php artisan migrate        - Correr migraciones
echo     php artisan migrate:fresh --seed  - Reset BD
echo.
echo   La prueba empieza cuando los tests aparezcan
echo   en estado INCOMPLETE (rojo) - eso es correcto.
echo =====================================================
echo.
pause
