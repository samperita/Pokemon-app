<?php
header('Content-Type: text/plain; version=0.0.4');

// URL de PHP-FPM status
$fpm_status_url = 'http://127.0.0.1/status';

// Obtener el estado de PHP-FPM
$fpm_status = file_get_contents($fpm_status_url);
if ($fpm_status === false) {
    http_response_code(500);
    echo "# ERROR: No se pudo obtener el estado de PHP-FPM\n";
    exit;
}

// Parsear el estado
$metrics = [];
foreach (explode("\n", $fpm_status) as $line) {
    if (preg_match('/^(.*):\s+(.*)$/', $line, $matches)) {
        $key = str_replace(' ', '_', strtolower(trim($matches[1])));
        $value = trim($matches[2]);
        $metrics[$key] = is_numeric($value) ? (float)$value : $value;
    }
}
// Archivos para almacenar métricas
$latency_log = '/tmp/metrics_latency.log';
$restart_log = '/tmp/metrics_restart.log';

// Inicializa los archivos si no existen
if (!file_exists($latency_log)) {
    file_put_contents($latency_log, '');
}
if (!file_exists($restart_log)) {
    file_put_contents($restart_log, '0');
}

// Cálculo de latencia
$start_time = microtime(true);

// Simula la solicitud aquí (reemplaza con lógica real)
usleep(rand(1000, 5000));

$end_time = microtime(true);
$latency = $end_time - $start_time;

// Registrar la latencia en el archivo
file_put_contents($latency_log, $latency . "\n", FILE_APPEND);

// Calcular la media de las últimas 100 latencias
$latency_values = file($latency_log, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$latency_values = array_slice($latency_values, -100);
$avg_latency = array_sum($latency_values) / count($latency_values);

// Reinicios de contenedores
$restart_count = (int)file_get_contents($restart_log);

// Exponer métricas en formato Prometheus
echo "# HELP php_fpm_active_processes Número de procesos activos en PHP-FPM\n";
echo "# TYPE php_fpm_active_processes gauge\n";
echo "php_fpm_active_processes {$metrics['active_processes']}\n";

echo "# HELP php_fpm_idle_processes Número de procesos inactivos en PHP-FPM\n";
echo "# TYPE php_fpm_idle_processes gauge\n";
echo "php_fpm_idle_processes {$metrics['idle_processes']}\n";

echo "# HELP php_fpm_total_processes Número total de procesos en PHP-FPM\n";
echo "# TYPE php_fpm_total_processes gauge\n";
echo "php_fpm_total_processes {$metrics['total_processes']}\n";

echo "# HELP php_fpm_max_active_processes Máximo de procesos activos en PHP-FPM\n";
echo "# TYPE php_fpm_max_active_processes gauge\n";
echo "php_fpm_max_active_processes {$metrics['max_active_processes']}\n";

echo "# HELP php_fpm_max_children_reached Número de veces que se alcanzó el límite de procesos hijos configurados en PHP-FPM\n";
echo "# TYPE php_fpm_max_children_reached counter\n";
echo "php_fpm_max_children_reached {$metrics['max_children_reached']}\n";

echo "# HELP php_fpm_start_since Tiempo en segundos desde que PHP-FPM inició\n";
echo "# TYPE php_fpm_start_since gauge\n";
echo "php_fpm_start_since {$metrics['start_since']}\n";

echo "# HELP php_fpm_listen_queue Número actual de solicitudes en la cola de escucha\n";
echo "# TYPE php_fpm_listen_queue gauge\n";
echo "php_fpm_listen_queue {$metrics['listen_queue']}\n";

echo "# HELP php_fpm_max_listen_queue Tamaño máximo alcanzado por la cola de escucha\n";
echo "# TYPE php_fpm_max_listen_queue gauge\n";
echo "php_fpm_max_listen_queue {$metrics['max_listen_queue']}\n";

echo "# HELP php_fpm_listen_queue_length Tamaño máximo configurado de la cola de escucha\n";
echo "# TYPE php_fpm_listen_queue_length gauge\n";
echo "php_fpm_listen_queue_length {$metrics['listen_queue_len']}\n";

echo "# HELP php_fpm_slow_requests Número total de solicitudes lentas en PHP-FPM\n";
echo "# TYPE php_fpm_slow_requests counter\n";
echo "php_fpm_slow_requests {$metrics['slow_requests']}\n";

echo "# HELP php_fpm_accepted_connections Número total de conexiones aceptadas por PHP-FPM\n";
echo "# TYPE php_fpm_accepted_connections counter\n";
echo "php_fpm_accepted_connections {$metrics['accepted_conn']}\n";

echo "# HELP php_memory_usage Uso de memoria actual del proceso PHP (en bytes)\n";
echo "# TYPE php_memory_usage gauge\n";
echo "php_memory_usage " . memory_get_usage(true) . "\n";

echo "# HELP php_cpu_usage_seconds_total Tiempo de CPU utilizado por el proceso PHP (en segundos)\n";
echo "# TYPE php_cpu_usage_seconds_total counter\n";
echo "php_cpu_usage_seconds_total " . (microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) . "\n";

echo "# HELP php_request_latency_seconds Latencia promedio de las últimas 100 solicitudes, en segundos\n";
echo "# TYPE php_request_latency_seconds gauge\n";
echo "php_request_latency_seconds {$avg_latency}\n";

echo "# HELP php_container_restarts Número total de reinicios de contenedores\n";
echo "# TYPE php_container_restarts counter\n";
echo "php_container_restarts {$restart_count}\n";
?>