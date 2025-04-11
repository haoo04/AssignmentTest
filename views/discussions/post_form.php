<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$userId = $_SESSION['user_id'] ?? null;

// If not logged in, redirect to the login page
if (!$userId) {
    header("Location: ../../login.php?error=" . urlencode("请先登录"));
    exit();
}

// Check for erroe message
$error = isset($_GET['error']) ? $_GET['error'] : '';

require_once '../../controllers/DiscussionController.php';
require_once '../../models/Discussion.php';

// Handle Request
global $con;
$controller = new DiscussionController($con);
$controller->addPost();

require_once(__DIR__ . "/../../views/navi/header.php");
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h2>Upload New Post</h2>
                </div>
                
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    
                    <form method="post" action="post_form.php">
                        <input type="hidden" name="action" value="add_post">
                        
                        <div class="form-group mb-3">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="content">Content</label>
                            <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
                            <small class="form-text text-muted">Please Write Your Post Content</small>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Upload</button>
                            <a href="all_posts.php" class="btn btn-secondary ml-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once(__DIR__ . "/../../views/navi/footer.php"); ?>