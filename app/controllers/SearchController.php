<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/Video.php';
require_once __DIR__ . '/../models/Category.php';

class SearchController extends Controller {
    private $videoModel;
    private $categoryModel;

    public function __construct() {
        $this->videoModel = new Video();
        $this->categoryModel = new Category();
    }

    public function liveSearch() {
        $query = $this->input('q', '');
        
        if (strlen($query) < 2) {
            $this->json(['results' => []]);
            return;
        }

        $results = $this->videoModel->search($query, 5);
        $this->json(['results' => $results]);
    }

    public function fullSearch() {
        $query = $this->input('q', '');
        $videos = [];
        
        if (strlen($query) >= 2) {
            $videos = $this->videoModel->search($query, 50);
        }

        $categories = $this->categoryModel->getAllWithVideoCount();

        $this->view('public/search', [
            'query' => $query,
            'videos' => $videos,
            'categories' => $categories,
            'pageTitle' => 'Arama: ' . $query
        ]);
    }

    // YENİ: Boş sıraları getir (AJAX için)
    public function getAvailableOrders() {
        session_start();
        
        // Auth kontrolü
        if (!isset($_SESSION['admin_id'])) {
            $this->json(['orders' => [], 'error' => 'Unauthorized']);
            return;
        }
        
        $categoryId = $_GET['category_id'] ?? null;
        $videoId = $_GET['video_id'] ?? null;
        
        if (!$categoryId) {
            $this->json(['orders' => []]);
            return;
        }
        
        $orders = $this->videoModel->getAvailableSortOrders($categoryId, $videoId);
        $this->json(['orders' => $orders]);
    }
}
