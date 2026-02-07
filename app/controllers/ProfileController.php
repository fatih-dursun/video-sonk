<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/Admin.php';

class ProfileController extends Controller {
    private $adminModel;

    public function __construct() {
        session_start();
        $this->adminModel = new Admin();
    }

    public function index() {
        $this->checkAuth();
        
        $user = $this->adminModel->find($_SESSION['admin_id']);
        
        if (!$user) {
            $this->redirect('/admin/login');
            return;
        }
        
        $success = $_SESSION['success_message'] ?? null;
        $error = $_SESSION['error_message'] ?? null;
        unset($_SESSION['success_message'], $_SESSION['error_message']);
        
        $this->view('admin/profile/view', [
            'user' => $user,
            'success' => $success,
            'error' => $error
        ]);
    }

    public function edit() {
        $this->checkAuth();
        
        $user = $this->adminModel->find($_SESSION['admin_id']);
        
        if (!$user) {
            $this->redirect('/admin/login');
            return;
        }

        if ($this->isPost()) {
            $data = [
                'email' => $_POST['email'],
                'first_name' => $_POST['first_name'] ?? null,
                'last_name' => $_POST['last_name'] ?? null
            ];

            // Email başkası tarafından kullanılıyor mu?
            $existingEmail = $this->adminModel->findBy('email', $_POST['email']);
            if ($existingEmail && $existingEmail['id'] != $_SESSION['admin_id']) {
                $_SESSION['error_message'] = '❌ Bu e-posta adresi zaten kullanılıyor!';
                $this->redirect('/admin/profile/edit');
                return;
            }

            $this->adminModel->update($_SESSION['admin_id'], $data);

            $_SESSION['success_message'] = '✅ Profiliniz başarıyla güncellendi!';
            $this->redirect('/admin/profile');
            return;
        }

        $this->view('admin/profile/edit', ['user' => $user]);
    }

    public function changePassword() {
        $this->checkAuth();
        
        $user = $this->adminModel->find($_SESSION['admin_id']);
        
        if (!$user) {
            $this->redirect('/admin/login');
            return;
        }

        if ($this->isPost()) {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Mevcut şifre kontrolü
            if (!password_verify($currentPassword, $user['password'])) {
                $_SESSION['error_message'] = '❌ Mevcut şifreniz yanlış!';
                $this->redirect('/admin/profile/change-password');
                return;
            }

            // Yeni şifre boş mu?
            if (empty($newPassword)) {
                $_SESSION['error_message'] = '❌ Yeni şifre boş olamaz!';
                $this->redirect('/admin/profile/change-password');
                return;
            }

            // Şifre uzunluğu
            if (strlen($newPassword) < 6) {
                $_SESSION['error_message'] = '❌ Yeni şifre en az 6 karakter olmalıdır!';
                $this->redirect('/admin/profile/change-password');
                return;
            }

            // Şifreler eşleşiyor mu?
            if ($newPassword !== $confirmPassword) {
                $_SESSION['error_message'] = '❌ Yeni şifreler eşleşmiyor!';
                $this->redirect('/admin/profile/change-password');
                return;
            }

            // Şifreyi güncelle
            $this->adminModel->update($_SESSION['admin_id'], [
                'password' => password_hash($newPassword, PASSWORD_DEFAULT)
            ]);

            $_SESSION['success_message'] = '✅ Şifreniz başarıyla değiştirildi!';
            $this->redirect('/admin/profile');
            return;
        }

        $this->view('admin/profile/change-password', ['user' => $user]);
    }

    private function checkAuth() {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect('/admin/login');
            exit;
        }
    }
}
