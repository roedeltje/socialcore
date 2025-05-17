<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;

class UserController extends Controller 
{
    public function __construct() 
    {
        // Controleer of de gebruiker is ingelogd en admin is
        // Later toe te voegen met sessiecontrole
    }
    
    public function index() 
    {
        // Haal gebruikers op uit de database (placeholder)
        $users = [
            ['id' => 1, 'username' => 'admin', 'email' => 'admin@example.com', 'role' => 'admin', 'status' => 'active', 'created_at' => '2025-01-01'],
            ['id' => 2, 'username' => 'user1', 'email' => 'user1@example.com', 'role' => 'member', 'status' => 'active', 'created_at' => '2025-01-15'],
            ['id' => 3, 'username' => 'user2', 'email' => 'user2@example.com', 'role' => 'member', 'status' => 'inactive', 'created_at' => '2025-02-01'],
        ];
        
        // Later vervangen door echte database query
        // $users = DB::table('users')->get();
        
        return $this->view('admin/users/index', ['users' => $users]);
    }
    
    public function create() 
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verwerk het formulier (placeholder)
            // Later toevoegen met database operaties
            
            // Redirect naar gebruikersoverzicht na succesvol aanmaken
            header('Location: ' . base_url('admin/users'));
            exit;
        }
        
        return $this->view('admin/users/create');
    }
    
    public function edit() 
    {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            // Redirect naar gebruikersoverzicht als geen ID is opgegeven
            header('Location: ' . base_url('admin/users'));
            exit;
        }
        
        // Haal gebruiker op uit de database (placeholder)
        $user = [
            'id' => $id,
            'username' => 'user' . $id,
            'email' => 'user' . $id . '@example.com',
            'role' => 'member',
            'status' => 'active',
            'created_at' => '2025-01-01'
        ];
        
        // Later vervangen door echte database query
        // $user = DB::table('users')->where('id', $id)->first();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verwerk het formulier (placeholder)
            // Later toevoegen met database operaties
            
            // Redirect naar gebruikersoverzicht na succesvol bewerken
            header('Location: ' . base_url('admin/users'));
            exit;
        }
        
        return $this->view('admin/users/edit', ['user' => $user]);
    }
    
    public function delete() 
    {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            // Redirect naar gebruikersoverzicht als geen ID is opgegeven
            header('Location: ' . base_url('admin/users'));
            exit;
        }
        
        // Verwijder gebruiker (placeholder)
        // Later toevoegen met database operaties
        
        // Redirect naar gebruikersoverzicht na succesvol verwijderen
        header('Location: ' . base_url('admin/users'));
        exit;
    }
}