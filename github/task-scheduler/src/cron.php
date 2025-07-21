<?php
$subscribers_file = __DIR__ . '/subscribers.txt';
$tasks_file = __DIR__ . '/tasks.txt';

$emailsSent = 0;
$log = [];

$isCLI = (php_sapi_name() === 'cli');

if (!file_exists($subscribers_file)) {
    $log[] = $isCLI ? "⚠️ No subscribers found." : "⚠️ No subscribers found.";
} else {
    $subscribers = file($subscribers_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $pendingTasks = getPendingTasksAsString();

    foreach ($subscribers as $line) {
        $line = trim($line);
        $parts = explode('|', $line);

        if (count($parts) !== 3) {
            $log[] = $isCLI ? "⚠️ Invalid email skipped: $line" : "⚠️ Invalid email skipped: $line";
            continue;
        }

        list($email, $code, $status) = $parts;

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || trim($status) !== '1') {
            $log[] = $isCLI ? "⚠️ Invalid email skipped: $line" : "⚠️ Invalid email skipped: $line";
            continue;
        }

        $unsubscribe_link = "http://localhost/unsubscribe.php?email=" . urlencode($email);

        if ($isCLI) {
            $log[] = "📬 Sending to: $email\n------------------\nSubject: Hourly Task Reminder\nBody:\n$pendingTasks\n\nTo unsubscribe: $unsubscribe_link\n";
        } else {
            $log[] = "📬 Sending to: <strong>$email</strong><br><pre>" .
                     "Subject: Hourly Task Reminder\n" .
                     "Body:\n$pendingTasks\n\nTo unsubscribe: $unsubscribe_link</pre>";
        }

        $emailsSent++;
    }

    $log[] = $isCLI
        ? "✅ Reminder job completed. Emails processed: $emailsSent"
        : "<strong>✅ Reminder job completed. Emails processed: $emailsSent</strong>";
}

if ($isCLI) {
    echo implode("\n\n", $log) . "\n";
    exit;
}

// If not CLI, output HTML
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>📧 Cron Log Viewer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px auto;
            max-width: 800px;
            background-color: #f0f8ff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #aaa;
        }
        h1 {
            text-align: center;
            color: #0066cc;
        }
        .log {
            background: #fff;
            border-radius: 5px;
            padding: 20px;
            line-height: 1.6;
            font-size: 15px;
            white-space: pre-wrap;
            border-left: 5px solid #0066cc;
        }
    </style>
</head>
<body>
    <h1>📨 Cron Email Log Viewer</h1>
    <div class="log">
        <?= implode("<hr>", $log); ?>
    </div>
</body>
</html>

<?php
// Function reused from cron.php
function getPendingTasksAsString() {
    $tasks_file = __DIR__ . '/tasks.txt';
    if (!file_exists($tasks_file)) return "No pending tasks.";

    $tasks = file($tasks_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $pending = [];

    foreach ($tasks as $task) {
        $parts = explode('|', $task);
        if (count($parts) >= 3 && trim($parts[2]) === '0') {
            $pending[] = "- " . trim($parts[1]);
        }
    }

    return empty($pending) ? "No pending tasks." : implode("\n", $pending);
}
?>
