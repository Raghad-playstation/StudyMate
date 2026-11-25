
<?php
session_start();

// Check login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

require_once "db_connect.php";

// Create security token
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// Load the student's tasks
$sql = "SELECT * FROM tasks
        WHERE user_id = :user_id
        ORDER BY due_date ASC";

$stmt = $conn->prepare($sql);
$stmt->execute([
    ":user_id" => $_SESSION["user_id"]
]);

$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Tasks - StudyMate</title>

    <link rel="stylesheet" href="css/style.css">
</head>

<body class="dashboard-page">

    <header class="dashboard-header">
        <h2 class="dashboard-logo">StudyMate</h2>

        <nav class="dashboard-nav">
            <a href="index.html">Home</a>
            <a href="dashboard.html">Dashboard</a>
            <a href="my_tasks.php" class="active">My Tasks</a>
            <a href="add_task_form.php">Add Task</a>
        </nav>
    </header>

    <main class="dashboard-main">

        <section class="dashboard-welcome">
            <h1>My Tasks</h1>
            <p>View and manage your study tasks.</p>
        </section>

        <section>
            <h2 class="dashboard-section-title">Task List</h2>
        </section>

        <!-- Search and filters -->
        <div class="dashboard-actions">

            <input
                type="search"
                id="search-input"
                placeholder="Search tasks..."
                aria-label="Search tasks"
            >

            <select id="filter-status" aria-label="Filter by status">
                <option value="">All Statuses</option>
                <option value="Pending">Pending</option>
                <option value="Completed">Completed</option>
            </select>

            <select id="filter-priority" aria-label="Filter by priority">
                <option value="">All Priorities</option>
                <option value="High">High</option>
                <option value="Medium">Medium</option>
                <option value="Low">Low</option>
            </select>

            <button type="button" id="clear-filters">
                Clear Filters
            </button>

        </div>

        <div class="dashboard-table-wrapper">

            <table
                class="dashboard-table"
                id="tasks-table"
                data-csrf="<?= htmlspecialchars($_SESSION["csrf_token"], ENT_QUOTES, 'UTF-8') ?>"
            >// data- allow js to read it when needed 

                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Due Date</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody id="tasks-body">

                    <?php foreach ($tasks as $task): ?>// printing each tasks informaion 
                        <tr>
                            <td>
                                <?= htmlspecialchars($task["title"], ENT_QUOTES, 'UTF-8') ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($task["due_date"], ENT_QUOTES, 'UTF-8') ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($task["priority"], ENT_QUOTES, 'UTF-8') ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($task["status"], ENT_QUOTES, 'UTF-8') ?>
                            </td>

                            <td>
                                <a href="edit_task.php?id=<?= (int)$task["task_id"] ?>">
                                    Edit
                                </a>

                                <form
                                    action="delete_task.php"
                                    method="post"
                                    style="display:inline;"
                                    onsubmit="return confirm('Are you sure you want to delete this task?');"
                                >

                                    <input
                                        type="hidden"
                                        name="task_id"
                                        value="<?= (int)$task["task_id"] ?>"
                                    >
                                    // user can not see this , it is for the system to delet the task 
                                    <input
                                        type="hidden" 
                                        name="csrf_token"
                                        value="<?= htmlspecialchars($_SESSION["csrf_token"], ENT_QUOTES, 'UTF-8') ?>"
                                    >

                                    <button type="submit">
                                        Delete
                                    </button>

                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($tasks)): ?>
                        <tr>
                            <td colspan="5">
                                No tasks found. Add your first task!
                            </td>
                        </tr>
                    <?php endif; ?>

                </tbody>

            </table>

        </div>

        <div class="dashboard-actions">
            <a href="add_task_form.php" class="dashboard-add-btn">
                + Add New Task
            </a>
        </div>

    </main>

    <footer>
        <p>&copy; 2026 StudyMate</p>
    </footer>

    <!-- My Tasks JavaScript -->
    <script src="js/my_tasks.js"></script>

</body>
</html>
