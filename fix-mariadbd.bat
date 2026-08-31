@echo off
setlocal EnableExtensions
title Restore mariadbd.exe

rem ===================================================================
rem  One-off fix. Run this ONCE after a reboot.
rem
rem  native\stack\mariadb\bin\mariadbd.exe (a 13 KB loader stub) got into
rem  an NTFS delete-pending state during development and cannot be
rem  recreated until the machine reboots. The running eap-mariadb
rem  service uses mysqld.exe + server.dll and is unaffected - this only
rem  restores the file for git cleanliness and fresh Setup runs.
rem ===================================================================

set "BIN=%~dp0native\stack\mariadb\bin"

if exist "%BIN%\mariadbd.exe" (
    echo   mariadbd.exe is already present - nothing to do.
    goto done
)

echo   Restoring native\stack\mariadb\bin\mariadbd.exe ...

rem 1) try git (exact repo copy)
where git >nul 2>&1 && (
    pushd "%~dp0"
    git checkout -- native/stack/mariadb/bin/mariadbd.exe
    popd
)

rem 2) fallback - it is byte-identical to mysqld.exe
if not exist "%BIN%\mariadbd.exe" (
    if exist "%BIN%\mysqld.exe" copy /Y "%BIN%\mysqld.exe" "%BIN%\mariadbd.exe" >nul
)

if exist "%BIN%\mariadbd.exe" (
    echo   Done - mariadbd.exe restored.
    where git >nul 2>&1 && (
        pushd "%~dp0"
        git status --porcelain native/stack/mariadb/bin/mariadbd.exe
        popd
    )
) else (
    echo   Still blocked. The delete-pending lock has not cleared -
    echo   make sure you have actually rebooted, then run this again.
)

:done
echo.
pause
