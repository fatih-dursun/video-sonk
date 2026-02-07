<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/base_path.php';

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Router.php';

$router = new Router();

// Public Routes
$router->get('/', 'HomeController', 'index');
$router->get('/video/{slug}', 'VideoController', 'show');
$router->get('/kategori/{slug}', 'CategoryController', 'show');
$router->get('/arama', 'SearchController', 'fullSearch');

// Ajax API Routes
$router->get('/api/search', 'SearchController', 'liveSearch');
$router->get('/api/available-orders', 'SearchController', 'getAvailableOrders');

// Admin Auth Routes
$router->get('/admin/login', 'AuthController', 'login');
$router->post('/admin/login', 'AuthController', 'login');
$router->get('/admin/logout', 'AuthController', 'logout');

// Admin Profile Routes
$router->get('/admin/profile', 'ProfileController', 'index');
$router->get('/admin/profile/edit', 'ProfileController', 'edit');
$router->post('/admin/profile/edit', 'ProfileController', 'edit');
$router->get('/admin/profile/change-password', 'ProfileController', 'changePassword');
$router->post('/admin/profile/change-password', 'ProfileController', 'changePassword');

// Admin User Routes
$router->get('/admin/users', 'UserController', 'index');
$router->get('/admin/users/create', 'UserController', 'create');
$router->post('/admin/users/create', 'UserController', 'create');
$router->get('/admin/users/edit/{id}', 'UserController', 'edit');
$router->post('/admin/users/edit/{id}', 'UserController', 'edit');
$router->get('/admin/users/toggle/{id}', 'UserController', 'toggleStatus');
$router->get('/admin/users/delete/{id}', 'UserController', 'delete');

// Admin Video Routes
$router->get('/admin/videos', 'VideoController', 'adminIndex');
$router->get('/admin/videos/create', 'VideoController', 'create');
$router->post('/admin/videos/create', 'VideoController', 'create');
$router->get('/admin/videos/edit/{id}', 'VideoController', 'edit');
$router->post('/admin/videos/edit/{id}', 'VideoController', 'edit');
$router->get('/admin/videos/delete/{id}', 'VideoController', 'delete');
$router->get('/admin/videos/toggle/{id}', 'VideoController', 'toggleStatus');

// Admin Video - Onay Sistemi
$router->get('/admin/videos/approve/{id}', 'VideoController', 'approve');
$router->get('/admin/videos/reject/{id}', 'VideoController', 'reject');
$router->get('/admin/videos/preview/{id}', 'VideoController', 'preview');

// Admin Video - Silme Onayı
$router->get('/admin/videos/approve-delete/{id}', 'VideoController', 'approveDelete');
$router->get('/admin/videos/reject-delete/{id}', 'VideoController', 'rejectDelete');

// Admin Video - Geri Dönüşüm Kutusu
$router->get('/admin/videos/trash', 'VideoController', 'trash');
$router->get('/admin/videos/restore/{id}', 'VideoController', 'restore');
$router->get('/admin/videos/permanent-delete/{id}', 'VideoController', 'permanentDelete');

// Admin Video - İstatistikler
$router->get('/admin/videos/statistics', 'VideoController', 'statistics');

// Admin Category Routes
$router->get('/admin/categories', 'CategoryController', 'adminIndex');
$router->get('/admin/categories/create', 'CategoryController', 'create');
$router->post('/admin/categories/create', 'CategoryController', 'create');
$router->get('/admin/categories/edit/{id}', 'CategoryController', 'edit');
$router->post('/admin/categories/edit/{id}', 'CategoryController', 'edit');
$router->get('/admin/categories/toggle/{id}', 'CategoryController', 'toggleStatus');
$router->get('/admin/categories/delete/{id}', 'CategoryController', 'delete');

// Dispatch
$router->dispatch();
