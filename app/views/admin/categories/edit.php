<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Düzenle - Admin</title>
    <link rel="stylesheet" href="<?= asset('/css/admin.css') ?>">
    <style>
        /* Canlı Demo Bölümü */
        .live-demo-section {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 20px;
            border: 1px solid #333;
        }

        .live-demo-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
        }

        .live-demo-header h3 {
            color: #fff;
            font-size: 15px;
            margin: 0;
        }

        .live-demo-header span {
            color: #666;
            font-size: 13px;
        }

        .demo-grid {
            display: flex;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap;
        }

        .demo-item {
            text-align: center;
        }

        .demo-item label {
            display: block;
            color: #888;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .demo-category-card {
            padding: 16px 20px;
            border-radius: 8px;
            text-align: center;
            transition: all 0.3s;
            min-width: 130px;
        }

        .demo-category-name {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .demo-category-count {
            font-size: 12px;
            opacity: 0.85;
        }

        .demo-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 500;
        }

        .demo-header {
            padding: 12px 24px;
            border-radius: 6px;
            text-align: center;
            min-width: 150px;
        }

        .demo-header h4 {
            font-size: 16px;
            margin-bottom: 3px;
        }

        .demo-header p {
            font-size: 12px;
            opacity: 0.85;
            margin: 0;
        }

        /* Paletler + Manuel Seçim Yan Yana */
        .color-selection-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        @media (max-width: 900px) {
            .color-selection-row {
                grid-template-columns: 1fr;
            }
        }

        /* Hazır Paletler */
        .presets-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 18px 20px;
        }

        .presets-section h3 {
            margin-bottom: 14px;
            color: #333;
            font-size: 15px;
        }

        .color-presets {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .color-preset {
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.2s;
            background: white;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        }

        .color-preset:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .color-preset.active {
            border-color: #333;
        }

        .color-preview {
            width: 52px;
            height: 50px;
            border-radius: 6px;
            margin: 0 auto 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
        }

        .color-name {
            font-size: 14px;
            font-weight: 600;
            color: #555;
        }

        /* Manuel Renk Seçimi */
        .color-picker-section {
            background: white;
            border-radius: 10px;
            padding: 18px 20px;
            border: 1px solid #e0e0e0;
        }

        .color-picker-section h3 {
            margin-bottom: 14px;
            color: #333;
            font-size: 15px;
        }

        .color-picker-column {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .color-picker-group {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            background: #f5f5f5;
            border-radius: 6px;
        }

        .color-picker-group label {
            font-weight: 500;
            color: #333;
            font-size: 16px;
            min-width: 75px;
        }

        .color-picker-group input[type="color"] {
            width: 40px;
            height: 34px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            padding: 0;
        }

        .color-picker-group .color-hex {
            font-family: monospace;
            font-size: 13px;
            color: #666;
            background: white;
            padding: 6px 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
            width: 80px;
            text-align: center;
        }

        /* Mevcut Logo */
        .current-logo-section {
            background: #f0f7ff;
            border: 1px solid #3B82F6;
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .current-logo-section h4 {
            color: #1e40af;
            font-size: 14px;
            margin: 0;
        }

        .current-logo-preview {
            background: white;
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ddd;
        }

        .current-logo-preview img {
            width: 60px;
            height: 60px;
            object-fit: contain;
            display: block;
        }

        /* Logo Preview */
        .logo-preview-container {
            margin-top: 10px;
            padding: 12px;
            background: #e8f5e9;
            border-radius: 6px;
            border: 2px dashed #4caf50;
            text-align: center;
            display: none;
        }

        .logo-preview-container.active {
            display: block;
        }

        .logo-preview-container img {
            max-width: 80px;
            max-height: 80px;
            border-radius: 6px;
        }

        .logo-preview-container p {
            margin: 6px 0 0;
            color: #2e7d32;
            font-size: 12px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../_header.php'; ?>
    
    <div class="admin-container">
        <div class="page-header">
            <h1>✏️ Kategori Düzenle</h1>
            <a href="<?= url('/admin/categories') ?>" class="btn btn-secondary">← Geri Dön</a>
        </div>

        <form method="POST" enctype="multipart/form-data" class="admin-form">
            
            <!-- Kategori Adı -->
            <div class="form-group">
                <label>Kategori Adı *</label>
                <input type="text" name="name" id="categoryName" class="form-control" 
                       value="<?= htmlspecialchars($category['name']) ?>" required>
            </div>

            <!-- CANLI DEMO -->
            <div class="live-demo-section">
                <div class="live-demo-header">
                    <h3>👁️ Canlı Önizleme</h3>
                    <span>• Anlık güncellenir</span>
                </div>
                
                <div class="demo-grid">
                    <div class="demo-item">
                        <label>Kart</label>
                        <div class="demo-category-card" id="demoCard">
                            <div class="demo-category-name" id="demoCategoryName"><?= htmlspecialchars($category['name']) ?></div>
                            <div class="demo-category-count">12 video</div>
                        </div>
                    </div>

                    <div class="demo-item">
                        <label>Badge</label>
                        <span class="demo-badge" id="demoBadge"><?= htmlspecialchars($category['name']) ?></span>
                    </div>

                    <div class="demo-item">
                        <label>Başlık</label>
                        <div class="demo-header" id="demoHeader">
                            <h4 id="demoHeaderTitle"><?= htmlspecialchars($category['name']) ?></h4>
                            <p>24 video</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PALETLER + MANUEL SEÇİM YAN YANA -->
            <div class="color-selection-row">
                <!-- Hazır Paletler -->
                <div class="presets-section">
                    <h3>🎨 Hazır Paletler</h3>
                    <div class="color-presets">
                        <?php foreach ($colorPresets as $key => $preset): ?>
                        <div class="color-preset <?= ($category['background_color'] === $preset['bg'] && $category['text_color'] === $preset['text']) ? 'active' : '' ?>" 
                             data-bg="<?= $preset['bg'] ?>" data-text="<?= $preset['text'] ?>">
                            <div class="color-preview" style="background: <?= $preset['bg'] ?>; color: <?= $preset['text'] ?>;">A</div>
                            <div class="color-name"><?= $preset['name'] ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Manuel Seçim -->
                <div class="color-picker-section">
                    <h3>🖌️ Manuel Seçim</h3>
                    <div class="color-picker-column">
                        <div class="color-picker-group">
                            <label>Arka Plan:</label>
                            <input type="color" id="background_color" name="background_color" 
                                   value="<?= $category['background_color'] ?>">
                            <input type="text" class="color-hex" id="bgHex" 
                                   value="<?= strtoupper($category['background_color']) ?>" maxlength="7">
                        </div>
                        <div class="color-picker-group">
                            <label>Yazı:</label>
                            <input type="color" id="text_color" name="text_color" 
                                   value="<?= $category['text_color'] ?>">
                            <input type="text" class="color-hex" id="textHex" 
                                   value="<?= strtoupper($category['text_color']) ?>" maxlength="7">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mevcut Logo -->
            <?php if (!empty($category['logo_path'])): ?>
            <div class="current-logo-section">
                <div class="current-logo-preview">
                    <img src="<?= $category['logo_path'] ?>" alt="Mevcut Logo">
                </div>
                <div>
                    <h4>📷 Mevcut Logo</h4>
                    <label class="checkbox-label" style="margin-top: 8px; font-size: 13px;">
                        <input type="checkbox" name="remove_logo" value="1">
                        🗑️ Logoyu kaldır
                    </label>
                </div>
            </div>
            <?php endif; ?>

            <!-- Yeni Logo Upload -->
            <div class="form-group">
                <label><?= !empty($category['logo_path']) ? '🔄 Logoyu Değiştir' : '📤 Kategori Logosu Ekle' ?></label>
                <input type="file" name="logo" class="form-control" accept="image/png,image/jpeg" id="logoInput">
                <small class="form-help">📐 150x150px | PNG veya JPG</small>
                <div id="logoPreview" class="logo-preview-container">
                    <img id="logoPreviewImg" src="" alt="Yeni Logo">
                    <p>✅ Yeni logo yüklenecek</p>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Güncelle</button>
                <a href="<?= url('/admin/categories') ?>" class="btn btn-secondary">İptal</a>
            </div>
        </form>
    </div>

    <script>
        const categoryNameInput = document.getElementById('categoryName');
        const bgColorInput = document.getElementById('background_color');
        const textColorInput = document.getElementById('text_color');
        const bgHexInput = document.getElementById('bgHex');
        const textHexInput = document.getElementById('textHex');
        const colorPresets = document.querySelectorAll('.color-preset');
        const logoInput = document.getElementById('logoInput');
        const logoPreview = document.getElementById('logoPreview');
        const logoPreviewImg = document.getElementById('logoPreviewImg');

        const demoCard = document.getElementById('demoCard');
        const demoBadge = document.getElementById('demoBadge');
        const demoHeader = document.getElementById('demoHeader');
        const demoCategoryName = document.getElementById('demoCategoryName');
        const demoHeaderTitle = document.getElementById('demoHeaderTitle');

        function updateDemo() {
            const bgColor = bgColorInput.value;
            const textColor = textColorInput.value;
            const name = categoryNameInput.value || 'Kategori';

            const gradient = `linear-gradient(135deg, ${bgColor} 0%, ${bgColor}dd 100%)`;

            demoCard.style.background = gradient;
            demoCard.style.color = textColor;
            demoCategoryName.textContent = name;

            demoBadge.style.backgroundColor = bgColor;
            demoBadge.style.color = textColor;
            demoBadge.textContent = name;

            demoHeader.style.background = gradient;
            demoHeader.style.color = textColor;
            demoHeaderTitle.textContent = name;

            bgHexInput.value = bgColor.toUpperCase();
            textHexInput.value = textColor.toUpperCase();
        }

        bgColorInput.addEventListener('input', () => { updateDemo(); clearPresetSelection(); });
        textColorInput.addEventListener('input', () => { updateDemo(); clearPresetSelection(); });
        categoryNameInput.addEventListener('input', updateDemo);

        bgHexInput.addEventListener('input', (e) => {
            let hex = e.target.value;
            if (!hex.startsWith('#')) hex = '#' + hex;
            if (/^#[0-9A-Fa-f]{6}$/.test(hex)) {
                bgColorInput.value = hex;
                updateDemo();
            }
        });

        textHexInput.addEventListener('input', (e) => {
            let hex = e.target.value;
            if (!hex.startsWith('#')) hex = '#' + hex;
            if (/^#[0-9A-Fa-f]{6}$/.test(hex)) {
                textColorInput.value = hex;
                updateDemo();
            }
        });

        colorPresets.forEach(preset => {
            preset.addEventListener('click', () => {
                colorPresets.forEach(p => p.classList.remove('active'));
                preset.classList.add('active');
                bgColorInput.value = preset.dataset.bg;
                textColorInput.value = preset.dataset.text;
                updateDemo();
            });
        });

        function clearPresetSelection() {
            colorPresets.forEach(p => p.classList.remove('active'));
        }

        logoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    logoPreviewImg.src = e.target.result;
                    logoPreview.classList.add('active');
                };
                reader.readAsDataURL(file);
            } else {
                logoPreview.classList.remove('active');
            }
        });

        updateDemo();
    </script>
</body>
</html>
