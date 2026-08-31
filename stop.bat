@echo off
setlocal EnableExtensions
title ITM GOI Exams - STOP

rem Double-click to stop the exam platform (web server + database).

net session >nul 2>&1
if %errorlevel% neq 0 (
    echo Requesting administrator rights...
    powershell -NoProfile -Command "Start-Process -FilePath '%~f0' -Verb RunAs"
    exit /b
)

echo Stopping web server (eap-apache)...
net stop eap-apache 2>nul

echo Stopping database (eap-mariadb)...
net stop eap-mariadb 2>nul

echo.
echo Stopped. This window closes in 5 seconds.
timeout /t 5 >nul
endlocal
