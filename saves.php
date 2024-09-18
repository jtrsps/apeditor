<?php
header('Content-Type: application/json');

// Asegúrate de que la carpeta 'saves' exista y tenga permisos de escritura
$savesDir = __DIR__ . '/saves';
if (!file_exists($savesDir)) {
    mkdir($savesDir, 0777, true);
}

// Obtener los datos enviados
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['fileName']) && isset($data['content'])) {
    $fileName = basename($data['fileName']); // Asegurarse de que el nombre del archivo sea seguro
    $filePath = $savesDir . '/' . $fileName;
    
    if (file_put_contents($filePath, $data['content']) !== false) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se pudo escribir el archivo']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
}
?>