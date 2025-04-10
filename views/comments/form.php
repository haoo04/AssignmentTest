<!--need header.php -->
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>添加评论</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .comment-form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .form-title {
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        
        .rating-container {
            margin-bottom: 20px;
        }
        
        .star-rating {
            font-size: 24px;
            cursor: pointer;
            color: #ddd;
        }
        
        .star-rating .fas {
            color: #FFD700;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        textarea {
            width: 100%;
            min-height: 150px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            resize: vertical;
        }
        
        .form-actions {
            display: flex;
            justify-content: space-between;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        
        .btn-secondary {
            background-color: #95a5a6;
            color: white;
        }
        
        .error-message {
            color: #e74c3c;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="comment-form-container">
        <h2 class="form-title">添加评论</h2>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>
        
        <form action="comment.php?action=add" method="POST">
            <input type="hidden" name="recipe_id" value="<?= $recipeId ?>">
            
            <div class="rating-container">
                <label for="rating">评分:</label>
                <div class="star-rating" id="star-rating">
                    <i class="far fa-star" data-rating="1"></i>
                    <i class="far fa-star" data-rating="2"></i>
                    <i class="far fa-star" data-rating="3"></i>
                    <i class="far fa-star" data-rating="4"></i>
                    <i class="far fa-star" data-rating="5"></i>
                </div>
                <input type="hidden" name="rating" id="rating-value" value="<?= $rating ?? '' ?>">
            </div>
            
            <div class="form-group">
                <label for="content">评论内容:</label>
                <textarea name="content" id="content" required><?= htmlspecialchars($content ?? '') ?></textarea>
            </div>
            
            <div class="form-actions">
                <a href="recipe_detail.php?id=<?= $recipeId ?>" class="btn btn-secondary">取消</a>
                <button type="submit" class="btn btn-primary">提交评论</button>
            </div>
        </form>
    </div>
    
    <script>
        // 星级评分交互效果
        document.addEventListener('DOMContentLoaded', function() {
            const starRating = document.getElementById('star-rating');
            const ratingValue = document.getElementById('rating-value');
            const stars = starRating.querySelectorAll('i');
            
            // 初始设置
            const initialRating = <?= isset($rating) ? $rating : 0 ?>;
            if (initialRating > 0) {
                updateStars(initialRating);
            }
            
            // 鼠标悬停效果
            stars.forEach(star => {
                star.addEventListener('mouseover', function() {
                    const rating = this.getAttribute('data-rating');
                    highlightStars(rating);
                });
            });
            
            // 鼠标离开效果
            starRating.addEventListener('mouseout', function() {
                const currentRating = ratingValue.value || 0;
                updateStars(currentRating);
            });
            
            // 点击选择评分
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = this.getAttribute('data-rating');
                    ratingValue.value = rating;
                    updateStars(rating);
                });
            });
            
            // 高亮显示星星
            function highlightStars(rating) {
                stars.forEach(star => {
                    const starRating = star.getAttribute('data-rating');
                    if (starRating <= rating) {
                        star.className = 'fas fa-star';
                    } else {
                        star.className = 'far fa-star';
                    }
                });
            }
            
            // 更新星星和隐藏字段值
            function updateStars(rating) {
                highlightStars(rating);
                ratingValue.value = rating;
            }
        });
    </script>
</body>
</html>