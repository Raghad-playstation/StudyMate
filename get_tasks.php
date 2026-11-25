<?php
// Returns the logged-in student's tasks as JSON, with optional search/filter
session_start();
require_once "db_connect.php";

header("Content-Type: application/json");//the info will be at json styled 

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(["error" => "Not logged in"]);
    exit;
}

$search   = trim($_GET["search"] ?? "");
$status   = $_GET["status"] ?? "";
$priority = $_GET["priority"] ?? "";

$sql = "SELECT task_id, title, due_date, priority, status
        FROM tasks WHERE user_id = :uid";
$params = [":uid" => $_SESSION["user_id"]];

// after calling the specified row , noe we are filtering it 
// either by search bar , status or priority and adding it to the sql query 
if ($search !== "") {
    $sql .= " AND (title LIKE :search OR description LIKE :search)";
    $params[":search"] = "%" . $search . "%";
}
if (in_array($status, ["Pending", "Completed"], true)) {
    $sql .= " AND status = :status";
    $params[":status"] = $status;
}
if (in_array($priority, ["High", "Medium", "Low"], true)) {
    $sql .= " AND priority = :priority";
    $params[":priority"] = $priority;
}

$sql .= " ORDER BY due_date ASC";// when we add the point >> add this to the vlaue of the same var name 

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(["error" => "Server error"]);
}
