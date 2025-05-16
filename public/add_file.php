<?php
// public/add_file.php

require_once __DIR__ . '/../config/database.php';

// Récupérer les catégories et utilisateurs pour les listes déroulantes
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
$users = $pdo->query("SELECT * FROM users")->fetchAll();

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filename = $_POST['filename'];
    $category_id = $_POST['category_id'];
    $user_id = $_POST['user_id'];

    $filepath = 'uploads/' . $filename; // dossier fictif

    // Insérer dans la BDD
    $sql = "INSERT INTO files (filename, filepath, category_id, user_id) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$filename, $filepath, $category_id, $user_id]);

    // Redirection
    header('Location: admin.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un fichier</title>
</head>
<body>
    <h1>Ajouter un nouveau fichier</h1>

    <form method="POST">
        <label>Nom du fichier :</label><br>
        <input type="text" name="filename" required><br><br>

        <label>Catégorie :</label><br>
        <select name="category_id" required>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Utilisateur :</label><br>
        <select name="user_id" required>
            <?php foreach ($users as $user): ?>
                <option value="<?php echo $user['id']; ?>"><?php echo $user['name']; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Ajouter</button>
    </form>

    <br>
    <a href="admin.php">⬅️ Retour à l'administration</a>
</body>
</html>
