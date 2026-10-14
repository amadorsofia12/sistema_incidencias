<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = "localhost";
$usuario = "root";
$contrasena = "";
$base_datos = "db_incidencias";

$conexion = new mysqli($host, $usuario, $contrasena, $base_datos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>