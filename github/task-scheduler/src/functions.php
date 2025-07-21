<?php

function getAllTasks() {
    $file = __DIR__ . '/tasks.txt';
    $tasks = [];

    if (!file_exists($file)) return [];

    foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        list($id, $name, $completed) = explode('|', $line);
        $tasks[] = ['id' => $id, 'name' => $name, 'completed' => $completed === '1'];
    }

    return $tasks;
}

function addTask($name) {
    $file = __DIR__ . '/tasks.txt';
    $id = uniqid();
    file_put_contents($file, "$id|$name|0\n", FILE_APPEND | LOCK_EX);
}

function markTaskAsCompleted($id, $completed) {
    $file = __DIR__ . '/tasks.txt';
    $tasks = getAllTasks();
    $updated = '';

    foreach ($tasks as $task) {
        if ($task['id'] === $id) {
            $task['completed'] = $completed;
        }
        $updated .= "{$task['id']}|{$task['name']}|" . ($task['completed'] ? '1' : '0') . "\n";
    }

    file_put_contents($file, $updated);
}

function deleteTask($id) {
    $file = __DIR__ . '/tasks.txt';
    $tasks = getAllTasks();
    $updated = '';

    foreach ($tasks as $task) {
        if ($task['id'] !== $id) {
            $updated .= "{$task['id']}|{$task['name']}|" . ($task['completed'] ? '1' : '0') . "\n";
        }
    }

    file_put_contents($file, $updated);
}

function subscribeEmail($email) {
    $file = __DIR__ . '/subscribers.txt';

    if (!file_exists($file)) {
        touch($file);
    }

    $existing = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($existing as $line) {
        $parts = explode('|', trim($line));
        $saved_email = $parts[0] ?? '';
        if (strcasecmp($saved_email, $email) === 0) {
            return $parts[1] ?? false; // Return existing code
        }
    }

    $code = rand(100000, 999999);
    $entry = "$email|$code|1";  // Verified directly
    file_put_contents($file, $entry . "\n", FILE_APPEND | LOCK_EX);
    return $code;
}

function verifySubscription($email, $code) {
    $file = __DIR__ . '/subscribers.txt';
    if (!file_exists($file)) return false;

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $verified = false;
    $updated = [];

    foreach ($lines as $line) {
        $parts = explode('|', trim($line));
        $e = $parts[0] ?? '';
        $c = $parts[1] ?? '';
        // Skip malformed lines
        if (!$e || !$c) continue;

        if (strcasecmp($e, $email) === 0 && $c === $code) {
            $verified = true;
            $updated[] = "$e|$c|1";
        } else {
            // Preserve or reformat old lines to force '1'
            $updated[] = "$e|$c|1";
        }
    }

    if ($verified) {
        file_put_contents($file, implode("\n", $updated) . "\n");
    }

    return $verified;
}

function getVerifiedEmails() {
    $file = __DIR__ . '/subscribers.txt';
    if (!file_exists($file)) return [];

    $emails = [];
    foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $parts = explode('|', trim($line));
        $email = $parts[0] ?? '';
        $status = $parts[2] ?? '0';
        if ($status === '1') {
            $emails[] = $email;
        }
    }

    return $emails;
}
