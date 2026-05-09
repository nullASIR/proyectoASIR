<?php

$servidor = "192.168.10.10";
$usuario = "web";
$password = "Abc1234";
$DB = "Pimas";

try {
    $conexion = new mysqli($servidor, $usuario, $password, $DB);
    $conexion->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("Error de conexión: " . $e->getMessage());
}

?>