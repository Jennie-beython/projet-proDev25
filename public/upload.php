<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        $filename = basename($file['name']);
        $destination = 'uploads/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $stmt = $pdo->prepare("INSERT INTO files (filename, filepath) VALUES (?, ?)");
            $stmt->execute([$filename, $destination]);
            echo "Fichier téléchargé avec succès.";
        } else {
            echo "Erreur lors du téléchargement.";
        }
    }
}
?>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="file" required />
    <button type="submit">Téléverser</button>
</form>
