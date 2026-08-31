@echo off
setlocal EnableExtensions
title ITM demo - STOP Cloudflare tunnel

rem Double-click to end the demo: kill the tunnel and put the portal
rem back to http://localhost:8080

echo   Stopping the Cloudflare tunnel...
taskkill /IM cloudflared.exe /F >nul 2>&1

echo   Restoring local config...
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0demo-stop.ps1"

del "%~dp0demo-url.txt" >nul 2>&1

echo.
echo   Demo ended. http://localhost:8080 is back to normal.
echo.
pause
