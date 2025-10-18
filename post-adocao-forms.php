<?php
include 'config/conexao.php';

$success = '';
$error = '';

$gatos = [];
$res = $conn->query("SELECT gato_id, nome FROM gatos ORDER BY nome");
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $gatos[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pegando os dados do formulário
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $pet = trim($_POST['pet']);
    $reason = trim($_POST['reason']);
    $termsAccepted = isset($_POST['terms']);

    // Validação básica
    if (!$name || !$email || !$address || !$pet || !$reason || !$termsAccepted) {
        $error = "Por favor, preencha todos os campos obrigatórios e aceite os termos.";
    } else {
        // Prepared statement para inserir no banco
        $stmt = $conn->prepare("INSERT INTO adotantes (nome, email, telefone, endereco, gato_id, motivo_adocao) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $email, $phone, $address, $pet, $reason);

        if ($stmt->execute()) {
            $success = "Formulário enviado com sucesso! Em breve entraremos em contato.";
            // Limpar campos do formulário
            $_POST = [];
        } else {
            $error = "Erro ao enviar o formulário: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quero Adotar - Lar Bola de Pelos</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <div id="header-placeholder"></div>

    <main class="main-content">
        <div class="container">
            <h2 class="section-title">Formulário de Adoção</h2>
            <p class="section-subtitle">Preencha o formulário abaixo para iniciar o processo de adoção.</p>

            <!-- Alertas de sucesso ou erro -->
            <?php
            if ($success) {
                $msg_json = json_encode($success);
                echo "<script>
                Swal.fire ({
                    title: $msg_json,
                    icon: 'success',
                    draggable: true
                });
            </script>";
            }

            if ($error) {
                $msg_json = json_encode($error);
                echo "<script>
                Swal.fire({
                title: $msg_json,
                icon: 'error',
                draggable: true
                });
                </script>";
            }
            ?>

            <form id="adoption-form" class="adoption-form" method="post" action="">
                <label for="name">Nome Completo: </label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>

                <label for="email">E-mail: </label>
                <input type="text" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    required>

                <label for="phone">Telefone: </label>
                <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">

                <label for="address">Endereço Completo: </label>
                <input type="text" id="address" name="address" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>"
                    required>

                <label for="pet">Gato de Interesse: </label>
                <select id="pet" name="pet" required>
                    <option value="">Selecione um gato</option>
                    <?php foreach ($gatos as $gato): ?>
                        <option value="<?= $gato['gato_id'] ?>" <?= (($_POST['pet'] ?? '') == $gato['gato_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($gato['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="reason">Por que você quer adotar?</label>
                <textarea id="reason" name="reason" rows="5"
                    required><?= htmlspecialchars($_POST['reason'] ?? '') ?></textarea>

                <label for="terms-de-acordo">
                    <input type="checkbox" id="terms" name="terms" <?= isset($_POST['terms']) ? 'checked' : '' ?>
                        required>
                    Eu Li e Concordo com os
                    <a href="#termos-modal">Termos de Adoção Responsável.</a>
                </label>

                <button type="submit" class="btn btn-primary btn-full">Enviar Formulário</button>
            </form>
        </div>
    </main>

    <div id="footer-placeholder"></div>

    <div class="paw-prints">
        <div class="paw">🐾</div>
        <div class="paw">🐾</div>
        <div class="paw">🐾</div>
        <div class="paw">🐾</div>
        <div class="paw">🐾</div>
        <div class="paw">🐾</div>
        <div class="paw">🐾</div>
        <div class="paw">🐾</div>
    </div>

    <script src=" assets/js/script.js"></script>
</body>

</html>