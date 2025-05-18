<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;

class UserController extends Controller
{
    
    public function index() 
{
    // Haal gebruikers op uit de database
    $users = [];
    
    try {
        $db = \App\Database\Database::getInstance();
        $users = $db->fetchAll("SELECT * FROM users ORDER BY id DESC");
    } catch (\Exception $e) {
        // Als er een fout is, gebruik placeholder data
        $users = [
            ['id' => 1, 'username' => 'admin', 'email' => 'admin@example.com', 'role' => 'admin', 'status' => 'active', 'created_at' => '2025-01-01'],
            ['id' => 2, 'username' => 'user1', 'email' => 'user1@example.com', 'role' => 'member', 'status' => 'active', 'created_at' => '2025-01-15'],
            ['id' => 3, 'username' => 'user2', 'email' => 'user2@example.com', 'role' => 'member', 'status' => 'inactive', 'created_at' => '2025-02-01'],
        ];
    }
    
    $data = [
        'users' => $users,
        'title' => 'Gebruikersbeheer',
        'contentView' => BASE_PATH . '/app/Views/admin/users/index.php'
    ];
    
    return $this->view('admin/layout', $data);
}
    
    public function create() 
{
    $data = [
        'title' => 'Nieuwe Gebruiker',
        'contentView' => BASE_PATH . '/app/Views/admin/users/create.php'
    ];
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validatie
        $errors = [];
        
        // Gebruikersnaam validatie
        $username = trim($_POST['username'] ?? '');
        if (empty($username)) {
            $errors[] = "Gebruikersnaam is verplicht.";
        } elseif (strlen($username) < 3) {
            $errors[] = "Gebruikersnaam moet minimaal 3 tekens bevatten.";
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors[] = "Gebruikersnaam mag alleen letters, cijfers en underscores bevatten.";
        }
        
        // Email validatie
        $email = trim($_POST['email'] ?? '');
        if (empty($email)) {
            $errors[] = "E-mailadres is verplicht.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Ongeldig e-mailadres.";
        }
        
        // Wachtwoord validatie
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';
        
        if (empty($password)) {
            $errors[] = "Wachtwoord is verplicht.";
        } elseif (strlen($password) < 8) {
            $errors[] = "Wachtwoord moet minimaal 8 tekens bevatten.";
        } elseif ($password !== $password_confirm) {
            $errors[] = "Wachtwoorden komen niet overeen.";
        }
        
        // Overige velden ophalen
        $display_name = trim($_POST['display_name'] ?? $username);
        $role = $_POST['role'] ?? 'member';
        $status = $_POST['status'] ?? 'active';
        $send_welcome = isset($_POST['send_welcome']);
        
        // Als er geen fouten zijn, gebruiker aanmaken
        if (empty($errors)) {
            try {
                // Database verbinding
                $db = \App\Database\Database::getInstance();
                
                // Controleer of gebruikersnaam of e-mail al bestaat
                $existingUser = $db->fetch(
                    "SELECT id FROM users WHERE username = ? OR email = ?", 
                    [$username, $email]
                );
                
                if ($existingUser) {
                    $errors[] = "Deze gebruikersnaam of dit e-mailadres is al in gebruik.";
                } else {
                    // Hash wachtwoord
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Gebruiker toevoegen
                    $success = $db->execute(
                        "INSERT INTO users (username, email, password, display_name, role, status, created_at) 
                         VALUES (?, ?, ?, ?, ?, ?, NOW())",
                        [$username, $email, $hashed_password, $display_name, $role, $status]
                    );
                    
                    if ($success) {
                        // Gebruiker succesvol aangemaakt
                        if ($send_welcome) {
                            // Stuur welkomst-e-mail (later implementeren)
                        }
                        
                        // Succes bericht in sessie opslaan
                        $_SESSION['success_message'] = "Gebruiker {$username} is succesvol aangemaakt.";
                        
                        // Redirect naar gebruikersoverzicht
                        header('Location: ' . base_url('admin/users'));
                        exit;
                    } else {
                        $errors[] = "Er is een fout opgetreden bij het aanmaken van de gebruiker.";
                    }
                }
            } catch (\Exception $e) {
                $errors[] = "Database fout: " . $e->getMessage();
            }
        }
        
        // Als er fouten zijn, toon het formulier opnieuw met foutmeldingen
        if (!empty($errors)) {
            $data['error_message'] = implode('<br>', $errors);
        }
    }
    
