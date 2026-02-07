<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İzleme İstatistikleri - Admin</title>
    <link rel="stylesheet" href="<?= asset('/css/admin.css') ?>">
</head>
<body>
    <?php include __DIR__ . '/../_header.php'; ?>
    
    <div class="admin-container">
        <div class="page-header">
            <h1>📊 İzleme İstatistikleri</h1>
            <a href="<?= url('/admin/videos') ?>" class="btn btn-secondary">← Video Listesine Dön</a>
        </div>

        <!-- GENEL İSTATİSTİKLER -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📹</div>
                <div class="stat-value"><?= number_format($generalStats['total_videos'] ?? 0) ?></div>
                <div class="stat-label">Toplam Video</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-value"><?= number_format($generalStats['active_videos'] ?? 0) ?></div>
                <div class="stat-label">Aktif Video</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">👁️</div>
                <div class="stat-value"><?= number_format($generalStats['total_views'] ?? 0) ?></div>
                <div class="stat-label">Toplam İzlenme</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">⭐</div>
                <div class="stat-value"><?= number_format($generalStats['featured_videos'] ?? 0) ?></div>
                <div class="stat-label">Öne Çıkan</div>
            </div>
        </div>

        <!-- KATEGORİ BAZLI -->
        <h2 style="margin: 30px 0 20px;">📁 Kategori Bazlı</h2>
        <div class="stats-grid">
            <?php foreach ($categoryStats as $cat): ?>
            <div class="stat-card" style="border-left: 4px solid <?= $cat['background_color'] ?>;">
                <div class="stat-label" style="color: <?= $cat['background_color'] ?>; font-weight: 600;"><?= htmlspecialchars($cat['name']) ?></div>
                <div style="display: flex; gap: 20px; margin-top: 10px;">
                    <div>
                        <div class="stat-value" style="font-size: 24px;"><?= number_format($cat['video_count'] ?? 0) ?></div>
                        <div class="stat-label">Video</div>
                    </div>
                    <div>
                        <div class="stat-value" style="font-size: 24px;"><?= number_format($cat['total_views'] ?? 0) ?></div>
                        <div class="stat-label">İzlenme</div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- TÜM VİDEOLAR - İZLENME SIRALI -->
        <h2 style="margin: 30px 0 20px;">📊 Tüm Videolar (İzlenme Sıralı)</h2>
        
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Video</th>
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
                        <th>Durum</th>
                        <th>
                            <!-- Ekleyen Filtresi -->
                            <select onchange="filterByColumn('ekul', this.value)" style="width: 100%; padding: 4px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="">Ekleyen (Tümü)</option>
                                <?php foreach ($admins as $admin): ?>
                                <option value="<?= $admin['id'] ?>" <?= ($filters['ekul'] ?? '') == $admin['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($admin['username']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </th>
                        <th>İzlenme</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $rank = 1;
                    foreach ($viewStats as $video): 
                    ?>
                    <tr>
                        <td>
                            <?php if ($rank <= 3): ?>
                                <span style="font-size: 20px;"><?= $rank == 1 ? '🥇' : ($rank == 2 ? '🥈' : '🥉') ?></span>
                            <?php else: ?>
                                <?= $rank ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($video['is_featured']): ?>
                                <span style="color: #ffc107;">⭐</span>
                            <?php endif; ?>
                            <a href="<?= url('/video/' . $video['slug']) ?>" target="_blank">
                                <?= htmlspecialchars($video['title']) ?>
                            </a>
                        </td>
                        <td>
                            <span style="background-color: <?= $video['background_color'] ?>; color: <?= $video['text_color'] ?>; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                <?= htmlspecialchars($video['category_name']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($video['status'] === 'active'): ?>
                                <span class="status-badge status-active">Aktif</span>
                            <?php elseif ($video['status'] === 'passive'): ?>
                                <span class="status-badge status-passive">Pasif</span>
                            <?php elseif ($video['status'] === 'pending'): ?>
                                <span class="status-badge status-pending">Onay Bekliyor</span>
                            <?php else: ?>
                                <span class="status-badge"><?= $video['status'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($video['created_by_username'] ?? '-') ?></td>
                        <td><strong style="color: #1976d2;"><?= number_format($video['view_count']) ?></strong></td>
                        <td class="action-buttons">
                            <a href="<?= url('/admin/videos/edit/' . $video['id']) ?>" class="btn btn-sm btn-edit">Düzenle</a>
                            <a href="<?= url('/admin/videos/toggle/' . $video['id']) ?>" class="btn btn-sm btn-warning">
                                <?= $video['status'] === 'active' ? 'Pasif' : 'Aktif' ?>
                            </a>
                            <a href="<?= url('/admin/videos/delete/' . $video['id']) ?>" class="btn btn-sm btn-danger" 
                               onclick="return confirm('Bu videoyu silmek istediğinize emin misiniz?')">Sil</a>
                        </td>
                    </tr>
                    <?php 
                    $rank++;
                    endforeach; 
                    ?>
                </tbody>
            </table>
        </div>
        
        <?php if (!empty($filters)): ?>
        <div style="margin-top: 16px;">
            <a href="<?= url('/admin/videos/statistics') ?>" class="btn btn-secondary">✕ Filtreleri Temizle</a>
        </div>
        <?php endif; ?>
        
        <p style="margin-top: 16px; color: #666;">Toplam <?= count($viewStats) ?> video listeleniyor.</p>
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
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }
    
    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .stat-icon {
        font-size: 32px;
        margin-bottom: 8px;
    }
    
    .stat-value {
        font-size: 28px;
        font-weight: bold;
        color: #333;
    }
    
    .stat-label {
        font-size: 13px;
        color: #666;
    }
    
    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }
    </style>
</body>
</html>
