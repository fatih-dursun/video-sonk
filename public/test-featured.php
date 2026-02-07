<?php
// Test dosyası - featured_source kontrolü
require_once __DIR__ . '/../config/database.php';

$config = require __DIR__ . '/../config/database.php';
$dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $config['host'], $config['database'], $config['charset']);
$pdo = new PDO($dsn, $config['username'], $config['password']);

echo "<h1>Featured Source Kontrolü</h1>";

// Tüm videoları çek
$sql = "SELECT id, title, featured_source, featured_image_path, thumbnail_path FROM videos LIMIT 10";
$videos = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' cellpadding='10'>";
echo "<tr>
        <th>ID</th>
        <th>Başlık</th>
        <th>Featured Source</th>
        <th>Featured Image Path</th>
        <th>Thumbnail Path</th>
        <th>Eşit mi?</th>
      </tr>";

foreach ($videos as $video) {
    $isEqual = $video['featured_image_path'] === $video['thumbnail_path'] ? '✅ Evet' : '❌ Hayır';
    
    echo "<tr>";
    echo "<td>{$video['id']}</td>";
    echo "<td>" . htmlspecialchars($video['title']) . "</td>";
    echo "<td><strong>" . ($video['featured_source'] ?? 'NULL') . "</strong></td>";
    echo "<td>" . htmlspecialchars($video['featured_image_path']) . "</td>";
    echo "<td>" . htmlspecialchars($video['thumbnail_path']) . "</td>";
    echo "<td>{$isEqual}</td>";
    echo "</tr>";
}

echo "</table>";

echo "<hr>";
echo "<h2>Açıklama:</h2>";
echo "<ul>";
echo "<li><strong>thumbnail</strong> = Thumbnail kullanılıyor (Featured = Thumbnail olmalı)</li>";
echo "<li><strong>text</strong> = Otomatik görsel kullanılıyor (Featured ≠ Thumbnail olmalı)</li>";
echo "<li><strong>NULL</strong> = Eski kayıt (migration yapılmamış)</li>";
echo "</ul>";
?>
