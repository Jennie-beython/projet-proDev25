<?php
require_once __DIR__ . '/../config/database.php';
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}
$stmt = $pdo->prepare('SELECT * FROM books WHERE id = ?');
$stmt->execute([$id]);
$book = $stmt->fetch();
if (!$book) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($book['title']) ?> - Détail du livre</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .book-detail { display: flex; gap: 2em; align-items: flex-start; }
        .book-detail img { width: 200px; height: 280px; object-fit: cover; border-radius: 8px; box-shadow: 0 2px 8px #0002; }
        .book-info { max-width: 500px; }
        .book-info h2 { margin-top: 0; }
    </style>
</head>
<body>
<header>
    <a href="index.php" class="btn">← Retour à la liste</a>
</header>
<div class="container">
    <div class="book-detail">
        <img src="<?= htmlspecialchars($book['cover']) ?>" alt="Couverture du livre">
        <div class="book-info">
            <h2><?= htmlspecialchars($book['title']) ?></h2>
            <h4 style="color:#5a6d4c; margin:0.5em 0;">Auteur : <?= htmlspecialchars($book['author']) ?></h4>
            <p><?= nl2br(htmlspecialchars($book['description'])) ?></p>
        </div>
    </div>
</div>
</body>
</html>
