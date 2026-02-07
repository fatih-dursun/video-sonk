<?php
class Controller {
    protected function view($view, $data = []) {
        extract($data);
        $viewPath = __DIR__ . '/../app/views/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("View bulunamadı: {$view}");
        }
    }

    protected function redirect($url) {
        if (strpos($url, 'http') !== 0) {
            $basePath = '/video-portal/public';
            if (strpos($url, $basePath) !== 0) {
                $url = $basePath . $url;
            }
        }
        header("Location: {$url}");
        exit;
    }

    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function input($key, $default = null) {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function validateRequired($fields) {
        $errors = [];
        foreach ($fields as $field => $label) {
            if (empty($_POST[$field])) {
                $errors[$field] = "{$label} alanı zorunludur";
            }
        }
        return $errors;
    }

    protected function uploadFile($file, $directory, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif']) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedTypes)) {
            return false;
        }

        $filename = uniqid() . '_' . time() . '.' . $ext;
        $uploadPath = __DIR__ . '/../public/uploads/' . $directory . '/' . $filename;
        
        if (!is_dir(dirname($uploadPath))) {
            mkdir(dirname($uploadPath), 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return '/uploads/' . $directory . '/' . $filename;
        }

        return false;
    }

    protected function slugify($text) {
        $text = mb_strtolower($text, 'UTF-8');
        
        $turkish = ['ş', 'Ş', 'ı', 'İ', 'ğ', 'Ğ', 'ü', 'Ü', 'ö', 'Ö', 'ç', 'Ç'];
        $english = ['s', 's', 'i', 'i', 'g', 'g', 'u', 'u', 'o', 'o', 'c', 'c'];
        $text = str_replace($turkish, $english, $text);
        
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        $text = trim($text, '-');
        
        return $text;
    }

    protected function isAdmin() {
        return isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'admin';
    }
    
    protected function isEditor() {
        return isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'editor';
    }
    
    protected function requireAdmin() {
        if (!$this->isAdmin()) {
            die('⛔ Bu işlem için Admin yetkisi gerekli!');
        }
    }
}