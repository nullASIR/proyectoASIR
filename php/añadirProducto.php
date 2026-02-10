<?php

include "database.php";

$ean;
$name;
$price;
$stock;
$status;
$tax;
$type;

$query = "INSERT INTO productos VALUES ($ean,$name,$price,$stock,$status,$tax,$type)";

?>