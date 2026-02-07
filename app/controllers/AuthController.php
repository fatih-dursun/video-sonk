<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/Admin.php';

class AuthController extends Controller {
    private $adminModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->adminModel = new Admin();
    }

    public function login() {
        if (isset($_SESSION['admin_id'])) {
            $this->redirect('/admin/videos');
            return;
        }

        if ($this->isPost()) {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($username) || empty($password)) {
                $error = 'Kullanıcı adı ve şifre gerekli';
            } else {
                $admin = $this->adminModel->authenticate($username, $password);

                if ($admin) {
                    // Pasif kullanıcı kontrolü
                    if (($admin['status'] ?? 'active') === 'passive') {
                        $error = 'Hesabınız pasif durumda. Lütfen yönetici ile iletişime geçin.';
                    } else {
                        $_SESSION['admin_id'] = $admin['id'];
                        $_SESSION['admin_username'] = $admin['username'];
                        $_SESSION['admin_role'] = $admin['role'];
                        
                        $this->redirect('/admin/videos');
                        exit;
                    }
                } else {
                    $error = 'Kullanıcı adı veya şifre hatalı';
                }
            }
        }

        $this->view('admin/login', ['error' => $error ?? null]);
    }

    public function logout() {
        session_destroy();
        $this->redirect('/admin/login');
    }
}
