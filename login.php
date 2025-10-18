<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once 'config/conexao.php';

$loginError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        // buscar usuário pelo nome
        $stmt = $conn->prepare("SELECT usuario_id, nome, senha FROM usuarios WHERE nome = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // senha em texto plano
        if ($user && $password === $user['senha']) {
            // login OK
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nome'];
            header('Location: admin-dashboard.php');
            exit;
        } else {
            $loginError = 'Usuário ou senha incorretos.';
        }

        $stmt->close();
    } else {
        $loginError = 'Preencha todos os campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div id="header-placeholder"></div>

<main class="main-content">
    <div class="login-container">
        <form id="admin-login-form" class="login-form" method="post" action="">
            <h2>Login Administrador</h2>

            <?php if ($loginError): ?>
                <div id="login-status" style="margin-top: 15px; font-weight: bold; color: red;">
                    <?= htmlspecialchars($loginError) ?>
                </div>
            <?php endif; ?>

            <label for="username">Usuário:</label>
            <input type="text" id="username" name="username" class="form-input" required
                   value="<?= isset($username) ? htmlspecialchars($username) : '' ?>">

            <label for="password">Senha:</label>
            <input type="password" id="password" name="password" class="form-input" required>

            <label class="show-password-label">
                <input type="checkbox" id="show-password"> Mostrar senha
            </label>

            <button type="submit" class="btn btn-primary">Entrar</button>
        </form>
    </div>
</main>

<div id="footer-placeholder"></div>

<script>
    // Mostrar senha
    const passwordInput = document.getElementById('password');
    const showPassword = document.getElementById('show-password');
    showPassword.addEventListener('change', () => {
        passwordInput.type = showPassword.checked ? 'text' : 'password';
    });
</script>
</body>
</html>
