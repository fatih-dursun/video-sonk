<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Düzenle - Admin</title>
    <link rel="stylesheet" href="<?= defined('BASE_PATH') ? BASE_PATH : '/video-portal/public' ?>/css/admin.css">
</head>
<body>
    <?php include __DIR__ . '/../_header.php'; ?>
    
    <div class="admin-container">
        <div class="page-header">
            <h1>✏️ Kullanıcı Düzenle</h1>
            <a href="<?= url('/admin/users') ?>" class="btn btn-secondary">← Geri Dön</a>
        </div>

        <form method="POST" class="admin-form">
            <div class="form-row">
                <div class="form-group">
                    <label>Kullanıcı Adı</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($admin['username']) ?>" disabled>
                    <small class="form-help">Kullanıcı adı değiştirilemez</small>
                </div>

                <div class="form-group">
                    <label>E-posta *</label>
                    <input type="email" name="email" class="form-control" 
                           value="<?= htmlspecialchars($admin['email']) ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Ad *</label>
                    <input type="text" name="first_name" class="form-control" 
                           value="<?= htmlspecialchars($admin['first_name'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label>Soyad *</label>
                    <input type="text" name="last_name" class="form-control" 
                           value="<?= htmlspecialchars($admin['last_name'] ?? '') ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Rol *</label>
                    <select name="role" class="form-control" required>
                        <option value="editor" <?= $admin['role'] === 'editor' ? 'selected' : '' ?>>✏️ Editör</option>
                        <option value="admin" <?= $admin['role'] === 'admin' ? 'selected' : '' ?>>👑 Admin</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Yeni Şifre (Opsiyonel)</label>
                    <input type="password" name="password" class="form-control" autocomplete="new-password">
                    <small class="form-help">Boş bırakırsanız şifre değişmez</small>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Güncelle</button>
                <a href="<?= url('/admin/users') ?>" class="btn btn-secondary">İptal</a>
            </div>
        </form>
    </div>
</body>
</html>
