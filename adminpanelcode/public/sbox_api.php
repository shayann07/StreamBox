<?php
require_once __DIR__ . '/../system/CodeIgniter.php';
require_once __DIR__ . '/../public/index.php';

header("Content-Type: application/json"); // Set JSON response type
echo json_encode(["status" => "error", "message" => "Missing required parameters"]);
exit;