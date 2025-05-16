<?php
require_once __DIR__ . '/../config/database.php';
// Supprimer tous les livres et réinitialiser l'auto-increment
$pdo->exec('DELETE FROM books');
$pdo->exec('ALTER TABLE books AUTO_INCREMENT = 1');
// Générer de nouveaux livres
require __DIR__ . '/seed_books.php';
echo "Livres réinitialisés et régénérés !";
