<?php

$servidor = "localhost";
$usuario = "root";
$password = "";
$DB = "Pimas";

try {
    $conexion = new mysqli($servidor, $usuario, $password, $DB);
    $conexion->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("Error de conexión: " . $e->getMessage());
}

?>