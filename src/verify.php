<?php
include 'functions.php';

if (isset($_GET['email']) && isset($_GET['code'])) {
    $email = $_GET['email'];
    $code = $_GET['code'];
    if (verifySubscription($email, $code)) {
        echo "Subscription verified successfully!";
    } else {
        echo "Verification failed. Invalid email or code.";
    }
} else {
    echo "Invalid request.";
}
?>
