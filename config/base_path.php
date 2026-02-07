<?php
// Base Path Helper
define('BASE_PATH', '/video-portal/public');

/**
 * Asset yollarını döndür (CSS, JS, resim vb.)
 */
function asset($path) {
    return BASE_PATH . $path;
}

/**
 * URL yollarını döndür (link, redirect vb.)
 */
function url($path = '') {
    return BASE_PATH . $path;
}

/**
 * Upload dosya yollarını döndür (video, resim vb.)
 * Bu fonksiyon BASE_PATH ekler çünkü tarayıcıda görüntülenecek
 */
function upload_url($path) {
    // Eğer path zaten BASE_PATH ile başlıyorsa, tekrar ekleme
    if (strpos($path, BASE_PATH) === 0) {
        return $path;
    }
    
    // Eğer path / ile başlıyorsa, BASE_PATH ekle
    if (strpos($path, '/') === 0) {
        return BASE_PATH . $path;
    }
    
    // Yoksa BASE_PATH + / + path
    return BASE_PATH . '/' . $path;
}
