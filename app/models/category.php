<?php
require_once __DIR__ . '/../../core/Model.php';

class Category extends Model {
    protected $table = 'categories';

    public function getAllWithVideoCount() {
        $sql = "
            SELECT c.*, COUNT(v.id) as video_count,
                   creator.username as created_by_username
            FROM categories c
            LEFT JOIN videos v ON c.id = v.category_id AND v.status = 'active'
            LEFT JOIN admins creator ON c.ekul = creator.id
            GROUP BY c.id
            ORDER BY c.name ASC
        ";
        return $this->db->fetchAll($sql);
    }

    public function getBySlug($slug) {
        return $this->findBy('slug', $slug);
    }

    public static $colorPresets = [
        'blue' => ['bg' => '#3B82F6', 'text' => '#FFFFFF', 'name' => 'Mavi'],
        'red' => ['bg' => '#EF4444', 'text' => '#FFFFFF', 'name' => 'Kırmızı'],
        'purple' => ['bg' => '#8B5CF6', 'text' => '#FFFFFF', 'name' => 'Mor'],
        'green' => ['bg' => '#10B981', 'text' => '#FFFFFF', 'name' => 'Yeşil'],
        'orange' => ['bg' => '#F59E0B', 'text' => '#1F2937', 'name' => 'Turuncu'],
        'cyan' => ['bg' => '#06B6D4', 'text' => '#FFFFFF', 'name' => 'Cyan'],
    ];
}
