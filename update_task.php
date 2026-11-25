// coming from the edit form post botton to update the vlues in the db ...

<?php
session_start();
require_once "db_connect.php";

// Check if the student is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

// Only accept POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: my_tasks.php");
    exit;
}

// Get the form data
$task_id = filter_input(INPUT_POST, "task_id", FILTER_VALIDATE_INT);
$title = trim($_POST["title"] ?? "");
$description = trim($_POST["description"] ?? "");
$due_date = $_POST["due_date"] ?? "";
$priority = $_POST["priority"] ?? "";
$status = $_POST["status"] ?? "";

// Check required information
if (!$task_id || $task_id < 1 || $title === "" || $due_date === "") {
    die("Please fill in all required fields.");
}

// Validate the date
$date = DateTime::createFromFormat("!Y-m-d", $due_date);

if (!$date || $date->format("Y-m-d") !== $due_date) {
    die("Invalid due date.");
}

// Validate priority
if (!in_array($priority, ["High", "Medium", "Low"], true)) {
    die("Invalid priority.");
}

// Validate status
if (!in_array($status, ["Pending", "Completed"], true)) {
    die("Invalid status.");
}

try {

    // Update only the student's own task
    $sql = "UPDATE tasks
            SET title = :title,
                description = :description,
                due_date = :due_date,
                priority = :priority,
                status = :status
            WHERE task_id = :task_id
            AND user_id = :user_id";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        ":title" => $title,
        ":description" => $description,
        ":due_date" => $due_date,
        ":priority" => $priority,
        ":status" => $status,
        ":task_id" => $task_id,
        ":user_id" => $_SESSION["user_id"]
    ]);

    // Go back to My Tasks after saving
    header("Location: my_tasks.php");
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());
    die("Could not update the task.");
}
?>
