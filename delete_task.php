
<?php
session_start();
require_once "db_connect.php";

// Check if the student is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

// Only allow POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: my_tasks.php");
    exit;
}

// Get the task ID
$task_id = filter_input(INPUT_POST, "task_id", FILTER_VALIDATE_INT);

if (!$task_id || $task_id < 1) {
    die("Invalid task ID.");
}

// Check the security token
if (
    !isset($_SESSION["csrf_token"], $_POST["csrf_token"]) ||
    !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
) {
    http_response_code(403);
    die("Invalid request.");
}

try {
    // Delete only a task belonging to this student
    $sql = "DELETE FROM tasks
            WHERE task_id = :task_id
            AND user_id = :user_id";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        ":task_id" => $task_id,
        ":user_id" => $_SESSION["user_id"]
    ]);

    header("Location: my_tasks.php");
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());
    die("Could not delete the task.");
}
?>
