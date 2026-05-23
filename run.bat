@echo off
setlocal

:: Request admin privileges to update hosts file
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo [INFO] Requesting administrative privileges to update hosts file...
    powershell -Command "Start-Process '%~dpnx0' -ArgumentList '%*' -Verb RunAs"
    exit /b
)

:: Blogify Windows Docker Runner

if "%1"=="--stop" goto stop_all

echo ==========================================================
echo  Starting Blogify (Windows Docker)
echo ==========================================================
echo.

:: 1. Copy .env if not exists
if not exist .env (
    echo [INFO] .env not found, copying from .env.docker...
    copy .env.docker .env >nul
)

:: 1.5 Update Windows Hosts file for domains
find /c /i "blogify.dev" "%WINDIR%\System32\drivers\etc\hosts" >nul
if %errorlevel% neq 0 (
    echo [INFO] Adding blogify.dev to Windows hosts file...
    echo.>> "%WINDIR%\System32\drivers\etc\hosts"
    echo # Blogify Local Environment>> "%WINDIR%\System32\drivers\etc\hosts"
    echo 127.0.0.1 blogify.dev www.blogify.dev admin.blogify.dev api.blogify.dev>> "%WINDIR%\System32\drivers\etc\hosts"
)

:: 2. Check for SSL Certificates (required by Nginx)
if not exist "app\SSL\_wildcard.blogify.dev+1.pem" (
    echo [INFO] SSL certificates not found in app\SSL.
    echo [INFO] Generating self-signed certificates using Docker...
    if not exist "app\SSL" mkdir "app\SSL"
    docker run --rm -v "%cd%\app\SSL:/certs" alpine sh -c "apk add --no-cache openssl && openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /certs/_wildcard.blogify.dev+1-key.pem -out /certs/_wildcard.blogify.dev+1.pem -subj \"/CN=*.blogify.dev\""
)

:: 3. Start Docker Compose
echo [INFO] Starting Docker containers...
docker compose up -d

echo.
echo ==========================================================
echo  Blogify is ready!
echo ==========================================================
echo  URL      : https://blogify.dev
echo  To stop  : run.bat --stop
echo  Logs     : docker compose logs -f
echo ==========================================================
goto end

:stop_all
echo [INFO] Stopping all Docker containers...
docker compose down
echo [INFO] All services stopped.

:end
endlocal
