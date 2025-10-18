<?php
include 'config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $status = $_POST['status'] ?? null;

    if ($id && $status) {
        $stmt = $conn->prepare("UPDATE adotantes SET status = ? WHERE adotante_id = ?");
        $stmt->bind_param("si", $status, $id);
        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        echo 'missing parameters';
    }
}

