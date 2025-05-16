<?php
session_start();
require_once __DIR__ . '/../config/database.php';
$books = $pdo->query('SELECT * FROM books ORDER BY created_at DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Stock de Livres</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Stock de Livres</h1>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="logout.php" class="btn">Déconnexion</a>
        <?php if (!empty($_SESSION['is_admin'])): ?>
            <a href="admin_books.php" class="btn">Admin Livres</a>
        <?php endif; ?>
    <?php else: ?>
        <a href="login.php" class="btn">Connexion</a>
        <a href="register.php" class="btn">Inscription</a>
    <?php endif; ?>
</header>
<div class="container">
    <h2>Livres</h2>
    <div style="display:flex; flex-wrap:wrap; gap:1.2em;">
    <?php foreach ($books as $book): ?>
        <a href="book.php?id=<?= $book['id'] ?>" style="text-decoration:none; color:inherit;">
        <div style="background:#e6e2d3; border-radius:8px; width:160px; padding:0.7em; box-shadow:0 1px 4px #0001; display:flex; flex-direction:column; align-items:center; min-height:270px;">
            <img src="<?= htmlspecialchars($book['cover']) ?>" alt="Couverture" style="width:100px; height:140px; object-fit:cover; border-radius:4px; margin-bottom:0.5em;">
            <h3 style="font-size:1.1em; margin:0.2em 0; text-align:center; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; width:100%">
                <?= htmlspecialchars($book['title']) ?>
            </h3>
            <p style="font-size:0.95em; color:#555; margin:0; text-align:center; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; width:100%">
                <?= htmlspecialchars($book['author']) ?>
            </p>
        </div>
        </a>
    <?php endforeach; ?>
    </div>
</div>
</body>
</html>
