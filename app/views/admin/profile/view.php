<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilim - Admin</title>
    <link rel="stylesheet" href="<?= asset('/css/admin.css') ?>">
</head>
<body>
    <?php include __DIR__ . '/../_header.php'; ?>
    
    <div class="admin-container">
        <div class="page-header">
            <h1>👤 Profilim</h1>
            <div>
                <a href="<?= url('/admin/profile/edit') ?>" class="btn btn-primary">✏️ Profili Düzenle</a>
                <a href="<?= url('/admin/profile/change-password') ?>" class="btn btn-warning">🔒 Şifre Değiştir</a>
            </div>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    <?= strtoupper(substr($user['username'], 0, 1)) ?>
                </div>
                <div class="profile-info">
                    <h2><?= htmlspecialchars($user['username']) ?></h2>
                    <span class="profile-role">
                        <?php if ($user['role'] === 'admin'): ?>
                            <span class="status-badge status-active">👑 Admin</span>
                        <?php else: ?>
                            <span class="status-badge status-passive">✏️ Editör</span>
                        <?php endif; ?>
                    </span>
                </div>
            </div>

            <div class="profile-details">
                <div class="detail-row">
                    <div class="detail-label">Kullanıcı Adı:</div>
                    <div class="detail-value"><?= htmlspecialchars($user['username']) ?></div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">E-posta:</div>
                    <div class="detail-value"><?= htmlspecialchars($user['email'] ?? '-') ?></div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Ad:</div>
                    <div class="detail-value"><?= htmlspecialchars($user['first_name'] ?? '-') ?></div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Soyad:</div>
                    <div class="detail-value"><?= htmlspecialchars($user['last_name'] ?? '-') ?></div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Rol:</div>
                    <div class="detail-value">
                        <?php if ($user['role'] === 'admin'): ?>
                            👑 Admin
                        <?php else: ?>
                            ✏️ Editör
                        <?php endif; ?>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Kayıt Tarihi:</div>
                    <div class="detail-value"><?= date('d.m.Y H:i', strtotime($user['created_at'])) ?></div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .profile-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px;
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: bold;
            color: #667eea;
        }

        .profile-info h2 {
            margin: 0;
            color: white;
            font-size: 28px;
        }

        .profile-info .profile-role {
            margin-top: 8px;
            display: inline-block;
        }

        .profile-details {
            padding: 32px;
        }

        .detail-row {
            display: flex;
            padding: 16px 0;
            border-bottom: 1px solid #eee;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            width: 180px;
            font-weight: 600;
            color: #666;
        }

        .detail-value {
            flex: 1;
            color: #333;
        }

        .btn-warning {
            background-color: #ffc107;
            color: #333;
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }
    </style>
</body>
</html>
