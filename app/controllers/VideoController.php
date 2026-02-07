<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/Video.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../helpers/ImageGenerator.php';
require_once __DIR__ . '/../helpers/NetworkHelper.php';

class VideoController extends Controller {
    private $videoModel;
    private $categoryModel;

    public function __construct() {
        session_start();
        $this->videoModel = new Video();
        $this->categoryModel = new Category();
    }

    public function show($slug) {
        $video = $this->videoModel->getBySlug($slug);
        
        if (!$video) {
            $this->redirect('/');
            return;
        }

        $this->videoModel->incrementView($video['id']);

        $relatedVideos = $this->videoModel->getByCategory($video['category_id'], 4);
        $categories = $this->categoryModel->getAllWithVideoCount();

        $this->view('public/video', [
            'video' => $video,
            'relatedVideos' => $relatedVideos,
            'categories' => $categories,
            'pageTitle' => $video['title']
        ]);
    }

    /**
     * Admin Video Listesi (Filtreli)
     */
    public function adminIndex() {
        $this->checkAuth();
        
        // Filtre parametreleri
        $filters = [
            'category_id' => $_GET['category'] ?? null,
            'ekul' => $_GET['ekul'] ?? null,
            'status' => $_GET['status'] ?? null
        ];
        
        // Boş filtreleri temizle
        $filters = array_filter($filters);
        
        if ($this->isEditor()) {
            $videos = $this->videoModel->getByCreatorForAdmin($_SESSION['admin_id'], $filters);
            $statusCounts = $this->videoModel->getStatusCounts($_SESSION['admin_id']);
        } else {
            $videos = $this->videoModel->getAllForAdmin($filters);
            $statusCounts = $this->videoModel->getStatusCounts();
        }
        
        // Filtre seçenekleri için
        $categories = $this->categoryModel->all();
        $adminModel = new Admin();
        $admins = $adminModel->getAllAdmins();
        
        // Mesajlar
        $success = $_SESSION['success_message'] ?? null;
        $error = $_SESSION['error_message'] ?? null;
        unset($_SESSION['success_message'], $_SESSION['error_message']);
        
        $this->view('admin/videos/index', [
            'videos' => $videos,
            'categories' => $categories,
            'admins' => $admins,
            'filters' => $filters,
            'statusCounts' => $statusCounts,
            'success' => $success,
            'error' => $error
        ]);
    }

    /**
     * GERİ DÖNÜŞÜM KUTUSU - Sadece Admin
     */
    public function trash() {
        $this->checkAuth();
        $this->requireAdmin();
        
        $deletedVideos = $this->videoModel->getDeletedVideos();
        
        $success = $_SESSION['success_message'] ?? null;
        $error = $_SESSION['error_message'] ?? null;
        unset($_SESSION['success_message'], $_SESSION['error_message']);
        
        $this->view('admin/videos/trash', [
            'videos' => $deletedVideos,
            'success' => $success,
            'error' => $error
        ]);
    }

    /**
     * Silinen videoyu geri yükle - Sadece Admin
     */
    public function restore($id) {
        $this->checkAuth();
        $this->requireAdmin();
        
        $video = $this->videoModel->find($id);
        
        if (!$video || $video['status'] !== 'deleted') {
            $_SESSION['error_message'] = '❌ Video bulunamadı!';
            $this->redirect('/admin/videos/trash');
            return;
        }
        
        // IP/MAC bilgisi
        $networkInfo = NetworkHelper::getClientInfo();
        
        $this->videoModel->update($id, [
            'status' => 'active',
            'delete_requested_by' => null,
            'delete_requested_at' => null,
            'dkul' => $_SESSION['admin_id'],
            'dkul_ip' => $networkInfo['ip'],
            'dkul_mac' => $networkInfo['mac']
        ]);
        
        $_SESSION['success_message'] = '✅ Video geri yüklendi!';
        $this->redirect('/admin/videos/trash');
    }

    /**
     * İZLEME İSTATİSTİKLERİ - Sadece Admin
     */
    public function statistics() {
        $this->checkAuth();
        $this->requireAdmin();
        
        // Filtre parametreleri
        $filters = [
            'category_id' => $_GET['category'] ?? null,
            'ekul' => $_GET['ekul'] ?? null
        ];
        
        // Boş filtreleri temizle
        $filters = array_filter($filters);
        
        $viewStats = $this->videoModel->getViewStatistics($filters);
        $generalStats = $this->videoModel->getStatistics();
        $categoryStats = $this->videoModel->getCategoryStatistics();
        
        // Filtre seçenekleri için
        $categories = $this->categoryModel->all();
        $adminModel = new Admin();
        $admins = $adminModel->getAllAdmins();
        
        $this->view('admin/videos/statistics', [
            'viewStats' => $viewStats,
            'generalStats' => $generalStats,
            'categoryStats' => $categoryStats,
            'categories' => $categories,
            'admins' => $admins,
            'filters' => $filters
        ]);
    }

