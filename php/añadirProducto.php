<?php

include "database.php";

$ean;
$name;
$price;
$stock;
$status;
$tax;
$type;

$query = "INSERT INTO Productos (Ean, Nombre, Precio, Stock, Estado, Impuesto, Tipo) VALUES ($ean,'$name',$price,$stock,'$status',$tax,'$type')";

?>
