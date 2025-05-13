<?php
require_once '../vendor/autoload.php';
require_once '../config/database.php';

$faker = Faker\Factory::create();

for ($i = 0; $i < 10; $i++) {
    $filename = $faker->word . '.txt';
    $filepath = 'uploads/' . $filename;

    // Crée un faux fichier
    file_put_contents('../public/' . $filepath, $faker->text(100));

    $stmt = $pdo->prepare("INSERT INTO files (filename, filepath) VALUES (?, ?)");
    $stmt->execute([$filename, $filepath]);
}

echo "Fichiers fictifs générés.";