    /**
     * Yeni Video Ekle
     */
    public function create() {
        $this->checkAuth();
        
        if ($this->isPost()) {
            $errors = $this->validateRequired([
                'title' => 'Video Başlığı',
                'category_id' => 'Kategori'
            ]);

            if (empty($errors)) {
                $videoPath = $this->uploadFile($_FILES['video'], 'videos', ['mp4', 'webm', 'ogg']);
                
                if (!$videoPath) {
                    $errors['file'] = 'Video yüklenemedi';
                } else {
                    $title = $_POST['title'];
                    $slug = $this->slugify($title);
                    $featuredText = !empty($_POST['featured_text']) ? $_POST['featured_text'] : $title;
                    $featuredSource = $_POST['featured_source'] ?? 'thumbnail';
                    
                    $category = $this->categoryModel->find($_POST['category_id']);
                    
                    $thumbnailPath = null;
                    if (!empty($_FILES['thumbnail']['name'])) {
                        $thumbnailPath = $this->uploadFile($_FILES['thumbnail'], 'thumbnails', ['jpg', 'jpeg', 'png']);
                    }
                    
                    if ($featuredSource === 'text' || !$thumbnailPath) {
                        $generator = new ImageGenerator();
                        $featuredImageName = uniqid() . '_' . time() . '.jpg';
                        $featuredImagePath = __DIR__ . '/../../public/uploads/featured/' . $featuredImageName;
                        
                        if (!is_dir(dirname($featuredImagePath))) {
                            mkdir(dirname($featuredImagePath), 0755, true);
                        }
                        
                        $generatedPath = $generator->generateFeaturedImage(
                            $featuredText,
                            $category['background_color'],
                            $category['text_color'],
                            $featuredImagePath
                        );
                        
                        if (!$thumbnailPath) {
                            $thumbnailPath = $generatedPath;
                        }
                        
                        $featuredImagePathFinal = $generatedPath;
                        $actualFeaturedSource = 'text';
                    } else {
                        $featuredImagePathFinal = $thumbnailPath;
                        $actualFeaturedSource = 'thumbnail';
                    }
                    
                    $sortOrder = null;
                    if (isset($_POST['sort_order']) && $_POST['sort_order'] !== 'auto') {
                        $sortOrder = (int)$_POST['sort_order'];
                    } else {
                        $sortOrder = $this->videoModel->getNextAvailableSortOrder($_POST['category_id']);
                    }

                    // IP/MAC bilgisi al
                    $networkInfo = NetworkHelper::getClientInfo();

                    // Status: Editor ise pending, Admin ise active
                    $status = $this->isAdmin() ? ($_POST['status'] ?? 'active') : 'pending';

                    $videoId = $this->videoModel->create([
                        'title' => $title,
                        'slug' => $slug,
                        'description' => $_POST['description'],
                        'featured_text' => $featuredText,
                        'video_path' => $videoPath,
                        'thumbnail_path' => $thumbnailPath,
                        'featured_image_path' => $featuredImagePathFinal,
                        'featured_source' => $actualFeaturedSource,
                        'category_id' => $_POST['category_id'],
                        'ekul' => $_SESSION['admin_id'],
                        'ekul_ip' => $networkInfo['ip'],
                        'ekul_mac' => $networkInfo['mac'],
                        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
                        'status' => $status,
                        'sort_order' => $sortOrder
                    ]);

                    if ($this->isEditor()) {
                        $_SESSION['success_message'] = '✅ Video eklendi ve admin onayına gönderildi!';
                    } else {
                        $_SESSION['success_message'] = '✅ Video başarıyla eklendi!';
                    }

                    $this->redirect('/admin/videos');
                    return;
                }
            }
        }

        $categories = $this->categoryModel->all();
        $this->view('admin/videos/create', ['categories' => $categories, 'errors' => $errors ?? []]);
    }

