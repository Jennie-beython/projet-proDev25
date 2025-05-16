<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$error = '';
$info = '';
if (isset($_GET['logout'])) {
    $info = 'Déconnexion réussie.';
}
if (isset($_GET['register'])) {
    $info = 'Inscription réussie, vous pouvez vous connecter.';
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['is_admin'] = $user['is_admin'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Identifiants invalides';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Connexion</h2>
    <?php if ($info): ?><p style="color:green;"><?= $info ?></p><?php endif; ?>
    <?php if ($error): ?><p style="color:red;"><?= $error ?></p><?php endif; ?>
    <form method="post">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Mot de passe" required><br>
        <button type="submit">Se connecter</button>
    </form>
    <p>Pas encore de compte ? <a href="register.php">S'inscrire</a></p>
</div>
</body>
</html>
