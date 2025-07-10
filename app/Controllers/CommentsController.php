<?php
namespace App\Controllers;

use App\Database\Database;
use App\Services\CommentService;
use Exception;

class CommentsController extends Controller
{
    /**
     * Verwijder een comment via CommentService
     * Alle logica gemigreerd naar CommentService
     */
    public function delete()
    {
        // 🔍 DEBUG: Log method call
        file_put_contents('/var/www/socialcore.local/debug/comments_controller_' . date('Y-m-d') . '.log', 
            "[" . date('Y-m-d H:i:s') . "] CommentsController::delete() calling CommentService\n", 
            FILE_APPEND | LOCK_EX);

        header('Content-Type: application/json');
        
        // Check login
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Je moet ingelogd zijn']);
            exit;
        }
        
        // Check POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        $commentId = $_POST['comment_id'] ?? null;
        $userId = $_SESSION['user_id'];
        $isAdmin = ($_SESSION['role'] ?? 'user') === 'admin';
        
        if (!$commentId) {
            echo json_encode(['success' => false, 'message' => 'Comment ID required']);
            exit;
        }
        
        // 🚀 ALLE LOGICA IN COMMENTSERVICE
        $commentService = new CommentService();
        $result = $commentService->deleteComment($commentId, $userId, $isAdmin);
        
        echo json_encode($result);
        exit;
    }
}