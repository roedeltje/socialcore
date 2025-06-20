<?php
namespace App\Controllers;

use App\Database\Database;

class CommentsController extends Controller
{
    public function delete()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }
        
        $commentId = $_POST['comment_id'] ?? null;
        
        if (!$commentId) {
            echo json_encode(['success' => false, 'message' => 'Comment ID required']);
            return;
        }
        
        try {
            $db = Database::getInstance()->getPdo();
            
            // Verwijder comment (soft delete)
            $stmt = $db->prepare("UPDATE post_comments SET is_deleted = 1 WHERE id = ?");
            $result = $stmt->execute([$commentId]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Comment deleted']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete comment']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }
}