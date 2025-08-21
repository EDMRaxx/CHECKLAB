<?php
require_once 'libs/qrlib.php';
require_once 'db.php';

// Ruta donde se guardarán los QR personales
$directorio = __DIR__ . '/qrs_personales/';
if (!file_exists($directorio)) {
    mkdir($directorio, 0777, true);
}

// Tamaño del QR
$tamaño = 50;

// Función para generar QR personal
function generarQR($tipo, $id, $pass, $directorio, $tamaño) {
    $data = json_encode([
        'tipo' => $tipo,
        'id' => $id,
        'pass' => $pass
    ]);

    $archivo = $directorio . "{$tipo}_{$id}_personal.png";
    QRcode::png($data, $archivo, QR_ECLEVEL_L, $tamaño);
    echo "✅ Generado: $archivo<br>";
}

// Obtener alumnos
$stmt = $pdo->query("SELECT matricula AS id, password FROM alumnos");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    generarQR('alumno', $row['id'], $row['password'], $directorio, $tamaño);
}

// Obtener maestros
$stmt = $pdo->query("SELECT empleado AS id, password FROM maestros");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    generarQR('maestro', $row['id'], $row['password'], $directorio, $tamaño);
}

echo "<br>✅ Todos los QR personales han sido generados correctamente.";
