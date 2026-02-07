<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geri Dönüşüm Kutusu - Admin</title>
    <link rel="stylesheet" href="<?= asset('/css/admin.css') ?>">
</head>
<body>
    <?php include __DIR__ . '/../_header.php'; ?>
    
    <div class="admin-container">
        <div class="page-header">
            <h1>🗑️ Geri Dönüşüm Kutusu</h1>
            <a href="<?= url('/admin/videos') ?>" class="btn btn-secondary">← Video Listesine Dön</a>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="alert alert-info" style="background: #e3f2fd; border: 1px solid #2196f3; color: #1565c0;">
            📌 Bu sayfada silinen videolar listelenmektedir. Videoları geri yükleyebilir veya kalıcı olarak silebilirsiniz.
            <br>⚠️ <strong>Kalıcı silme işlemi geri alınamaz!</strong>
        </div>

        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Görsel</th>
                        <th>Başlık</th>
                        <th>Kategori</th>
                        <th>Ekleyen</th>
                        <th>Silen</th>
                        <th>Silinme Tarihi</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($videos)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 60px; color: #888;">
                            <div style="font-size: 48px; margin-bottom: 16px;">🗑️</div>
                            <p>Geri dönüşüm kutusu boş.</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php foreach ($videos as $video): ?>
                    <tr>
                        <td>
                            <img src="<?= upload_url($video['featured_image_path']) ?>" alt="" 
                                 style="width: 80px; border-radius: 4px; opacity: 0.7;">
                        </td>
                        <td style="color: #888;"><?= htmlspecialchars($video['title']) ?></td>
                        <td><?= htmlspecialchars($video['category_name']) ?></td>
                        <td><?= htmlspecialchars($video['created_by_username'] ?? 'Bilinmiyor') ?></td>
                        <td><?= htmlspecialchars($video['deleted_by_username'] ?? 'Bilinmiyor') ?></td>
                        <td><?= date('d.m.Y H:i', strtotime($video['updated_at'])) ?></td>
                        <td class="action-buttons">
                            <a href="<?= url('/admin/videos/restore/' . $video['id']) ?>" 
                               class="btn btn-sm btn-success"
                               onclick="return confirm('Bu videoyu geri yüklemek istediğinize emin misiniz?')">
                                ↩️ Geri Yükle
                            </a>
                            <a href="<?= url('/admin/videos/permanent-delete/' . $video['id']) ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('⚠️ DİKKAT! Bu işlem geri alınamaz!\n\nVideoyu kalıcı olarak silmek istediğinize emin misiniz?')">
                                💀 Kalıcı Sil
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
