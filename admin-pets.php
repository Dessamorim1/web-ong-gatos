<?php

session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['user_name'])) {
    header('Location: login.php');
    exit;
}

include 'config/conexao.php';

if (isset($_GET['buscar'])) {
    $id = intval($_GET['buscar']);
    $res = $conn->query("SELECT * FROM gatos WHERE gato_id=$id");
    if ($res->num_rows > 0) {
        echo json_encode($res->fetch_assoc());
    } else {
        echo json_encode([]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $nome = $_POST['nome'];
    $idade = $_POST['idade'];
    $genero = $_POST['genero'];
    $descricao = $_POST['descricao'];
    $disponivel = isset($_POST['disponivel']) ? 1 : 0;

    $imagem = '';
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
        $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $nomeArquivo = uniqid() . "." . $extensao;
        $caminho = 'uploads/gatos/' . $nomeArquivo;

        if (!is_dir('uploads/gatos')) {
            mkdir('uploads/gatos', 0777, true);
        }

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho)) {
            $imagem = $caminho;
        }
    } else if (isset($_POST['imagem_antiga'])) {
        $imagem = $_POST['imagem_antiga'];
    }

    if ($id) {
        $sql = "UPDATE gatos SET 
                    nome='$nome', 
                    idade='$idade', 
                    genero='$genero', 
                    descricao='$descricao', 
                    disponivel_adocao='$disponivel', 
                    url_foto_principal='$imagem' 
                WHERE gato_id=$id";
        $conn->query($sql);
        $msg = "Pet atualizado com sucesso!";
    } else {
        $sql = "INSERT INTO gatos (nome, idade, genero, descricao, disponivel_adocao, url_foto_principal) 
                VALUES ('$nome', '$idade', '$genero', '$descricao', '$disponivel', '$imagem')";
        $conn->query($sql);
        $msg = "Pet adicionado com sucesso!";
    }
}

if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    $conn->query("DELETE FROM gatos WHERE gato_id=$id");
    $msg = "Pet excluído com sucesso!";
}


$result = $conn->query("SELECT * FROM gatos");
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Pets - Dashboard Admin</title>
    <link rel="stylesheet" href=" assets/css/admin-dashboard.css">
    <link rel="stylesheet" href=" assets/css/style.css">
</head>

<body>

    <main class="admin-main">
        <div class="container">
            <h1 class="section-title">Gerenciar Gatinhos</h1>
            <p class="section-subtitle">Use o formulário abaixo para Adicionar ou Editar um pet. Use a lista para
                Excluir.</p>

            <?php if (isset($msg))
                echo "<p style='color:green;'>$msg</p>"; ?>

            <a href="admin-dashboard.php" class="btn btn-lg btn-dashboard-voltar">← Voltar para o Dashboard</a>

            <!-- FORMULÁRIO -->
            <section class="admin-form-section">
                <h2>Adicionar / Editar Pet</h2>
                <form id="pet-form" method="POST" action="admin-pets.php" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="pet-id" value="">
                    <input type="hidden" name="imagem_antiga" id="imagem-antiga" value="">

                    <div class="form-group">
                        <label for="pet-name">Nome do Gatinho:</label>
                        <input type="text" id="pet-name" name="nome" required>
                    </div>

                    <div class="form-group">
                        <label for="pet-gender">Gênero:</label>
                        <select id="pet-gender" name="genero" required>
                            <option value="">Selecione</option>
                            <option value="F">Fêmea</option>
                            <option value="M">Macho</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="pet-age">Idade (anos):</label>
                        <input type="number" id="pet-age" name="idade" required min="0">
                    </div>

                    <div class="form-group">
                        <label for="pet-description">Descrição:</label>
                        <textarea id="pet-description" name="descricao" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Foto do gato:</label>
                        <input type="file" name="imagem" id="pet-image">
                    </div>

                    <div class="form-group">
                        <label for="pet-special">Disponível para Adoção?</label>
                        <input type="checkbox" id="pet-special" name="disponivel">
                    </div>

                    <button type="submit" class="btn btn-primary">Adicionar / Salvar Pet</button>
                </form>
            </section>

            <!-- LISTA DE GATOS -->
            <section class="admin-list-section">
                <h2>Pets Cadastrados</h2>
                <table class="admin-pet-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Gênero / Idade</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                ?>
                                <tr>
                                    <td><?= $row['gato_id'] ?></td>
                                    <td><?= $row['nome'] ?></td>
                                    <td><?= $row['genero'] ?> / <?= $row['idade'] ?></td>
                                    <td>
                                        <button class="btn btn-edit" onclick="editPet(<?= $row['gato_id'] ?>)">Editar</button>
                                        <button class="btn btn-delete"
                                            onclick="confirmDelete(<?= $row['gato_id'] ?>)">Excluir</button>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='4' style='text-align:center;'>Nenhum pet cadastrado.</td></tr>";
                        }
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </section>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/admin-pets.js"></script>
</body>

</html>