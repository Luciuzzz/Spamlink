#!/usr/bin/env bash
# =========================================================
#  Spamlink - Script de despliegue automatizado (Linux/Mac)
#  Automatiza: git add/commit/push + docker build + docker run
#  Uso:  ./deploy.sh "mensaje de commit"
# =========================================================

set -e

IMAGE_NAME="spamlink"
CONTAINER_NAME="spamlink-app"
HOST_PORT=8080
CONTAINER_PORT=8080
BRANCH="main"

# Mensaje de commit: usa el argumento o uno por defecto con fecha/hora
if [ -z "$1" ]; then
    COMMIT_MSG="deploy: actualizacion automatica $(date '+%Y-%m-%d %H:%M:%S')"
else
    COMMIT_MSG="$1"
fi

echo "============================================"
echo " [1/5] Git add"
echo "============================================"
git add .

echo "============================================"
echo " [2/5] Git commit"
echo "============================================"
git commit -m "$COMMIT_MSG" || echo "(Sin cambios para commitear, se continua)"

echo "============================================"
echo " [3/5] Git push (rama $BRANCH)"
echo "============================================"
git push origin "$BRANCH"

echo "============================================"
echo " [4/5] Docker build"
echo "============================================"
docker build -t "$IMAGE_NAME" .

echo "============================================"
echo " [5/5] Docker run"
echo "============================================"
# Detener y eliminar contenedor anterior si existe
docker rm -f "$CONTAINER_NAME" >/dev/null 2>&1 || true
docker run -d --name "$CONTAINER_NAME" -p "${HOST_PORT}:${CONTAINER_PORT}" "$IMAGE_NAME"

echo ""
echo "============================================"
echo " Despliegue completado correctamente"
echo " App disponible en: http://localhost:${HOST_PORT}"
echo "============================================"
