<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Önizleme - <?= htmlspecialchars($video['title']) ?></title>
    <link rel="stylesheet" href="<?= asset('/css/admin.css') ?>">
    <style>
        .preview-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .preview-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .preview-header h1 {
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        
        .preview-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 600;
        }
        
        .status-pending { background: #fff3cd; color: #856404; }
        .status-pending_delete { background: #f8d7da; color: #721c24; }
        
        .video-wrapper {
            background: #000;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        
        .video-wrapper video {
            width: 100%;
            max-height: 500px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }
        
        .info-card {
            background: white;
            padding: 16px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .info-card label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            display: block;
            margin-bottom: 4px;
        }
        
        .info-card .value {
            font-size: 16px;
            font-weight: 500;
            color: #333;
        }
        
        .thumbnail-preview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }
        
        .thumbnail-preview img {
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        
        .description-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .description-box h3 {
            margin: 0 0 10px 0;
            color: #333;
        }
        
        .description-box p {
            margin: 0;
            color: #555;
            line-height: 1.6;
            white-space: pre-line;
        }
        
        .action-bar {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .action-bar .spacer {
            flex: 1;
        }
        
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../_header.php'; ?>
    
    <div class="admin-container">
        <div class="preview-container">
            <div class="preview-header">
                <h1><?= htmlspecialchars($video['title']) ?></h1>
                <span class="preview-status status-<?= $video['status'] ?>">
                    <?php if ($video['status'] === 'pending'): ?>
                        ⏳ Onay Bekliyor
                    <?php elseif ($video['status'] === 'pending_delete'): ?>
                        🗑️ Silme Onayı Bekliyor
                    <?php else: ?>
                        <?= ucfirst($video['status']) ?>
                    <?php endif; ?>
                </span>
            </div>

            <!-- VIDEO OYNATICI -->
            <div class="video-wrapper">
                <video controls>
                    <source src="<?= upload_url($video['video_path']) ?>" type="video/mp4">
                    Tarayıcınız video oynatmayı desteklemiyor.
                </video>
            </div>

            <!-- BİLGİ KARTLARI -->
            <div class="info-grid">
                <div class="info-card">
                    <label>Kategori</label>
                    <div class="value">
                        <span style="background: <?= $video['background_color'] ?>; color: <?= $video['text_color'] ?>; padding: 4px 10px; border-radius: 4px;">
                            <?= htmlspecialchars($video['category_name']) ?>
                        </span>
                    </div>
                </div>
                
                <div class="info-card">
                    <label>Ekleyen</label>
                    <div class="value"><?= htmlspecialchars($video['created_by_username'] ?? 'Bilinmiyor') ?></div>
                </div>
                
                <div class="info-card">
                    <label>Eklenme Tarihi</label>
                    <div class="value"><?= date('d.m.Y H:i', strtotime($video['created_at'])) ?></div>
                </div>
                
                <div class="info-card">
                    <label>Öne Çıkan</label>
                    <div class="value"><?= $video['is_featured'] ? '⭐ Evet' : 'Hayır' ?></div>
                </div>
                
                <?php if ($video['sort_order']): ?>
                <div class="info-card">
                    <label>Sıra Numarası</label>
                    <div class="value">#<?= $video['sort_order'] ?></div>
                </div>
                <?php endif; ?>
            </div>

            <!-- THUMBNAIL VE ÖNE ÇIKAN GÖRSEL -->
            <div class="thumbnail-preview">
                <div>
                    <h4 style="margin-bottom: 10px; color: #666;">📸 Thumbnail</h4>
                    <img src="<?= upload_url($video['thumbnail_path']) ?>" alt="Thumbnail">
                </div>
                <div>
                    <h4 style="margin-bottom: 10px; color: #666;">🖼️ Öne Çıkan Görsel</h4>
                    <img src="<?= upload_url($video['featured_image_path']) ?>" alt="Featured">
                </div>
            </div>

            <!-- AÇIKLAMA -->
            <div class="description-box">
                <h3>📝 Açıklama</h3>
                <p><?= htmlspecialchars($video['description']) ?></p>
            </div>

            <!-- ÖNEÇIKAN YAZI -->
            <?php if (!empty($video['featured_text'])): ?>
            <div class="description-box">
                <h3>✨ Öne Çıkan Yazı</h3>
                <p><?= htmlspecialchars($video['featured_text']) ?></p>
            </div>
            <?php endif; ?>

            <!-- EYLEM ÇUBUĞU -->
            <div class="action-bar">
                <a href="<?= url('/admin/videos') ?>" class="btn btn-secondary">← Geri Dön</a>
                
                <div class="spacer"></div>
                
                <?php if ($video['status'] === 'pending'): ?>
                    <a href="<?= url('/admin/videos/approve/' . $video['id']) ?>" class="btn btn-success" 
                       onclick="return confirm('Bu videoyu onaylamak istediğinize emin misiniz?')">
                        Onayla
                    </a>
                    <a href="<?= url('/admin/videos/reject/' . $video['id']) ?>" class="btn btn-danger" 
                       onclick="return confirm('Bu videoyu reddetmek istediğinize emin misiniz?')">
                        Reddet
                    </a>
                <?php elseif ($video['status'] === 'pending_delete'): ?>
                    <a href="<?= url('/admin/videos/approve-delete/' . $video['id']) ?>" class="btn btn-danger" 
                       onclick="return confirm('Bu videoyu silmek istediğinize emin misiniz?')">
                        Silmeyi Onayla
                    </a>
                    <a href="<?= url('/admin/videos/reject-delete/' . $video['id']) ?>" class="btn btn-success" 
                       onclick="return confirm('Silme isteğini iptal etmek istediğinize emin misiniz?')">
                        Silmeyi İptal Et
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
