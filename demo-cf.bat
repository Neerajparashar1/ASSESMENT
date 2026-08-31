@echo off
setlocal EnableExtensions
title ITM demo - START Cloudflare tunnel

rem ===================================================================
rem  Double-click to put the exam portal on a public Cloudflare link.
rem  No account needed. The link is random each time - it is printed
rem  here, copied to your clipboard, and saved to demo-url.txt.
rem  End the demo with  demo-cf-stop.bat
rem ===================================================================

powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0demo-cf.ps1"

echo.
echo   The Cloudflare tunnel keeps running in the background.
echo   When the demo is over, run  demo-cf-stop.bat
echo.
pause
