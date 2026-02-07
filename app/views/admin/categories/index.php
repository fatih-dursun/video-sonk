<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Yönetimi - Admin</title>
    <link rel="stylesheet" href="<?= asset('/css/admin.css') ?>">
</head>
<body>
    <?php include __DIR__ . '/../_header.php'; ?>
    
    <div class="admin-container">
        <div class="page-header">
            <h1>📁 Kategori Yönetimi</h1>
            <?php if ($_SESSION['admin_role'] === 'admin'): ?>
            <a href="<?= url('/admin/categories/create') ?>" class="btn btn-primary">+ Yeni Kategori Ekle</a>
            <?php endif; ?>
        </div>

        <!-- Başarı Mesajı -->
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- Hata Mesajı -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Logo</th>
                        <th>Önizleme</th>
                        <th>Kategori Adı</th>
                        <th>Slug</th>
                        <th>Durum</th>
                        <th>Video Sayısı</th>
                        <th>Ekleyen</th>
                        <th>Oluşturma</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td>
                            <?php if (!empty($cat['logo_path'])): ?>
                                <img src="<?= $cat['logo_path'] ?>" alt="Logo" style="width: 50px; height: 50px; object-fit: contain; background: white; padding: 4px; border-radius: 4px; border: 1px solid #ddd;">
                            <?php else: ?>
                                <span style="color: #999;">Yok</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display: inline-block; padding: 8px 16px; border-radius: 8px; background: <?= $cat['background_color'] ?>; color: <?= $cat['text_color'] ?>; font-weight: 500;">
                                <?= htmlspecialchars($cat['name']) ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($cat['name']) ?></td>
                        <td><code><?= $cat['slug'] ?></code></td>
                        <td>
                            <?php if (($cat['status'] ?? 'active') === 'active'): ?>
                                <span class="status-badge status-active">✅ Aktif</span>
                            <?php else: ?>
                                <span class="status-badge status-passive">⏸️ Pasif</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $cat['video_count'] ?> video</td>
                        <td><?= $cat['created_by_username'] ?? 'Bilinmiyor' ?></td>
                        <td><?= date('d.m.Y H:i', strtotime($cat['created_at'])) ?></td>
                        <td class="action-buttons">
                            <?php if ($_SESSION['admin_role'] === 'admin'): ?>
                            <a href="<?= url('/admin/categories/edit/' . $cat['id']) ?>" class="btn btn-sm btn-edit">Düzenle</a>
                            
                            <?php if (($cat['status'] ?? 'active') === 'active'): ?>
                                <a href="<?= url('/admin/categories/toggle/' . $cat['id']) ?>" class="btn btn-sm btn-warning" 
                                   onclick="return confirm('Bu kategoriyi pasife almak istediğinize emin misiniz?')">Pasife Al</a>
                            <?php else: ?>
                                <a href="<?= url('/admin/categories/toggle/' . $cat['id']) ?>" class="btn btn-sm btn-success" 
                                   onclick="return confirm('Bu kategoriyi aktif yapmak istediğinize emin misiniz?')">Aktif Yap</a>
                            <?php endif; ?>
                            
                            <a href="<?= url('/admin/categories/delete/' . $cat['id']) ?>" class="btn btn-sm btn-danger" 
                               onclick="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?')">Sil</a>
                            <?php else: ?>
                            <span style="color: #888;">Sadece görüntüleme</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <style>
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #218838;
        }
    </style>
</body>
</html>
