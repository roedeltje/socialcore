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
                    $success = $db->query(
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
        header('Location: ' . base_url('admin/users'));
        exit;
    }
    
    try {
        $db = \App\Database\Database::getInstance();
        
        // Haal gebruiker op uit de database
        $user = $db->fetch("SELECT * FROM users WHERE id = ?", [$id]);
        
        if (!$user) {
            $_SESSION['error_message'] = "Gebruiker niet gevonden.";
            header('Location: ' . base_url('admin/users'));
            exit;
        }
        
        // Maak een nieuwe FormHelper met de gebruikersgegevens
        $form = new \App\Helpers\FormHelper($user);
        
        // Stel validatieregels in
        $form->addRule('username', 'required')
             ->addRule('username', 'min', 3)
             ->addRule('username', 'regex', '/^[a-zA-Z0-9_]+$/', "Gebruikersnaam mag alleen letters, cijfers en underscores bevatten.")
             ->addRule('username', 'unique', [$db, 'users', 'username', $id], "Deze gebruikersnaam is al in gebruik.")
             ->addRule('email', 'required')
             ->addRule('email', 'email')
             ->addRule('email', 'unique', [$db, 'users', 'email', $id], "Dit e-mailadres is al in gebruik.");
        
        // Als wachtwoord is ingevuld, valideer het
        if (!empty($_POST['password'])) {
            $form->addRule('password', 'min', 8, "Wachtwoord moet minimaal 8 tekens bevatten.")
                 ->addRule('password', 'matches', 'password_confirm', "Wachtwoorden komen niet overeen.");
        }
        
        $success = false;
        
        // Verwerk het formulier als dit een POST verzoek is
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($form->validate($_POST)) {
                // Bouw de updatequery
                $query = "UPDATE users SET 
                          username = ?,
                          email = ?,
                          display_name = ?,
                          role = ?,
                          status = ?,
                          updated_at = NOW()";
                
                $formValues = $form->getValues();
                $params = [
                    $formValues['username'], 
                    $formValues['email'], 
                    $formValues['display_name'] ?? $formValues['username'], 
                    $formValues['role'], 
                    $formValues['status']
                ];
                
                // Als wachtwoord moet worden bijgewerkt, voeg het toe aan de query
                if (!empty($formValues['password'])) {
                    $hashed_password = password_hash($formValues['password'], PASSWORD_DEFAULT);
                    $query .= ", password = ?";
                    $params[] = $hashed_password;
                }
                
                // Voeg WHERE-clausule toe
                $query .= " WHERE id = ?";
                $params[] = $id;
                
                // Voer query uit
                $success = $db->query($query, $params);
                
                if ($success) {
                    // Succes bericht in sessie opslaan
                    $_SESSION['success_message'] = "Gebruiker {$formValues['username']} is succesvol bijgewerkt.";
                    
                    // Haal de bijgewerkte gebruiker op en update de form values
                    $user = $db->fetch("SELECT * FROM users WHERE id = ?", [$id]);
                    $form->setValues($user);
                } else {
                    $form->addError("Er is een fout opgetreden bij het bijwerken van de gebruiker.");
                }
            }
        }
    } catch (\Exception $e) {
        if (isset($form)) {
            $form->addError("Database fout: " . $e->getMessage());
        } else {
            // Als er een database fout is bij het ophalen van de gebruiker
            $user = [
                'id' => $id,
                'username' => 'user' . $id,
                'email' => 'user' . $id . '@example.com',
                'display_name' => 'User ' . $id,
                'role' => 'member',
                'status' => 'active',
                'created_at' => '2025-01-01'
            ];
            $form = new \App\Helpers\FormHelper($user);
            $form->addError("Database fout: " . $e->getMessage());
        }
    }
    
    $data = [
        'user' => $user,
        'form' => $form,
        'title' => 'Gebruiker Bewerken',
        'contentView' => BASE_PATH . '/app/Views/admin/users/edit.php',
        'success' => $success ?? false
    ];
    
    return $this->view('admin/layout', $data);
}
    
        /**
     * Verwijder een gebruiker
     */
    public function delete()
{
    // Haal gebruikers-ID op uit de query parameters
    $userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    // Controleer of het ID geldig is
    if (empty($userId)) {
        $_SESSION['error_message'] = 'Ongeldige gebruiker-ID';
        header('Location: ' . base_url('?route=admin/users'));
        exit;
    }
    
    // Controleer of de gebruiker niet zichzelf probeert te verwijderen
    if ($userId == $_SESSION['user_id']) {
        $_SESSION['error_message'] = 'Je kunt je eigen account niet verwijderen';
        header('Location: ' . base_url('?route=admin/users'));
        exit;
    }
    
    // Haal de database verbinding
    $db = Database::getInstance();
    
    // Controleer of de gebruiker bestaat
    $user = $db->fetch("SELECT * FROM users WHERE id = ?", [$userId]);
    
    if (!$user) {
        $_SESSION['error_message'] = 'Gebruiker niet gevonden';
        header('Location: ' . base_url('?route=admin/users'));
        exit;
    }
    
    // Verwijder de gebruiker
    try {
        // Als de user_profiles tabel een foreign key heeft met ON DELETE CASCADE,
        // dan zal het profiel automatisch worden verwijderd
        $db->query("DELETE FROM users WHERE id = ?", [$userId]);
        $_SESSION['success_message'] = 'Gebruiker succesvol verwijderd';
    } catch (\PDOException $e) {
        // Log de fout (in een productie-omgeving)
        // error_log($e->getMessage());
        $_SESSION['error_message'] = 'Er is een fout opgetreden bij het verwijderen van de gebruiker';
    }
    
    // Stuur terug naar gebruikersoverzicht
    header('Location: ' . base_url('?route=admin/users'));
    exit;
}
}