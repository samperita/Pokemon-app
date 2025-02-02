<?php
header('Content-Type: text/plain');

// URL del endpoint nginx_status
$nginx_status_url = 'http://127.0.0.1/nginx_status';

// Obtener los datos de nginx_status
$nginx_status = file_get_contents($nginx_status_url);
if ($nginx_status === false) {
    http_response_code(500);
    echo "# ERROR: No se pudo obtener el estado de NGINX\n";
    exit;
}

// Parsear las líneas de nginx_status
$lines = array_map('trim', explode("\n", $nginx_status)); // Elimina espacios al inicio/final de cada línea

// Métrica 1: Conexiones activas
if (isset($lines[0]) && strpos($lines[0], ':') !== false) {
    $active_connections = (int)trim(explode(':', $lines[0])[1]);
    echo "# HELP nginx_active_connections Número de conexiones activas\n";
    echo "# TYPE nginx_active_connections gauge\n";
    echo "nginx_active_connections {$active_connections}\n";
}

// Métrica 2: Conexiones aceptadas y solicitudes
if (isset($lines[2])) {
    $accepts_handled_requests = preg_split('/\s+/', $lines[2]); // Divide por espacios múltiples
    $accepts = isset($accepts_handled_requests[0]) ? (int)$accepts_handled_requests[0] : 0;
    $handled = isset($accepts_handled_requests[1]) ? (int)$accepts_handled_requests[1] : 0;
    $requests = isset($accepts_handled_requests[2]) ? (int)$accepts_handled_requests[2] : 0;

    echo "# HELP nginx_accepts_total Total de conexiones aceptadas\n";
    echo "# TYPE nginx_accepts_total counter\n";
    echo "nginx_accepts_total {$accepts}\n";

    echo "# HELP nginx_requests_total Total de solicitudes manejadas\n";
    echo "# TYPE nginx_requests_total counter\n";
    echo "nginx_requests_total {$requests}\n";
}

// Métrica 3: Conexiones leyendo, escribiendo y esperando
if (isset($lines[3])) {
    $line_3 = strtolower($lines[3]); // Convierte todo a minúsculas para evitar problemas de formato
    preg_match('/reading:\s*(\d+)\s*writing:\s*(\d+)\s*waiting:\s*(\d+)/', $line_3, $matches);

    $reading = isset($matches[1]) ? (int)$matches[1] : 0;
    $writing = isset($matches[2]) ? (int)$matches[2] : 0;
    $waiting = isset($matches[3]) ? (int)$matches[3] : 0;

    echo "# HELP nginx_reading_connections Conexiones leyendo solicitudes\n";
    echo "# TYPE nginx_reading_connections gauge\n";
    echo "nginx_reading_connections {$reading}\n";

    echo "# HELP nginx_writing_connections Conexiones escribiendo respuestas\n";
    echo "# TYPE nginx_writing_connections gauge\n";
    echo "nginx_writing_connections {$writing}\n";

    echo "# HELP nginx_waiting_connections Conexiones en espera\n";
    echo "# TYPE nginx_waiting_connections gauge\n";
    echo "nginx_waiting_connections {$waiting}\n";
}
?>
