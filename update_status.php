<?php
// Changes a task's status (Pending / Completed) - called by js/my_tasks.js
session_start();
require_once "db_connect.php";

header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "POST only"]);
    exit;
}

if (
    !isset($_SESSION["csrf_token"], $_POST["csrf_token"]) ||
    !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
) {
    http_response_code(403);
    echo json_encode(["success" => false, "error" => "Invalid request"]);
    exit;
}

$task_id = filter_input(INPUT_POST, "task_id", FILTER_VALIDATE_INT);
$status  = $_POST["status"] ?? "";

if (!$task_id || !in_array($status, ["Pending", "Completed"], true)) {
    echo json_encode(["success" => false, "error" => "Invalid data"]);
    exit;
}

try {
    $stmt = $conn->prepare(
        "UPDATE tasks SET status = :status
         WHERE task_id = :task_id AND user_id = :uid"
    );
    $stmt->execute([
        ":status"  => $status,
        ":task_id" => $task_id,
        ":uid"     => $_SESSION["user_id"]
    ]);
    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(["success" => false, "error" => "Server error"]);
}
