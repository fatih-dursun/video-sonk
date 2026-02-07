<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arama: <?= htmlspecialchars($query) ?> - Video Portal</title>
    <link rel="stylesheet" href="<?= asset('/css/style.css') ?>">
</head>
<body>
    <header class="header">
        <button class="menu-btn" id="menuToggle">☰</button>
        <div class="logo">
            <a href="<?= url('/') ?>" style="color: white; text-decoration: none;">
                <span class="logo-full">🎬 VideoPortal</span>
                <span class="logo-mobile">🎬</span>
            </a>
        </div>
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Ara..." class="search-input" value="<?= htmlspecialchars($query) ?>">
            <div class="search-actions">
                <button class="search-clear active" id="searchClear" title="Temizle">✕</button>
                <button class="search-btn" id="searchBtn">🔍</button>
            </div>
            <div id="searchResults" class="search-results"></div>
        </div>
    </header>

    <aside class="sidebar" id="sidebar">
        <nav class="sidebar-nav">
            <a href="<?= url('/') ?>" class="nav-item">🏠 Ana Sayfa</a>
            <div class="nav-separator">Kategoriler</div>
            <?php foreach ($categories as $cat): ?>
                <a href="<?= url('/kategori/' . $cat['slug']) ?>" class="nav-item">
                    <?= htmlspecialchars($cat['name']) ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </aside>

    <main class="main-content">
        <h1 class="search-page-title">🔍 Arama Sonuçları: "<?= htmlspecialchars($query) ?>"</h1>
        
        <?php if (empty($videos)): ?>
            <div class="empty-state">
                <p>Aramanızla eşleşen video bulunamadı.</p>
            </div>
        <?php else: ?>
            <p class="search-results-count"><?= count($videos) ?> sonuç bulundu</p>
            <div class="video-grid">
                <?php foreach ($videos as $video): ?>
                <a href="<?= url('/video/' . $video['slug']) ?>" class="video-card">
                    <div class="video-thumbnail">
                        <img src="<?= upload_url($video['featured_image_path']) ?>" alt="<?= htmlspecialchars($video['title']) ?>" class="thumb-img">
                        <?php if (!empty($video['category_logo'])): ?>
                            <img src="<?= $video['category_logo'] ?>" alt="" class="category-logo">
                        <?php endif; ?>
                    </div>
                    <div class="video-info">
                        <h3 class="video-title"><?= htmlspecialchars($video['title']) ?></h3>
                        <span class="category-badge" style="background-color: <?= $video['background_color'] ?>; color: <?= $video['text_color'] ?>">
                            <?= htmlspecialchars($video['category_name']) ?>
                        </span>
                        <div class="video-meta">
                            <?= number_format($video['view_count']) ?> görüntülenme
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <script src="<?= asset('/js/main.js') ?>"></script>
</body>
</html>
