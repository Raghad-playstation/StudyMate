
<?php
// Returns task counters and upcoming tasks as JSON
session_start();

require_once "db_connect.php";
require_once "TaskManager.php";

header("Content-Type: application/json");

// Check if the student is logged in
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(["error" => "Not logged in"]);
    exit;
}

try {
    $uid = $_SESSION["user_id"];

    // Create a TaskManager object
    $taskManager = new TaskManager($conn);

    // Get the total number of tasks
    $totalTasks = $taskManager->countTasks($uid);

    // Count pending and completed tasks
    $stmt = $conn->prepare(
        "SELECT
            COALESCE(SUM(status = 'Pending'), 0) AS pending,
            COALESCE(SUM(status = 'Completed'), 0) AS completed
         FROM tasks
         WHERE user_id = :uid"
    );

    $stmt->execute([":uid" => $uid]);
    $counts = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get the next 5 pending tasks
    $stmt = $conn->prepare(
        "SELECT title, due_date, priority, status
         FROM tasks
         WHERE user_id = :uid AND status = 'Pending'
         ORDER BY due_date ASC
         LIMIT 5"
    );

    $stmt->execute([":uid" => $uid]);

    // Send the results to JavaScript
    echo json_encode([
        "total"     => $totalTasks,
        "pending"   => (int)$counts["pending"],
        "completed" => (int)$counts["completed"],
        "upcoming"  => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);

} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(["error" => "Server error"]);
}
?>
