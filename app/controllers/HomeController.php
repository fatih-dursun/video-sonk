<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/Video.php';
require_once __DIR__ . '/../models/Category.php';

class HomeController extends Controller {
    private $videoModel;
    private $categoryModel;

public function __construct() {
    $this->videoModel = new Video();
    $this->categoryModel = new Category();
}

public function index() {
    $featuredVideos = $this->videoModel->getFeatured(3);
    $latestVideos = $this->videoModel->getAllActive(6);
    $categories = $this->categoryModel->getAllWithVideoCount();

    $this->view('public/home', [
        'featuredVideos' => $featuredVideos,
        'latestVideos' => $latestVideos,
        'categories' => $categories,
        'pageTitle' => 'Ana Sayfa'
    ]);
}
	

}