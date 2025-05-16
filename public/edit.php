<?php
// public/edit.php

require_once __DIR__ . '/../config/database.php';

// Vérifier si l'ID est passé dans l'URL
if (isset($_GET['id'])) {
    $file_id = $_GET['id'];

    // Récupérer les informations du fichier
    $sql = "SELECT * FROM files WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$file_id]);
    $file = $stmt->fetch();

    if (!$file) {
        echo "Fichier non trouvé.";
        exit;
    }

    // Récupérer les catégories pour le dropdown
    $categories_sql = "SELECT * FROM categories";
    $categories_stmt = $pdo->prepare($categories_sql);
    $categories_stmt->execute();
    $categories = $categories_stmt->fetchAll();

    // Mise à jour des données après soumission du formulaire
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $filename = $_POST['filename'];
        $category_id = $_POST['category_id'];

        $update_sql = "UPDATE files SET filename = ?, category_id = ? WHERE id = ?";
        $update_stmt = $pdo->prepare($update_sql);
        $update_stmt->execute([$filename, $category_id, $file_id]);

        // Redirection vers admin.php après modification
        header("Location: admin.php");
        exit;
    }
} else {
    echo "ID de fichier manquant.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Modifier le fichier</title>
</head>
<body>
    <h1>Modifier le fichier</h1>

    <form action="edit.php?id=<?php echo htmlspecialchars($file['id']); ?>" method="post">
        <label for="filename">Nom du fichier :</label>
        <input type="text" name="filename" value="<?php echo htmlspecialchars($file['filename']); ?>" required /><br /><br />

        <label for="category_id">Catégorie :</label>
        <select name="category_id" required>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['id']; ?>" <?php if ($category['id'] == $file['category_id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($category['name']); ?>
                </option>
            <?php endforeach; ?>
        </select><br /><br />

        <input type="submit" value="Modifier" />
    </form>
</body>
</html>
