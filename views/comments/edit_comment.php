<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId = $_SESSION['user_id'] ?? null;

// If not logged in, redirect to the login page
if (!$userId) {
    header("Location: ../auth/login.php?error=" . urlencode("Please Login"));
    exit();
}

// get the commentId and recipeId
$commentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$recipeId = isset($_GET['recipe_id']) ? (int)$_GET['recipe_id'] : 0;

if ($commentId === 0 || $recipeId === 0) {
    header("Location: ../index.php?error=" . urlencode("comment or recipe not found"));
    exit;
}

require_once(__DIR__ . "/../../controllers/DiscussionController.php");
$controller = new $commentController($con);
$commentData = $controller->showCommentDetail($commentId);
$controller->updateComment();

// Check if the comment exists and if the current user is the comment author
if (!$comment || $comment['user_id'] != $_SESSION['user_id']) {
    header("Location: recipe_detail.php?id=" . $recipeId . "&error=" . urlencode("You do not have permission to edit this comment"));
    exit;
}
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h2>Edit Comment</h2>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
                    <?php endif; ?>
                    
                    <form method="post" action="edit_comment.php?id=<?=$commentId?>">
                        <input type="hidden" name="comment_id" value="<?php echo $comment['comment_id']; ?>">
                        <input type="hidden" name="recipe_id" value="<?php echo $comment['recipe_id']; ?>">
                        
                        <div class="form-group">
                            <label for="rating">Rating：</label>
                            <div class="rating-input">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" 
                                       <?php echo ($i == $comment['rating']) ? 'checked' : ''; ?>>
                                <label for="star<?php echo $i; ?>"><?php echo $i; ?>Star</label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="content">Comment Content：</label>
                            <textarea name="content" id="content" class="form-control" rows="4" required><?php echo htmlspecialchars($comment['content']); ?></textarea>
                        </div>
                        
                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">Update Comment</button>
                            <a href="recipe_detail.php?id=<?php echo $comment['recipe_id']; ?>" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .rating-input {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
    }
    
    .rating-input input {
        display: none;
    }
    
    .rating-input label {
        cursor: pointer;
        padding: 5px;
        font-size: 20px;
        color: #ddd;
    }
    
    .rating-input label:hover,
    .rating-input label:hover ~ label,
    .rating-input input:checked ~ label {
        color: #ffd700;
    }
</style>
