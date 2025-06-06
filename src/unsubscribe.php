<?php
include 'functions.php';

if (isset($_GET['email'])) {
    $email = $_GET['email'];
    unsubscribeEmail($email);
    echo "You have been unsubscribed successfully.";
} else {
    echo "Invalid request.";
}
?>