    /**
     * Video Düzenle
     */
    public function edit($id) {
        $this->checkAuth();
        $video = $this->videoModel->find($id);
        
        if (!$video) {
            $this->redirect('/admin/videos');
            return;
        }
        
        // Editor ise ve video kendi değilse
        if ($this->isEditor()) {
            if (($video['ekul'] ?? null) != $_SESSION['admin_id']) {
                die('⛔ Bu videoyu düzenleme yetkiniz yok!');
            }
        }

        if ($this->isPost()) {
            // IP/MAC bilgisi
            $networkInfo = NetworkHelper::getClientInfo();
            
            $data = [
                'title' => $_POST['title'],
                'slug' => $this->slugify($_POST['title']),
                'description' => $_POST['description'],
                'category_id' => $_POST['category_id'],
                'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
                'dkul' => $_SESSION['admin_id'],
                'dkul_ip' => $networkInfo['ip'],
                'dkul_mac' => $networkInfo['mac']
            ];
            
            // Editor düzenlerse pending'e al, Admin düzenlerse status'u koruyabilir
            if ($this->isEditor()) {
                $data['status'] = 'pending';
            } else {
                $data['status'] = $_POST['status'];
            }
            
            // Sort order
            if (isset($_POST['sort_order']) && $_POST['sort_order'] !== 'auto') {
                $data['sort_order'] = (int)$_POST['sort_order'];
            } else {
                $data['sort_order'] = $this->videoModel->getNextAvailableSortOrder($_POST['category_id']);
            }

            // Featured text
            if (!empty($_POST['featured_text'])) {
                $data['featured_text'] = $_POST['featured_text'];
            }
            
            $featuredSource = $_POST['featured_source'] ?? 'thumbnail';
            $data['featured_source'] = $featuredSource;
            
            $regenerateFeatured = false;
            if ($featuredSource === 'text' && $featuredSource !== ($video['featured_source'] ?? 'thumbnail')) {
                $regenerateFeatured = true;
            }
            
            if (!empty($_POST['featured_text']) && $_POST['featured_text'] !== $video['featured_text'] && $featuredSource === 'text') {
                $regenerateFeatured = true;
            }

            // Video dosyası
            if (!empty($_FILES['video']['name'])) {
                $videoPath = $this->uploadFile($_FILES['video'], 'videos', ['mp4', 'webm', 'ogg']);
                if ($videoPath) $data['video_path'] = $videoPath;
            }

            // Thumbnail
            $newThumbnail = false;
            if (!empty($_FILES['thumbnail']['name'])) {
                $thumbnailPath = $this->uploadFile($_FILES['thumbnail'], 'thumbnails', ['jpg', 'jpeg', 'png']);
                if ($thumbnailPath) {
                    $data['thumbnail_path'] = $thumbnailPath;
                    $newThumbnail = true;
                    
                    if ($featuredSource === 'thumbnail') {
                        $data['featured_image_path'] = $thumbnailPath;
                    }
                }
            }
            
            // Featured image regenerate
            if ($regenerateFeatured) {
                $category = $this->categoryModel->find($_POST['category_id']);
                $generator = new ImageGenerator();
                $featuredImageName = uniqid() . '_' . time() . '.jpg';
                $featuredImagePath = __DIR__ . '/../../public/uploads/featured/' . $featuredImageName;
                
                $generatedPath = $generator->generateFeaturedImage(
                    $_POST['featured_text'] ?: $video['title'],
                    $category['background_color'],
                    $category['text_color'],
                    $featuredImagePath
                );
                
                $data['featured_image_path'] = $generatedPath;
            } elseif ($featuredSource === 'thumbnail' && !$newThumbnail) {
                $data['featured_image_path'] = $video['thumbnail_path'];
            }

            $this->videoModel->update($id, $data);
            
            if ($this->isEditor()) {
                $_SESSION['success_message'] = '✅ Video güncellendi ve admin onayına gönderildi!';
            } else {
                $_SESSION['success_message'] = '✅ Video başarıyla güncellendi!';
            }
            
            $this->redirect('/admin/videos');
            return;
        }

        $categories = $this->categoryModel->all();
        $availableOrders = $this->videoModel->getAvailableSortOrders($video['category_id'], $video['id']);
        
        $this->view('admin/videos/edit', [
            'video' => $video,
            'categories' => $categories,
            'availableOrders' => $availableOrders
        ]);
    }

