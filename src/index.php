<?php
include 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['task-name'])) {
        addTask($_POST['task-name']);
    } elseif (isset($_POST['email'])) {
        subscribeEmail($_POST['email']);
    }
}

$tasks = getAllTasks();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task Scheduler</title>
</head>
<body>
    <h1>Task Scheduler</h1>
    
    <h2>Add Task</h2>
    <form method="POST">
        <input type="text" name="task-name" id="task-name" placeholder="Enter new task" required>
        <button type="submit" id="add-task">Add Task</button>
    </form>

    <h2>Tasks</h2>
    <ul class="tasks-list">
        <?php foreach ($tasks as $task): ?>
            <li class="task-item <?= $task['completed'] ? 'completed' : '' ?>">
                <input type="checkbox" class="task-status" <?= $task['completed'] ? 'checked' : '' ?> onchange="markTaskAsCompleted(<?= $task['id'] ?>, this.checked)">
                <button class="delete-task" onclick="deleteTask(<?= $task['id'] ?>)">Delete</button>
                <?= htmlspecialchars($task['name']) ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <h2>Email Subscription</h2>
    <form method="POST">
        <input type="email" name="email" required />
        <button id="submit-email">Submit</button>
    </form>

    <script>
        function markTaskAsCompleted(taskId, isCompleted) {
            // AJAX call to mark task as completed
        }

        function deleteTask(taskId) {
            // AJAX call to delete task
        }
    </script>
</body>
</html>
