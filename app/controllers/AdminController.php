<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/Admin.php';

class AdminController extends Controller {
    private $adminModel;

    public function __construct() {
        session_start();
        $this->adminModel = new Admin();
    }

    /**
     * Admin listesi (sadece admin görebilir)
     */
    public function index() {
        $this->checkAuth();
        $this->requireAdmin();
        
        $admins = $this->adminModel->getAllAdmins();
        
        $this->view('admin/admins/index', ['admins' => $admins]);
    }

    /**
     * Yeni admin ekle (sadece admin)
     */
    public function create() {
        $this->checkAuth();
        $this->requireAdmin();
        
        if ($this->isPost()) {
            $errors = $this->validateRequired([
                'username' => 'Kullanıcı Adı',
                'email' => 'E-posta',
                'password' => 'Şifre',
                'first_name' => 'Ad',
                'last_name' => 'Soyad'
            ]);

            if (empty($errors)) {
                // Kullanıcı adı kontrolü
                $existing = $this->adminModel->findBy('username', $_POST['username']);
                if ($existing) {
                    $errors['username'] = 'Bu kullanıcı adı zaten kullanılıyor';
                }
                
                // Email kontrolü
                $existing = $this->adminModel->findBy('email', $_POST['email']);
                if ($existing) {
                    $errors['email'] = 'Bu e-posta adresi zaten kullanılıyor';
                }
            }

            if (empty($errors)) {
                $this->adminModel->createAdmin([
                    'username' => $_POST['username'],
                    'first_name' => $_POST['first_name'],
                    'last_name' => $_POST['last_name'],
                    'email' => $_POST['email'],
                    'password' => $_POST['password'],
                    'role' => $_POST['role'] ?? 'editor'
                ]);

                $this->redirect('/admin/users');
                return;
            }
        }

        $this->view('admin/admins/create', ['errors' => $errors ?? []]);
    }

    /**
     * Admin düzenle (sadece admin)
     */
    public function edit($id) {
        $this->checkAuth();
        $this->requireAdmin();
        
        $admin = $this->adminModel->find($id);
        
        if (!$admin) {
            $this->redirect('/admin/users');
            return;
        }

        if ($this->isPost()) {
            $data = [
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'email' => $_POST['email'],
                'role' => $_POST['role']
            ];

            // Şifre değiştirilecekse
            if (!empty($_POST['password'])) {
                $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }

            $this->adminModel->update($id, $data);
            $this->redirect('/admin/users');
            return;
        }

        $this->view('admin/admins/edit', ['admin' => $admin]);
    }

    /**
     * Admin'i pasife al / aktif et
     */
    public function toggleStatus($id) {
        $this->checkAuth();
        $this->requireAdmin();
        
        // Kendi kendini pasife alamaz
        if ($id == $_SESSION['admin_id']) {
            die('⛔ Kendi hesabınızı pasife alamazsınız!');
        }

        $admin = $this->adminModel->find($id);
        
        if ($admin['status'] === 'active') {
            $this->adminModel->deactivate($id);
        } else {
            $this->adminModel->activate($id);
        }

        $this->redirect('/admin/users');
    }

    /**
     * Admin'i kalıcı sil (sadece admin)
     */
    public function permanentDelete($id) {
        $this->checkAuth();
        $this->requireAdmin();
        
        // Kendi kendini silemez
        if ($id == $_SESSION['admin_id']) {
            die('⛔ Kendi hesabınızı silemezsiniz!');
        }

        $this->adminModel->delete($id);
        $this->redirect('/admin/users');
    }

    /**
     * KENDİ profilini görüntüle (herkes)
     */
    public function profile() {
        $this->checkAuth();
        
        $admin = $this->adminModel->getAdminInfo($_SESSION['admin_id']);
        
        $this->view('admin/profile/view', ['admin' => $admin]);
    }

    /**
     * KENDİ profilini düzenle (herkes)
     */
    public function editProfile() {
        $this->checkAuth();
        
        $admin = $this->adminModel->getAdminInfo($_SESSION['admin_id']);

        if ($this->isPost()) {
            $result = $this->adminModel->updateProfile($_SESSION['admin_id'], [
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'email' => $_POST['email']
            ]);

            if ($result['success']) {
                $_SESSION['success_message'] = $result['message'];
            } else {
                $_SESSION['error_message'] = $result['message'];
            }

            $this->redirect('/admin/profile');
            return;
        }

        $this->view('admin/profile/edit', ['admin' => $admin]);
    }

    /**
     * Şifre değiştir (herkes kendi şifresini)
     */
    public function changePassword() {
        $this->checkAuth();

        if ($this->isPost()) {
            $errors = $this->validateRequired([
                'old_password' => 'Mevcut Şifre',
                'new_password' => 'Yeni Şifre',
                'confirm_password' => 'Şifre Tekrar'
            ]);

            if (empty($errors)) {
                // Şifre eşleşiyor mu?
                if ($_POST['new_password'] !== $_POST['confirm_password']) {
                    $errors['confirm_password'] = 'Şifreler eşleşmiyor';
                }

                // Şifre en az 6 karakter mi?
                if (strlen($_POST['new_password']) < 6) {
                    $errors['new_password'] = 'Şifre en az 6 karakter olmalıdır';
                }
            }

            if (empty($errors)) {
                $result = $this->adminModel->changePassword(
                    $_SESSION['admin_id'],
                    $_POST['old_password'],
                    $_POST['new_password']
                );

                if ($result['success']) {
                    $_SESSION['success_message'] = $result['message'];
                    $this->redirect('/admin/profile');
                    return;
                } else {
                    $errors['old_password'] = $result['message'];
                }
            }
        }

        $this->view('admin/profile/change-password', ['errors' => $errors ?? []]);
    }

    private function checkAuth() {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect('/admin/login');
            exit;
        }
    }
}
