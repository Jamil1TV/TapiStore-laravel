@echo off
setlocal
cd /d "%~dp0.."

echo Laravel Commerce - XAMPP MySQL setup
echo.

if not exist "vendor\autoload.php" (
    echo Running composer install...
    composer install --no-interaction
    if errorlevel 1 exit /b 1
)

if not exist ".env" (
    copy .env.example .env
    php artisan key:generate
)

echo Creating database laravel_ecommerce (requires MySQL running in XAMPP)...
if exist "C:\xampp\mysql\bin\mysql.exe" (
    C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS laravel_ecommerce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    if errorlevel 1 (
        echo.
        echo ERROR: Could not connect to MySQL. Start MySQL in XAMPP Control Panel, then run this script again.
        exit /b 1
    )
) else (
    echo Skipped mysql.exe - create database "laravel_ecommerce" manually in phpMyAdmin if needed.
)

php artisan config:clear
php artisan migrate --seed --force
if errorlevel 1 (
    echo.
    echo Migration failed. Check .env DB_* settings and that MySQL is running.
    exit /b 1
)

php artisan storage:link 2>nul

echo.
echo Done. Start the app with: php artisan serve
echo Then open http://127.0.0.1:8000
echo Admin: admin@example.com / password
endlocal
