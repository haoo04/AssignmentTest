<?php
include '../header.php' ;
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$userId = $_SESSION['user_id'] ?? null;
$recipeId = isset($recipeId) ? $recipeId : (isset($_GET['id']) ? $_GET['id'] : 0);

require_once(__DIR__ . "/../../controllers/CommentController.php");
$controller = new CommentController($con);
$comments = $controller->showComments($recipeId);
$controller->deleteComment($commentIs,$recipeId,$userId);

?>

<div class="comments-section">
    <h3>Comments (<?php echo count($comments); ?>)</h3>
    
    <?php if (empty($comments)): ?>
        <p>There are no comments yet. Be the first to comment!</p>
    <?php else: ?>
        <?php foreach ($comments as $comment): ?>
            <div class="comment-item" id="comment-<?php echo $comment['comment_id']; ?>">
                <div class="comment-header">
                    <div class="user-info">
                        <?php if (!empty($comment['profile_image'])): ?>
                            <img src="<?php echo htmlspecialchars($comment['profile_image']); ?>" alt="User Profile" class="profile-image">
                        <?php else: ?>
                            <div class="profile-placeholder"><?php echo substr($comment['username'], 0, 1); ?></div>
                        <?php endif; ?>
                        <span class="username"><?php echo htmlspecialchars($comment['username']); ?></span>
                    </div>
                    
                    <div class="comment-meta">
                        <div class="rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="star <?php echo ($i <= $comment['rating']) ? 'filled' : ''; ?>">★</span>
                            <?php endfor; ?>
                            <span class="rating-text"><?php echo $comment['rating']; ?>/5</span>
                        </div>
                        <span class="date"><?php echo date('Y-m-d H:i', strtotime($comment['created_at'])); ?></span>
                    </div>
                </div>
                
                <div class="comment-content">
                    <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                </div>
                
                <?php if ($currentUserId == $comment['user_id']): ?>
                    <div class="comment-actions">
                        <a href="edit_comment.php?id=<?php echo $comment['comment_id']; ?>&recipe_id=<?php echo $recipeId; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="../controllers/process_comment_delete.php" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this comment?');">
                            <input type="hidden" name="comment_id" value="<?php echo $comment['comment_id']; ?>">
                            <input type="hidden" name="recipe_id" value="<?php echo $recipeId; ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
    .comments-section {
        margin: 30px 0;
    }
    
    .comment-item {
        padding: 15px;
        border-bottom: 1px solid #eee;
        margin-bottom: 15px;
    }
    
    .comment-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    
    .user-info {
        display: flex;
        align-items: center;
    }
    
    .profile-image, .profile-placeholder {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
    }
    
    .profile-placeholder {
        background-color: #007bff;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    
    .username {
        font-weight: bold;
    }
    
    .comment-meta {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }
    
    .rating {
        margin-bottom: 5px;
    }
    
    .star {
        color: #ddd;
    }
    
    .star.filled {
        color: #ffd700;
    }
    
    .date {
        font-size: 0.8em;
        color: #777;
    }
    
    .comment-content {
        margin-bottom: 10px;
    }
    
    .comment-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
</style>