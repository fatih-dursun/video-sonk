<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Yönetimi - Admin</title>
    <link rel="stylesheet" href="<?= asset('/css/admin.css') ?>">
</head>
<body>
    <?php include __DIR__ . '/../_header.php'; ?>
    
    <div class="admin-container">
        <div class="page-header">
            <h1>📹 Video Yönetimi</h1>
            <div class="header-actions">
                <?php if ($_SESSION['admin_role'] === 'admin'): ?>
                <a href="<?= url('/admin/videos/statistics') ?>" class="btn btn-info">📊 İstatistikler</a>
                <a href="<?= url('/admin/videos/trash') ?>" class="btn btn-secondary">🗑️ Geri Dönüşüm (<?= $statusCounts['deleted'] ?? 0 ?>)</a>
                <?php endif; ?>
                <a href="<?= url('/admin/videos/create') ?>" class="btn btn-primary">+ Yeni Video Ekle</a>
            </div>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- ONAY BEKLEYEN UYARISI -->
        <?php if ($_SESSION['admin_role'] === 'admin' && ($statusCounts['pending'] ?? 0) > 0): ?>
        <div class="alert alert-warning">
            ⏳ <strong><?= $statusCounts['pending'] ?></strong> video onay bekliyor!
            <a href="?status=pending" style="margin-left: 10px;">Göster →</a>
        </div>
        <?php endif; ?>

        <?php if ($_SESSION['admin_role'] === 'admin' && ($statusCounts['pending_delete'] ?? 0) > 0): ?>
        <div class="alert alert-error">
            🗑️ <strong><?= $statusCounts['pending_delete'] ?></strong> video silme onayı bekliyor!
            <a href="?status=pending_delete" style="margin-left: 10px; color: white;">Göster →</a>
        </div>
        <?php endif; ?>

        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Görsel</th>
                        <th>Başlık</th>
                        <th>
                            <!-- Kategori Filtresi -->
                            <select onchange="filterByColumn('category', this.value)" style="width: 100%; padding: 4px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="">Kategori (Tümü)</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($filters['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </th>
                        <th>
                            <!-- Durum Filtresi -->
                            <select onchange="filterByColumn('status', this.value)" style="width: 100%; padding: 4px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="">Durum (Tümü)</option>
                                <option value="active" <?= ($filters['status'] ?? '') == 'active' ? 'selected' : '' ?>>✅ Aktif</option>
                                <option value="passive" <?= ($filters['status'] ?? '') == 'passive' ? 'selected' : '' ?>>⏸️ Pasif</option>
                                <option value="pending" <?= ($filters['status'] ?? '') == 'pending' ? 'selected' : '' ?>>⏳ Onay Bekliyor</option>
                                <?php if ($_SESSION['admin_role'] === 'admin'): ?>
                                <option value="pending_delete" <?= ($filters['status'] ?? '') == 'pending_delete' ? 'selected' : '' ?>>🗑️ Silme Onayı</option>
                                <?php endif; ?>
                            </select>
                        </th>
                        <th>Sıra</th>
                        <th>
                            <!-- Ekleyen Filtresi (Sadece Admin) -->
                            <?php if ($_SESSION['admin_role'] === 'admin'): ?>
                            <select onchange="filterByColumn('ekul', this.value)" style="width: 100%; padding: 4px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="">Ekleyen (Tümü)</option>
                                <?php foreach ($admins as $admin): ?>
                                <option value="<?= $admin['id'] ?>" <?= ($filters['ekul'] ?? '') == $admin['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($admin['username']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php else: ?>
                            Ekleyen
                            <?php endif; ?>
                        </th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($videos as $video): ?>
                    <tr>
                        <td>
                            <img src="<?= upload_url($video['featured_image_path']) ?>" alt="" style="width: 80px; border-radius: 4px;">
                        </td>
                        <td>
                            <?php if ($video['is_featured']): ?>
                                <span style="color: #ffc107;">⭐</span>
                            <?php endif; ?>
                            <?= htmlspecialchars($video['title']) ?>
                        </td>
                        <td><?= htmlspecialchars($video['category_name']) ?></td>
                        <td>
                            <?php if ($video['status'] === 'active'): ?>
                                <span class="status-badge status-active">Aktif</span>
                            <?php elseif ($video['status'] === 'passive'): ?>
                                <span class="status-badge status-passive">Pasif</span>
                            <?php elseif ($video['status'] === 'pending'): ?>
                                <span class="status-badge status-pending">⏳ Onay Bekliyor</span>
                            <?php elseif ($video['status'] === 'pending_delete'): ?>
                                <span class="status-badge status-deleted">🗑️ Silme Onayı</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($video['sort_order']): ?>
                                <span style="background: #e8f5e9; color: #2e7d32; padding: 4px 8px; border-radius: 4px; font-weight: 600;">
                                    #<?= $video['sort_order'] ?>
                                </span>
                            <?php else: ?>
                                <span style="color: #999;">-</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($video['created_by_username'] ?? 'Bilinmiyor') ?></td>
                        <td class="action-buttons">
                            <?php 
                            $isAdmin = $_SESSION['admin_role'] === 'admin';
                            $isOwner = ($video['ekul'] ?? null) == $_SESSION['admin_id'];
                            $canEdit = $isAdmin || $isOwner;
                            ?>
                            
                            <?php if ($video['status'] === 'pending' && $isAdmin): ?>
                                <!-- ONAY BEKLİYOR - Admin önizleme + onay/red -->
                                <a href="<?= url('/admin/videos/preview/' . $video['id']) ?>" class="btn btn-sm btn-info">İncele</a>
                                <a href="<?= url('/admin/videos/approve/' . $video['id']) ?>" class="btn btn-sm btn-success" 
                                   onclick="return confirm('Bu videoyu onaylamak istediğinize emin misiniz?')">Onayla</a>
                                <a href="<?= url('/admin/videos/reject/' . $video['id']) ?>" class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Bu videoyu reddetmek istediğinize emin misiniz?')">Reddet</a>
                            
                            <?php elseif ($video['status'] === 'pending_delete' && $isAdmin): ?>
                                <!-- SİLME ONAYI -->
                                <a href="<?= url('/admin/videos/preview/' . $video['id']) ?>" class="btn btn-sm btn-info">İncele</a>
                                <a href="<?= url('/admin/videos/approve-delete/' . $video['id']) ?>" class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Bu videoyu silmek istediğinize emin misiniz?')">Sil</a>
                                <a href="<?= url('/admin/videos/reject-delete/' . $video['id']) ?>" class="btn btn-sm btn-success" 
                                   onclick="return confirm('Silme isteğini iptal etmek istediğinize emin misiniz?')">İptal</a>
                            
                            <?php elseif ($video['status'] === 'pending' && !$isAdmin): ?>
                                <span style="color: #856404;">⏳ Onay bekleniyor</span>
                            
                            <?php elseif ($video['status'] === 'pending_delete' && !$isAdmin): ?>
                                <span style="color: #721c24;">🗑️ Silme onayı bekleniyor</span>
                            
                            <?php elseif ($canEdit): ?>
                                <a href="<?= url('/admin/videos/edit/' . $video['id']) ?>" class="btn btn-sm btn-edit">Düzenle</a>
                                <a href="<?= url('/admin/videos/toggle/' . $video['id']) ?>" class="btn btn-sm btn-warning">
                                    <?= $video['status'] === 'active' ? 'Pasif Yap' : 'Aktif Yap' ?>
                                </a>
                                <a href="<?= url('/admin/videos/delete/' . $video['id']) ?>" class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Bu videoyu silmek istediğinize emin misiniz?')">Sil</a>
                            
                            <?php else: ?>
                                <span style="color: #888;">Yetkiniz yok</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (!empty($filters)): ?>
        <div style="margin-top: 16px;">
            <a href="<?= url('/admin/videos') ?>" class="btn btn-secondary">✕ Filtreleri Temizle</a>
        </div>
        <?php endif; ?>
    </div>

    <script>
    function filterByColumn(column, value) {
        const url = new URL(window.location.href);
        
        if (value) {
            url.searchParams.set(column, value);
        } else {
            url.searchParams.delete(column);
        }
        
        window.location.href = url.toString();
    }
    </script>

    <style>
    .header-actions {
        display: flex;
        gap: 10px;
    }
    
    .btn-success {
        background-color: #28a745;
        color: white;
    }
    
    .btn-success:hover {
        background-color: #218838;
    }
    
    .btn-info {
        background-color: #17a2b8;
        color: white;
    }
    
    .btn-info:hover {
        background-color: #138496;
    }
    
    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .alert-warning {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffc107;
    }
    </style>
</body>
</html>
