<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId = $_SESSION['user_id'] ?? null;

// If not logged in, redirect to the login page
if (!$userId) {
    header("Location: ../auth/login.php?error=" . urlencode("请先登录"));
    exit();
}

//
$replyId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// If not reply id, redirect to the all_post page
if ($replyId === 0) {
    header("Location: all_posts.php");
    exit();
}

$success = isset($_GET['success']) ? true : false;
$updated = isset($_GET['updated']) ? true : false;
$error = isset($_GET['error']) ? $_GET['error'] : null;

require_once(__DIR__ . "/../../controllers/DiscussionController.php");
$controller = new DiscussionController($con);
$replyData = $controller->showReply($replyId);
$postId = $replyData['post_id'];
$postData = $controller->showPost($postId);
$controller->updateReply();

// Check if the current user is the reply author
if ($replyData['user_id'] != $userId) {
    header("Location: post_detail.php?id=" . $replyData['post_id'] . "&error=" . urlencode("You do not have permission to edit this reply"));
    exit();
}

require_once(__DIR__ . "/../../views/navi/header.php");
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h2>Edit Reply</h2>
                    <p class="text-muted">
                        Reply At: <a href="post_detail.php?id=<?= $postId ?>"><?= htmlspecialchars($postData['title']) ?></a>
                    </p>
                </div>
                
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    
                    <form method="post" action="edit_reply.php?id=<?=$replyId?>">
                        <input type="hidden" name="action" value="update_reply">
                        <input type="hidden" name="reply_id" value="<?= $replyId ?>">
                        <input type="hidden" name="post_id" value="<?= $replyData['post_id'] ?>">
                        
                        <div class="form-group mb-3">
                            <label for="content">Content</label>
                            <textarea class="form-control" id="content" name="content" rows="5" required><?= htmlspecialchars($replyData['content']) ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="post_detail.php?id=<?= $replyData['post_id'] ?>" class="btn btn-secondary ml-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once(__DIR__ . "/../../views/navi/footer.php"); ?>