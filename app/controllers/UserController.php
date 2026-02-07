<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../helpers/NetworkHelper.php';

class UserController extends Controller {
    private $adminModel;

    public function __construct() {
        session_start();
        $this->adminModel = new Admin();
    }

    public function index() {
        $this->checkAuth();
        $this->requireAdmin();
        
        $users = $this->adminModel->all();
        
        $success = $_SESSION['success_message'] ?? null;
        $error = $_SESSION['error_message'] ?? null;
        unset($_SESSION['success_message'], $_SESSION['error_message']);
        
        $this->view('admin/users/index', [
            'users' => $users,
            'success' => $success,
            'error' => $error
        ]);
    }

    public function create() {
        $this->checkAuth();
        $this->requireAdmin();
        
        if ($this->isPost()) {
            $errors = $this->validateRequired([
                'username' => 'Kullanıcı Adı',
                'email' => 'E-posta',
                'password' => 'Şifre',
                'role' => 'Rol'
            ]);

            if (empty($errors)) {
                $existing = $this->adminModel->findBy('username', $_POST['username']);
                if ($existing) {
                    $errors['username'] = 'Bu kullanıcı adı zaten kullanılıyor';
                }
                
                $existingEmail = $this->adminModel->findBy('email', $_POST['email']);
                if ($existingEmail) {
                    $errors['email'] = 'Bu e-posta adresi zaten kullanılıyor';
                }
            }

            if (empty($errors)) {
                $networkInfo = NetworkHelper::getClientInfo();
                
                $this->adminModel->create([
                    'username' => $_POST['username'],
                    'email' => $_POST['email'],
                    'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                    'first_name' => $_POST['first_name'] ?? null,
                    'last_name' => $_POST['last_name'] ?? null,
                    'role' => $_POST['role'],
                    'status' => 'active',
                    'ekul' => $_SESSION['admin_id'],
                    'ekul_ip' => $networkInfo['ip'],
                    'ekul_mac' => $networkInfo['mac']
                ]);

                $_SESSION['success_message'] = '✅ Kullanıcı başarıyla eklendi!';
                $this->redirect('/admin/users');
                return;
            }
        }

        $this->view('admin/users/create', ['errors' => $errors ?? []]);
    }

    public function edit($id) {
        $this->checkAuth();
        $this->requireAdmin();
        
        $user = $this->adminModel->find($id);
        
        if (!$user) {
            $_SESSION['error_message'] = '❌ Kullanıcı bulunamadı!';
            $this->redirect('/admin/users');
            return;
        }

        if ($this->isPost()) {
            $networkInfo = NetworkHelper::getClientInfo();
            
            $data = [
                'username' => $_POST['username'],
                'email' => $_POST['email'],
                'first_name' => $_POST['first_name'] ?? null,
                'last_name' => $_POST['last_name'] ?? null,
                'role' => $_POST['role'],
                'dkul' => $_SESSION['admin_id'],
                'dkul_ip' => $networkInfo['ip'],
                'dkul_mac' => $networkInfo['mac']
            ];

            if (!empty($_POST['password'])) {
                $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }

            $this->adminModel->update($id, $data);

            $_SESSION['success_message'] = '✅ Kullanıcı başarıyla güncellendi!';
            $this->redirect('/admin/users');
            return;
        }

        $this->view('admin/users/edit', ['user' => $user]);
    }

    public function delete($id) {
        $this->checkAuth();
        $this->requireAdmin();
        
        if ($id == $_SESSION['admin_id']) {
            $_SESSION['error_message'] = '❌ Kendi hesabınızı silemezsiniz!';
            $this->redirect('/admin/users');
            return;
        }
        
        $this->adminModel->delete($id);
        $_SESSION['success_message'] = '✅ Kullanıcı silindi!';
        $this->redirect('/admin/users');
    }

    public function toggleStatus($id) {
        $this->checkAuth();
        $this->requireAdmin();
        
        if ($id == $_SESSION['admin_id']) {
            $_SESSION['error_message'] = '❌ Kendi hesabınızın durumunu değiştiremezsiniz!';
            $this->redirect('/admin/users');
            return;
        }
        
        $user = $this->adminModel->find($id);
        
        if (!$user) {
            $_SESSION['error_message'] = '❌ Kullanıcı bulunamadı!';
            $this->redirect('/admin/users');
            return;
        }
        
        $networkInfo = NetworkHelper::getClientInfo();
        
        $newStatus = ($user['status'] ?? 'active') === 'active' ? 'passive' : 'active';
        
        $this->adminModel->update($id, [
            'status' => $newStatus,
            'dkul' => $_SESSION['admin_id'],
            'dkul_ip' => $networkInfo['ip'],
            'dkul_mac' => $networkInfo['mac']
        ]);
        
        $statusText = $newStatus === 'active' ? 'aktif yapıldı' : 'pasife alındı';
        $_SESSION['success_message'] = "✅ Kullanıcı {$statusText}!";
        $this->redirect('/admin/users');
    }

    private function checkAuth() {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect('/admin/login');
            exit;
        }
    }
}
