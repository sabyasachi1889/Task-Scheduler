<?php

function addTask($task_name) {
    $tasks = getAllTasks();
    foreach ($tasks as $task) {
        if ($task['name'] === $task_name) {
            return; // Duplicate task, do not add
        }
    }
    $task_id = count($tasks) + 1; // Simple ID generation
    $tasks[] = ['id' => $task_id, 'name' => $task_name, 'completed' => false];
    file_put_contents('tasks.txt', serialize($tasks));
}

function getAllTasks() {
    if (!file_exists('tasks.txt')) {
        return [];
    }
    return unserialize(file_get_contents('tasks.txt'));
}

function markTaskAsCompleted($task_id, $is_completed) {
    $tasks = getAllTasks();
    foreach ($tasks as &$task) {
        if ($task['id'] == $task_id) {
            $task['completed'] = $is_completed;
            break;
        }
    }
    file_put_contents('tasks.txt', serialize($tasks));
}

function deleteTask($task_id) {
    $tasks = getAllTasks();
    $tasks = array_filter($tasks, function($task) use ($task_id) {
        return $task['id'] != $task_id;
    });
    file_put_contents('tasks.txt', serialize(array_values($tasks)));
}

function generateVerificationCode() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

function subscribeEmail($email) {
    $pending_subscriptions = getPendingSubscriptions();
    foreach ($pending_subscriptions as $pending) {
        if ($pending['email'] === $email) {
            return; // Already pending
        }
    }
    $code = generateVerificationCode();
    $pending_subscriptions[] = ['email' => $email, 'code' => $code];
    file_put_contents('pending_subscriptions.txt', serialize($pending_subscriptions));
    sendVerificationEmail($email, $code);
}

function getPendingSubscriptions() {
    if (!file_exists('pending_subscriptions.txt')) {
        return [];
    }
    return unserialize(file_get_contents('pending_subscriptions.txt'));
}

function verifySubscription($email, $code) {
    $pending_subscriptions = getPendingSubscriptions();
    foreach ($pending_subscriptions as $key => $pending) {
        if ($pending['email'] === $email && $pending['code'] === $code) {
            $verified_subscribers = getVerifiedSubscribers();
            $verified_subscribers[] = $email;
            file_put_contents('subscribers.txt', serialize($verified_subscribers));
            unset($pending_subscriptions[$key]);
            file_put_contents('pending_subscriptions.txt', serialize(array_values($pending_subscriptions)));
            return true;
        }
    }
    return false;
}

function getVerifiedSubscribers() {
    if (!file_exists('subscribers.txt')) {
        return [];
    }
    return unserialize(file_get_contents('subscribers.txt'));
}

function unsubscribeEmail($email) {
    $subscribers = getVerifiedSubscribers();
    $subscribers = array_filter($subscribers, function($subscriber) use ($email) {
        return $subscriber !== $email;
    });
    file_put_contents('subscribers.txt', serialize(array_values($subscribers)));
}

function sendTaskReminders() {
    $subscribers = getVerifiedSubscribers();
    $tasks = getAllTasks();
    foreach ($subscribers as $email) {
        $pending_tasks = array_filter($tasks, function($task) {
            return !$task['completed'];
        });
        if (!empty($pending_tasks)) {
            sendTaskEmail($email, $pending_tasks);
        }
    }
}

function sendTaskEmail($email, $pending_tasks) {
    $subject = "Task Planner - Pending Tasks Reminder";
    $body = "<h2>Pending Tasks Reminder</h2><p>Here are the current pending tasks:</p><ul>";
    foreach ($pending_tasks as $task) {
        $body .= "<li>" . htmlspecialchars($task['name']) . "</li>";
    }
    $body .= "</ul><p><a id='unsubscribe-link' href='unsubscribe.php?email=" . urlencode($email) . "'>Unsubscribe from notifications</a></p>";
    mail($email, $subject, $body, "Content-Type: text/html; charset=UTF-8");
}

function sendVerificationEmail($email, $code) {
    $verification_link = "http://yourdomain.com/verify.php?email=" . urlencode($email) . "&code=" . $code;
    $subject = "Verify subscription to Task Planner";
    $body = "<p>Click the link below to verify your subscription to Task Planner:</p>";
    $body .= "<p><a id='verification-link' href='" . $verification_link . "'>Verify Subscription</a></p>";
    mail($email, $subject, $body, "Content-Type: text/html; charset=UTF-8");
}
?>
