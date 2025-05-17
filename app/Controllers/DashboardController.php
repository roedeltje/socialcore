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
    
    $userId = Auth::user();
    
    $userData = [
        'id' => $userId,
        'username' => $_SESSION['username'] ?? 'gebruiker'
    ];
    
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
        'title' => 'Dashboard',
        'contentView' => BASE_PATH . '/app/Views/dashboard/dashboard-home.php' // Dit is het belangrijke deel
    ];
    
    // Laad de admin layout in plaats van rechtstreeks dashboard/index
    $this->view('admin/layout', $data);
}
}