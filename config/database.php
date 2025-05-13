<?php
$host = 'localhost';
$db = 'storage_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>
