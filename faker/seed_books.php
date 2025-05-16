<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';

$faker = Faker\Factory::create('fr_FR');
for ($i = 0; $i < 20; $i++) {
    $title = $faker->sentence(3);
    $author = $faker->name;
    $description = $faker->paragraph(4);
    $cover = 'https://picsum.photos/seed/' . urlencode($title) . '/200/300';
    $stmt = $pdo->prepare('INSERT INTO books (title, author, description, cover) VALUES (?, ?, ?, ?)');
    $stmt->execute([$title, $author, $description, $cover]);
}
echo "Livres générés !";
