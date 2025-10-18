<?php
$host = "localhost";
$usuario = "root";
$senha = "@2020";
$banco = "ONG";

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
