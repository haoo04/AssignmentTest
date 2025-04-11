<?php
require_once(__DIR__ . "/../config.php");

class Discussion
{
    private $con;

    public function __construct($con)
    {
        $this->con = $con;
    }

    // Create a discussion post
    public function createPost($userId, $title, $content)
    {
        if (!$this->con) {
            die("Database connection is missing.");
        }

        $title = mysqli_real_escape_string($this->con, $title);
        $content = mysqli_real_escape_string($this->con, $content);

        $query = "INSERT INTO discussion_posts (user_id, title, content) 
                  VALUES ('$userId', '$title', '$content')";

        if (!mysqli_query($this->con, $query)) {
            die("Insert query failed:" . mysqli_error($this->con));
        }
        return true;
    }

    // Get discussion posts
    public function getPost()
    {
        if (!$this->con) {
            die("Database connection is missing.");
        }

        $query = "SELECT * FROM discussion_posts 
                    ORDER BY created_at DESC
                    LIMIT 100";
        $result = mysqli_query($this->con, $query);

        if (!$result) {
            die("Database query failed:" . mysqli_error($this->con));
        }
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // Show Post detail
    public function getPostDetail($postId)
    {
        if (!$this->con) {
            die("Database connection is missing.");
        }

        $postId = (int) $postId;
        $query = "SELECT p.*, u.username, u.profile_image 
                  FROM discussion_posts p
                  JOIN users u ON p.user_id = u.user_id
                  WHERE p.post_id = $postId";

        $result = mysqli_query($this->con, $query);

        if (!$result || mysqli_num_rows($result) === 0) {
            return null;
        }

        return mysqli_fetch_assoc($result);

    }

    // Delete discussin Post
    public function deletePost($postId, $userId)
    {
        if (!$this->con) {
            die("Database connection is missing.");
        }
        $postId = (int)$postId;
        $userId = (int)$userId;
    
        // Verify that the user is the post author
        $query = "SELECT user_id FROM discussion_posts WHERE post_id = $postId";
        $result = mysqli_query($this->con, $query);
    
        if (!$result || mysqli_num_rows($result) === 0) {
            return false;
        }
    
        $post = mysqli_fetch_assoc($result);
        if ($post['user_id'] != $userId) {
            return false;
        }
    
        // Delete all replies before delte post
        $deleteRepliesQuery = "DELETE FROM post_replies WHERE post_id = $postId";
        mysqli_query($this->con, $deleteRepliesQuery);
    
        // Delete the post
        $deletePostQuery = "DELETE FROM discussion_posts WHERE post_id = $postId";
        return mysqli_query($this->con, $deletePostQuery);
    }

    // Reply to discussion post
    public function replyToPost($postId, $userId, $content)
    {
        if (!$this->con) {
            die("Database connection is missing.");
        }

        $postId = (int)$postId;
        $userId = (int)$userId;
        $content = mysqli_real_escape_string($this->con, trim($content));

        $query = "INSERT INTO post_replies (post_id, user_id, content) 
                  VALUES ('$postId', '$userId', '$content')";

        if (!mysqli_query($this->con, $query)) {
            die("Insert query failed:" . mysqli_error($this->con));
        }
        return true;
    }

    public function getAllReply($postId)
    {
        if (!$this->con) {
            die("Database connection is missing.");
        }

        $query = "SELECT r.*, u.username, u.profile_image 
                      FROM post_replies r 
                      JOIN users u ON r.user_id = u.user_id 
                      WHERE r.post_id = $postId 
                      ORDER BY r.created_at DESC";
        $result = mysqli_query($this->con, $query);

        if (!$result) {
            die("Database query failed:" . mysqli_error($this->con));
        }
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function getReply($replyId){
        if (!$this->con) {
            die("Database connection is missing.");
        }
        $replyId = (int) $replyId;

        $query = "SELECT r.*, p.post_id FROM post_replies r 
        JOIN discussion_posts p ON r.post_id = p.post_id 
        WHERE r.reply_id = $replyId";

        $result = mysqli_query($this->con, $query);

        if (!$result || mysqli_num_rows($result) === 0) {
            return null;
        }
        return mysqli_fetch_assoc($result);
    }

    public function deleteReply($replyId, $userId)
    {
        if (!$this->con) {
            die("Database connection is missing.");
        }

        $replyId = (int) $replyId;
        $userId = (int) $userId;

        $query = "SELECT user_id FROM post_replies WHERE reply_id = $replyId";
        $result = mysqli_query($this->con, $query);

        if (!$result || mysqli_num_rows($result) === 0) {
            return false;
        }

        $reply = mysqli_fetch_assoc($result);
        if ($reply['user_id'] != $userId) {
            return false; 
        }

        $deleteQuery = "DELETE FROM post_replies WHERE reply_id = $replyId";
        return mysqli_query($this->con, $deleteQuery);
    }

    // Update discussion post
    public function updatePost($postId, $userId, $title, $content)
    {
        if (!$this->con) {
            die("Database connection is missing.");
        }

        $postId = (int) $postId;
        $userId = (int) $userId;
        $title = mysqli_real_escape_string($this->con, $title);
        $content = mysqli_real_escape_string($this->con, $content);

        $query = "SELECT user_id FROM discussion_posts WHERE post_id = $postId";
        $result = mysqli_query($this->con, $query);

        if (!$result || mysqli_num_rows($result) === 0) {
            return false;
        }

        $post = mysqli_fetch_assoc($result);
        if ($post['user_id'] != $userId) {
            return false; 
        }

        $updateQuery = "UPDATE discussion_posts SET title = '$title', content = '$content', 
                   updated_at = NOW() WHERE post_id = $postId";
        return mysqli_query($this->con, $updateQuery);
    }

    // Update reply comment
    public function updateReply($replyId, $userId, $content)
    {
        if (!$this->con) {
            die("Database connection is missing.");
        }

        $replyId = (int) $replyId;
        $userId = (int) $userId;
        $content = mysqli_real_escape_string($this->con, $content);

        $query = "SELECT user_id FROM post_replies WHERE reply_id = $replyId";
        $result = mysqli_query($this->con, $query);

        if (!$result || mysqli_num_rows($result) === 0) {
            return false;
        }

        $reply = mysqli_fetch_assoc($result);
        if ($reply['user_id'] != $userId) {
            return false; 
        }

        $updateQuery = "UPDATE post_replies SET content = '$content', 
                   updated_at = NOW() WHERE reply_id = $replyId";
        return mysqli_query($this->con, $updateQuery);
    }

}
?>