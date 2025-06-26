<?php
require_once(__DIR__ . "../../config.php");
require_once(__DIR__ . "../../models/Discussion.php");


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

    public function showReply($replyId)
    {
        $replyData = $this->discussion->getReply($replyId);
        return $replyData;
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

    // Handling delet posts
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

    // Handling delete reply
    public function deleteReply($replyId, $postId, $userId)
    {
        if ($userId) {
            if ($this->discussion->deleteReply($replyId, $userId)) {
                header("Location: post_detail.php?id=$postId&success=1");
                exit();
            } else {
                header("Location: post_detail.php?id=$postId&error=" . urlencode("Failed to delete reply"));
                exit();
            }
        } else {
            $this->error('../views/discussions/all_posts.php', 'Failed to delete the reply. Maybe you are not the author of the reply.');
            return false;
        }
    }

    public function updatePost()
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $this->error('login.php', 'Please Login');
            return;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $postId = (int) $_POST['post_id'];
            $title = $_POST['title'];
            $content = $_POST['content'];

            if ($this->discussion->updatePost($postId, $userId, $title, $content)) {
                header("Location: ../discussions/post_detail.php?id=" . $postId . "&updated=1");
                exit();
            } else {
                $this->error('../discussions/edit_post.php?id=' . $postId, 'Update failed, maybe you are not the post author');
            }
        }
    }

    // Handling update replies
    public function updateReply()
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $this->error('login.php', 'Please Login');
            return;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $replyId = (int) $_POST['reply_id'];
            $postId = (int) $_POST['post_id'];
            $content = $_POST['content'];

            if ($this->discussion->updateReply($replyId, $userId, $content)) {
                header("Location: ../discussions/post_detail.php?id=" . $postId . "&updated=1");
                exit();
            } else {
                $this->error('../discussions/edit_reply.php?id=' . $replyId, 'Update failed, maybe you are not the author');
            }
        }
    }

    // Show add post form
    public function showAddPostForm()
    {
        include(__DIR__ . "/../views/discussions/post_form.php");
    }

    // Display the edit post form
    public function showEditPostForm($postId)
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $this->error('login.php', 'Please Login');
            return;
        }

        $postData = $this->discussion->getPostDetail($postId);

        if (!$postData) {
            $this->error('../index.php', 'The post was not found');
            return;
        }

        if ($postData['user_id'] != $userId) {
            $this->error('../index.php', 'You do not have permission to edit this post');
            return;
        }

        include(__DIR__ . "/../views/discussions/edit_post.php");
    }

    // Show edit reply form
    public function showEditReplyForm($replyId)
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $this->error('login.php', 'Please Login');
            return;
        }

        $replyData = $this->discussion->getReply($replyId);

        if ($replyData['user_id'] != $userId) {
            $this->error('../index.php', 'You do not have permission to edit this reply');
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