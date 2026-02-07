<?php
require_once __DIR__ . '/../../core/Model.php';

class Video extends Model {
    protected $table = 'videos';

    public function getAllActive($limit = null) {
        $sql = "
            SELECT v.*, c.name as category_name, c.slug as category_slug,
                   c.background_color, c.text_color, c.logo_path as category_logo
            FROM videos v
            INNER JOIN categories c ON v.category_id = c.id
            WHERE v.status = 'active' AND c.status = 'active'
            ORDER BY v.created_at DESC
        ";
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        return $this->db->fetchAll($sql);
    }

    public function getFeatured($limit = 3) {
        $sql = "
            SELECT v.*, c.name as category_name, c.slug as category_slug,
                   c.background_color, c.text_color, c.logo_path as category_logo
            FROM videos v
            INNER JOIN categories c ON v.category_id = c.id
            WHERE v.status = 'active' AND v.is_featured = 1 AND c.status = 'active'
            ORDER BY v.created_at DESC
            LIMIT {$limit}
        ";
        return $this->db->fetchAll($sql);
    }

    public function getBySlug($slug) {
        $sql = "
            SELECT v.*, c.name as category_name, c.slug as category_slug,
                   c.background_color, c.text_color, c.logo_path as category_logo
            FROM videos v
            INNER JOIN categories c ON v.category_id = c.id
            WHERE v.slug = ? AND v.status = 'active' AND c.status = 'active'
            LIMIT 1
        ";
        return $this->db->fetch($sql, [$slug]);
    }

    public function getByCategory($categoryId, $limit = null) {
        $sql = "
            SELECT v.*, c.name as category_name, c.slug as category_slug,
                   c.background_color, c.text_color, c.logo_path as category_logo
            FROM videos v
            INNER JOIN categories c ON v.category_id = c.id
            WHERE v.category_id = ? AND v.status = 'active' AND c.status = 'active'
            ORDER BY 
                CASE WHEN v.sort_order IS NULL THEN 1 ELSE 0 END,
                v.sort_order ASC,
                v.created_at DESC
        ";
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        return $this->db->fetchAll($sql, [$categoryId]);
    }

