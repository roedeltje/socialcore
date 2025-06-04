<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Database\Database;
use PDO;

/**
 * ContentController - Beheer van berichten, reacties, media en gerapporteerde content
 */
class ContentController extends Controller
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getPdo();
    }
    
    /**
     * View methode die admin layout gebruikt
     */
    protected function view($view, $data = [])
    {
        // Gebruik de admin layout
        $title = $data['title'] ?? 'Content Beheer';
        $contentView = BASE_PATH . "/app/Views/{$view}.php";
        
        // Extract data om variabelen beschikbaar te maken in de view
        extract($data);
        
        // Laad de admin layout
        include BASE_PATH . '/app/Views/admin/layout.php';
    }
    
    /**
     * Berichten overzicht
     */
    public function posts()
    {
        try {
            // Haal alle berichten op met gebruikersinformatie
            $query = "
                SELECT p.*, u.username, u.display_name, up.avatar
                FROM posts p 
                LEFT JOIN users u ON p.user_id = u.id 
                LEFT JOIN user_profiles up ON u.id = up.user_id
                WHERE p.is_deleted = 0
                ORDER BY p.created_at DESC 
                LIMIT 50
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Statistieken ophalen
            $statsQuery = "
                SELECT 
                    COUNT(*) as total_posts,
                    SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR) THEN 1 ELSE 0 END) as posts_today,
                    SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as posts_week
                FROM posts 
                WHERE is_deleted = 0
            ";
            
            $statsStmt = $this->db->prepare($statsQuery);
            $statsStmt->execute();
            $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
            
            $data = [
                'title' => 'Berichten beheren',
                'posts' => $posts,
                'stats' => $stats
            ];
            
            $this->view('admin/content/posts', $data);
            
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij laden berichten: " . $e->getMessage();
            header('Location: ' . base_url('?route=admin/dashboard'));
            exit;
        }
    }
    
    /**
     * Reacties overzicht
     */
    public function comments()
    {
        try {
            // Haal alle reacties op met bericht- en gebruikersinformatie
            $query = "
                SELECT c.*, u.username, u.display_name, up.avatar,
                       p.content as post_content, pu.username as post_author
                FROM post_comments c 
                LEFT JOIN users u ON c.user_id = u.id 
                LEFT JOIN user_profiles up ON u.id = up.user_id
                LEFT JOIN posts p ON c.post_id = p.id
                LEFT JOIN users pu ON p.user_id = pu.id
                WHERE c.is_deleted = 0
                ORDER BY c.created_at DESC 
                LIMIT 50
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Statistieken
            $statsQuery = "
                SELECT 
                    COUNT(*) as total_comments,
                    SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR) THEN 1 ELSE 0 END) as comments_today
                FROM post_comments
                WHERE is_deleted = 0
            ";
            
            $statsStmt = $this->db->prepare($statsQuery);
            $statsStmt->execute();
            $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
            
            $data = [
                'title' => 'Reacties beheren',
                'comments' => $comments,
                'stats' => $stats
            ];
            
            $this->view('admin/content/comments', $data);
            
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij laden reacties: " . $e->getMessage();
            header('Location: ' . base_url('?route=admin/dashboard'));
            exit;
        }
    }
    
    /**
     * Media overzicht
     */
    public function media()
    {
        try {
            // Haal media bestanden op uit post_media tabel met post en gebruiker info
            $query = "
                SELECT pm.*, p.content as post_content, p.created_at as post_created_at,
                       u.username, u.display_name, up.avatar
                FROM post_media pm
                LEFT JOIN posts p ON pm.post_id = p.id 
                LEFT JOIN users u ON p.user_id = u.id 
                LEFT JOIN user_profiles up ON u.id = up.user_id
                WHERE p.is_deleted = 0 OR p.is_deleted IS NULL
                ORDER BY pm.created_at DESC 
                LIMIT 50
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $mediaPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Media statistieken uit post_media tabel
            $statsQuery = "
                SELECT 
                    COUNT(*) as total_media,
                    SUM(CASE WHEN media_type = 'image' THEN 1 ELSE 0 END) as photos,
                    SUM(CASE WHEN media_type = 'video' THEN 1 ELSE 0 END) as videos
                FROM post_media pm
                LEFT JOIN posts p ON pm.post_id = p.id
                WHERE p.is_deleted = 0 OR p.is_deleted IS NULL
            ";
            
            $statsStmt = $this->db->prepare($statsQuery);
            $statsStmt->execute();
            $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
            
            $data = [
                'title' => 'Media beheren',
                'mediaPosts' => $mediaPosts,
                'stats' => $stats
            ];
            
            $this->view('admin/content/media', $data);
            
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij laden media: " . $e->getMessage();
            header('Location: ' . base_url('?route=admin/dashboard'));
            exit;
        }
    }
    
    /**
     * Gerapporteerde content
     */
    public function reported()
    {
        try {
            // Voor nu simuleren we gerapporteerde content
            // Later kun je een reports tabel toevoegen
            $data = [
                'title' => 'Gerapporteerde content',
                'reports' => [] // Placeholder voor toekomstige functionaliteit
            ];
            
            $this->view('admin/content/reported', $data);
            
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij laden rapporten: " . $e->getMessage();
            header('Location: ' . base_url('?route=admin/dashboard'));
            exit;
        }
    }
    
    /**
     * Verwijder een bericht (soft delete)
     */
    public function deletePost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . base_url('?route=admin/content/posts'));
            exit;
        }
        
        $postId = $_POST['post_id'] ?? null;
        
        if (!$postId) {
            $_SESSION['error_message'] = 'Geen geldig bericht ID opgegeven.';
            header('Location: ' . base_url('?route=admin/content/posts'));
            exit;
        }
        
        try {
            // Soft delete - posts tabel heeft is_deleted kolom
            $stmt = $this->db->prepare("UPDATE posts SET is_deleted = 1 WHERE id = ?");
            $stmt->execute([$postId]);
            
            $_SESSION['success_message'] = 'Bericht succesvol verwijderd.';
            
        } catch (\Exception $e) {
            $_SESSION['error_message'] = 'Fout bij verwijderen bericht: ' . $e->getMessage();
        }
        
        header('Location: ' . base_url('?route=admin/content/posts'));
        exit;
    }
    
    /**
     * Verwijder een reactie (soft delete)
     */
    public function deleteComment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . base_url('?route=admin/content/comments'));
            exit;
        }
        
        $commentId = $_POST['comment_id'] ?? null;
        
        if (!$commentId) {
            $_SESSION['error_message'] = 'Geen geldig reactie ID opgegeven.';
            header('Location: ' . base_url('?route=admin/content/comments'));
            exit;
        }
        
        try {
            // Soft delete - post_comments tabel heeft is_deleted kolom
            $stmt = $this->db->prepare("UPDATE post_comments SET is_deleted = 1 WHERE id = ?");
            $stmt->execute([$commentId]);
            
            $_SESSION['success_message'] = 'Reactie succesvol verwijderd.';
            
        } catch (\Exception $e) {
            $_SESSION['error_message'] = 'Fout bij verwijderen reactie: ' . $e->getMessage();
        }
        
        header('Location: ' . base_url('?route=admin/content/comments'));
        exit;
    }
    
    /**
     * Verwijder media bestand uit post_media tabel
     */
    public function deleteMedia()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . base_url('?route=admin/content/media'));
            exit;
        }
        
        $mediaId = $_POST['media_id'] ?? null;
        $type = $_POST['type'] ?? 'media'; // 'media' voor post_media tabel
        
        if (!$mediaId) {
            $_SESSION['error_message'] = 'Geen geldig media ID opgegeven.';
            header('Location: ' . base_url('?route=admin/content/media'));
            exit;
        }
        
        try {
            if ($type === 'media') {
                // Verwijder uit post_media tabel
                // Haal eerst het bestand op om het fysiek te verwijderen
                $stmt = $this->db->prepare("SELECT file_path FROM post_media WHERE id = ?");
                $stmt->execute([$mediaId]);
                $media = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($media && $media['file_path']) {
                    // Verwijder het fysieke bestand
                    $filePath = BASE_PATH . '/public' . $media['file_path'];
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
                
                // Verwijder de database record
                $deleteStmt = $this->db->prepare("DELETE FROM post_media WHERE id = ?");
                $deleteStmt->execute([$mediaId]);
                
                $_SESSION['success_message'] = 'Media bestand succesvol verwijderd.';
            } else {
                $_SESSION['error_message'] = 'Ongeldig type opgegeven.';
            }
            
        } catch (\Exception $e) {
            $_SESSION['error_message'] = 'Fout bij verwijderen media: ' . $e->getMessage();
        }
        
        header('Location: ' . base_url('?route=admin/content/media'));
        exit;
    }
}