<?php
require_once __DIR__ . '/../../core/Model.php';

class Admin extends Model {
    protected $table = 'admins';

    /**
     * Kullanıcı adı ve şifre ile giriş doğrulama
     * SADECE AKTİF KULLANICILAR GİRİŞ YAPABİLİR
     */
    public function authenticate($username, $password) {
        $sql = "SELECT * FROM {$this->table} WHERE username = ? AND status = 'active' LIMIT 1";
        $admin = $this->db->fetch($sql, [$username]);
        
        if ($admin && password_verify($password, $admin['password'])) {
            return $admin;
        }
        
        return false;
    }

    /**
     * Yeni admin oluştur
     */
    public function createAdmin($data) {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        return $this->create([
            'username' => $data['username'],
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'password' => $hashedPassword,
            'email' => $data['email'],
            'role' => $data['role'] ?? 'editor',
            'status' => 'active'
        ]);
    }

    /**
     * Şifre değiştir
     */
    public function changePassword($adminId, $oldPassword, $newPassword) {
        // Mevcut şifreyi kontrol et
        $admin = $this->find($adminId);
        
        if (!$admin) {
            return ['success' => false, 'message' => 'Kullanıcı bulunamadı'];
        }

        if (!password_verify($oldPassword, $admin['password'])) {
            return ['success' => false, 'message' => 'Mevcut şifreniz hatalı'];
        }

        // Yeni şifreyi hashle ve güncelle
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->update($adminId, ['password' => $hashedPassword]);

        return ['success' => true, 'message' => 'Şifreniz başarıyla güncellendi'];
    }

    /**
     * Admin profilini güncelle (ad, soyad, email)
     */
    public function updateProfile($adminId, $data) {
        $updateData = [];
        
        if (isset($data['first_name'])) {
            $updateData['first_name'] = $data['first_name'];
        }
        
        if (isset($data['last_name'])) {
            $updateData['last_name'] = $data['last_name'];
        }
        
        if (isset($data['email'])) {
            // Email benzersiz mi kontrol et
            $existing = $this->db->fetch(
                "SELECT id FROM {$this->table} WHERE email = ? AND id != ?", 
                [$data['email'], $adminId]
            );
            
            if ($existing) {
                return ['success' => false, 'message' => 'Bu e-posta adresi zaten kullanılıyor'];
            }
            
            $updateData['email'] = $data['email'];
        }

        if (empty($updateData)) {
            return ['success' => false, 'message' => 'Güncellenecek bilgi yok'];
        }

        $this->update($adminId, $updateData);
        return ['success' => true, 'message' => 'Profil bilgileriniz güncellendi'];
    }

    /**
     * Admin'i pasife al (soft delete)
     */
    public function deactivate($adminId) {
        return $this->update($adminId, ['status' => 'passive']);
    }

    /**
     * Admin'i aktif et
     */
    public function activate($adminId) {
        return $this->update($adminId, ['status' => 'active']);
    }

    /**
     * Tüm adminleri getir (status dahil)
     */
    public function getAllAdmins() {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Sadece aktif adminleri getir
     */
    public function getActiveAdmins() {
        $sql = "SELECT * FROM {$this->table} WHERE status = 'active' ORDER BY created_at DESC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Admin bilgilerini getir (şifre hariç)
     */
    public function getAdminInfo($adminId) {
        $sql = "SELECT id, username, first_name, last_name, email, role, status, created_at 
                FROM {$this->table} WHERE id = ? LIMIT 1";
        return $this->db->fetch($sql, [$adminId]);
    }

    /**
     * Admin tam adını getir
     */
    public function getFullName($admin) {
        if (!empty($admin['first_name']) || !empty($admin['last_name'])) {
            return trim(($admin['first_name'] ?? '') . ' ' . ($admin['last_name'] ?? ''));
        }
        return $admin['username'];
    }
}
