<?php
session_start();

if (!isset($_SESSION['user_name'])) {
    header('Location: login.php');
    exit;
}

include 'config/conexao.php';

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Administrador - Lar Bola de Pelos</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin-dashboard.css">
</head>

<body>

    <div id="header-placeholder"></div>

    <main class="admin-main">
        <div class="container-adm-dash">
            <h1 class="section-title">Bem-vindo(a), Administrador(a)!</h1>
            <p class="section-subtitle">Gerencie o abrigo e acompanhe as adoções.</p>

            <div class="dashboard-grid">

                <div class="dashboard-card">
                    <div class="card-icon">🐾</div> </br>
                    <h3>Gatinhos para Adoção</h3>
                    <p>Adicione, edite ou remova perfis de gatinhos.</p>
                    <a href="admin-pets.php" class="btn btn-primary">Gerenciar Perfis</a>
                </div>

                <div class="dashboard-card">
                    <div class="card-icon">💌</div> </br>
                    <h3>Formulários de Adoção</h3>
                    <p>Revise e aprove formulários enviados por interessados.</p>
                    <a href="gerenciamento-forms.php" class="btn btn-secondary">Ver 5 Novos</a>
                </div>

                <div class="dashboard-card">
                    <div class="card-icon">🚪</div> </br>
                    <h3>Sair do Painel de Adm</h3>
                    <p>Clique aqui para encerrar sua sessão com segurança.</p>
                    <button class="btn btn-danger" onclick="logoutAdmin()">Sair</button>
                </div>
            </div>
        </div>
    </main>

    <div id="footer-placeholder"></div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/admin-script.js"></script>

</body>

</html>