@echo off
setlocal EnableExtensions
title ITM GOI Exams - START

rem ===================================================================
rem  Double-click this file to start the exam platform (no Docker).
rem  It starts MariaDB + Apache, waits for Moodle, then opens the site.
rem ===================================================================

rem --- folder this .bat lives in (E:\ASSESMENT), no trailing backslash
set "ROOT=%~dp0"
if "%ROOT:~-1%"=="\" set "ROOT=%ROOT:~0,-1%"

set "PHP=%ROOT%\native\stack\php\php.exe"
set "CRON=%ROOT%\native\stack\moodle\admin\cli\cron.php"
set "PORTAL=http://localhost:8080"

rem --- need Administrator to control Windows services: self-elevate ---
net session >nul 2>&1
if %errorlevel% neq 0 (
    echo Requesting administrator rights...
    powershell -NoProfile -Command "Start-Process -FilePath '%~f0' -Verb RunAs"
    exit /b
)

echo.
echo [1/4] Starting database (eap-mariadb)...
net start eap-mariadb 2>nul
if %errorlevel% equ 0 (echo       started.) else (echo       already running.)

echo [2/4] Starting web server (eap-apache)...
net start eap-apache 2>nul
if %errorlevel% equ 0 (echo       started.) else (echo       already running.)

echo [3/4] Ensuring the 1-minute cron task exists...
schtasks /query /tn "EAP-Moodle-Cron" >nul 2>&1
if errorlevel 1 (
    schtasks /create /tn "EAP-Moodle-Cron" /sc minute /mo 1 /ru SYSTEM /rl HIGHEST /f ^
        /tr "\"%PHP%\" \"%CRON%\"" >nul
    echo       registered.
) else (
    echo       already registered.
)

echo [4/4] Waiting for Moodle to answer on %PORTAL% ...
set "UP="
for /L %%i in (1,1,40) do (
    if not defined UP (
        powershell -NoProfile -Command "try{ if((Invoke-WebRequest -UseBasicParsing -TimeoutSec 5 '%PORTAL%/login/index.php').StatusCode -eq 200){exit 0} }catch{}; exit 1"
        if not errorlevel 1 (
            set "UP=1"
            echo       portal is UP.
        ) else (
            timeout /t 2 >nul
        )
    )
)
if not defined UP echo       WARNING: no response yet - check the two service windows.

echo.
echo ===================================================================
echo   Portal : %PORTAL%/
echo   Admin  : examadmin  /  44BDUbI!1wF*i+evVKrG@C
echo ===================================================================
start "" %PORTAL%/
echo.
echo This window closes in 8 seconds.
timeout /t 8 >nul
endlocal
