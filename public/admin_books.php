<?php
session_start();
require_once __DIR__ . '/../config/database.php';
if (empty($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header('Location: ../public/login.php');
    exit;
}
$tab = $_GET['tab'] ?? 'books';
$message = '';
// --- GESTION CRUD LIVRES ---
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;
if ($tab === 'books') {
    // Ajouter un livre
    if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $cover = trim($_POST['cover'] ?? '');
        $quantity = (int)($_POST['quantity'] ?? 1);
        $stmt = $pdo->prepare('INSERT INTO books (title, author, description, cover, quantity) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$title, $author, $description, $cover, $quantity]);
        $message = 'Livre ajouté !';
    }
    // Modifier un livre
    if ($action === 'edit' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $cover = trim($_POST['cover'] ?? '');
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        $stmt = $pdo->prepare('UPDATE books SET title=?, author=?, description=?, cover=?, quantity=? WHERE id=?');
        $stmt->execute([$title, $author, $description, $cover, $quantity, $id]);
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
}
// --- GESTION UTILISATEURS ---
if ($tab === 'users') {
    // Accepter un utilisateur
    if (isset($_GET['accept'])) {
        $uid = (int)$_GET['accept'];
        $stmt = $pdo->prepare('UPDATE users SET is_accepted=1 WHERE id=?');
        $stmt->execute([$uid]);
        $message = 'Utilisateur accepté.';
    }
    // Promouvoir admin
    if (isset($_GET['promote'])) {
        $uid = (int)$_GET['promote'];
        $stmt = $pdo->prepare('UPDATE users SET is_admin=1 WHERE id=?');
        $stmt->execute([$uid]);
        $message = 'Utilisateur promu admin.';
    }
    // Rétrograder admin
    if (isset($_GET['demote'])) {
        $uid = (int)$_GET['demote'];
        $stmt = $pdo->prepare('UPDATE users SET is_admin=0 WHERE id=?');
        $stmt->execute([$uid]);
        $message = 'Utilisateur rétrogradé.';
    }
    // Liste des utilisateurs
    $users = $pdo->query('SELECT * FROM users ORDER BY id DESC')->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-layout { display: flex; min-height: 80vh; }
        .admin-sidebar {
            width: 220px;
            background: linear-gradient(180deg, #3e4c3a 80%, #e6e2d3 100%);
            padding: 2em 1em 2em 1em;
            border-radius: 12px 0 0 12px;
            box-shadow: 2px 0 8px #0001;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .admin-sidebar a {
            display: flex;
            align-items: center;
            gap: 0.7em;
            margin-bottom: 1.2em;
            color: #fff;
            font-weight: 500;
            text-decoration: none;
            font-size: 1.08em;
            padding: 0.6em 1em;
            border-radius: 6px;
            transition: background 0.2s, color 0.2s;
        }
        .admin-sidebar a.active, .admin-sidebar a:hover {
            background: #5a6d4c;
            color: #fff;
        }
        .admin-sidebar .icon {
            font-size: 1.2em;
            opacity: 0.85;
        }
        .admin-content { flex: 1; padding: 2em; }
        @media (max-width: 800px) {
            .admin-layout { flex-direction: column; }
            .admin-sidebar { flex-direction: row; width: 100%; border-radius: 12px 12px 0 0; }
            .admin-sidebar a { margin-bottom: 0; margin-right: 1em; }
        }
    </style>
</head>
<body>
<header>
    <span class="header-title">Administration</span>
    <div class="header-actions">
        <a href="../index.php" class="btn">Accueil</a>
        <a href="logout.php" class="btn">Déconnexion</a>
    </div>
</header>
<div class="container admin-layout">
    <nav class="admin-sidebar">
        <a href="admin_books.php?tab=books" class="<?= $tab==='books'?'active':'' ?>">
            <span class="icon">📚</span> Livres
        </a>
        <a href="admin_books.php?tab=users" class="<?= $tab==='users'?'active':'' ?>">
            <span class="icon">👤</span> Utilisateurs
        </a>
    </nav>
    <div class="admin-content">
        <?php if ($message): ?><p style="color:green;"> <?= $message ?> </p><?php endif; ?>
        <?php if ($tab==='books'): ?>
            <h2><?= $editBook ? 'Modifier' : 'Ajouter' ?> un livre</h2>
            <form method="post" action="?tab=books&action=<?= $editBook ? 'edit&id=' . $editBook['id'] : 'add' ?>">
                <input type="text" name="title" placeholder="Titre" value="<?= htmlspecialchars($editBook['title'] ?? '') ?>" required><br>
                <input type="text" name="author" placeholder="Auteur" value="<?= htmlspecialchars($editBook['author'] ?? '') ?>" required><br>
                <input type="text" name="cover" placeholder="URL de la couverture" value="<?= htmlspecialchars($editBook['cover'] ?? '') ?>"><br>
                <textarea name="description" placeholder="Description" required><?= htmlspecialchars($editBook['description'] ?? '') ?></textarea><br>
                <input type="number" name="quantity" placeholder="Quantité" min="0" value="<?= htmlspecialchars($editBook['quantity'] ?? 1) ?>" required><br>
                <button type="submit">Enregistrer</button>
                <?php if ($editBook): ?>
                    <a href="admin_books.php?tab=books" class="btn">Annuler</a>
                <?php endif; ?>
            </form>
            <h2>Liste des livres</h2>
            <table>
                <tr>
                    <th>ID</th><th>Titre</th><th>Auteur</th><th>Couverture</th><th>Description</th><th>Quantité</th><th>Actions</th>
                </tr>
                <?php foreach ($books as $book): ?>
                <tr>
                    <td><?= $book['id'] ?></td>
                    <td><?= htmlspecialchars($book['title']) ?></td>
                    <td><?= htmlspecialchars($book['author']) ?></td>
                    <td><img src="<?= htmlspecialchars($book['cover']) ?>" alt="" style="width:50px;"></td>
                    <td style="max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        <?= nl2br(htmlspecialchars(mb_strimwidth($book['description'], 0, 60, '...'))) ?>
                    </td>
                    <td><?= (int)$book['quantity'] ?></td>
                    <td>
                        <div class="admin-actions">
                            <a href="?tab=books&action=edit&id=<?= $book['id'] ?>" class="btn">Modifier</a>
                            <a href="?tab=books&action=delete&id=<?= $book['id'] ?>" class="btn" onclick="return confirm('Supprimer ce livre ?')">Supprimer</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php elseif ($tab==='users'): ?>
            <h2>Utilisateurs</h2>
            <table>
                <tr>
                    <th>ID</th><th>Nom</th><th>Email</th><th>Admin</th><th>Accepté</th><th>Actions</th>
                </tr>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= $user['is_admin'] ? 'Oui' : 'Non' ?></td>
                    <td><?= $user['is_accepted'] ? 'Oui' : 'Non' ?></td>
                    <td>
                        <?php if (!$user['is_accepted']): ?>
                            <a href="?tab=users&accept=<?= $user['id'] ?>" class="btn">Accepter</a>
                        <?php endif; ?>
                        <?php if ($user['is_admin']): ?>
                            <a href="?tab=users&demote=<?= $user['id'] ?>" class="btn">Rétrograder</a>
                        <?php else: ?>
                            <a href="?tab=users&promote=<?= $user['id'] ?>" class="btn">Promouvoir admin</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
