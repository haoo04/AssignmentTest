<?php
require_once(__DIR__ . "/../config.php");
require_once(__DIR__ . "/../models/Comment.php");

global $con;
class CommentController {
    private $comment;
    public function __construct($con) {
        $this->comment = new Comment($con);
    }
    
    // 显示食谱评论列表
    public function showComments() {
        global $con;
        $recipeId = isset($_GET['recipe_id']) ? (int)$_GET['recipe_id'] : 0;

        $commentsData = $this->comment->getComment($recipeId);
        $ratings = $this->comment->getAverageRating($recipeId);

        $comments = [];
        foreach ($commentsData as $commentData) {
            $comments[] = new Comment($commentData);
        }

        include(__DIR__ . "/../views/comments/list.php");
        
    }
    
    // 显示添加评论表单
    public function showAddCommentForm() {
        $recipeId = isset($_GET['recipe_id']) ? (int)$_GET['recipe_id'] : 0;
        $content = isset($_POST['content']) ? trim($_POST['content']) : '';
        $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : null;


        include (__DIR__ . "/../views/comments/form.php");
    }
    
    // 处理添加评论的表单提交
    public function addComment() {
        global $con;
        $userId = $_SESSION['user_id']??null;
        if(!$userId){
            $this->error('login.php', '请先登录');
            return;
        }

        if($_SERVER["REQUEST_METHOD"] === "POST"){
            $recipeId = isset($_POST['recipe_id']) ? (int)$_POST['recipe_id'] : 0;
            $content = isset($_POST['content']) ? trim($_POST['content']) : '';
            $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : null;

            if($this->comment->addComment($recipeId,$userId,$content,$rating)){
                header("Location:recipe_detail.php?id={$recipeId}&message=" . urlencode('Comment Successful'));
                exit();
            }
        }
    }

    private function error($url, $errorMessage) {
        header("Location: {$url}?error=" . urlencode($errorMessage));
        exit;
    } 
}
if(isset($_POST['submit'])){
    global $con;
    $commentController = new $commentController($con);
    $commentController->addComment();
}
?>