<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../config/database.php';
$stmt = $pdo->prepare('SELECT is_accepted, is_admin FROM users WHERE id=?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
if ((!$user || !$user['is_accepted']) && empty($user['is_admin'])) {
    header('Location: login.php?not_accepted=1');
    exit;
}
require_once __DIR__ . '/../vendor/autoload.php';
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$error = '';
$success = '';
// Modification du nom ou du mot de passe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    if ($password && $password !== $password2) {
        $error = 'Les mots de passe ne correspondent pas.';
    } elseif ($password && strlen($password) < 6) {
        $error = 'Le mot de passe doit faire au moins 6 caractères.';
    } else {
        if ($password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET name=?, password=? WHERE id=?');
            $stmt->execute([$name, $hash, $user_id]);
        } else {
            $stmt = $pdo->prepare('UPDATE users SET name=? WHERE id=?');
            $stmt->execute([$name, $user_id]);
        }
        $success = 'Profil mis à jour.';
        $user['name'] = $name;
    }
}
// Activation OTP
if (isset($_POST['enable_otp'])) {
    $g = new \Sonata\GoogleAuthenticator\GoogleAuthenticator();
    $secret = $g->generateSecret();
    $stmt = $pdo->prepare('UPDATE users SET otp_secret=?, otp_enabled=1 WHERE id=?');
    $stmt->execute([$secret, $user_id]);
    $user['otp_secret'] = $secret;
    $user['otp_enabled'] = 1;
    $success = 'Double authentification activée.';
}
// Désactivation OTP
if (isset($_POST['disable_otp'])) {
    $stmt = $pdo->prepare('UPDATE users SET otp_secret=NULL, otp_enabled=0 WHERE id=?');
    $stmt->execute([$user_id]);
    $user['otp_secret'] = null;
    $user['otp_enabled'] = 0;
    $success = 'Double authentification désactivée.';
}
$otp_qr = '';
if (!empty($user['otp_secret'])) {
    $otp_qr = \Sonata\GoogleAuthenticator\GoogleQrUrl::generate(
        'StockLivres-'.$user['email'],
        $user['otp_secret'],
        'StockLivres'
    );
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon compte</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <span class="header-title">Mon compte</span>
    <div class="header-actions">
        <a href="../index.php" class="btn">Accueil</a>
        <a href="logout.php" class="btn">Déconnexion</a>
    </div>
</header>
<div class="container">
    <h2>Mon compte</h2>
    <?php if ($error): ?><p style="color:red;"> <?= $error ?> </p><?php endif; ?>
    <?php if ($success): ?><p style="color:green;"> <?= $success ?> </p><?php endif; ?>
    <form method="post">
        <label>Nom d'utilisateur :</label><br>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required><br>
        <label>Nouveau mot de passe :</label><br>
        <input type="password" name="password" placeholder="Laisser vide pour ne pas changer"><br>
        <input type="password" name="password2" placeholder="Confirmer le mot de passe"><br>
        <button type="submit" name="update_profile">Mettre à jour</button>
    </form>
    <hr>
    <h3>Double authentification (Google Authenticator)</h3>
    <?php if (empty($user['otp_enabled'])): ?>
        <form method="post">
            <button type="submit" name="enable_otp">Activer la double authentification</button>
        </form>
    <?php else: ?>
        <p>Scannez ce QR code avec Google Authenticator :</p>
        <img src="<?= $otp_qr ?>" alt="QR Code Google Authenticator"><br>
        <form method="post">
            <button type="submit" name="disable_otp">Désactiver la double authentification</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
