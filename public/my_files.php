<?php
// public/my_files.php

require_once __DIR__ . '/../config/database.php';

// Id de l'utilisateur (à remplacer par la logique de connexion utilisateur)
$user_id = 1;

// Récupérer les fichiers de l'utilisateur
$sql = "SELECT files.id, files.filename, files.filepath, categories.name AS category_name 
        FROM files 
        JOIN categories ON files.category_id = categories.id 
        WHERE files.user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$files = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes fichiers</title>
</head>
<body>
    <h1>Mes fichiers</h1>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nom du fichier</th>
            <th>Catégorie</th>
            <th>Action</th>
        </tr>
        <?php foreach ($files as $file): ?>
        <tr>
            <td><?php echo $file['id']; ?></td>
            <td><?php echo $file['filename']; ?></td>
            <td><?php echo $file['category_name']; ?></td>
            <td>
                <a href="edit.php?id=<?php echo $file['id']; ?>">Modifier</a> |
                <a href="delete.php?id=<?php echo $file['id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce fichier ?')">Supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
