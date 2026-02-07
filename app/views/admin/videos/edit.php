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

        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <div class="form-row">
                <div class="form-group">
                    <label>Video Başlığı *</label>
                    <input type="text" name="title" class="form-control" 
                           value="<?= htmlspecialchars($video['title']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Kategori *</label>
                    <select name="category_id" id="category_id" class="form-control" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" 
                                    <?= $video['category_id'] == $cat['id'] ? 'selected' : '' ?>>
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

            <!-- SIRALAMA EKLENDİ -->
            <div class="form-group">
                <label>Sıra Numarası (Kategori Sayfasında)</label>
                <select name="sort_order" id="sort_order" class="form-control">
                    <option value="auto">İlk Boş Sıra (Otomatik)</option>
                    <?php if (!empty($availableOrders)): ?>
                        <?php foreach ($availableOrders as $order): ?>
                            <?php 
                            $isCurrent = ($order == $video['sort_order']);
                            $selected = $isCurrent ? 'selected' : '';
                            ?>
                            <option value="<?= $order ?>" <?= $selected ?> <?= $isCurrent ? 'style="font-weight: bold;"' : '' ?>>
                                <?= $order ?><?= $isCurrent ? ' (Mevcut)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <small class="form-help">
                    <?php if ($video['sort_order']): ?>
                        Mevcut sıra: <strong><?= $video['sort_order'] ?></strong>. Kategorideki gösterim sırasını belirler.
                    <?php else: ?>
                        Şu anda sırasız (en sonda görünüyor). Kategorideki gösterim sırasını belirler.
                    <?php endif; ?>
                </small>
            </div>

            <div class="current-files">
                <h3>Mevcut Dosyalar</h3>
                <div class="file-preview-grid">
                    <div class="file-preview">
                        <label>Mevcut Video:</label>
                        <video controls style="max-width: 100%; border-radius: 8px;">
                            <source src="<?= upload_url($video['video_path']) ?>" type="video/mp4">
                        </video>
                    </div>
                    
                    <div class="file-preview">
                        <label>Mevcut Thumbnail:</label>
                        <img src="<?= upload_url($video['thumbnail_path']) ?>" alt="Thumbnail" style="max-width: 100%; border-radius: 8px;">
                    </div>

                    <div class="file-preview">
                        <label>Mevcut Öne Çıkan Görsel:</label>
                        <img src="<?= upload_url($video['featured_image_path']) ?>" alt="Featured" style="max-width: 100%; border-radius: 8px;">
                        <small style="display: block; margin-top: 8px; color: #666;">
                            <?php if (($video['featured_source'] ?? 'thumbnail') === 'thumbnail'): ?>
                                📸 Kaynak: Thumbnail
                            <?php else: ?>
                                ✨ Kaynak: Otomatik Görsel
                            <?php endif; ?>
                        </small>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Video Dosyası (Değiştirmek için)</label>
                    <input type="file" name="video" id="videoInput" class="form-control" accept="video/*">
                    <small class="form-help">Yeni video yüklemek isterseniz seçin, yoksa mevcut video kalacak</small>
                    
                    <div id="videoPreview" class="file-preview-box" style="display: none;">
                        <label style="font-size: 13px; color: #28a745; margin-bottom: 8px; display: block;">✅ Yeni Video Önizleme:</label>
                        <video id="videoPreviewPlayer" controls style="max-width: 100%; max-height: 300px; border-radius: 8px;">
                            Video önizlemesi
                        </video>
                    </div>
                </div>

                <div class="form-group">
                    <label>Thumbnail (Değiştirmek için)</label>
                    <input type="file" name="thumbnail" id="thumbnailInput" class="form-control" accept="image/*">
                    <small class="form-help">Yeni thumbnail yüklemek isterseniz seçin, yoksa mevcut thumbnail kalacak</small>
                    
                    <div id="thumbnailPreview" class="file-preview-box" style="display: none;">
                        <label style="font-size: 13px; color: #28a745; margin-bottom: 8px; display: block;">✅ Yeni Thumbnail Önizleme:</label>
                        <img id="thumbnailPreviewImage" src="" alt="Thumbnail Önizleme" 
                             style="max-width: 100%; max-height: 300px; border-radius: 8px;">
                    </div>
                </div>
            </div>

            <!-- Öne Çıkan Görsel Seçimi -->
            <div class="featured-source-section">
                <h3>🖼️ Öne Çıkan Görsel Seçimi</h3>
                <p class="section-description">Videoların ana sayfada ve listelerde gösterilecek öne çıkan görselini belirleyin</p>
                
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="featured_source" value="thumbnail" 
                               <?= ($video['featured_source'] ?? 'thumbnail') === 'thumbnail' ? 'checked' : '' ?>>
                        <div class="radio-content">
                            <strong>📸 Thumbnail Kullan</strong>
                            <small>Mevcut veya yeni yükleyeceğiniz thumbnail öne çıkan görsel olarak kullanılır.</small>
                        </div>
                    </label>
                    
                    <label class="radio-label">
                        <input type="radio" name="featured_source" value="text"
                               <?= ($video['featured_source'] ?? 'thumbnail') === 'text' ? 'checked' : '' ?>>
                        <div class="radio-content">
                            <strong>✨ Otomatik Görsel Oluştur</strong>
                            <small>Aşağıdaki metin kullanılarak kategorinin renklerinde otomatik bir kapak görseli oluşturulur.</small>
                        </div>
                    </label>
                </div>

                <div class="form-group" id="featuredTextGroup">
                    <label>Öne Çıkan Yazı (Otomatik Görsel için)</label>
                    <input type="text" name="featured_text" id="featuredTextInput" class="form-control" 
                           value="<?= htmlspecialchars($video['featured_text'] ?? $video['title']) ?>"
                           placeholder="Boş bırakılırsa video başlığı kullanılır">
                    <small class="form-help" id="featuredTextHelp">Bu alan "Otomatik Görsel Oluştur" seçeneği için kullanılır</small>
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
                        <input type="checkbox" name="is_featured" value="1" 
                               <?= $video['is_featured'] ? 'checked' : '' ?>>
                        Öne Çıkan Video Olarak İşaretle
                    </label>
                    <small class="form-help">Öne çıkan videolar ana sayfada özel olarak gösterilir</small>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Güncelle</button>
                <a href="<?= url('/admin/videos') ?>" class="btn btn-secondary">İptal</a>
            </div>
        </form>
    </div>

    <style>
        .current-files {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .current-files h3 {
            margin-bottom: 16px;
            color: #333;
        }

        .file-preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .file-preview {
            background: white;
            padding: 16px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .file-preview label {
            display: block;
            font-weight: 600;
            margin-bottom: 12px;
            color: #555;
        }

        .file-preview img,
        .file-preview video {
            width: 100%;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .file-preview-box {
            margin-top: 12px;
            padding: 12px;
            background: #e7f5e9;
            border: 2px solid #28a745;
            border-radius: 8px;
        }

        .featured-source-section {
            background: #f8f9fa;
            padding: 24px;
            border-radius: 12px;
            margin: 30px 0;
        }

        .featured-source-section h3 {
            margin-bottom: 8px;
            color: #333;
        }

        .section-description {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .radio-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 20px;
        }

        .radio-label {
            display: flex;
            align-items: flex-start;
            padding: 16px;
            background: white;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .radio-label:hover {
            border-color: #667eea;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
        }

        .radio-label input[type="radio"] {
            margin-top: 4px;
            margin-right: 12px;
            cursor: pointer;
        }

        .radio-label input[type="radio"]:checked + .radio-content {
            color: #667eea;
        }

        .radio-label:has(input:checked) {
            border-color: #667eea;
            background: #f0f4ff;
        }

        .radio-content {
            flex: 1;
        }

        .radio-content strong {
            display: block;
            margin-bottom: 4px;
            font-size: 15px;
        }

        .radio-content small {
            display: block;
            color: #666;
            line-height: 1.5;
        }
    </style>

    <script>
        // Video Önizleme
        document.getElementById('videoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('videoPreview');
            const player = document.getElementById('videoPreviewPlayer');
            
            if (file) {
                const url = URL.createObjectURL(file);
                player.src = url;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        });

        // Thumbnail Önizleme
        document.getElementById('thumbnailInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('thumbnailPreview');
            const image = document.getElementById('thumbnailPreviewImage');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    image.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });

        // Radio button değişimi
        const radioButtons = document.querySelectorAll('input[name="featured_source"]');
        const featuredTextInput = document.getElementById('featuredTextInput');
        const featuredTextHelp = document.getElementById('featuredTextHelp');
        
        radioButtons.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'thumbnail') {
                    featuredTextInput.style.opacity = '0.5';
                    featuredTextInput.disabled = true;
                    featuredTextHelp.textContent = 'Bu alan "Otomatik Görsel Oluştur" seçeneği için kullanılır';
                } else {
                    featuredTextInput.style.opacity = '1';
                    featuredTextInput.disabled = false;
                    featuredTextHelp.textContent = 'Bu metin kategori renklerinde bir kapak görseli oluşturacak';
                }
            });
        });
        
        // Sayfa yüklendiğinde kontrol et
        document.querySelector('input[name="featured_source"]:checked').dispatchEvent(new Event('change'));

        // Kategori değiştiğinde boş sıraları yenile
        const categorySelect = document.getElementById('category_id');
        const sortOrderSelect = document.getElementById('sort_order');
        const currentVideoId = <?= $video['id'] ?>;
        const currentSortOrder = <?= $video['sort_order'] ?? 'null' ?>;
        const originalCategoryId = <?= $video['category_id'] ?>;
        
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
                    
                    // Mevcut kategoriye dönüldü mü kontrol et
                    const isOriginalCategory = (categoryId == originalCategoryId);
                    
                    if (data.orders && data.orders.length > 0) {
                        data.orders.forEach(order => {
                            // Sadece orijinal kategorideyse "mevcut" göster
                            const isCurrent = isOriginalCategory && (order == currentSortOrder);
                            const selected = isCurrent ? 'selected' : '';
                            const style = isCurrent ? ' style="font-weight: bold;"' : '';
                            const label = isCurrent ? ' (Mevcut)' : '';
                            html += `<option value="${order}" ${selected}${style}>${order}${label}</option>`;
                        });
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
