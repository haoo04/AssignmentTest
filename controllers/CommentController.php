<?php
require_once(__DIR__ . "/../config.php");
require_once(__DIR__ . "/../models/Comment.php");

global $con;
class CommentController
{
    private $comment;
    public function __construct($con)
    {
        $this->comment = new Comment($con);
    }

    public function showComments($recipeId)
    {
        $commentsData = $this->comment->getAllComment($recipeId);
        return $commentsData;

    }

    public function showCommentDetail($commentId){
        $commentDetail = $this->comment->getComment($commentId);
        return $commentDetail;
    }

    public function showAddCommentForm()
    {
        include(__DIR__ . "/../views/comments/comment_form.php");
    }

    public function addComment($recipeId)
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            $this->error('login.php', 'Please Login');
            return false;
        }

        $content = $_POST['content'];
        $rating = $_POST['rating'];

        if (empty($content)) {
            $this->error("", 'The comment content cannot be empty'); //need insert link
            return false;
        }
        if ($this->comment->addComment($recipeId, $userId, $content, $rating)) {
            header("Location: ../discussions/post_detail.php?id=$recipeId&success=1"); //need insert link
            exit();
        } else {
            $this->error("../discussions/post_detail.php?id=$recipeId", 'Reply failed');
            return false;
        }

    }

    // Handling update replies
    public function updateComment()
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $this->error('login.php', 'Please Login');
            return;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $commentId = (int) $_POST['comment_id'];
            $recipeId = (int) $_POST['recipe_id'];
            $content = $_POST['content'];
            $rating = (int) $_POST['rating'];

            if ($this->comment->updateComment(    $commentId,$userId,$content,$rating)) {
                header("Location: ../discussions/post_detail.php?id=" . $recipeId . "&updated=1");
                exit();
            } else {
                $this->error('../discussions/edit_reply.php?id=' . $commentId, 'Update failed, maybe you are not the author');
            }
        }
    }

    // Handling delete comment
    public function deleteComment($commentId, $recipeId, $userId)
    {
        if ($userId) {
            if ($this->comment->deleteComment($commentId, $userId)) {
                header("Location: post_detail.php?id=$recipeId&success=1"); //need insert link
                exit();
            } else {
                header("Location: post_detail.php?id=$recipeId&error=" . urlencode("Failed to delete comment")); //need insert link
                exit();
            }
        } else {
            $this->error('../views/discussions/all_posts.php', 'Failed to delete the reply. Maybe you are not the author of the reply.');
            return false;
        }
    }

    private function error($url, $errorMessage)
    {
        header("Location: {$url}?error=" . urlencode($errorMessage));
        exit;
    }
}
?>