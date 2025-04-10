<?php
// 确保有评论数据
if (empty($commentsData)) {
    echo "<div class='no-comments'>暂无评论，成为第一个评论的人吧！</div>";
} else {
    // 显示平均评分
    $avgRating = !empty($ratings[0]['avg_rating']) ? number_format($ratings[0]['avg_rating'], 1) : 0;
    $totalRatings = !empty($ratings[0]['total_ratings']) ? $ratings[0]['total_ratings'] : 0;
?>
<div class="recipe-ratings">
        <h3>User's Rating</h3>
        <div class="rating-summary">
            <div class="average-rating">
                <span class="rating-value"><?= $avgRating ?></span>
                <div class="stars">
                    <?php
                    // 显示星星
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= round($avgRating)) {
                            echo '<i class="fas fa-star"></i>';
                        } else {
                            echo '<i class="far fa-star"></i>';
                        }
                    }
                    ?>
                </div>
                <span class="total-ratings">(<?= $totalRatings ?>个评分)</span>
            </div>
        </div>
    </div>

    <div class="comments-section">
        <h3>User's Comment/h3>
        <div class="comments-list">
            <?php foreach ($commentsData as $comment): ?>
                <div class="comment-item">
                    <div class="comment-header">
                        <div class="user-info">
                            <?php if (!empty($comment['profile_image'])): ?>
                                <img src="<?= htmlspecialchars($comment['profile_image']) ?>" alt="<?= htmlspecialchars($comment['username']) ?>" class="user-avatar">
                            <?php else: ?>
                                <div class="default-avatar"><?= substr(htmlspecialchars($comment['username']), 0, 1) ?></div>
                            <?php endif; ?>
                            <span class="username"><?= htmlspecialchars($comment['username']) ?></span>
                        </div>
                        <div class="comment-date">
                            <?= date('Y-m-d H:i', strtotime($comment['created_at'])) ?>
                        </div>
                    </div>
                    <?php if (isset($comment['rating']) && $comment['rating'] > 0): ?>
                        <div class="comment-rating">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $comment['rating']) {
                                    echo '<i class="fas fa-star"></i>';
                                } else {
                                    echo '<i class="far fa-star"></i>';
                                }
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                    <div class="comment-content">
                        <?= nl2br(htmlspecialchars($comment['content'])) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="add-comment-btn">
        <a href="comment.php?action=show_form&recipe_id=<?= $recipeId ?>" class="btn btn-primary">Comment</a>
    </div>
<?php
}
?>