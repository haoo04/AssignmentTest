<?php
// 确保已经登录
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId = $_SESSION['user_id'] ?? null;
$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 如果未登录，重定向到登录页面
if (!$userId) {
    header("Location: ../auth/login.php?error=" . urlencode("请先登录"));
    exit();
}

// 如果没有提供帖子ID，重定向到所有帖子页面
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
$discussionController = new DiscussionController($con);

// Get post data
global $con;
$controller = new DiscussionController($con);
$postData = $controller->showPost($postId);
$controller->updatePost();


// 如果帖子不存在，重定向回所有帖子页面
if (!$postData) {
    header("Location: all_posts.php?error=" . urlencode("帖子不存在"));
    exit();
}

// 检查当前用户是否是帖子作者
if ($postData['user_id'] != $userId) {
    header("Location: post_detail.php?id=$postId&error=" . urlencode("您无权编辑此帖子"));
    exit();
}

// Include Header
require_once(__DIR__ . "/../../views/navi/header.php");
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h2>编辑讨论</h2>
                </div>
                
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    
                    <form method="post" action="edit_post.php?id=<?=$postId?>">
                        <input type="hidden" name="action" value="update_post">
                        <input type="hidden" name="post_id" value="<?= $postId ?>">
                        
                        <div class="form-group mb-3">
                            <label for="title">标题</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($postData['title']) ?>" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="content">内容</label>
                            <textarea class="form-control" id="content" name="content" rows="10" required><?= htmlspecialchars($postData['content']) ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">更新讨论</button>
                            <a href="post_detail.php?id=<?= $postId ?>" class="btn btn-secondary ml-2">取消</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once(__DIR__ . "/../../views/navi/footer.php"); ?>