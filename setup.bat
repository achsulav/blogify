@echo off
setlocal

:: Request admin privileges to update hosts file
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo [INFO] Requesting administrative privileges to update hosts file...
    powershell -Command "Start-Process '%~dpnx0' -ArgumentList '%*' -Verb RunAs"
    exit /b
)

echo ==========================================================
echo  Blogify One-Command Setup (Windows Docker)
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

:: 2. Generate SSL Certificates if they don't exist
if not exist "app\SSL\_wildcard.blogify.dev+1.pem" (
    echo [INFO] Generating SSL certificates using Docker...
    if not exist "app\SSL" mkdir "app\SSL"
    docker run --rm -v "%cd%\app\SSL:/certs" alpine sh -c "apk add --no-cache openssl && openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /certs/_wildcard.blogify.dev+1-key.pem -out /certs/_wildcard.blogify.dev+1.pem -subj \"/CN=*.blogify.dev\""
) else (
    echo [INFO] SSL certificates already exist.
)

:: 3. Start Docker Containers
echo [INFO] Starting Docker containers...
docker compose up -d

:: 4. Wait for DB to be ready
echo [INFO] Waiting for database to be ready (approx 15 seconds)...
timeout /t 15 /nobreak >nul

:: 5. Run Migrations and Seeds
echo [INFO] Setting up database schema...
docker exec blogify_php php migrate.php
docker exec blogify_php php seed_categories.php

echo.
echo ==========================================================
echo [SUCCESS] Setup complete! 
echo Access your project at https://blogify.dev
echo ==========================================================
endlocal
