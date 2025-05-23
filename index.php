<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: ./public/login.php');
    exit;
}
require_once __DIR__ . '/config/database.php';
$stmt = $pdo->prepare('SELECT is_accepted, is_admin FROM users WHERE id=?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
if ((!$user || !$user['is_accepted']) && empty($user['is_admin'])) {
    header('Location: ./public/login.php?not_accepted=1');
    exit;
}
$books = $pdo->query('SELECT * FROM books ORDER BY created_at DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Stock de Livres</title>
    <link rel="stylesheet" href="public/style.css">
</head>
<body>
<header>
    <span class="header-title">Stock de Livres</span>
    <div class="header-actions">
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="public/account.php" class="btn">Mon compte</a>
        <a href="./public/logout.php" class="btn">Déconnexion</a>
        <?php if (!empty($_SESSION['is_admin'])): ?>
            <a href="public/admin_books.php" class="btn">Administration</a>
        <?php endif; ?>
    <?php else: ?>
        <a href="public/login.php" class="btn">Connexion</a>
        <a href="public/register.php" class="btn">Inscription</a>
    <?php endif; ?>
    </div>
</header>
<div class="container">
    <h2>Liste des livres</h2>
    <div class="books-grid" style="justify-content:center;">
    <?php foreach ($books as $book): ?>
        <div class="book-card">
            <a href="public/book.php?id=<?= $book['id'] ?>" style="display:block; width:100%; text-align:center;">
                <img src="<?= htmlspecialchars($book['cover']) ?>" alt="Couverture" style="width:100%; border-radius:4px;">
                <h3 style="margin:0.5em 0 0.2em 0; font-size:1.1em; color:#3e4c3a;"> <?= htmlspecialchars($book['title']) ?> </h3>
                <p style="margin:0; font-size:0.95em;"><em><?= htmlspecialchars($book['author']) ?></em></p>
                <p style="margin:0.5em 0 0 0; font-size:0.95em; color:#5a6d4c;"><strong>Stock :</strong> <?= (int)$book['quantity'] ?></p>
            </a>
        </div>
    <?php endforeach; ?>
    </div>
</div>
</body>
</html>
