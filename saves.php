<?php
header('Content-Type: application/json');

// Habilitar la visualización de errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Función para registrar errores
function logError($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, __DIR__ . '/error.log');
}

// Asegúrate de que la carpeta 'saves' exista y tenga permisos de escritura
$savesDir = __DIR__ . '/saves';
if (!file_exists($savesDir)) {
    if (!mkdir($savesDir, 0777, true)) {
        logError("No se pudo crear el directorio: $savesDir");
        echo json_encode(['success' => false, 'message' => 'No se pudo crear el directorio de guardado']);
        exit;
    }
}

// Obtener los datos enviados
$rawData = file_get_contents('php://input');
if ($rawData === false) {
    logError("No se pudieron leer los datos de entrada");
    echo json_encode(['success' => false, 'message' => 'No se pudieron leer los datos de entrada']);
    exit;
}

$data = json_decode($rawData, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    logError("Error al decodificar JSON: " . json_last_error_msg());
    echo json_encode(['success' => false, 'message' => 'Error al procesar los datos enviados']);
    exit;
}

if (isset($data['fileName']) && isset($data['content'])) {
    $fileName = basename($data['fileName']); // Asegurarse de que el nombre del archivo sea seguro
    $filePath = $savesDir . '/' . $fileName;
    
    $result = file_put_contents($filePath, $data['content']);
    if ($result !== false) {
        echo json_encode(['success' => true]);
    } else {
        logError("No se pudo escribir en el archivo: $filePath");
        echo json_encode(['success' => false, 'message' => 'No se pudo escribir el archivo']);
    }
} else {
    logError("Datos incompletos recibidos");
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
}
?>
