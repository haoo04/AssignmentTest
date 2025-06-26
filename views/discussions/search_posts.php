<?php
header('Content-Type: application/json');

require_once(__DIR__ . '/../../controllers/SearchController.php');
require_once(__DIR__ . '/../../config.php');

global $con;
$controller = new SearchController($con);

$searchTerm = $_GET['term'] ?? '';
$posts = $controller->searchPosts($searchTerm);

echo json_encode($posts); 