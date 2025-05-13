<?php
require_once '../config/database.php';
$files = $pdo->query("SELECT * FROM files ORDER BY uploaded_at DESC")->fetchAll();
?>

<h2>Fichiers stockés</h2>
<ul>
<?php foreach ($files as $file): ?>
    <li>
        <a href="<?= $file['filepath'] ?>" download>
            <?= htmlspecialchars($file['filename']) ?>
        </a>
    </li>
<?php endforeach; ?>
</ul>

<a href="upload.php">Ajouter un fichier</a>
