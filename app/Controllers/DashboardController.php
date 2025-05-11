<?php
namespace App\Controllers;

use App\Auth\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }
        
        // Data voor het dashboard
        $data = [
            'title' => 'Dashboard',
            'user' => Auth::user(),
            'activities' => [] // Vul dit met echte data als die beschikbaar is
        ];
        
        $this->view('dashboard/index', $data);
    }
}