    return $this->view('admin/layout', $data);
}
    
    public function edit()
{
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        // Redirect naar gebruikersoverzicht als geen ID is opgegeven
        header('Location: ' . base_url('admin/users'));
        exit;
    }
    
    $errors = [];
    $success = false;
    
    try {
        $db = \App\Database\Database::getInstance();
        
        // Haal gebruiker op uit de database op basis van ID
        $user = $db->fetch("SELECT * FROM users WHERE id = ?", [$id]);
        
        if (!$user) {
            $_SESSION['error_message'] = "Gebruiker niet gevonden.";
            header('Location: ' . base_url('admin/users'));
            exit;
        }
        
        // Verwerk formulier als dit een POST verzoek is
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Gebruikersnaam validatie
            $username = trim($_POST['username'] ?? '');
            if (empty($username)) {
                $errors[] = "Gebruikersnaam is verplicht.";
            } elseif (strlen($username) < 3) {
                $errors[] = "Gebruikersnaam moet minimaal 3 tekens bevatten.";
            } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                $errors[] = "Gebruikersnaam mag alleen letters, cijfers en underscores bevatten.";
            } else {
                // Controleer of gebruikersnaam al bestaat (bij een andere gebruiker)
                $existingUser = $db->fetch(
                    "SELECT id FROM users WHERE username = ? AND id != ?", 
                    [$username, $id]
                );
                
                if ($existingUser) {
                    $errors[] = "Deze gebruikersnaam is al in gebruik.";
                }
            }
            
            // Email validatie
            $email = trim($_POST['email'] ?? '');
            if (empty($email)) {
                $errors[] = "E-mailadres is verplicht.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Ongeldig e-mailadres.";
            } else {
                // Controleer of e-mail al bestaat (bij een andere gebruiker)
                $existingUser = $db->fetch(
                    "SELECT id FROM users WHERE email = ? AND id != ?", 
                    [$email, $id]
                );
                
                if ($existingUser) {
                    $errors[] = "Dit e-mailadres is al in gebruik.";
                }
            }
            
            // Wachtwoord validatie (optioneel bij bewerken)
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';
            
            $update_password = false;
            if (!empty($password)) {
                if (strlen($password) < 8) {
                    $errors[] = "Wachtwoord moet minimaal 8 tekens bevatten.";
                } elseif ($password !== $password_confirm) {
                    $errors[] = "Wachtwoorden komen niet overeen.";
                } else {
                    $update_password = true;
                }
            }
            
            // Overige velden ophalen
            $display_name = trim($_POST['display_name'] ?? $username);
            $role = $_POST['role'] ?? 'member';
            $status = $_POST['status'] ?? 'active';
            
            // Als er geen fouten zijn, gebruiker bijwerken
            if (empty($errors)) {
                // Bouw de updatequery
                $query = "UPDATE users SET 
                          username = ?,
                          email = ?,
                          display_name = ?,
                          role = ?,
                          status = ?,
                          updated_at = NOW()";
                
                $params = [$username, $email, $display_name, $role, $status];
                
                // Als wachtwoord moet worden bijgewerkt, voeg het toe aan de query
                if ($update_password) {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $query .= ", password = ?";
                    $params[] = $hashed_password;
                }
                
                // Voeg WHERE-clausule toe
                $query .= " WHERE id = ?";
                $params[] = $id;
                
                // Voer query uit
                $success = $db->execute($query, $params);
                
                if ($success) {
                    // Succes bericht in sessie opslaan
                    $_SESSION['success_message'] = "Gebruiker {$username} is succesvol bijgewerkt.";
                    
                    // Haal de bijgewerkte gebruiker op
                    $user = $db->fetch("SELECT * FROM users WHERE id = ?", [$id]);
                    
                    // Als de bewerking succesvol is, toon bericht in de huidige view
                    $success = true;
                } else {
                    $errors[] = "Er is een fout opgetreden bij het bijwerken van de gebruiker.";
                }
            }
        }
    } catch (\Exception $e) {
        $errors[] = "Database fout: " . $e->getMessage();
        
        // Als er een database fout is, gebruik placeholder data
        if (!isset($user)) {
            $user = [
                'id' => $id,
                'username' => 'user' . $id,
                'email' => 'user' . $id . '@example.com',
                'display_name' => 'User ' . $id,
                'role' => 'member',
                'status' => 'active',
                'created_at' => '2025-01-01'
            ];
        }
    }
    
    $data = [
        'user' => $user,
        'title' => 'Gebruiker Bewerken',
        'contentView' => BASE_PATH . '/app/Views/admin/users/edit.php',
        'errors' => $errors,
        'success' => $success
    ];
    
    return $this->view('admin/layout', $data);
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