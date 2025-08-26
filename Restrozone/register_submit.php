<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = htmlspecialchars($_POST['name']);
    $email    = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']); // You should hash this before storing

    // Simulate a database save here
    echo "<h2>Registration Successful</h2>";
    echo "<p>Name: $name</p>";
    echo "<p>Email: $email</p>";
    // Do not echo password in production
} else {
    echo "Invalid request.";
}
?>
