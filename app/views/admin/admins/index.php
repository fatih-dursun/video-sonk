<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Yönetimi - Admin</title>
    <link rel="stylesheet" href="<?= defined('BASE_PATH') ? BASE_PATH : '/video-portal/public' ?>/css/admin.css">
</head>
<body>
    <?php include __DIR__ . '/../_header.php'; ?>
    
    <div class="admin-container">
        <div class="page-header">
            <h1>👥 Kullanıcı Yönetimi</h1>
            <a href="<?= url('/admin/users/create') ?>" class="btn btn-primary">+ Yeni Kullanıcı Ekle</a>
        </div>

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
                    <?php foreach ($admins as $admin): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($admin['username']) ?></strong>
                            <?php if ($admin['id'] == $_SESSION['admin_id']): ?>
                                <span class="badge badge-info">Siz</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                            $fullName = trim(($admin['first_name'] ?? '') . ' ' . ($admin['last_name'] ?? ''));
                            echo htmlspecialchars($fullName ?: '-');
                            ?>
                        </td>
                        <td><?= htmlspecialchars($admin['email']) ?></td>
                        <td>
                            <span class="role-badge role-<?= $admin['role'] ?>">
                                <?= $admin['role'] === 'admin' ? '👑 Admin' : '✏️ Editör' ?>
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-<?= $admin['status'] ?>">
                                <?= $admin['status'] === 'active' ? 'Aktif' : 'Pasif' ?>
                            </span>
                        </td>
                        <td><?= date('d.m.Y H:i', strtotime($admin['created_at'])) ?></td>
                        <td class="action-buttons">
                            <?php if ($admin['id'] != $_SESSION['admin_id']): ?>
                                <a href="<?= url('/admin/users/edit/' . $admin['id']) ?>" 
                                   class="btn btn-sm btn-edit">Düzenle</a>
                                
                                <a href="<?= url('/admin/users/toggle/' . $admin['id']) ?>" 
                                   class="btn btn-sm btn-warning"
                                   onclick="return confirm('Bu kullanıcının durumunu değiştirmek istediğinize emin misiniz?')">
                                    <?= $admin['status'] === 'active' ? 'Pasif Yap' : 'Aktif Yap' ?>
                                </a>
                                
                                <a href="<?= url('/admin/users/permanent-delete/' . $admin['id']) ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Bu kullanıcıyı kalıcı olarak silmek istediğinize emin misiniz? Bu işlem geri alınamaz!')">
                                    Kalıcı Sil
                                </a>
                            <?php else: ?>
                                <span style="color: #888;">Kendi hesabınız</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <style>
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 8px;
        }
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 500;
        }
        .role-admin {
            background-color: #ffd700;
            color: #000;
        }
        .role-editor {
            background-color: #e3f2fd;
            color: #1976d2;
        }
    </style>
</body>
</html>