    /**
     * Video Silme (Editor: pending_delete, Admin: deleted)
     */
    public function delete($id) {
        $this->checkAuth();
        
        $video = $this->videoModel->find($id);
        
        if (!$video) {
            $_SESSION['error_message'] = '❌ Video bulunamadı!';
            $this->redirect('/admin/videos');
            return;
        }
        
        // IP/MAC bilgisi
        $networkInfo = NetworkHelper::getClientInfo();
        
        if ($this->isEditor()) {
            // Editor: Kendi videosu mu?
            if (($video['ekul'] ?? null) != $_SESSION['admin_id']) {
                die('⛔ Bu videoyu silme yetkiniz yok!');
            }
            
            // Kilitli mi?
            if (($video['admin_locked'] ?? 0) == 1) {
                die('⛔ Bu video admin tarafından kilitlenmiş!');
            }
            
            // Editor silerse: pending_delete (admin onayı bekle)
            $this->videoModel->update($id, [
                'status' => 'pending_delete',
                'delete_requested_by' => $_SESSION['admin_id'],
                'delete_requested_at' => date('Y-m-d H:i:s'),
                'dkul' => $_SESSION['admin_id'],
                'dkul_ip' => $networkInfo['ip'],
                'dkul_mac' => $networkInfo['mac']
            ]);
            
            $_SESSION['success_message'] = '⏳ Video silme isteği admin onayına gönderildi!';
        } else {
            // Admin: Direkt sil
            $this->videoModel->update($id, [
                'status' => 'deleted',
                'delete_requested_by' => $_SESSION['admin_id'],
                'delete_requested_at' => date('Y-m-d H:i:s'),
                'dkul' => $_SESSION['admin_id'],
                'dkul_ip' => $networkInfo['ip'],
                'dkul_mac' => $networkInfo['mac']
            ]);
            
            $_SESSION['success_message'] = '✅ Video silindi! (Geri Dönüşüm Kutusuna taşındı)';
        }
        
        $this->redirect('/admin/videos');
    }

    /**
     * SİLME ONAYLA - Sadece Admin
     */
    public function approveDelete($id) {
        $this->checkAuth();
        $this->requireAdmin();
        
        $video = $this->videoModel->find($id);
        
        if (!$video || $video['status'] !== 'pending_delete') {
            $_SESSION['error_message'] = '❌ Video bulunamadı veya silme onayı beklemiyor!';
            $this->redirect('/admin/videos');
            return;
        }
        
        // IP/MAC bilgisi
        $networkInfo = NetworkHelper::getClientInfo();
        
        $this->videoModel->update($id, [
            'status' => 'deleted',
            'dkul' => $_SESSION['admin_id'],
            'dkul_ip' => $networkInfo['ip'],
            'dkul_mac' => $networkInfo['mac']
        ]);
        
        $_SESSION['success_message'] = '✅ Video silme onaylandı!';
        $this->redirect('/admin/videos');
    }

    /**
     * SİLME REDDET - Sadece Admin
     */
    public function rejectDelete($id) {
        $this->checkAuth();
        $this->requireAdmin();
        
        $video = $this->videoModel->find($id);
        
        if (!$video || $video['status'] !== 'pending_delete') {
            $_SESSION['error_message'] = '❌ Video bulunamadı!';
            $this->redirect('/admin/videos');
            return;
        }
        
        // IP/MAC bilgisi
        $networkInfo = NetworkHelper::getClientInfo();
        
        $this->videoModel->update($id, [
            'status' => 'active',
            'delete_requested_by' => null,
            'delete_requested_at' => null,
            'dkul' => $_SESSION['admin_id'],
            'dkul_ip' => $networkInfo['ip'],
            'dkul_mac' => $networkInfo['mac']
        ]);
        
        $_SESSION['success_message'] = '↩️ Video silme isteği reddedildi, video aktif!';
        $this->redirect('/admin/videos');
    }

    /**
     * Video Onaylama - Admin
     */
    public function approve($id) {
        $this->checkAuth();
        $this->requireAdmin();
        
        $video = $this->videoModel->find($id);
        
        if (!$video) {
            $_SESSION['error_message'] = '❌ Video bulunamadı!';
            $this->redirect('/admin/videos');
            return;
        }
        
        // IP/MAC bilgisi
        $networkInfo = NetworkHelper::getClientInfo();
        
        $this->videoModel->update($id, [
            'status' => 'active',
            'dkul' => $_SESSION['admin_id'],
            'dkul_ip' => $networkInfo['ip'],
            'dkul_mac' => $networkInfo['mac']
        ]);
        
        $_SESSION['success_message'] = '✅ Video onaylandı ve yayına alındı!';
        $this->redirect('/admin/videos');
    }

