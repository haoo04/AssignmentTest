<?php
require_once(__DIR__ . "/../config.php");
require_once(__DIR__ . "/../models/Discussion.php");

global $con;
class DiscussionController
{
    private $discussion;
    public function __construct($con)
    {
        $this->discussion = new Discussion($con);
    }

    // Show all plost 
    public function showAllPosts()
    {
        $postsData = $this->discussion->getPost();
        return $postsData;
    }

    public function showAllReplies($postId)
    {
        $repliesData = $this->discussion->getAllReply($postId);
        return $repliesData;
    }


    // show post detail
    public function showPost($postId)
    {

        $postData = $this->discussion->getPostDetail($postId);
        return $postData;
    }

    // Handling form submissions for adding comments
    public function addPost()
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $this->error('login.php', 'Please Login');
            return;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $title = $_POST['title'];
            $content = $_POST['content'];

            if ($this->discussion->createPost($userId, $title, $content)) {
                header("Location:../discussions/all_posts.php");
                exit();
            }
        }
    }

    public function addReply($postId)
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            $this->error('login.php', 'Please Login');
            return false;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $content = $_POST['content'];

            if (empty($content)) {
                $this->error("../discussions/post_detail.php?id=$postId", 'The reply content cannot be empty');
                return false;
            }
            if ($this->discussion->replyToPost($postId,$userId,  $content)) {
                header("Location: ../discussions/post_detail.php?id=$postId&success=1");
                exit();
            }else {
                $this->error("../discussions/post_detail.php?id=$postId", 'Reply failed');
                return false;
            }
        }
    }

    // Handling deleted posts
    public function deletePost($postId, $userId)
    {
        if ($userId) {
            if ($this->discussion->deletePost($postId, $userId)) {
                header("Location: all_posts.php?success=1");
                exit();
            } else {
                header("Location: post_detail.php?id=$postId&error=" . urlencode("Deletion failed"));
                exit();
            }
        }
    }

    public function deleteReply($replyId, $postId, $userId)
    {
        if ($userId) {
            if ($this->discussion->deleteReply($replyId, $userId)) {
                header("Location: post_detail.php?id=$postId&success=1");
                exit();
            } else {
                header("Location: post_detail.php?id=$postId&error=" . urlencode("删除回复失败"));
                exit();
            }
        } else {
            $this->error('../views/discussions/all_posts.php', 'Failed to delete the reply. Maybe you are not the author of the reply.');
            return false;
        }
    }

    // 处理更新回复
    public function updateReply()
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $this->error('login.php', '请先登录');
            return;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_reply'])) {
            $replyId = (int) $_POST['reply_id'];
            $postId = (int) $_POST['post_id'];
            $content = $_POST['content'];

            if ($this->discussion->updateReply($replyId, $userId, $content)) {
                header("Location: ../views/discussions/post_detail.php?id=" . $postId . "&updated=1");
                exit();
            } else {
                $this->error('../views/discussions/edit_reply.php?id=' . $replyId, '更新失败，可能您不是回复作者');
            }
        }
    }

    // 显示添加评论表单
    public function showAddPostForm()
    {
        $content = isset($_POST['content']) ? trim($_POST['content']) : '';


        include(__DIR__ . "/../views/discussions/post_form.php");
    }

    // 显示编辑帖子表单
    public function showEditPostForm($postId)
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $this->error('login.php', '请先登录');
            return;
        }

        $postData = $this->discussion->getPostDetail($postId);

        if (!$postData) {
            $this->error('../index.php', '未找到该帖子');
            return;
        }

        if ($postData['user_id'] != $userId) {
            $this->error('../index.php', '您无权编辑此帖子');
            return;
        }

        include(__DIR__ . "/../views/discussions/edit_post.php");
    }

    // 显示编辑回复表单
    public function showEditReplyForm($replyId)
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $this->error('login.php', '请先登录');
            return;
        }

        // 获取回复详情
        global $con;
        $replyId = (int) $replyId;
        $query = "SELECT r.*, p.post_id FROM post_replies r 
                  JOIN discussion_posts p ON r.post_id = p.post_id 
                  WHERE r.reply_id = $replyId";
        $result = mysqli_query($con, $query);

        if (!$result || mysqli_num_rows($result) === 0) {
            $this->error('../index.php', '未找到该回复');
            return;
        }

        $replyData = mysqli_fetch_assoc($result);

        if ($replyData['user_id'] != $userId) {
            $this->error('../index.php', '您无权编辑此回复');
            return;
        }

        include(__DIR__ . "/../views/discussions/edit_reply.php");
    }

    private function error($url, $errorMessage)
    {
        header("Location: {$url}?error=" . urlencode($errorMessage));
        exit;
    }

}
?>