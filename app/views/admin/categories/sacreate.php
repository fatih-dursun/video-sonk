<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Kategori Ekle - Admin</title>
    <link rel="stylesheet" href="<?= asset('/css/admin.css') ?>">
</head>
<body>
    <?php include __DIR__ . '/../_header.php'; ?>
    
    <div class="admin-container">
        <div class="page-header">
            <h1>➕ Yeni Kategori Ekle</h1>
            <a href="<?= url('/admin/categories') ?>" class="btn btn-secondary">← Geri Dön</a>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <div class="form-group">
                <label>Kategori Adı *</label>
                <input type="text" name="name" class="form-control" placeholder="örn: 🎬 Filmler" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Arka Plan Rengi *</label>
                    <input type="color" id="background_color" name="background_color" class="form-control" value="#3B82F6" required>
                </div>

                <div class="form-group">
                    <label>Yazı Rengi *</label>
                    <input type="color" id="text_color" name="text_color" class="form-control" value="#FFFFFF" required>
                </div>
            </div>

            <div class="form-group">
                <label>Kategori Logosu (Opsiyonel)</label>
                <input type="file" name="logo" class="form-control" accept="image/png,image/jpeg" id="logoInput">
                <small class="form-help">
                    📐 Önerilen boyut: 150x150px | 
                    🎨 Format: PNG (şeffaf arka plan önerili) veya JPG |
                    📍 Öne çıkan görsellerde sol üst köşede görünür
                </small>
                <div id="logoPreview" style="margin-top: 12px; display: none;">
                    <img id="logoPreviewImg" style="max-width: 150px; max-height: 150px; border: 2px solid #ddd; border-radius: 8px; padding: 8px; background: white;">
                </div>
            </div>

            <div class="form-group">
                <label>Hazır Renk Paletleri</label>
                <div class="color-presets">
                    <?php foreach ($colorPresets as $key => $preset): ?>
                    <div class="color-preset" data-bg="<?= $preset['bg'] ?>" data-text="<?= $preset['text'] ?>">
                        <div class="color-preview" style="background: linear-gradient(135deg, <?= $preset['bg'] ?> 0%, <?= $preset['bg'] ?>dd 100%); color: <?= $preset['text'] ?>">
                            <div style="padding-top: 18px; font-size: 18px;">A</div>
                        </div>
                        <div class="color-name"><?= $preset['name'] ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Kategori Ekle</button>
                <a href="<?= url('/admin/categories') ?>" class="btn btn-secondary">İptal</a>
            </div>
        </form>
    </div>

    <script src="<?= asset('/js/main.js') ?>"></script>
    <script>
        // Logo preview
        const logoInput = document.getElementById('logoInput');
        const logoPreview = document.getElementById('logoPreview');
        const logoPreviewImg = document.getElementById('logoPreviewImg');

        if (logoInput) {
            logoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        logoPreviewImg.src = e.target.result;
                        logoPreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    </script>
</body>
</html>
