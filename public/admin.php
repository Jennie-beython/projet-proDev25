<?php
// public/admin.php

require_once __DIR__ . '/../config/database.php';

// Récupérer tous les fichiers
$sql = "SELECT files.id, files.filename, files.filepath, users.name AS user_name, categories.name AS category_name 
        FROM files 
        JOIN users ON files.user_id = users.id 
        JOIN categories ON files.category_id = categories.id";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$files = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Gestion des fichiers</title>
</head>
<body>
    <h1>Gestion des fichiers - Admin</h1>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nom du fichier</th>
            <th>Catégorie</th>
            <th>Utilisateur</th>
            <th>Action</th>
        </tr>
        <?php foreach ($files as $file): ?>
        <tr>
            <td><?php echo $file['id']; ?></td>
            <td><?php echo $file['filename']; ?></td>
            <td><?php echo $file['category_name']; ?></td>
            <td><?php echo $file['user_name']; ?></td>
            <td>
                <a href="edit.php?id=<?php echo $file['id']; ?>">Modifier</a> |
                <a href="delete.php?id=<?php echo $file['id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce fichier ?')">Supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
