# Automatización programada (Plan B)

Este documento describe cómo programar la ejecución automática del script de
despliegue (`deploy.sh` / `deploy.bat`) usando **Crontab** en Linux o el
**Programador de tareas** en Windows.

> Coloca las capturas de evidencia en `docs/evidencias/` (por ejemplo
> `cron-listado.png`, `task-scheduler.png`, `ejecucion-log.png`).

---

## Opción A — Crontab (Linux / macOS)

El despliegue se ejecuta de forma desatendida en un horario fijo.

### 1. Abrir el editor de crontab

```bash
crontab -e
```

### 2. Agregar la tarea programada

Ejemplo: ejecutar el despliegue **todos los días a las 03:00 AM** y guardar el log.

```cron
0 3 * * * cd /ruta/al/proyecto/Spamlink && ./deploy.sh "deploy: cron automatico" >> /var/log/spamlink-deploy.log 2>&1
```

Otros ejemplos de frecuencia:

```cron
# Cada hora, en el minuto 0
0 * * * * cd /ruta/Spamlink && ./deploy.sh >> ~/spamlink.log 2>&1

# Cada lunes a las 08:00
0 8 * * 1 cd /ruta/Spamlink && ./deploy.sh >> ~/spamlink.log 2>&1
```

### 3. Verificar que la tarea quedó registrada

```bash
crontab -l
```

### 4. Revisar el log de ejecución

```bash
tail -n 50 /var/log/spamlink-deploy.log
```

**Evidencia sugerida:** captura de `crontab -l` y captura del archivo de log.

---

## Opción B — Programador de tareas (Windows)

### Opción B.1 — Por interfaz gráfica

1. Abrir **Programador de tareas** (`taskschd.msc`).
2. **Crear tarea básica** → Nombre: `Spamlink Deploy`.
3. **Desencadenador**: Diariamente → 03:00 AM.
4. **Acción**: Iniciar un programa.
   - Programa o script: `C:\Users\PC_Z3RO\PhpstormProjects\Spamlink\deploy.bat`
   - Iniciar en: `C:\Users\PC_Z3RO\PhpstormProjects\Spamlink`
5. Finalizar y marcar **"Ejecutar tanto si el usuario inició sesión como si no"**.

### Opción B.2 — Por línea de comandos (PowerShell)

Crear la tarea programada que ejecuta `deploy.bat` todos los días a las 03:00:

```powershell
$accion = New-ScheduledTaskAction `
  -Execute "C:\Users\PC_Z3RO\PhpstormProjects\Spamlink\deploy.bat" `
  -WorkingDirectory "C:\Users\PC_Z3RO\PhpstormProjects\Spamlink"

$disparador = New-ScheduledTaskTrigger -Daily -At 3:00AM

Register-ScheduledTask `
  -TaskName "Spamlink Deploy" `
  -Action $accion `
  -Trigger $disparador `
  -Description "Despliegue automatico diario de Spamlink"
```

### Verificar la tarea

```powershell
Get-ScheduledTask -TaskName "Spamlink Deploy"
```

### Ejecutar la tarea manualmente (para generar evidencia)

```powershell
Start-ScheduledTask -TaskName "Spamlink Deploy"
```

**Evidencia sugerida:** captura del Programador de tareas mostrando la tarea
`Spamlink Deploy` y captura de la consola tras la ejecución manual.

---

## Resumen

| Sistema | Herramienta | Archivo ejecutado |
|---------|-------------|-------------------|
| Linux / macOS | Crontab | `deploy.sh` |
| Windows | Programador de tareas | `deploy.bat` |
