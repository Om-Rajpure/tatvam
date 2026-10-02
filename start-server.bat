@echo off
setlocal
echo ===================================================
echo   Starting Tatvam Publication Local Server...
echo ===================================================

set "PHP_EXE=%LOCALAPPDATA%\Microsoft\WinGet\Packages\PHP.PHP.8.2_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe"

if not exist "%PHP_EXE%" (
    where php >nul 2>&1
    if %errorlevel% equ 0 (
        set "PHP_EXE=php"
    ) else (
        echo [ERROR] PHP is not found! Please ensure PHP is installed.
        pause
        exit /b 1
    )
)

echo PHP Executable: %PHP_EXE%
echo Document Root : %~dp0public_html
echo Server URL    : http://localhost:8000
echo.
echo Press Ctrl+C in this window to stop the server.
echo ===================================================

cd /d "%~dp0public_html"
"%PHP_EXE%" -S localhost:8000
pause
