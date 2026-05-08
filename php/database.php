<?php

$servidor = "localhost";
$usuario = "root";
$password = "";
$DB = "Pimas";

try {
    $conexion = new PDO("mysql:host=$servidor;dbname=$DB;charset=utf8mb4", $usuario, $password);
    // Configurar PDO para que no lance excepciones globales y actúe como mysqli,
    // devolviendo false en los queries erróneos para no romper la lógica existente.
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

?>