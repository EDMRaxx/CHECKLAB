<?php
require_once 'libs/qrlib.php'; // 👈 Asegúrate de que la ruta sea correcta

// Carpeta de salida
$directorio = __DIR__ . '/qrs_universales/';
if (!file_exists($directorio)) {
    mkdir($directorio, 0777, true);
}

// QR universal para alumnos
$dataAlumno = json_encode([
    "tipo" => "alumno",
    "lab"  => "Laboratorio1"
]);
QRcode::png($dataAlumno, $directorio . "qr_alumnos_Lab1.png", 2, 20);


$dataMaestro = json_encode([
    "tipo" => "maestro",
    "lab"  => "Laboratorio1"
]);
QRcode::png($dataMaestro, $directorio . "qr_maestros_Lab1.png", 2, 20);

// QR universal para alumnos
$dataAlumno = json_encode([
    "tipo" => "alumno",
    "lab"  => "Laboratorio Cisco"
]);
QRcode::png($dataAlumno, $directorio . "qr_alumnos_LabC.png", 2, 20);


$dataMaestro = json_encode([
    "tipo" => "maestro",
    "lab"  => "Laboratorio Cisco"
]);
QRcode::png($dataMaestro, $directorio . "qr_maestros_LabC.png", 2, 20);

$dataMaestro = json_encode([
  "url" =>  "https://b1df1c84685c.ngrok-free.app/catalogo/perfil.php#lista"
]);
QRcode::png("https://b1df1c84685c.ngrok-free.app/catalogo/perfil.php#lista", $directorio . "qr_maestros_LabPrueba2.png", 2, 20);

$dataMaestro = json_encode([
  "url" =>  "https://b1df1c84685c.ngrok-free.app/catalogo/perfil.php#reporte"

]);
QRcode::png("https://b1df1c84685c.ngrok-free.app/catalogo/perfil.php#reporte", $directorio . "qr_maestros_LabCisco.png", 2, 20);

echo "✅ QR universales generados en /qrs_universales/";

