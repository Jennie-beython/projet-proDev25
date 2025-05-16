<?php
require_once __DIR__ . '/../config/database.php';
$name = 'Admin';
$email = 'admin@site.fr';
$password = password_hash('admin123', PASSWORD_DEFAULT);
$is_admin = 1;
$stmt = $pdo->prepare('INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, ?)');
$stmt->execute([$name, $email, $password, $is_admin]);
echo "Admin créé : admin@site.fr / admin123";
