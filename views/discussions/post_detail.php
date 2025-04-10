<?php
// Make sure user are logged in
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId = $_SESSION['user_id'] ?? null;
$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// If no post ID is provided, redirect to the all posts page
if ($postId === 0) {
    header("Location: all_posts.php");
    exit();
}

// Check for success or update messages
$success = isset($_GET['success']) ? true : false;
$updated = isset($_GET['updated']) ? true : false;
$error = isset($_GET['error']) ? $_GET['error'] : null;

// Include Controller
require_once(__DIR__ . "/../../controllers/DiscussionController.php");

// Get post data and replies
global $con;
$controller = new DiscussionController($con);
$postData = $controller->showPost($postId);
$replies = $controller->showAllReplies($postId);

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'delete_post':
            // Delete Post
            $controller->deletePost($postId,$userId);
            break;
            
        case 'add_reply':
            // Add reply
            $controller->addReply($postId);
            break;
            
        case 'delete_reply':
            // Delete Reply
            $replyId = $_POST['reply_id'];
            $controller->deleteReply($replyId,$postId, $userId);
            break;
    }
}

// If the post does not exist, redirect back to the all posts page
if (!$postData) {
    header("Location: all_posts.php?error=" . urlencode("Post does not exist"));
    exit();
}

// Include Header
require_once(__DIR__ . "/../../views/navi/header.php");
?>

<div class="container mt-4">
    <?php if ($success): ?>
        <div class="alert alert-success">Successful!</div>
    <?php endif; ?>
    
    <?php if ($updated): ?>
        <div class="alert alert-success">Update successful!</div>
    <?php endif; ?>

    <!-- Post Details -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h2><?= htmlspecialchars($postData['title']) ?></h2>
                <small>
                    Author: <?= htmlspecialchars($postData['username']) ?> | 
                    Created At: <?= date('Y-m-d H:i', strtotime($postData['created_at'])) ?>
                    <?php if ($postData['updated_at'] !== $postData['created_at']): ?>
                        | Last Update: <?= date('Y-m-d H:i', strtotime($postData['updated_at'])) ?>
                    <?php endif; ?>
                </small>
            </div>
            
            <?php if ($userId && $userId == $postData['user_id']): ?>
                <div>
                    <a href="edit_post.php?id=<?= $postData['post_id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                    
                    <form method="post" action="post_detail.php?id=<?=$postId?>" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this post?');">
                        <input type="hidden" name="action" value="delete_post">
                        <input type="hidden" name="post_id" value="<?= $postData['post_id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="card-body">
            <div class="post-content">
                <?= nl2br(htmlspecialchars($postData['content'])) ?>
            </div>
        </div>
    </div>
    
    <!-- Reply Form -->
    <?php if ($userId): ?>
        <div class="card mb-4">
            <div class="card-header">Reply</div>
            <div class="card-body">
                <form method="post" action="post_detail.php?id=<?=$postId?>">
                    <input type="hidden" name="action" value="add_reply">
                    <input type="hidden" name="post_id" value="<?= $postId ?>">
                    
                    <div class="form-group">
                        <textarea name="content" class="form-control" rows="3" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary mt-2">Reply</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Please<a href="../auth/login.php">Login</a>to join discussion</div>
    <?php endif; ?>
    
    <!-- Reply List -->
    <h3>Replies (<?= count($replies) ?>)</h3>
    
    <?php if (empty($replies)): ?>
        <div class="alert alert-info">No reply yet</div>
    <?php else: ?>
        <?php foreach ($replies as $reply): ?>
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <?php if (!empty($reply['profile_image'])): ?>
                            <img src="<?= htmlspecialchars($reply['profile_image']) ?>" class="rounded-circle" width="30" height="30" alt="User">
                        <?php endif; ?>
                        <?= htmlspecialchars($reply['username']) ?> | 
                        <?= date('Y-m-d H:i', strtotime($reply['created_at'])) ?>
                        <?php if ($reply['updated_at'] !== $reply['created_at']): ?>
                            | Edited
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($userId && $userId == $reply['user_id']): ?>
                        <div>
                            <a href="edit_reply.php?id=<?= $reply['reply_id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                            
                            <form method="post" action="post_detail.php?id=<?=$postId?>" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this reply?');">
                                <input type="hidden" name="action" value="delete_reply">
                                <input type="hidden" name="reply_id" value="<?= $reply['reply_id'] ?>">
                                <input type="hidden" name="post_id" value="<?= $postId ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="card-body">
                    <?= nl2br(htmlspecialchars($reply['content'])) ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <div class="mt-3 mb-5">
        <a href="all_posts.php" class="btn btn-secondary">Return to discussion list</a>
    </div>
</div>

<?php require_once(__DIR__ . "/../../views/navi/footer.php"); ?>