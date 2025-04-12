<?php
require_once(__DIR__ . "/../config.php");

class Comment
{
    private $con;

    public function __construct($con)
    {
        $this->con = $con;
    }

    // 添加评论和评分
    public function addComment($recipeId, $userId, $content, $rating){
        if (!$this->con) {
            die("Database connection is missing.");
        }

        $recipeId = (int)$recipeId;
        $userId = (int)$userId;
        $rating = (int)$rating;
        $content = mysqli_real_escape_string($this->con, $content);

        $query = "INSERT INTO comments (recipe_id, user_id, content, rating) 
                      VALUES ('$recipeId', '$userId', '$content', '$rating')";

        if (!mysqli_query($this->con, $query)) {
            die("Insert query failed:" . mysqli_error($this->con));
        }
        return true;
    }

    // 获取食谱的所有评论
    public function getAllComment($recipeId){
        if (!$this->con) {
            die("Database connection is missing.");
        }

        $query = "SELECT c.*, u.username, u.profile_image 
                      FROM comments c 
                      JOIN users u ON c.user_id = u.user_id 
                      WHERE c.recipe_id = $recipeId 
                      ORDER BY c.created_at DESC";
        $result = mysqli_query($this->con, $query);

        if (!$result) {
            die("Database query failed:" . mysqli_error($this->con));
        }
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function getComment($commentId){
        if (!$this->con) {
            die("Database connection is missing.");
        }
        $commentId = (int) $commentId;

        $query = "SELECT c.*, r.recipe_id FROM comments c 
        JOIN recipes r ON c.recipe_id = r.recipe_id
        WHERE c.comment_id = $commentId";

        $result = mysqli_query($this->con, $query);

        if (!$result || mysqli_num_rows($result) === 0) {
            return null;
        }
        return mysqli_fetch_assoc($result);
    }

    public function deleteComment($commentId, $userId)
    {
        if (!$this->con) {
            die("Database connection is missing.");
        }

        $commentId = (int) $commentId;
        $userId = (int) $userId;

        $query = "SELECT user_id FROM comments WHERE comment_id = $commentId";
        $result = mysqli_query($this->con, $query);

        if (!$result || mysqli_num_rows($result) === 0) {
            return false;
        }

        $comment = mysqli_fetch_assoc($result);
        if ($comment['user_id'] != $userId) {
            return false; 
        }

        $deleteQuery = "DELETE FROM comments WHERE comment_id = $commentId";
        return mysqli_query($this->con, $deleteQuery);
    }

    public function updateComment($commentId, $userId, $content, $rating)
    {
        if (!$this->con) {
            die("Database connection is missing.");
        }

        $commentId = (int) $commentId;
        $userId = (int) $userId;
        $content = mysqli_real_escape_string($this->con, $content);
        $rating = (int) $rating;

        $query = "SELECT user_id FROM comments WHERE comment_id = $commentId";
        $result = mysqli_query($this->con, $query);

        if (!$result || mysqli_num_rows($result) === 0) {
            return false;
        }

        $comment = mysqli_fetch_assoc($result);
        if ($comment['user_id'] != $userId) {
            return false; 
        }

        $updateQuery = "UPDATE comments SET content = '$content', rating = '$rating' 
                   updated_at = NOW() WHERE comment_id = $commentId";
        return mysqli_query($this->con, $updateQuery);
    }

    // 计算食谱的平均评分
    public function getAverageRating($recipeId){
        if (!$this->con) {
            die("Database connection is missing.");
        }

        $query = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_ratings 
                      FROM comments 
                      WHERE recipe_id = $recipeId AND rating IS NOT NULL";
        $result = mysqli_query($this->con,$query);

        if (!$result) {
            die("Database query failed:" . mysqli_error($this->con));
        }
        return mysqli_fetch_all($result, MYSQL_ASSOC);
    }
}
?>