<?php
require_once __DIR__ . '/../config/database.php';
// Création automatique des tables si elles n'existent pas
$pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    is_accepted TINYINT(1) DEFAULT 0,
    otp_secret VARCHAR(32) DEFAULT NULL,
    otp_enabled TINYINT(1) DEFAULT 0
)");
$pdo->exec("CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    description TEXT,
    cover VARCHAR(255),
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
// Supprimer tous les livres et réinitialiser l'auto-increment
$pdo->exec('DELETE FROM books');
$pdo->exec('ALTER TABLE books AUTO_INCREMENT = 1');
// Générer de nouveaux livres
require __DIR__ . '/seed_books.php';
echo "Livres réinitialisés et régénérés !";
