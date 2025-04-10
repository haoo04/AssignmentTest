<?php
require_once(__DIR__ . "/../config.php");

class Discussion
{
    private $con;

    public function __construct($con)
    {
        $this->con = $con;
    }

    // 创建讨论帖子
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

    // 获取讨论帖子
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

    // 删除讨论帖子
    public function deletePost($postId, $userId)
    {
        if (!$this->con) {
            die("Database connection is missing.");
        }
        $postId = (int)$postId;
        $userId = (int)$userId;
    
        // 验证用户是否是帖子作者
        $query = "SELECT user_id FROM discussion_posts WHERE post_id = $postId";
        $result = mysqli_query($this->con, $query);
    
        if (!$result || mysqli_num_rows($result) === 0) {
            return false;
        }
    
        $post = mysqli_fetch_assoc($result);
        if ($post['user_id'] != $userId) {
            return false;
        }
    
        // 先删除所有回复
        $deleteRepliesQuery = "DELETE FROM post_replies WHERE post_id = $postId";
        mysqli_query($this->con, $deleteRepliesQuery);
    
        // 再删除帖子
        $deletePostQuery = "DELETE FROM discussion_posts WHERE post_id = $postId";
        return mysqli_query($this->con, $deletePostQuery);
    }

    // 回复讨论帖子
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

    public function deleteReply($replyId, $userId)
    {
        if (!$this->con) {
            die("Database connection is missing.");
        }

        $replyId = (int) $replyId;
        $userId = (int) $userId;

        // 验证用户是否是回复作者
        $query = "SELECT user_id FROM post_replies WHERE reply_id = $replyId";
        $result = mysqli_query($this->con, $query);

        if (!$result || mysqli_num_rows($result) === 0) {
            return false;
        }

        $reply = mysqli_fetch_assoc($result);
        if ($reply['user_id'] != $userId) {
            return false; // 用户不是回复作者
        }

        $deleteQuery = "DELETE FROM post_replies WHERE reply_id = $replyId";
        return mysqli_query($this->con, $deleteQuery);
    }

    // 更新讨论帖子
    public function updatePost($postId, $userId, $title, $content)
    {
        if (!$this->con) {
            die("Database connection is missing.");
        }

        $postId = (int) $postId;
        $userId = (int) $userId;
        $title = mysqli_real_escape_string($this->con, $title);
        $content = mysqli_real_escape_string($this->con, $content);

        // 验证用户是否是帖子作者
        $query = "SELECT user_id FROM discussion_posts WHERE post_id = $postId";
        $result = mysqli_query($this->con, $query);

        if (!$result || mysqli_num_rows($result) === 0) {
            return false;
        }

        $post = mysqli_fetch_assoc($result);
        if ($post['user_id'] != $userId) {
            return false; // 用户不是帖子作者
        }

        $updateQuery = "UPDATE discussion_posts SET title = '$title', content = '$content', 
                   updated_at = NOW() WHERE post_id = $postId";
        return mysqli_query($this->con, $updateQuery);
    }

    // 更新回复评论
    public function updateReply($replyId, $userId, $content)
    {
        if (!$this->con) {
            die("Database connection is missing.");
        }

        $replyId = (int) $replyId;
        $userId = (int) $userId;
        $content = mysqli_real_escape_string($this->con, $content);

        // 验证用户是否是回复作者
        $query = "SELECT user_id FROM post_replies WHERE reply_id = $replyId";
        $result = mysqli_query($this->con, $query);

        if (!$result || mysqli_num_rows($result) === 0) {
            return false;
        }

        $reply = mysqli_fetch_assoc($result);
        if ($reply['user_id'] != $userId) {
            return false; // 用户不是回复作者
        }

        $updateQuery = "UPDATE post_replies SET content = '$content', 
                   updated_at = NOW() WHERE reply_id = $replyId";
        return mysqli_query($this->con, $updateQuery);
    }

}
?>