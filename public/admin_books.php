<?php
session_start();
require_once __DIR__ . '/../config/database.php';
if (empty($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header('Location: login.php');
    exit;
}
// CRUD logique
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;
$message = '';
// Ajouter un livre
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $cover = trim($_POST['cover'] ?? '');
    $stmt = $pdo->prepare('INSERT INTO books (title, author, description, cover) VALUES (?, ?, ?, ?)');
    $stmt->execute([$title, $author, $description, $cover]);
    $message = 'Livre ajouté !';
}
// Modifier un livre
if ($action === 'edit' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $cover = trim($_POST['cover'] ?? '');
    $stmt = $pdo->prepare('UPDATE books SET title=?, author=?, description=?, cover=? WHERE id=?');
    $stmt->execute([$title, $author, $description, $cover, $id]);
    $message = 'Livre modifié !';
}
// Supprimer un livre
if ($action === 'delete' && $id) {
    $stmt = $pdo->prepare('DELETE FROM books WHERE id=?');
    $stmt->execute([$id]);
    $message = 'Livre supprimé !';
}
// Récupérer les livres
$books = $pdo->query('SELECT * FROM books ORDER BY created_at DESC')->fetchAll();
// Récupérer un livre pour édition
$editBook = null;
if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare('SELECT * FROM books WHERE id=?');
    $stmt->execute([$id]);
    $editBook = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Livres</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Administration des Livres</h1>
    <a href="index.php" class="btn">Accueil</a>
    <a href="logout.php" class="btn">Déconnexion</a>
</header>
<div class="container">
    <?php if ($message): ?><p style="color:green;"> <?= $message ?> </p><?php endif; ?>
    <h2><?= $editBook ? 'Modifier' : 'Ajouter' ?> un livre</h2>
    <form method="post" action="?action=<?= $editBook ? 'edit&id=' . $editBook['id'] : 'add' ?>">
        <input type="text" name="title" placeholder="Titre" value="<?= htmlspecialchars($editBook['title'] ?? '') ?>" required><br>
        <input type="text" name="author" placeholder="Auteur" value="<?= htmlspecialchars($editBook['author'] ?? '') ?>" required><br>
        <input type="text" name="cover" placeholder="URL de la couverture" value="<?= htmlspecialchars($editBook['cover'] ?? '') ?>"><br>
        <textarea name="description" placeholder="Description" required><?= htmlspecialchars($editBook['description'] ?? '') ?></textarea><br>
        <button type="submit">Enregistrer</button>
        <?php if ($editBook): ?>
            <a href="admin_books.php" class="btn">Annuler</a>
        <?php endif; ?>
    </form>
    <h2>Liste des livres</h2>
    <table>
        <tr>
            <th>ID</th><th>Titre</th><th>Auteur</th><th>Couverture</th><th>Description</th><th>Actions</th>
        </tr>
        <?php foreach ($books as $book): ?>
        <tr>
            <td><?= $book['id'] ?></td>
            <td><?= htmlspecialchars($book['title']) ?></td>
            <td><?= htmlspecialchars($book['author']) ?></td>
            <td><img src="<?= htmlspecialchars($book['cover']) ?>" alt="" style="width:50px;"></td>
            <td><?= nl2br(htmlspecialchars($book['description'])) ?></td>
            <td>
                <a href="?action=edit&id=<?= $book['id'] ?>" class="btn">Modifier</a>
                <a href="?action=delete&id=<?= $book['id'] ?>" class="btn" onclick="return confirm('Supprimer ce livre ?')">Supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
