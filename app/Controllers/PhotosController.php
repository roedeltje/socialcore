<?php

namespace App\Controllers;

use App\Database\Database;
use PDO;
use Exception;

class PhotosController extends Controller
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getPdo();
    }

    /**
     * Toon alle openbare foto's
     */
    public function index()
    {
        // Controleer of gebruiker is ingelogd
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
            return;
        }

        $viewerId = $_SESSION['user_id'];
        
        // Haal alle openbare foto's op
        $photos = $this->getAllPublicPhotos($viewerId);
        
        $data = [
            'title' => 'Foto\'s - SocialCore',
            'photos' => $photos,
            'total_photos' => count($photos)
        ];
        
        $this->view('photos/index', $data);
    }

    /**
     * 🔒 Haal alle openbare foto's op met privacy filtering
     */
    private function getAllPublicPhotos($viewerId, $limit = 50)
    {
        try {
            // Haal alle foto's op met gebruiker en post informatie
            $query = "
                SELECT 
                    pm.id as media_id,
                    pm.file_path,
                    pm.file_name,
                    pm.created_at as uploaded_at,
                    p.id as post_id,
                    p.content as description,
                    p.user_id,
                    p.created_at as post_created_at,
                    u.username,
                    COALESCE(up.display_name, u.username) as user_name,
                    up.avatar
                FROM post_media pm
                JOIN posts p ON pm.post_id = p.id
                JOIN users u ON p.user_id = u.id
                LEFT JOIN user_profiles up ON u.id = up.user_id
                WHERE pm.media_type = 'image'
                AND p.is_deleted = 0
                ORDER BY pm.created_at DESC
                LIMIT ?
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$limit * 2]); // Haal meer op voor privacy filtering
            $allPhotos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // 🔒 PRIVACY FILTER: Filter foto's op basis van privacy instellingen
            $filteredPhotos = [];
            
            foreach ($allPhotos as $photo) {
                // Check of viewer deze foto's mag zien
                if ($this->canViewUserPhotos($photo['user_id'], $viewerId)) {
                    // Voeg extra data toe
                    $photo['full_url'] = base_url('uploads/' . $photo['file_path']);
                    $photo['avatar_url'] = get_avatar_url($photo['avatar']);
                    $photo['time_ago'] = $this->formatDate($photo['uploaded_at']);
                    $photo['description'] = $photo['description'] ?: 'Geen beschrijving';
                    
                    $filteredPhotos[] = $photo;
                }
            }
            
            // Limiteer tot gewenste aantal na filtering
            return array_slice($filteredPhotos, 0, $limit);
            
        } catch (\Exception $e) {
            error_log("Error getting public photos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * 🔒 PRIVACY: Check of viewer de foto's van een gebruiker mag zien
     */
    private function canViewUserPhotos($photoOwnerId, $viewerId)
    {
        // Eigenaar kan altijd eigen foto's zien
        if ($photoOwnerId == $viewerId) {
            return true;
        }

        // Haal privacy instellingen van foto eigenaar op
        $privacySettings = $this->getPrivacySettings($photoOwnerId);
        
        if (!$privacySettings) {
            // Geen privacy instellingen = openbaar (backwards compatibility)
            return true;
        }

        switch ($privacySettings['photos_visibility']) {
            case 'public':
                return true;
                
            case 'private':
                return false;
                
            case 'friends':
                return $this->areFriends($photoOwnerId, $viewerId);
                
            default:
                return true; // Fallback
        }
    }

    /**
     * 🔒 PRIVACY: Haal privacy instellingen op voor een gebruiker
     */
    private function getPrivacySettings($userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM user_privacy_settings 
                WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error getting privacy settings: " . $e->getMessage());
            return null;
        }
    }

    /**
     * 🔒 PRIVACY: Check of twee gebruikers vrienden zijn
     */
    private function areFriends($userId1, $userId2)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM friendships 
                WHERE ((user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?))
                AND status = 'accepted'
            ");
            $stmt->execute([$userId1, $userId2, $userId2, $userId1]);
            
            return $stmt->fetchColumn() > 0;
        } catch (\Exception $e) {
            error_log("Error checking friendship: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Format datetime voor weergave
     */
    private function formatDate($datetime)
    {
        $date = new \DateTime($datetime);
        $now = new \DateTime();
        $diff = $now->diff($date);
        
        if ($diff->days == 0) {
            if ($diff->h > 0) {
                return $diff->h . ' uur geleden';
            } elseif ($diff->i > 0) {
                return $diff->i . ' minuten geleden';
            } else {
                return 'Net nu';
            }
        } elseif ($diff->days == 1) {
            return 'Gisteren om ' . $date->format('H:i');
        } else {
            return $date->format('d-m-Y H:i');
        }
    }
}