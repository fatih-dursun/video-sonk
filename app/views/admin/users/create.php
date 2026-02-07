<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Kullanıcı Ekle - Admin</title>
    <link rel="stylesheet" href="<?= asset('/css/admin.css') ?>">
</head>
<body>
    <?php include __DIR__ . '/../_header.php'; ?>
    
    <div class="admin-container">
        <div class="page-header">
            <h1>➕ Yeni Kullanıcı Ekle</h1>
            <a href="<?= url('/admin/users') ?>" class="btn btn-secondary">← Geri Dön</a>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= url('/admin/users/create') ?>" class="admin-form">
            <div class="form-row">
                <div class="form-group">
                    <label>Kullanıcı Adı *</label>
                    <input type="text" name="username" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>E-posta *</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Ad</label>
                    <input type="text" name="first_name" class="form-control">
                </div>

                <div class="form-group">
                    <label>Soyad</label>
                    <input type="text" name="last_name" class="form-control">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Şifre *</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Rol *</label>
                    <select name="role" class="form-control" required>
                        <option value="editor">Editör</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Kullanıcı Ekle</button>
                <a href="<?= url('/admin/users') ?>" class="btn btn-secondary">İptal</a>
            </div>
        </form>
    </div>
</body>
</html>
