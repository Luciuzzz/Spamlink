<?php

return [
    'path' => env('BACKUP_PATH', storage_path('app/backups')),
    'keep_days' => (int) env('BACKUP_KEEP_DAYS', 7),
    'logs_path' => env('BACKUP_LOGS_PATH', storage_path('logs')),
    'include_logs' => filter_var(env('BACKUP_INCLUDE_LOGS', true), FILTER_VALIDATE_BOOL),
    'include_database' => filter_var(env('BACKUP_INCLUDE_DATABASE', true), FILTER_VALIDATE_BOOL),
    'schedule_time' => env('BACKUP_SCHEDULE_TIME', '02:00'),
];
