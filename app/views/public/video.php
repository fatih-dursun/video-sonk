<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($video['title']) ?> - Video Portal</title>
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
            <input type="text" id="searchInput" placeholder="Ara..." class="search-input">
            <div class="search-actions">
                <button class="search-clear" id="searchClear" title="Temizle">✕</button>
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
        <div class="video-player-container">
            <video controls class="video-player">
                <source src="<?= upload_url($video['video_path']) ?>" type="video/mp4">
                Tarayıcınız video oynatmayı desteklemiyor.
            </video>
        </div>

        <div class="video-details">
            <h1 class="video-detail-title"><?= htmlspecialchars($video['title']) ?></h1>
            <div class="video-detail-meta">
                <span class="category-badge" style="background-color: <?= $video['background_color'] ?>; color: <?= $video['text_color'] ?>">
                    <?= htmlspecialchars($video['category_name']) ?>
                </span>
                <span><?= number_format($video['view_count']) ?> görüntülenme</span>
                <span><?= date('d.m.Y', strtotime($video['created_at'])) ?></span>
            </div>
            <?php if (!empty($video['description'])): ?>
            <p class="video-description"><?= nl2br(htmlspecialchars($video['description'])) ?></p>
            <?php endif; ?>
        </div>

        <?php if (!empty($relatedVideos)): ?>
        <section class="section">
            <h2 class="section-title">İlgili Videolar</h2>
            <div class="video-grid">
                <?php foreach ($relatedVideos as $rel): ?>
                    <?php if ($rel['id'] != $video['id']): ?>
                    <a href="<?= url('/video/' . $rel['slug']) ?>" class="video-card">
                        <div class="video-thumbnail">
                            <img src="<?= upload_url($rel['featured_image_path']) ?>" alt="<?= htmlspecialchars($rel['title']) ?>" class="thumb-img">
                            <?php if (!empty($rel['category_logo'])): ?>
                                <img src="<?= $rel['category_logo'] ?>" alt="" class="category-logo">
                            <?php endif; ?>
                        </div>
                        <div class="video-info">
                            <h3 class="video-title"><?= htmlspecialchars($rel['title']) ?></h3>
                            <div class="video-meta">
                                <?= number_format($rel['view_count']) ?> görüntülenme
                            </div>
                        </div>
                    </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </main>

    <script src="<?= asset('/js/main.js') ?>"></script>
</body>
</html>
