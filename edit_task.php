
<?php
session_start();
require_once "db_connect.php";

// Check if the student is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

// Get the task ID from the URL
$task_id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$task_id || $task_id < 1) {
    die("Invalid task ID.");
}

// Get only the logged-in student's task
$sql = "SELECT * FROM tasks
        WHERE task_id = :task_id
        AND user_id = :user_id";

$stmt = $conn->prepare($sql);

$stmt->execute([
    ":task_id" => $task_id,
    ":user_id" => $_SESSION["user_id"]
]);

$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    die("Task not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task - StudyMate</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
        .edit-task-form {
            max-width: 600px;
            margin: 30px 0;
        }

        .edit-task-form label {
            display: block;
            margin: 18px 0 8px;
            font-weight: 600;
        }

        .edit-task-form input,
        .edit-task-form textarea,
        .edit-task-form select {
            display: block;
            width: 100%;
            padding: 12px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 6px;
            font: inherit;
        }

        .edit-task-form .dashboard-actions {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 25px;
        }
    </style>
</head>

<body class="dashboard-page">

    <header class="dashboard-header">
        <h2 class="dashboard-logo">StudyMate</h2>

        <nav class="dashboard-nav">
            <a href="index.html">Home</a>
            <a href="dashboard.html">Dashboard</a>
            <a href="my_tasks.php">My Tasks</a>
            <a href="add_task_form.php">Add Task</a>
        </nav>
    </header>

    <main class="dashboard-main">

        <section class="dashboard-welcome">
            <h1>Edit Task</h1>
            <p>Update your study task information.</p>
        </section>

        <section class="dashboard-section-title">
            <h2>Task Details</h2>
        </section>

        <form class="task-form edit-task-form"
              action="update_task.php"
              method="post">

            <input type="hidden"
                   name="task_id"
                   value="<?= (int)$task["task_id"] ?>">

            <label for="title">Task Title</label>
            <input type="text"
                   id="title"
                   name="title"
                   value="<?= htmlspecialchars($task["title"], ENT_QUOTES, 'UTF-8') ?>"
                   required>

            <label for="description">Description</label>
            <textarea id="description"
                      name="description"
                      rows="4"><?= htmlspecialchars($task["description"] ?? "", ENT_QUOTES, 'UTF-8') ?></textarea>

            <label for="due-date">Due Date</label>
            <input type="date"
                   id="due-date"
                   name="due_date"
                   value="<?= htmlspecialchars($task["due_date"], ENT_QUOTES, 'UTF-8') ?>"
                   required>

            <label for="priority">Priority</label>
            <select id="priority" name="priority" required>
                <?php foreach (["High", "Medium", "Low"] as $priority): ?>
                    <option value="<?= $priority ?>"
                        <?= $task["priority"] === $priority ? "selected" : "" ?>>
                        <?= $priority ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="status">Status</label>
            <select id="status" name="status" required>
                <?php foreach (["Pending", "Completed"] as $status): ?>
                    <option value="<?= $status ?>"
                        <?= $task["status"] === $status ? "selected" : "" ?>>
                        <?= $status ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="dashboard-actions">
                <button type="submit" class="dashboard-add-btn">
                    Save Changes
                </button>

                <a href="my_tasks.php">Cancel</a>
            </div>

        </form>

    </main>

    <footer>
        <p>&copy; 2026 StudyMate</p>
    </footer>

</body>
</html>
