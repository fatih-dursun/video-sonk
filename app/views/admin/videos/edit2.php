<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Düzenle - Admin</title>
    <link rel="stylesheet" href="<?= asset('/css/admin.css') ?>">
</head>
<body>
    <?php include __DIR__ . '/../_header.php'; ?>
    
    <div class="admin-container">
        <div class="page-header">
            <h1>✏️ Video Düzenle</h1>
            <a href="<?= url('/admin/videos') ?>" class="btn btn-secondary">← Geri Dön</a>
        </div>

        <form method="POST" enctype="multipart/form-data" class="admin-form" id="videoForm">
            <div class="form-row">
                <div class="form-group">
                    <label>Video Başlığı *</label>
                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($video['title']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Kategori *</label>
                    <select name="category_id" id="category_id" class="form-control" required>
                        <option value="">Kategori Seçin</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $video['category_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Açıklama *</label>
                <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($video['description']) ?></textarea>
            </div>

            <!-- SORT ORDER DROPDOWN -->
            <div class="form-group">
                <label>Sıra Numarası (Kategori Sayfasında)</label>
                <select name="sort_order" id="sort_order" class="form-control">
                    <option value="auto">İlk Boş Sıra (Otomatik)</option>
                    <?php if (!empty($availableOrders)): ?>
                        <?php foreach ($availableOrders as $order): ?>
                            <option value="<?= $order ?>" <?= $order == $video['sort_order'] ? 'selected' : '' ?>>
                                <?= $order ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?php if ($video['sort_order'] && !in_array($video['sort_order'], $availableOrders ?? [])): ?>
                        <option value="<?= $video['sort_order'] ?>" selected><?= $video['sort_order'] ?> (Mevcut)</option>
                    <?php endif; ?>
                </select>
                <small class="form-help">
                    <?php if ($video['sort_order']): ?>
                        Mevcut sıra: <strong><?= $video['sort_order'] ?></strong>
                    <?php else: ?>
                        Şu anda sırasız (en sonda görünüyor)
                    <?php endif; ?>
                </small>
            </div>

            <div class="form-group">
                <label>Öne Çıkan Yazı</label>
                <input type="text" name="featured_text" id="featured_text" class="form-control" 
                       value="<?= htmlspecialchars($video['featured_text'] ?? $video['title']) ?>"
                       placeholder="Boş bırakılırsa video başlığı kullanılır">
            </div>

            <!-- Featured Source Radio -->
            <div class="form-group">
                <label>Öne Çıkan Görsel Kaynağı</label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="featured_source" value="thumbnail" 
                               <?= ($video['featured_source'] ?? 'thumbnail') === 'thumbnail' ? 'checked' : '' ?>>
                        <div class="radio-content">
                            <strong>📸 Thumbnail Kullan</strong>
                            <small>Yüklediğiniz thumbnail görsel olarak kullanılır</small>
                        </div>
                    </label>
                    
                    <label class="radio-label">
                        <input type="radio" name="featured_source" value="text"
                               <?= ($video['featured_source'] ?? 'thumbnail') === 'text' ? 'checked' : '' ?>>
                        <div class="radio-content">
                            <strong>✨ Otomatik Görsel Oluştur</strong>
                            <small>Kategori renklerinde otomatik görsel oluşturulur</small>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Video Preview -->
            <div class="form-group">
                <label>Videonun Şu Anki Hali</label>
                <video controls style="max-width: 600px; border-radius: 8px;">
                    <source src="<?= upload_url($video['video_path']) ?>" type="video/mp4">
                </video>
            </div>

            <!-- Thumbnail Preview -->
            <div class="form-group">
                <label>Mevcut Thumbnail</label>
                <div>
                    <img src="<?= upload_url($video['thumbnail_path']) ?>" alt="" style="max-width: 400px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                </div>
            </div>

            <!-- Featured Image Preview -->
            <div class="form-group">
                <label>Mevcut Öne Çıkan Görsel</label>
                <div>
                    <img src="<?= upload_url($video['featured_image_path']) ?>" alt="" style="max-width: 600px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                    <p style="margin-top: 8px; color: #666; font-size: 13px;">
                        Kaynak: 
                        <?php if (($video['featured_source'] ?? 'thumbnail') === 'thumbnail'): ?>
                            📸 Thumbnail
                        <?php else: ?>
                            ✨ Otomatik Oluşturulmuş
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Yeni Video Dosyası (.mp4, .webm)</label>
                    <input type="file" name="video" class="form-control" accept="video/*">
                    <small class="form-help">Boş bırakırsanız mevcut video kalır</small>
                </div>

                <div class="form-group">
                    <label>Yeni Thumbnail (.jpg, .png)</label>
                    <input type="file" name="thumbnail" class="form-control" accept="image/*">
                    <small class="form-help">Boş bırakırsanız mevcut thumbnail kalır</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Durum</label>
                    <select name="status" class="form-control">
                        <option value="active" <?= $video['status'] === 'active' ? 'selected' : '' ?>>Aktif</option>
                        <option value="passive" <?= $video['status'] === 'passive' ? 'selected' : '' ?>>Pasif</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_featured" value="1" <?= $video['is_featured'] ? 'checked' : '' ?>>
                        Öne Çıkan Video Olarak İşaretle
                    </label>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Güncelle</button>
                <a href="<?= url('/admin/videos') ?>" class="btn btn-secondary">İptal</a>
            </div>
        </form>
    </div>

    <script>
    // Kategori değiştiğinde boş sıraları yenile
    const categorySelect = document.getElementById('category_id');
    const sortOrderSelect = document.getElementById('sort_order');
    const currentVideoId = <?= $video['id'] ?>;
    const currentSortOrder = <?= $video['sort_order'] ?? 'null' ?>;
    
    if (categorySelect && sortOrderSelect) {
        categorySelect.addEventListener('change', async function() {
            const categoryId = this.value;
            
            if (!categoryId) {
                sortOrderSelect.innerHTML = '<option value="auto">İlk Boş Sıra (Otomatik)</option>';
                return;
            }
            
            try {
                const basePath = '<?= defined('BASE_PATH') ? BASE_PATH : '/video-portal/public' ?>';
                const response = await fetch(`${basePath}/api/available-orders?category_id=${categoryId}&video_id=${currentVideoId}`);
                const data = await response.json();
                
                let html = '<option value="auto">İlk Boş Sıra (Otomatik)</option>';
                
                if (data.orders && data.orders.length > 0) {
                    data.orders.forEach(order => {
                        const selected = order == currentSortOrder ? 'selected' : '';
                        html += `<option value="${order}" ${selected}>${order}</option>`;
                    });
                }
                
                // Mevcut sıra boş listede yoksa ekle
                if (currentSortOrder && !data.orders.includes(currentSortOrder)) {
                    html += `<option value="${currentSortOrder}" selected>${currentSortOrder} (Mevcut)</option>`;
                }
                
                sortOrderSelect.innerHTML = html;
            } catch (error) {
                console.error('Sıra listesi yüklenemedi:', error);
            }
        });
    }
    </script>
</body>
</html>
