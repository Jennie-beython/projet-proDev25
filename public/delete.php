<?php
// public/delete.php

require_once __DIR__ . '/../config/database.php';

// Vérifier si l'ID est passé dans l'URL
if (isset($_GET['id'])) {
    $file_id = $_GET['id'];

    // Supprimer le fichier de la base de données
    $sql = "DELETE FROM files WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$file_id]);

    echo "Fichier supprimé avec succès!";
} else {
    echo "ID de fichier manquant.";
    exit;
}
?>
