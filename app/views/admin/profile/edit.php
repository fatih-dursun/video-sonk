<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profili Düzenle - Admin</title>
    <link rel="stylesheet" href="<?= asset('/css/admin.css') ?>">
</head>
<body>
    <?php include __DIR__ . '/../_header.php'; ?>
    
    <div class="admin-container">
        <div class="page-header">
            <h1>✏️ Profili Düzenle</h1>
            <a href="<?= url('/admin/profile') ?>" class="btn btn-secondary">← Geri Dön</a>
        </div>

        <form method="POST" class="admin-form">
            <div class="form-row">
                <div class="form-group">
                    <label>Kullanıcı Adı</label>
                    <input type="text" class="form-control" 
                           value="<?= htmlspecialchars($user['username']) ?>" disabled>
                    <small class="form-help">Kullanıcı adı değiştirilemez</small>
                </div>

                <div class="form-group">
                    <label>E-posta *</label>
                    <input type="email" name="email" class="form-control" 
                           value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Ad</label>
                    <input type="text" name="first_name" class="form-control" 
                           value="<?= htmlspecialchars($user['first_name'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Soyad</label>
                    <input type="text" name="last_name" class="form-control" 
                           value="<?= htmlspecialchars($user['last_name'] ?? '') ?>">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Güncelle</button>
                <a href="<?= url('/admin/profile') ?>" class="btn btn-secondary">İptal</a>
            </div>
        </form>
    </div>
</body>
</html>
