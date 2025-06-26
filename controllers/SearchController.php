<?php
require_once(__DIR__ . '/../config.php');

class SearchController
{
    private $con;

    public function __construct($con)
    {
        $this->con = $con;
    }

    public function searchPosts($searchTerm)
    {
        if (!$this->con) {
            return ['error' => 'Database connection failed'];
        }

        $searchTerm = mysqli_real_escape_string($this->con, $searchTerm);
        
        $query = "SELECT p.*, u.username 
                  FROM discussion_posts p 
                  JOIN users u ON p.user_id = u.user_id 
                  WHERE p.title LIKE '%$searchTerm%' OR p.content LIKE '%$searchTerm%'
                  ORDER BY p.created_at DESC";

        $result = mysqli_query($this->con, $query);

        if (!$result) {
            return ['error' => 'Database query failed: ' . mysqli_error($this->con)];
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
} 