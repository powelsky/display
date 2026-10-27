@echo off
rem Stops the hidden PHP dev server started by start.vbs (port 8080 only).
setlocal enabledelayedexpansion
set "PORT=8080"
set "FOUND="

for /f "tokens=5" %%p in ('netstat -ano ^| findstr /R /C:"127.0.0.1:%PORT% .*LISTENING"') do (
    echo Stopping PID %%p on port %PORT% ...
    taskkill /PID %%p /F >nul 2>&1
    set "FOUND=1"
)

if not defined FOUND echo Tidak ada server yang listening di port %PORT%.
endlocal
