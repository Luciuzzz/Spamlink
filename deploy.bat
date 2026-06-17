@echo off
REM =========================================================
REM  Spamlink - Script de despliegue automatizado (Windows)
REM  Automatiza: git add/commit/push + docker build + docker run
REM  Uso:  deploy.bat "mensaje de commit"
REM =========================================================

setlocal enabledelayedexpansion

set IMAGE_NAME=spamlink
set CONTAINER_NAME=spamlink-app
set HOST_PORT=8080
set CONTAINER_PORT=8080
set BRANCH=main

REM Mensaje de commit: usa el argumento o uno por defecto con fecha/hora
if "%~1"=="" (
    set COMMIT_MSG=deploy: actualizacion automatica %DATE% %TIME%
) else (
    set COMMIT_MSG=%~1
)

echo ============================================
echo  [1/5] Git add
echo ============================================
git add .
if errorlevel 1 goto :error

echo ============================================
echo  [2/5] Git commit
echo ============================================
git commit -m "!COMMIT_MSG!"
if errorlevel 1 echo (Sin cambios para commitear, se continua)

echo ============================================
echo  [3/5] Git push (rama %BRANCH%)
echo ============================================
git push origin %BRANCH%
if errorlevel 1 goto :error

echo ============================================
echo  [4/5] Docker build
echo ============================================
docker build -t %IMAGE_NAME% .
if errorlevel 1 goto :error

echo ============================================
echo  [5/5] Docker run
echo ============================================
REM Detener y eliminar contenedor anterior si existe
docker rm -f %CONTAINER_NAME% >nul 2>&1
docker run -d --name %CONTAINER_NAME% -p %HOST_PORT%:%CONTAINER_PORT% %IMAGE_NAME%
if errorlevel 1 goto :error

echo.
echo ============================================
echo  Despliegue completado correctamente
echo  App disponible en: http://localhost:%HOST_PORT%
echo ============================================
goto :end

:error
echo.
echo *** ERROR durante el despliegue. Revise el mensaje anterior. ***
exit /b 1

:end
endlocal
