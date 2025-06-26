<?php
include '../header.php' ;
//ensure user login
if (!isset($_SESSION['user_id'])) {
    echo '<div class="alert alert-warning">Please log in before adding comments</div>';
    return;
}

// get the recipe id
$recipeId = isset($recipeId) ? $recipeId : (isset($_GET['id']) ? $_GET['id'] : 0);

require_once '../../controllers/CommentController.php';
require_once '../../models/Comment.php';

// Handle Request
global $con;
$controller = new CommentController($con);
$controller->addComment($recipeId);
?>

<div class="comment-form-container">
    <h3>Add Comment</h3>
    <form action="comment_form.php" method="POST">
        <input type="hidden" name="recipe_id" value="<?php echo $recipeId; ?>">
        
        <div class="form-group">
            <label for="rating">rating：</label>
            <div class="rating-input">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" <?php echo ($i == 5) ? 'checked' : ''; ?>>
                <label for="star<?php echo $i; ?>"><?php echo $i; ?>star</label>
                <?php endfor; ?>
            </div>
        </div>
        
        <div class="form-group">
            <label for="content">Comment：</label>
            <textarea name="content" id="content" class="form-control" rows="4" required></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<style>
    .comment-form-container {
        margin: 20px 0;
        padding: 15px;
        background-color: #f9f9f9;
        border-radius: 5px;
    }
    
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
    
    .form-group {
        margin-bottom: 15px;
    }
</style>

