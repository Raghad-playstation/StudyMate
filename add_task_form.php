<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>StudyMate | Add Task</title>

    <link rel="stylesheet" href="css/style.css">
</head>

<body class="task-page">

    <!-- Header -->
    <header class="task-header">
        <h1 class="task-logo">StudyMate</h1>

        <nav class="task-nav">
            <a href="dashboard.html">Dashboard</a>
          <a href="add_task_form.php" class="active">Add Task</a>
           <a href="my_tasks.php">My Tasks</a>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="task-main">

        <section class="task-form-card">

            <h2>Add New Task</h2>
            <p class="task-description">
                Organize your studies by adding a new task.
            </p>

            <form action="add_task.php" method="post">
                <input type="hidden"
       name="csrf_token"
       value="<?= htmlspecialchars($_SESSION["csrf_token"], ENT_QUOTES, 'UTF-8') ?>">

                <label for="task-title">Task Title</label>
                <input type="text" id="task-title"
                       name="task_title"
                       placeholder="Enter task title" required>

                <label for="task-description">Description</label>
                <textarea id="task-description"
                          name="description"
                          rows="4"
                          placeholder="Enter task details"></textarea>

                <label for="due-date">Due Date</label>
                <input type="date" id="due-date"
                       name="due_date" required>

                <label for="priority">Priority</label>
                <select id="priority" name="priority" required>
                    <option value="">Select priority</option>// default value 
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>

                <div class="task-form-actions">
                    <button type="submit" class="task-save-btn">
                        Save Task
                    </button>

                    <a href="dashboard.html" class="task-cancel-btn">
                        Cancel
                    </a>
                </div>

            </form>
        </section>

    </main>
<script src="js/validation.js"></script>
</body>
</html>