    public function search($query, $limit = 5) {
        $searchTerm = "%{$query}%";
        $sql = "
            SELECT v.*, c.name as category_name, c.slug as category_slug,
                   c.background_color, c.text_color, c.logo_path as category_logo
            FROM videos v
            INNER JOIN categories c ON v.category_id = c.id
            WHERE v.status = 'active' AND c.status = 'active'
            AND (v.title LIKE ? OR v.description LIKE ?)
            ORDER BY v.created_at DESC
            LIMIT {$limit}
        ";
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm]);
    }

    public function incrementView($id) {
        $sql = "UPDATE videos SET view_count = view_count + 1 WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }

    /**
     * Admin panel için videolar (SİLİNENLER HARİÇ)
     */
    public function getAllForAdmin($filters = []) {
        $sql = "
            SELECT v.*, 
                   c.name as category_name, 
                   creator.username as created_by_username,
                   creator.first_name as creator_first_name, 
                   creator.last_name as creator_last_name,
                   editor.username as dkul_username
            FROM videos v
            INNER JOIN categories c ON v.category_id = c.id
            LEFT JOIN admins creator ON v.ekul = creator.id
            LEFT JOIN admins editor ON v.dkul = editor.id
            WHERE v.status != 'deleted'
        ";
        
        $params = [];
        
        // Kategori filtresi
        if (!empty($filters['category_id'])) {
            $sql .= " AND v.category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        // Ekleyen filtresi
        if (!empty($filters['ekul'])) {
            $sql .= " AND v.ekul = ?";
            $params[] = $filters['ekul'];
        }
        
        // Status filtresi
        if (!empty($filters['status'])) {
            $sql .= " AND v.status = ?";
            $params[] = $filters['status'];
        }
        
        $sql .= " ORDER BY v.created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Editör için kendi videoları (SİLİNENLER HARİÇ)
     */
    public function getByCreatorForAdmin($creatorId, $filters = []) {
        $sql = "
            SELECT v.*, 
                   c.name as category_name, 
                   creator.username as created_by_username,
                   creator.first_name as creator_first_name, 
                   creator.last_name as creator_last_name,
                   editor.username as dkul_username
            FROM videos v
            INNER JOIN categories c ON v.category_id = c.id
            LEFT JOIN admins creator ON v.ekul = creator.id
            LEFT JOIN admins editor ON v.dkul = editor.id
            WHERE v.ekul = ? AND v.status != 'deleted'
        ";
        
        $params = [$creatorId];
        
        // Kategori filtresi
        if (!empty($filters['category_id'])) {
            $sql .= " AND v.category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        // Status filtresi
        if (!empty($filters['status'])) {
            $sql .= " AND v.status = ?";
            $params[] = $filters['status'];
        }
        
        $sql .= " ORDER BY v.created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * GERİ DÖNÜŞÜM KUTUSU - Sadece silinen videolar
     */
    public function getDeletedVideos() {
        $sql = "
            SELECT v.*, 
                   c.name as category_name, 
                   creator.username as created_by_username,
                   deleter.username as deleted_by_username
            FROM videos v
            INNER JOIN categories c ON v.category_id = c.id
            LEFT JOIN admins creator ON v.ekul = creator.id
            LEFT JOIN admins deleter ON v.delete_requested_by = deleter.id
            WHERE v.status = 'deleted'
            ORDER BY v.updated_at DESC
        ";
        return $this->db->fetchAll($sql);
    }

    /**
     * İZLEME İSTATİSTİKLERİ - Tüm videolar izlenme sıralı (silinmiş hariç)
     */
    public function getViewStatistics($filters = []) {
        $sql = "
            SELECT v.id, v.title, v.slug, v.view_count, v.status, v.is_featured, v.created_at,
                   c.name as category_name, c.background_color, c.text_color,
                   creator.username as created_by_username
            FROM videos v
            INNER JOIN categories c ON v.category_id = c.id
            LEFT JOIN admins creator ON v.ekul = creator.id
            WHERE v.status != 'deleted'
        ";
        
        $params = [];
        
        // Kategori filtresi
        if (!empty($filters['category_id'])) {
            $sql .= " AND v.category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        // Ekleyen filtresi
        if (!empty($filters['ekul'])) {
            $sql .= " AND v.ekul = ?";
            $params[] = $filters['ekul'];
        }
        
        $sql .= " ORDER BY v.view_count DESC";
        
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Genel istatistikler
     */
    public function getStatistics() {
        $sql = "
            SELECT 
                COUNT(*) as total_videos,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_videos,
                SUM(CASE WHEN status = 'passive' THEN 1 ELSE 0 END) as passive_videos,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_videos,
                SUM(CASE WHEN status = 'pending_delete' THEN 1 ELSE 0 END) as pending_delete_videos,
                SUM(CASE WHEN status = 'deleted' THEN 1 ELSE 0 END) as deleted_videos,
                SUM(view_count) as total_views,
                SUM(CASE WHEN is_featured = 1 THEN 1 ELSE 0 END) as featured_videos
            FROM videos
        ";
        return $this->db->fetch($sql);
    }

    /**
     * Kategori bazlı istatistikler
     */
    public function getCategoryStatistics() {
        $sql = "
            SELECT 
                c.id, c.name, c.background_color, c.text_color,
                COUNT(v.id) as video_count,
                SUM(v.view_count) as total_views
            FROM categories c
            LEFT JOIN videos v ON c.id = v.category_id AND v.status = 'active'
            WHERE c.status = 'active'
            GROUP BY c.id
            ORDER BY total_views DESC
        ";
        return $this->db->fetchAll($sql);
    }

    public function getAvailableSortOrders($categoryId, $excludeVideoId = null) {
        $sql = "SELECT sort_order FROM videos WHERE category_id = ? AND sort_order IS NOT NULL";
        $params = [$categoryId];
        
        if ($excludeVideoId) {
            $sql .= " AND id != ?";
            $params[] = $excludeVideoId;
        }
        
        $used = $this->db->fetchAll($sql, $params);
        $usedOrders = array_column($used, 'sort_order');
        
        $currentOrder = null;
        if ($excludeVideoId) {
            $currentVideo = $this->find($excludeVideoId);
            $currentOrder = $currentVideo['sort_order'] ?? null;
        }
        
        $available = [];
        for ($i = 1; $i <= 50; $i++) {
            if (!in_array($i, $usedOrders) || $i == $currentOrder) {
                $available[] = $i;
            }
        }
        
        return $available;
    }

    public function getNextAvailableSortOrder($categoryId) {
        $available = $this->getAvailableSortOrders($categoryId);
        return !empty($available) ? $available[0] : null;
    }

    /**
     * Silinen videoyu geri yükle
     */
    public function restore($id) {
        return $this->update($id, [
            'status' => 'active',
            'delete_requested_by' => null,
            'delete_requested_at' => null
        ]);
    }

    /**
     * Kalıcı silme
     */
    public function permanentDelete($id) {
        return $this->delete($id);
    }

    /**
     * Status sayılarını getir
     */
    public function getStatusCounts($creatorId = null) {
        $where = $creatorId ? "WHERE ekul = ?" : "";
        $params = $creatorId ? [$creatorId] : [];
        
        $sql = "
            SELECT 
                SUM(CASE WHEN status NOT IN ('deleted') THEN 1 ELSE 0 END) as total,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status = 'passive' THEN 1 ELSE 0 END) as passive,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'pending_delete' THEN 1 ELSE 0 END) as pending_delete,
                SUM(CASE WHEN status = 'deleted' THEN 1 ELSE 0 END) as deleted
            FROM videos
            {$where}
        ";
        return $this->db->fetch($sql, $params);
    }
}
