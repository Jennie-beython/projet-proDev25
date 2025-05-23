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
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: ../index.php');
    exit;
}
$stmt = $pdo->prepare('SELECT * FROM books WHERE id = ?');
$stmt->execute([$id]);
$book = $stmt->fetch();
if (!$book) {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($book['title']) ?> - Détail du livre</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <span class="header-title">Détail du livre</span>
    <div class="header-actions">
        <a href="../index.php" class="btn">Accueil</a>
        <a href="logout.php" class="btn">Déconnexion</a>
    </div>
</header>
<div class="container">
    <div class="book-detail">
        <img src="<?= htmlspecialchars($book['cover']) ?>" alt="Couverture du livre">
        <div class="book-info">
            <h2><?= htmlspecialchars($book['title']) ?></h2>
            <h4 style="color:#5a6d4c; margin:0.5em 0;">Auteur : <?= htmlspecialchars($book['author']) ?></h4>
            <p><?= nl2br(htmlspecialchars($book['description'])) ?></p>
            <p><strong>Quantité en stock :</strong> <?= (int)$book['quantity'] ?></p>
        </div>
    </div>
</div>
</body>
</html>
