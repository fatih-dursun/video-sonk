<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Video Ekle - Admin</title>
    <link rel="stylesheet" href="<?= asset('/css/admin.css') ?>">
</head>
<body>
    <?php include __DIR__ . '/../_header.php'; ?>
    
    <div class="admin-container">
        <div class="page-header">
            <h1>➕ Yeni Video Ekle</h1>
            <a href="<?= url('/admin/videos') ?>" class="btn btn-secondary">← Geri Dön</a>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="admin-form" id="videoForm">
            <div class="form-row">
                <div class="form-group">
                    <label>Video Başlığı *</label>
                    <input type="text" name="title" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Kategori *</label>
                    <select name="category_id" id="category_id" class="form-control" required>
                        <option value="">Kategori Seçin</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Açıklama</label>
                <textarea name="description" class="form-control" rows="4"></textarea>
            </div>

            <!-- SIRALAMA -->
            <div class="form-group">
                <label>Sıra Numarası (Kategori Sayfasında)</label>
                <select name="sort_order" id="sort_order" class="form-control">
                    <option value="auto">İlk Boş Sıra (Otomatik)</option>
                </select>
                <small class="form-help">Kategorideki gösterim sırasını belirler. Otomatik bırakırsanız ilk boş sıraya eklenir.</small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Video Dosyası * (.mp4, .webm)</label>
                    <input type="file" name="video" id="videoInput" class="form-control" accept="video/*" required>
                    <small class="form-help">Video dosyanızı seçin</small>
                    
                    <div id="videoPreview" class="file-preview-box" style="display: none;">
                        <label style="font-size: 13px; color: #28a745; margin-bottom: 8px; display: block;">✅ Video Önizleme:</label>
                        <video id="videoPreviewPlayer" controls style="max-width: 100%; max-height: 300px; border-radius: 8px;">
                            Video önizlemesi
                        </video>
                    </div>
                </div>

                <div class="form-group">
                    <label>Thumbnail (.jpg, .png)</label>
                    <input type="file" name="thumbnail" id="thumbnailInput" class="form-control" accept="image/*">
                    <small class="form-help">Opsiyonel. Yüklemezseniz otomatik görsel oluşturulur.</small>
                    
                    <div id="thumbnailPreview" class="file-preview-box" style="display: none;">
                        <label style="font-size: 13px; color: #28a745; margin-bottom: 8px; display: block;">✅ Thumbnail Önizleme:</label>
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
                        <input type="radio" name="featured_source" value="thumbnail" checked>
                        <div class="radio-content">
                            <strong>📸 Thumbnail Kullan</strong>
                            <small>Yüklediğiniz thumbnail öne çıkan görsel olarak kullanılır.</small>
                        </div>
                    </label>
                    
                    <label class="radio-label">
                        <input type="radio" name="featured_source" value="text">
                        <div class="radio-content">
                            <strong>✨ Otomatik Görsel Oluştur</strong>
                            <small>Aşağıdaki metin kullanılarak kategorinin renklerinde otomatik bir kapak görseli oluşturulur.</small>
                        </div>
                    </label>
                </div>

                <div class="form-group" id="featuredTextGroup">
                    <label>Öne Çıkan Yazı (Otomatik Görsel için)</label>
                    <input type="text" name="featured_text" id="featuredTextInput" class="form-control" 
                           placeholder="Boş bırakılırsa video başlığı kullanılır">
                    <small class="form-help" id="featuredTextHelp">Bu alan "Otomatik Görsel Oluştur" seçeneği için kullanılır</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Durum</label>
                    <select name="status" class="form-control">
                        <option value="active">Aktif</option>
                        <option value="passive">Pasif</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_featured" value="1">
                        Öne Çıkan Video Olarak İşaretle
                    </label>
                    <small class="form-help">Öne çıkan videolar ana sayfada özel olarak gösterilir</small>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Video Ekle</button>
                <a href="<?= url('/admin/videos') ?>" class="btn btn-secondary">İptal</a>
            </div>
        </form>
    </div>

    <style>
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

        // Kategori değiştiğinde boş sıraları getir
        const categorySelect = document.getElementById('category_id');
        const sortOrderSelect = document.getElementById('sort_order');
        
        if (categorySelect && sortOrderSelect) {
            categorySelect.addEventListener('change', async function() {
                const categoryId = this.value;
                
                if (!categoryId) {
                    sortOrderSelect.innerHTML = '<option value="auto">İlk Boş Sıra (Otomatik)</option>';
                    return;
                }
                
                try {
                    const basePath = '<?= defined('BASE_PATH') ? BASE_PATH : '/video-portal/public' ?>';
                    const response = await fetch(`${basePath}/api/available-orders?category_id=${categoryId}`);
                    const data = await response.json();
                    
                    let html = '<option value="auto">İlk Boş Sıra (Otomatik)</option>';
                    
                    if (data.orders && data.orders.length > 0) {
                        data.orders.forEach(order => {
                            html += `<option value="${order}">${order}</option>`;
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
