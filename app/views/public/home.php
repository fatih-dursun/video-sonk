<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Video Portal</title>
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
            <a href="<?= url('/') ?>" class="nav-item active">🏠 Ana Sayfa</a>
            <div class="nav-separator">Kategoriler</div>
            <?php foreach ($categories as $cat): ?>
                <a href="<?= url('/kategori/' . $cat['slug']) ?>" class="nav-item">
                    <?= htmlspecialchars($cat['name']) ?>
                    <span class="video-count">(<?= $cat['video_count'] ?>)</span>
                </a>
            <?php endforeach; ?>
        </nav>
    </aside>

    <main class="main-content">
        <?php if (!empty($featuredVideos)): ?>
        <section class="section">
            <h2 class="section-title">⭐ Öne Çıkan Videolar</h2>
            <div class="video-grid featured-grid">
                <?php foreach ($featuredVideos as $video): ?>
                <a href="<?= url('/video/' . $video['slug']) ?>" class="video-card featured">
                    <div class="video-thumbnail">
                        <img src="<?= upload_url($video['featured_image_path']) ?>" alt="<?= htmlspecialchars($video['title']) ?>" class="thumb-img">
                        <?php if (!empty($video['category_logo'])): ?>
                            <img src="<?= $video['category_logo'] ?>" alt="" class="category-logo large">
                        <?php endif; ?>
                        <span class="featured-badge">⭐ Öne Çıkan</span>
                    </div>
                    <div class="video-info">
                        <h3 class="video-title"><?= htmlspecialchars($video['title']) ?></h3>
                        <span class="category-badge" style="background-color: <?= $video['background_color'] ?>; color: <?= $video['text_color'] ?>">
                            <?= htmlspecialchars($video['category_name']) ?>
                        </span>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <section class="section">
            <h2 class="section-title">📺 Son Yüklenen Videolar</h2>
            <div class="video-grid">
                <?php foreach ($latestVideos as $video): ?>
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
        </section>

        <section class="section">
            <h2 class="section-title">🎯 Kategoriler</h2>
            <div class="category-grid">
                <?php foreach ($categories as $cat): ?>
                <a href="<?= url('/kategori/' . $cat['slug']) ?>" class="category-card" 
                   style="background: linear-gradient(135deg, <?= $cat['background_color'] ?> 0%, <?= $cat['background_color'] ?>dd 100%); color: <?= $cat['text_color'] ?>">
                    <h3 class="category-name"><?= htmlspecialchars($cat['name']) ?></h3>
                    <p class="category-count"><?= $cat['video_count'] ?> video</p>
                </a>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <script src="<?= asset('/js/main.js') ?>"></script>
</body>
</html>
