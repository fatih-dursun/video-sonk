<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Yönetimi - Admin</title>
    <link rel="stylesheet" href="<?= asset('/css/admin.css') ?>">
</head>
<body>
    <?php include __DIR__ . '/../_header.php'; ?>
    
    <div class="admin-container">
        <div class="page-header">
            <h1>👥 Kullanıcı Yönetimi</h1>
            <a href="<?= url('/admin/users/create') ?>" class="btn btn-primary">+ Yeni Kullanıcı Ekle</a>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Kullanıcı Adı</th>
                        <th>Ad Soyad</th>
                        <th>E-posta</th>
                        <th>Rol</th>
                        <th>Durum</th>
                        <th>Kayıt Tarihi</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td>
                            <?php 
                            $fullName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
                            echo htmlspecialchars($fullName ?: '-');
                            ?>
                        </td>
                        <td><?= htmlspecialchars($user['email'] ?? '-') ?></td>
                        <td>
                            <?php if ($user['role'] === 'admin'): ?>
                                <span class="status-badge status-active">👑 Admin</span>
                            <?php else: ?>
                                <span class="status-badge status-passive">✏️ Editör</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (($user['status'] ?? 'active') === 'active'): ?>
                                <span class="status-badge status-active">✅ Aktif</span>
                            <?php else: ?>
                                <span class="status-badge status-passive">⏸️ Pasif</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d.m.Y H:i', strtotime($user['created_at'])) ?></td>
                        <td class="action-buttons">
                            <?php if ($user['id'] != $_SESSION['admin_id']): ?>
                                <a href="<?= url('/admin/users/edit/' . $user['id']) ?>" class="btn btn-sm btn-edit">Düzenle</a>
                                
                                <?php if (($user['status'] ?? 'active') === 'active'): ?>
                                    <a href="<?= url('/admin/users/toggle/' . $user['id']) ?>" class="btn btn-sm btn-warning" 
                                       onclick="return confirm('Bu kullanıcıyı pasife almak istediğinize emin misiniz?')">Pasife Al</a>
                                <?php else: ?>
                                    <a href="<?= url('/admin/users/toggle/' . $user['id']) ?>" class="btn btn-sm btn-success" 
                                       onclick="return confirm('Bu kullanıcıyı aktif yapmak istediğinize emin misiniz?')">Aktif Yap</a>
                                <?php endif; ?>
                                
                                <a href="<?= url('/admin/users/delete/' . $user['id']) ?>" class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?')">Sil</a>
                            <?php else: ?>
                                <span style="color: #28a745; font-weight: 500;">👤 Siz</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <style>
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #218838;
        }
    </style>
</body>
</html>