    /**
     * Video Önizleme - Admin için (onay bekleyen videoları inceleme)
     */
    public function preview($id) {
        $this->checkAuth();
        $this->requireAdmin();
        
        $video = $this->videoModel->find($id);
        
        if (!$video) {
            $_SESSION['error_message'] = '❌ Video bulunamadı!';
            $this->redirect('/admin/videos');
            return;
        }
        
        // Kategori bilgisi al
        $category = $this->categoryModel->find($video['category_id']);
        $video['category_name'] = $category['name'];
        $video['background_color'] = $category['background_color'];
        $video['text_color'] = $category['text_color'];
        
        // Ekleyen bilgisi
        $adminModel = new Admin();
        $creator = $adminModel->find($video['ekul']);
        $video['created_by_username'] = $creator['username'] ?? 'Bilinmiyor';
        
        $this->view('admin/videos/preview', [
            'video' => $video
        ]);
    }

    /**
     * Video Reddetme - Admin
     */
    public function reject($id) {
        $this->checkAuth();
        $this->requireAdmin();
        
        $video = $this->videoModel->find($id);
        
        if (!$video) {
            $_SESSION['error_message'] = '❌ Video bulunamadı!';
            $this->redirect('/admin/videos');
            return;
        }
        
        // IP/MAC bilgisi
        $networkInfo = NetworkHelper::getClientInfo();
        
        $this->videoModel->update($id, [
            'status' => 'passive',
            'dkul' => $_SESSION['admin_id'],
            'dkul_ip' => $networkInfo['ip'],
            'dkul_mac' => $networkInfo['mac']
        ]);
        
        $_SESSION['success_message'] = '❌ Video reddedildi!';
        $this->redirect('/admin/videos');
    }

    /**
     * Status Toggle (Active/Passive)
     */
    public function toggleStatus($id) {
        $this->checkAuth();
        
        $video = $this->videoModel->find($id);
        
        if (!$video) {
            $_SESSION['error_message'] = '❌ Video bulunamadı!';
            $this->redirect('/admin/videos');
            return;
        }
        
        // Editor ise kontrol
        if ($this->isEditor()) {
            if (($video['ekul'] ?? null) != $_SESSION['admin_id']) {
                die('⛔ Bu videoyu düzenleme yetkiniz yok!');
            }
        }
        
        // IP/MAC bilgisi
        $networkInfo = NetworkHelper::getClientInfo();
        
        $newStatus = $video['status'] === 'active' ? 'passive' : 'active';
        
        $this->videoModel->update($id, [
            'status' => $newStatus,
            'dkul' => $_SESSION['admin_id'],
            'dkul_ip' => $networkInfo['ip'],
            'dkul_mac' => $networkInfo['mac']
        ]);
        
        $_SESSION['success_message'] = $newStatus === 'active' ? '✅ Video aktif edildi!' : '⏸️ Video pasife alındı!';
        $this->redirect('/admin/videos');
    }

    /**
     * Kalıcı Silme - Sadece Admin (Dosyaları da siler)
     */
    public function permanentDelete($id) {
        $this->checkAuth();
        $this->requireAdmin();
        
        // Önce video bilgilerini al
        $video = $this->videoModel->find($id);
        
        if ($video) {
            // Dosya yollarını belirle
            $basePath = __DIR__ . '/../../public';
            
            // Video dosyasını sil
            if (!empty($video['video_path'])) {
                $videoFile = $basePath . $video['video_path'];
                if (file_exists($videoFile)) {
                    unlink($videoFile);
                }
            }
            
            // Thumbnail dosyasını sil
            if (!empty($video['thumbnail_path'])) {
                $thumbnailFile = $basePath . $video['thumbnail_path'];
                if (file_exists($thumbnailFile)) {
                    unlink($thumbnailFile);
                }
            }
            
            // Featured image dosyasını sil (thumbnail'den farklıysa)
            if (!empty($video['featured_image_path']) && $video['featured_image_path'] !== $video['thumbnail_path']) {
                $featuredFile = $basePath . $video['featured_image_path'];
                if (file_exists($featuredFile)) {
                    unlink($featuredFile);
                }
            }
        }
        
        // Veritabanından sil
        $this->videoModel->permanentDelete($id);
        
        $_SESSION['success_message'] = '🗑️ Video ve dosyaları kalıcı olarak silindi!';
        $this->redirect('/admin/videos/trash');
    }

    private function checkAuth() {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect('/admin/login');
            exit;
        }
    }
}
