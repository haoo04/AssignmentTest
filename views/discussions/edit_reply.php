<?php
// 确保已经登录
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId = $_SESSION['user_id'] ?? null;

// 如果未登录，重定向到登录页面
if (!$userId) {
    header("Location: ../auth/login.php?error=" . urlencode("请先登录"));
    exit();
}

// 获取回复ID
$replyId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 如果没有提供回复ID，重定向到所有帖子页面
if ($replyId === 0) {
    header("Location: all_posts.php");
    exit();
}

// Check for success or update messages
$success = isset($_GET['success']) ? true : false;
$updated = isset($_GET['updated']) ? true : false;
$error = isset($_GET['error']) ? $_GET['error'] : null;

// 获取回复数据
// Include Controller
require_once(__DIR__ . "/../../controllers/DiscussionController.php");
$controller = new DiscussionController($con);
$replyData = $controller->showReply($replyId);
$controller->updateReply();

// 检查当前用户是否是回复作者
if ($replyData['user_id'] != $userId) {
    header("Location: post_detail.php?id=" . $replyData['post_id'] . "&error=" . urlencode("您无权编辑此回复"));
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
                    <h2>编辑回复</h2>
                    <p class="text-muted">
                        回复于: <a href="post_detail.php?id=<?= $replyData['post_id'] ?>"><?= htmlspecialchars($replyData['post_title']) ?></a>
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
                            <label for="content">回复内容</label>
                            <textarea class="form-control" id="content" name="content" rows="5" required><?= htmlspecialchars($replyData['content']) ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">更新回复</button>
                            <a href="post_detail.php?id=<?= $replyData['post_id'] ?>" class="btn btn-secondary ml-2">取消</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once(__DIR__ . "/../../views/navi/footer.php"); ?>