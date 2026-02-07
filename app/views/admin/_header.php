<header class="admin-header">
    <div class="admin-header-left">
        <h2>🎬 VideoPortal Admin</h2>
    </div>
    <button class="admin-menu-toggle" id="adminMenuToggle">☰</button>
    <nav class="admin-nav" id="adminNav">
        <a href="<?= defined('BASE_PATH') ? BASE_PATH : '/video-portal/public' ?>/admin/videos">📹 Videolar</a>
        <a href="<?= defined('BASE_PATH') ? BASE_PATH : '/video-portal/public' ?>/admin/categories">📁 Kategoriler</a>
        <?php if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'admin'): ?>
        <a href="<?= defined('BASE_PATH') ? BASE_PATH : '/video-portal/public' ?>/admin/users">👥 Kullanıcılar</a>
        <?php endif; ?>
        <a href="<?= defined('BASE_PATH') ? BASE_PATH : '/video-portal/public' ?>/" target="_blank">🌐 Siteyi Görüntüle</a>
        <a href="<?= defined('BASE_PATH') ? BASE_PATH : '/video-portal/public' ?>/admin/profile">👤 <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Profilim') ?></a>
        <a href="<?= defined('BASE_PATH') ? BASE_PATH : '/video-portal/public' ?>/admin/logout">🚪 Çıkış</a>
    </nav>
</header>

<style>
/* Admin Header */
.admin-header {
    display: flex;
    align-items: center;
    gap: 20px;
}

.admin-header-left {
    /* Desktop'ta normal */
}

.admin-menu-toggle {
    display: none;
    background: none;
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    padding: 8px 12px;
    border-radius: 4px;
    transition: background 0.2s;
}

.admin-menu-toggle:hover {
    background: rgba(255,255,255,0.1);
}

@media (max-width: 768px) {
    .admin-header {
        flex-wrap: wrap;
    }

    .admin-header-left {
        flex: 1;
        min-width: 0;
    }

    .admin-header-left h2 {
        font-size: 18px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .admin-menu-toggle {
        display: block;
        flex-shrink: 0;
    }

    .admin-nav {
        display: none;
        width: 100%;
        flex-direction: column;
        background: rgba(0,0,0,0.2);
        padding: 12px 0;
        margin-top: 12px;
        border-radius: 8px;
    }

    .admin-nav.active {
        display: flex;
    }

    .admin-nav a {
        padding: 12px 20px;
        border-left: 3px solid transparent;
    }

    .admin-nav a:hover {
        background: rgba(255,255,255,0.1);
        border-left-color: white;
    }
}
</style>

<script>
// Admin Mobil Menü Toggle
const adminMenuToggle = document.getElementById('adminMenuToggle');
const adminNav = document.getElementById('adminNav');

if (adminMenuToggle && adminNav) {
    adminMenuToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        adminNav.classList.toggle('active');
    });

    // Menü dışına tıklayınca kapat
    document.addEventListener('click', (e) => {
        if (!adminMenuToggle.contains(e.target) && !adminNav.contains(e.target)) {
            adminNav.classList.remove('active');
        }
    });

    // Link'e tıklayınca menüyü kapat
    adminNav.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            adminNav.classList.remove('active');
        });
    });
}
</script>
