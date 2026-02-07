<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/Video.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../helpers/NetworkHelper.php';

class CategoryController extends Controller {
    private $videoModel;
    private $categoryModel;

    public function __construct() {
        session_start();
        $this->videoModel = new Video();
        $this->categoryModel = new Category();
    }

    public function show($slug) {
        $category = $this->categoryModel->getBySlug($slug);
        
        if (!$category) {
            $this->redirect('/');
            return;
        }

        $videos = $this->videoModel->getByCategory($category['id']);
        $categories = $this->categoryModel->getAllWithVideoCount();

        $this->view('public/category', [
            'category' => $category,
            'videos' => $videos,
            'categories' => $categories,
            'pageTitle' => $category['name']
        ]);
    }

    public function adminIndex() {
        $this->checkAuth();
        
        $success = $_SESSION['success_message'] ?? null;
        $error = $_SESSION['error_message'] ?? null;
        unset($_SESSION['success_message'], $_SESSION['error_message']);
        
        $categories = $this->categoryModel->getAllWithVideoCount();
        
        $this->view('admin/categories/index', [
            'categories' => $categories,
            'success' => $success,
            'error' => $error
        ]);
    }

    public function create() {
        $this->checkAuth();
        $this->requireAdmin();
        
        if ($this->isPost()) {
            $errors = $this->validateRequired([
                'name' => 'Kategori Adı',
                'background_color' => 'Arka Plan Rengi',
                'text_color' => 'Yazı Rengi'
            ]);

            if (empty($errors)) {
                $networkInfo = NetworkHelper::getClientInfo();
                
                $data = [
                    'name' => $_POST['name'],
                    'slug' => $this->slugify($_POST['name']),
                    'background_color' => $_POST['background_color'],
                    'text_color' => $_POST['text_color'],
                    'ekul' => $_SESSION['admin_id'],
                    'ekul_ip' => $networkInfo['ip'],
                    'ekul_mac' => $networkInfo['mac'],
                    'status' => 'active'
                ];

                if (!empty($_FILES['logo']['name'])) {
                    $logoPath = $this->uploadFile($_FILES['logo'], 'category-logos', ['png', 'jpg', 'jpeg']);
                    if ($logoPath) {
                        $basePath = defined('BASE_PATH') ? BASE_PATH : '/video-portal/public';
                        $data['logo_path'] = $basePath . $logoPath;
                    }
                }

                $this->categoryModel->create($data);
                
                $_SESSION['success_message'] = '✅ Kategori başarıyla eklendi!';
                $this->redirect('/admin/categories');
                return;
            }
        }

        $this->view('admin/categories/create', [
            'errors' => $errors ?? [],
            'colorPresets' => Category::$colorPresets
        ]);
    }

    public function edit($id) {
        $this->checkAuth();
        $this->requireAdmin();
        
        $category = $this->categoryModel->find($id);
        
        if (!$category) {
            $_SESSION['error_message'] = '❌ Kategori bulunamadı!';
            $this->redirect('/admin/categories');
            return;
        }

        if ($this->isPost()) {
            $networkInfo = NetworkHelper::getClientInfo();
            
            $data = [
                'name' => $_POST['name'],
                'slug' => $this->slugify($_POST['name']),
                'background_color' => $_POST['background_color'],
                'text_color' => $_POST['text_color'],
                'dkul' => $_SESSION['admin_id'],
                'dkul_ip' => $networkInfo['ip'],
                'dkul_mac' => $networkInfo['mac']
            ];

            if (!empty($_FILES['logo']['name'])) {
                $logoPath = $this->uploadFile($_FILES['logo'], 'category-logos', ['png', 'jpg', 'jpeg']);
                if ($logoPath) {
                    $basePath = defined('BASE_PATH') ? BASE_PATH : '/video-portal/public';
                    $data['logo_path'] = $basePath . $logoPath;
                }
            }

            if (isset($_POST['remove_logo']) && $_POST['remove_logo'] == '1') {
                $data['logo_path'] = null;
            }

            $this->categoryModel->update($id, $data);
            
            $_SESSION['success_message'] = '✅ Kategori başarıyla güncellendi!';
            $this->redirect('/admin/categories');
            return;
        }

        $this->view('admin/categories/edit', [
            'category' => $category,
            'colorPresets' => Category::$colorPresets
        ]);
    }

    public function delete($id) {
        $this->checkAuth();
        $this->requireAdmin();
        
        $category = $this->categoryModel->find($id);
        
        if (!$category) {
            $_SESSION['error_message'] = '❌ Kategori bulunamadı!';
            $this->redirect('/admin/categories');
            return;
        }
        
        $videos = $this->videoModel->getByCategory($id);
        if (!empty($videos)) {
            $_SESSION['error_message'] = '❌ Bu kategoride ' . count($videos) . ' video var! Önce videoları silin veya taşıyın.';
            $this->redirect('/admin/categories');
            return;
        }
        
        $this->categoryModel->delete($id);
        
        $_SESSION['success_message'] = '✅ Kategori başarıyla silindi!';
        $this->redirect('/admin/categories');
    }

    public function toggleStatus($id) {
        $this->checkAuth();
        $this->requireAdmin();
        
        $category = $this->categoryModel->find($id);
        
        if (!$category) {
            $_SESSION['error_message'] = '❌ Kategori bulunamadı!';
            $this->redirect('/admin/categories');
            return;
        }
        
        $networkInfo = NetworkHelper::getClientInfo();
        
        $newStatus = ($category['status'] ?? 'active') === 'active' ? 'passive' : 'active';
        
        $this->categoryModel->update($id, [
            'status' => $newStatus,
            'dkul' => $_SESSION['admin_id'],
            'dkul_ip' => $networkInfo['ip'],
            'dkul_mac' => $networkInfo['mac']
        ]);
        
        $statusText = $newStatus === 'active' ? 'aktif yapıldı' : 'pasife alındı';
        $_SESSION['success_message'] = "✅ Kategori {$statusText}!";
        
        $this->redirect('/admin/categories');
    }

    private function checkAuth() {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect('/admin/login');
            exit;
        }
    }
}
