<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifre Değiştir - Admin</title>
    <link rel="stylesheet" href="<?= asset('/css/admin.css') ?>">
</head>
<body>
    <?php include __DIR__ . '/../_header.php'; ?>
    
    <div class="admin-container">
        <div class="page-header">
            <h1>🔒 Şifre Değiştir</h1>
            <a href="<?= url('/admin/profile') ?>" class="btn btn-secondary">← Geri Dön</a>
        </div>

        <div class="password-change-card">
            <form method="POST" class="admin-form">
                <div class="form-group">
                    <label>Mevcut Şifre *</label>
                    <input type="password" name="current_password" class="form-control" required>
                    <small class="form-help">Güvenlik için mevcut şifrenizi girmeniz gerekiyor</small>
                </div>

                <div class="form-group">
                    <label>Yeni Şifre *</label>
                    <input type="password" name="new_password" class="form-control" required minlength="6">
                    <small class="form-help">En az 6 karakter olmalıdır</small>
                </div>

                <div class="form-group">
                    <label>Yeni Şifre (Tekrar) *</label>
                    <input type="password" name="confirm_password" class="form-control" required minlength="6">
                    <small class="form-help">Yeni şifrenizi tekrar girin</small>
                </div>

                <div class="password-tips">
                    <h4>💡 Güçlü Şifre İpuçları:</h4>
                    <ul>
                        <li>En az 6 karakter kullanın</li>
                        <li>Büyük ve küçük harf karışımı kullanın</li>
                        <li>Sayı ve özel karakter ekleyin</li>
                        <li>Kolay tahmin edilebilecek şifrelerden kaçının</li>
                    </ul>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">🔒 Şifreyi Değiştir</button>
                    <a href="<?= url('/admin/profile') ?>" class="btn btn-secondary">İptal</a>
                </div>
            </form>
        </div>
    </div>

    <style>
        .password-change-card {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 32px;
        }

        .password-tips {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 24px 0;
        }

        .password-tips h4 {
            margin: 0 0 12px 0;
            color: #333;
            font-size: 16px;
        }

        .password-tips ul {
            margin: 0;
            padding-left: 20px;
        }

        .password-tips li {
            margin: 8px 0;
            color: #666;
            line-height: 1.6;
        }
    </style>
</body>
</html>
