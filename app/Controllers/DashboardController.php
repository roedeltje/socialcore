<?php
namespace App\Controllers;

use App\Auth\Auth;
use App\Database\Database;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
        header('Location: ' . base_url('?route=login'));
        exit;
    }
        
        // Auth::user() retourneert alleen het user_id
        $userId = Auth::user();
        
        // Maak een gebruikersobject dat ook de username bevat
        $userData = [
            'id' => $userId,
            'username' => $_SESSION['username'] ?? 'gebruiker'
        ];
        
        // Als je database-gegevens wilt gebruiken:

        try {
            $db = Database::getInstance();
            $userFromDb = $db->fetch("SELECT * FROM users WHERE id = ?", [$userId]);
            if ($userFromDb) {
                $userData = $userFromDb;
            }
        } catch (\Exception $e) {
            // Log error en gebruik session data als fallback
        }
        
        $data = [
            'user' => $userData,
            'title' => 'Dashboard'
        ];
        
        $this->view('dashboard/index', $data);
    }
}