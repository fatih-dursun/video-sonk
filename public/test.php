<?php
// Test dosyası - Projenin çalışıp çalışmadığını kontrol et

echo "<h1>✅ PHP Çalışıyor!</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";

// Veritabanı bağlantısı testi
try {
    $config = require __DIR__ . '/../config/database.php';
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        $config['host'],
        $config['database'],
        $config['charset']
    );
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    echo "<p>✅ Veritabanı Bağlantısı Başarılı!</p>";
    
    // Tabloları kontrol et
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>📊 Tablolar: " . implode(', ', $tables) . "</p>";
    
} catch (PDOException $e) {
    echo "<p>❌ Veritabanı Hatası: " . $e->getMessage() . "</p>";
    echo "<p><strong>Çözüm:</strong> config/database.php dosyasındaki ayarları kontrol edin ve database.sql dosyasını import edin.</p>";
}

// Dizin kontrolleri
$dirs = [
    '../core' => 'Core Klasörü',
    '../app/models' => 'Models Klasörü',
    '../app/controllers' => 'Controllers Klasörü',
    '../app/views' => 'Views Klasörü',
    './uploads' => 'Uploads Klasörü',
];

echo "<h2>📁 Dizin Kontrolleri:</h2>";
foreach ($dirs as $dir => $name) {
    $path = __DIR__ . '/' . $dir;
    if (is_dir($path)) {
        echo "<p>✅ $name mevcut</p>";
    } else {
        echo "<p>❌ $name BULUNAMADI! Oluşturun: $path</p>";
    }
}

echo "<hr>";
echo "<p><strong>Test tamamlandı!</strong></p>";
echo "<p>Her şey yeşil ise ana sayfaya gidin: <a href='/'>Ana Sayfa</a></p>";


echo "<hr><h2>🔑 Yeni Admin Şifresi Oluştur</h2>";
$newPassword = 'admin123';
$newHash = password_hash($newPassword, PASSWORD_DEFAULT);
echo "<p><strong>Şifre:</strong> $newPassword</p>";
echo "<p><strong>Yeni Hash:</strong></p>";
echo "<textarea style='width: 100%; height: 60px;'>$newHash</textarea>";
echo "<p><strong>Test:</strong> " . (password_verify($newPassword, $newHash) ? '✅ ÇALIŞIYOR' : '❌ ÇALIŞMIYOR') . "</p>";
?>