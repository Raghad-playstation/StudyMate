
<?php
session_start();
require_once "db_connect.php";

// Check if the student is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

// Only accept form submissions
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: add_task_form.php");
    exit;
}

// Check the security token
if (
    !isset($_SESSION["csrf_token"], $_POST["csrf_token"]) ||
    !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
) {
    http_response_code(403);
    die("Invalid request.");
}

// Get the task information
$user_id = $_SESSION["user_id"];
$title = trim($_POST["task_title"] ?? "");
$description = trim($_POST["description"] ?? "");
$due_date = $_POST["due_date"] ?? "";
$priority = $_POST["priority"] ?? "";

// Check required fields
if ($title === "" || $due_date === "" || $priority === "") {
    die("Please fill in all required fields.");
}

// Check title length
if (mb_strlen($title) < 3) {
    die("Task title must be at least 3 characters.");
}

// Check date format
$date = DateTime::createFromFormat("!Y-m-d", $due_date);

if (!$date || $date->format("Y-m-d") !== $due_date) {
    die("Invalid due date.");
}

// Do not allow past dates
$today = new DateTime("today");

if ($date < $today) {
    die("Due date cannot be in the past.");
}

// Check priority
if (!in_array($priority, ["Low", "Medium", "High"], true)) {
    die("Invalid priority.");
}

try {
    // Save the task in MySQL
    $sql = "INSERT INTO tasks
            (user_id, title, description, due_date, priority)
            VALUES
            (:user_id, :title, :description, :due_date, :priority)";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        ":user_id" => $user_id,
        ":title" => $title,
        ":description" => $description,
        ":due_date" => $due_date,
        ":priority" => $priority
    ]);

    // Return to My Tasks
    header("Location: my_tasks.php");
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());
    die("Could not save the task. Please try again.");
}
?>
