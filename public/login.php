<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../vendor/autoload.php';

$error = '';
$info = '';
if (isset($_GET['not_accepted'])) {
    $error = "Votre compte n'a pas encore été validé par un administrateur.";
}
$showOtp = false;
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$otp = $_POST['otp'] ?? '';
if (isset($_GET['logout'])) {
    $info = 'Déconnexion réussie.';
}
if (isset($_GET['register'])) {
    $info = 'Inscription réussie, vous pouvez vous connecter.';
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        if (!empty($user['otp_enabled']) && !empty($user['otp_secret'])) {
            $showOtp = true;
            if (empty($otp)) {
                $error = 'Veuillez entrer le code de vérification Google Authenticator.';
            } else {
                $g = new \Sonata\GoogleAuthenticator\GoogleAuthenticator();
                if (!$g->checkCode($user['otp_secret'], $otp)) {
                    $error = 'Code Google Authenticator invalide.';
                } else {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['is_admin'] = $user['is_admin'];
                    header('Location: index.php');
                    exit;
                }
            }
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['is_admin'] = $user['is_admin'];
            header('Location: index.php');
            exit;
        }
    } else {
        // Si l'utilisateur existe et a activé l'OTP, afficher le champ OTP même si le mot de passe est faux
        if ($user && !empty($user['otp_enabled']) && !empty($user['otp_secret'])) {
            $showOtp = true;
        }
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
        <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($email) ?>" required><br>
        <input type="password" name="password" placeholder="Mot de passe" value="<?= htmlspecialchars($password) ?>" required><br>
        <?php if ($showOtp): ?>
            <input type="text" name="otp" placeholder="Code Google Authenticator" value="<?= htmlspecialchars($otp) ?>" autocomplete="one-time-code"><br>
        <?php endif; ?>
        <button type="submit">Se connecter</button>
    </form>
    <p>Pas encore de compte ? <a href="register.php">S'inscrire</a></p>
</div>
</body>
</html>
