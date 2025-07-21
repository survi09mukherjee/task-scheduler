<?php
require_once 'functions.php';

$errors = [];
$success_message = '';

// Handle Task Form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['task-name'])) {
        $task_name = trim($_POST['task-name']);
        $tasks = getAllTasks();
        $exists = false;

        foreach ($tasks as $task) {
            if (strcasecmp($task['name'], $task_name) === 0) {
                $exists = true;
                break;
            }
        }

        if (!$exists) {
            addTask($task_name);
        } else {
            $errors[] = "Duplicate task not allowed.";
        }
    }

    // Task Status Toggle
    if (isset($_POST['toggle_status'])) {
        markTaskAsCompleted($_POST['toggle_status'], $_POST['is_completed'] === '1');
    }

    // Delete Task
    if (isset($_POST['delete_id'])) {
        deleteTask($_POST['delete_id']);
    }

    // Email Subscription
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        } else {
            $code = subscribeEmail($email);
            if ($code === false) {
                $errors[] = "⚠️ Failed to save verification code.";
            } else {
                $success_message = "✅ Code for <strong>$email</strong> is <strong>$code</strong>.";
            }
        }
    }
}

$tasks = getAllTasks();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Task Scheduler</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px auto;
            max-width: 600px;
            background-color: #f9f9f9;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }

        h1, h2 {
            text-align: center;
        }

        form {
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="email"] {
            padding: 10px;
            width: 70%;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            padding: 10px 20px;
            background-color: #0088cc;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        ul {
            list-style-type: none;
            padding-left: 0;
        }

        li.task-item {
            padding: 10px;
            background: #fff;
            margin: 10px 0;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .task-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .completed {
            text-decoration: line-through;
            color: #888;
        }

        .error {
            color: red;
            font-weight: bold;
        }

        .success {
            color: green;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .nav-buttons {
            text-align: center;
            margin-top: 30px;
        }

        .nav-buttons form {
            display: inline;
            margin: 0 10px;
        }

        .nav-buttons button {
            background-color: #28a745;
        }

        .unsubscribe-btn {
            background-color: #dc3545 !important;
        }
    </style>
</head>
<body>

    <h1>🗓️ Task Scheduler</h1>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $e): ?>
                <p><?= htmlspecialchars($e ?? '') ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success_message)): ?>
        <div class="success"><?= $success_message ?></div>
    <?php endif; ?>

    <!-- Add Task Form -->
    <form method="POST">
        <input type="text" name="task-name" id="task-name" placeholder="Enter new task" required>
        <button type="submit" id="add-task">Add Task</button>
    </form>

    <!-- Tasks List -->
    <h2>📋 Tasks</h2>
    <ul id="tasks-list">
        <?php foreach ($tasks as $task): ?>
            <li class="task-item">
                <div class="task-left">
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="toggle_status" value="<?= $task['id'] ?>">
                        <input type="hidden" name="is_completed" value="<?= $task['completed'] ? '0' : '1' ?>">
                        <input type="checkbox" class="task-status" onchange="this.form.submit()" <?= $task['completed'] ? 'checked' : '' ?>>
                    </form>
                    <span class="<?= $task['completed'] ? 'completed' : '' ?>">
                        <?= htmlspecialchars($task['name'] ?? '') ?>
                    </span>
                </div>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="delete_id" value="<?= $task['id'] ?>">
                    <button type="submit" class="delete-task">Delete</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- Subscription Form -->
    <h2>📧 Subscribe for Email Reminders</h2>
    <form method="POST">
        <input type="email" name="email" id="email" placeholder="Enter your email" required>
        <button type="submit" id="submit-email">Subscribe</button>
    </form>

    <!-- Navigation Buttons -->
    <div class="nav-buttons">
        <form action="verify.php" method="get">
            <button type="submit">✅ Verify Email</button>
        </form>
        <form action="unsubscribe.php" method="get">
            <button type="submit" class="unsubscribe-btn">🚫 Unsubscribe</button>
        </form>
    </div>

</body>
</html>
