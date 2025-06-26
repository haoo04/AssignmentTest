<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$userId = $_SESSION['user_id'] ?? null;

// Check for success or error messages
$success = isset($_GET['success']) ? true : false;
$error = isset($_GET['error']) ? $_GET['error'] : '';

require_once(__DIR__ . "/../../controllers/DiscussionController.php");
require_once(__DIR__ . "/../../models/Discussion.php");

global $con;
$controller = new DiscussionController($con);
$posts = $controller->showAllPosts();

// Include Header
require_once(__DIR__ . "/../../views/navi/header.php");
?>

<link rel="stylesheet" href="../../assets/all_posts.css">
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Community Discussion</h1>
        <?php if ($userId): ?>
            <a href="post_form.php" class="btn btn-primary">New Post</a>
        <?php else: ?>
            <a href="../../../login.php" class="btn btn-outline-primary">Log in to create Post</a>
        <?php endif; ?>
    </div>

    <!-- Search Bar -->
    <div class="mb-4">
        <input type="text" id="searchInput" class="form-control" placeholder="Search posts by title or content...">
    </div>
    
    <?php if ($success): ?>
        <div class="alert alert-success">Successful!</div>
    <?php endif; ?>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if (empty($posts)): ?>
        <div class="alert alert-info">There are no discussion posts yet. Be the first one to post!</div>
    <?php else: ?>
        <div class="row" id="postsContainer">
            <div class="col-md-12">
                <div class="list-group">
                    <?php foreach ($posts as $post): ?>
                        <a href="post_detail.php?id=<?= $post['post_id'] ?>" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-1"><?= htmlspecialchars($post['title']) ?></h5>
                                <small><?= date('Y-m-d', strtotime($post['created_at'])) ?></small>
                            </div>
                            <p class="mb-1 text-truncate"><?= htmlspecialchars(substr($post['content'], 0, 150)) ?><?= strlen($post['content']) > 150 ? '...' : '' ?></p>
                            
                            <?php
                            // Get the number of replies to a post
                            $repliesCount = mysqli_query($con, "SELECT COUNT(*) as count FROM post_replies WHERE post_id = " . $post['post_id']);
                            $count = mysqli_fetch_assoc($repliesCount)['count'];
                            ?>
                            
                            <?php
                            // Get the username
                            $result = mysqli_query($con, "SELECT username FROM users WHERE user_id = " . $post['user_id']);
                            $username = '';
                            if ($row = mysqli_fetch_assoc($result)) {
                                $username = $row['username'];
                            }
                            ?>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-user"></i> <?= htmlspecialchars($username) ?> 
                                </small>
                                <span class="badge bg-primary rounded-pill"><?= $count ?> Reply</span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    var searchTerm = this.value;
    var postsContainer = document.getElementById('postsContainer');

    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'search_posts.php?term=' + encodeURIComponent(searchTerm), true);
    
    xhr.onload = function() {
        if (this.status === 200) {
            try {
                var posts = JSON.parse(this.responseText);
                postsContainer.innerHTML = ''; // Clear previous results

                if (posts.error) {
                    postsContainer.innerHTML = `<div class="alert alert-danger">Error: ${posts.error}</div>`;
                } else if (posts.length === 0) {
                    postsContainer.innerHTML = '<div class="alert alert-info">No posts found matching your search.</div>';
                } else {
                    var listGroup = document.createElement('div');
                    listGroup.className = 'list-group';

                    posts.forEach(function(post) {
                        var postContent = post.content.substring(0, 150) + (post.content.length > 150 ? '...' : '');
                        var postDate = new Date(post.created_at).toISOString().split('T')[0];

                        var item = document.createElement('a');
                        item.href = `post_detail.php?id=${post.post_id}`;
                        item.className = 'list-group-item list-group-item-action';
                        item.innerHTML = `
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-1">${post.title}</h5>
                                <small>${postDate}</small>
                            </div>
                            <p class="mb-1 text-truncate">${postContent}</p>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted"><i class="fas fa-user"></i> ${post.username}</small>
                            </div>`;
                        listGroup.appendChild(item);
                    });
                    
                    var col = document.createElement('div');
                    col.className = 'col-md-12';
                    col.appendChild(listGroup);
                    postsContainer.appendChild(col);
                }
            } catch (e) {
                postsContainer.innerHTML = '<div class="alert alert-danger">Error parsing server response.</div>';
            }
        }
    };
    
    xhr.send();
});
</script>

<?php require_once(__DIR__ . "/../../views/navi/footer.php"); ?>