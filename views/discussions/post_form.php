<?php
// 启动会话
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$userId = $_SESSION['user_id'] ?? null;

// 如果未登录，重定向到登录页面
if (!$userId) {
    header("Location: ../../login.php?error=" . urlencode("请先登录"));
    exit();
}

// 检查是否有错误消息
$error = isset($_GET['error']) ? $_GET['error'] : '';

require_once '../../controllers/DiscussionController.php';
require_once '../../models/Discussion.php';

global $con;
$controller = new DiscussionController($con);
$controller->addPost();

// 引入头部
require_once(__DIR__ . "/../../views/navi/header.php");
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h2>发布新讨论</h2>
                </div>
                
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    
                    <form method="post" action="post_form.php">
                        <input type="hidden" name="action" value="add_post">
                        
                        <div class="form-group mb-3">
                            <label for="title">标题</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="content">内容</label>
                            <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
                            <small class="form-text text-muted">请详细描述您想讨论的内容</small>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">发布讨论</button>
                            <a href="all_posts.php" class="btn btn-secondary ml-2">取消</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once(__DIR__ . "/../../views/navi/footer.php"); ?>