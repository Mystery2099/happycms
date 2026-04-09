@echo off
setlocal enabledelayedexpansion

set "PROJECT_ROOT=%~dp0"
if "%PROJECT_ROOT:~-1%"=="\" set "PROJECT_ROOT=%PROJECT_ROOT:~0,-1%"

if "%1"=="--help" goto :help
if "%1"=="-h" goto :help

set "HOST=%HOST%"
if "%HOST%"=="" set "HOST=127.0.0.1"

set "PORT=%1"
if "%PORT%"=="" set "PORT=%PORT%"
if "%PORT%"=="" set "PORT=8000"

set "PKG_MANAGER="

where bun >nul 2>nul
if %ERRORLEVEL%==0 (
  set "PKG_MANAGER=bun"
  goto :found_pkg
)

where npm >nul 2>nul
if %ERRORLEVEL%==0 (
  set "PKG_MANAGER=npm"
  echo Note: bun is not installed. npm will be used instead.
  echo       Consider installing bun for faster builds: https://bun.sh
  goto :found_pkg
)

echo Neither bun nor npm is installed.
echo Frontend assets will not be rebuilt. Using previously compiled build.
goto :skip_build

:found_pkg
cd /d "%PROJECT_ROOT%"
echo Installing dependencies with %PKG_MANAGER%...
if "%PKG_MANAGER%"=="bun" (
  bun install
) else (
  npm install
)
if %ERRORLEVEL% neq 0 (
  echo Install failed with exit code %ERRORLEVEL%.
  exit /b %ERRORLEVEL%
)
echo Building frontend assets with %PKG_MANAGER%...
if "%PKG_MANAGER%"=="bun" (
  bun run build
) else (
  npm run build
)
if %ERRORLEVEL% neq 0 (
  echo Build failed with exit code %ERRORLEVEL%.
  exit /b %ERRORLEVEL%
)

:skip_build

where php >nul 2>nul
if %ERRORLEVEL% neq 0 (
  echo Error: php is not installed or not in PATH.
  echo        Install PHP to run the development server: https://www.php.net
  exit /b 1
)

echo Starting HappyCMS at http://%HOST%:%PORT%
echo Press Ctrl+C to stop.

cd /d "%PROJECT_ROOT%"
php -S "%HOST%:%PORT%" -t "%PROJECT_ROOT%" "%PROJECT_ROOT%\router.php"
goto :eof

:help
echo Usage:
echo   run.bat [port]
echo.
echo Examples:
echo   run.bat
echo   run.bat 8080
echo.
echo Environment overrides:
echo   set HOST=0.0.0.0 ^& run.bat
echo   set PORT=9000 ^& run.bat
goto :eof