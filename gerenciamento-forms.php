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


$sql = "SELECT a.*, g.nome AS nome_gato
FROM adotantes AS a
INNER JOIN gatos AS g ON a.gato_id = g.gato_id
ORDER BY a.adotante_id DESC;
";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Lar Bola de Pelos</title>
    <link rel="stylesheet" href="assets/css/admin-form.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <a href="admin-dashboard.php" class="btn btn-lg btn-dashboard-voltar">← Voltar para o Dashboard</a>

    <div class="admin-container">
        <!-- Header -->
        <div class="admin-header">
            <h1 class="admin-title">Painel Administrativo</h1>
            <p class="admin-subtitle">Gerencie os formulários de adoção recebidos</p>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <?php
            // Estatísticas simples
            $total = $conn->query("SELECT COUNT(*) AS total FROM adotantes")->fetch_assoc()['total'];
            $pendentes = $conn->query("SELECT COUNT(*) AS pendentes FROM adotantes WHERE status = 'Pendente'")->fetch_assoc()['pendentes'];
            $aprovados = $conn->query("SELECT COUNT(*) AS aprovados FROM adotantes WHERE status = 'Aprovado'")->fetch_assoc()['aprovados'];
            ?>
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Total de Solicitações</span>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?= $total ?></div>
                    <p class="stat-description">Formulários recebidos</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Pendentes</span>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?= $pendentes ?></div>
                    <p class="stat-description">Aguardando análise</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Aprovados</span>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?= $aprovados ?></div>
                    <p class="stat-description">Adoções confirmadas</p>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="table-card">
            <div class="table-card-header">
                <div>
                    <h2 class="table-title">Formulários de Adoção</h2>
                    <p class="table-description">Visualize e gerencie todas as solicitações</p>
                </div>
            </div>
            <div class="table-card-content">
                <!-- Search and Filters -->
                <div class="filters-container">
                    <div class="search-wrapper">
                        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                        <input type="text" id="searchInput" class="search-input"
                            placeholder="Buscar por nome, email ou gato...">
                    </div>
                </div>

                <!-- Table -->
                <div class="table-wrapper">
                    <table class="data-table" id="adotantesTable">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Telefone</th>
                                <th>Gato</th>
                                <th>Data</th>
                                <th>Status</th>
                                <th class="text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="font-medium"><?= htmlspecialchars($row['nome']) ?></td>
                                        <td><?= htmlspecialchars($row['email']) ?></td>
                                        <td><?= htmlspecialchars($row['telefone']) ?></td>
                                        <td><?= htmlspecialchars($row['nome_gato']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($row['data_cadastro'])) ?></td>
                                        <td>
                                            <?php if ($row['status'] == 'Aprovado'): ?>
                                                <span class="badge badge-default">Aprovado</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Pendente</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right">
                                            <?php if ($row['status'] == 'Pendente'): ?>
                                                <button class="btn btn-primary btn-sm"
                                                    onclick="alterarStatus(<?= $row['adotante_id'] ?>, 'Aprovado')">Aprovar</button>
                                            <?php else: ?>
                                                <button class="btn btn-outline btn-sm"
                                                    onclick="alterarStatus(<?= $row['adotante_id'] ?>, 'Pendente')">Voltar</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="text-align:center; padding:15px;">Nenhuma solicitação encontrada.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Detalhes -->
    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="fecharModal()">&times;</span>
            <h2 class="modal-title">Detalhes da Solicitação</h2>
            <div id="modalBody" class="modal-body">
                <!-- Conteúdo será preenchido dinamicamente via JS -->
            </div>
            <div class="modal-actions">
                <button class="btn btn-outline" onclick="fecharModal()">Fechar</button>
                <button class="btn btn-primary">Aprovar</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/script.js"></script>

</body>

</html>