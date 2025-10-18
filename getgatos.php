<?php
include 'config/conexao.php';

$result = $conn->query("SELECT gato_id AS id, nome AS name, idade AS age, genero AS sex, url_foto_principal AS image, descricao AS description 
                        FROM gatos 
                        WHERE disponivel_adocao = 1");

$gatos = [];

while ($row = $result->fetch_assoc()) {
    $row['age'] = $row['age'] . ' anos';
    $row['sex'] = ($row['sex'] == 'F') ? 'Fêmea' : 'Macho';
    $gatos[] = $row;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($gatos, JSON_UNESCAPED_UNICODE);

$conn->close();

