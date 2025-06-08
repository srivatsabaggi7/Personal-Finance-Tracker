<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $description = $_POST['description'];
    $amount = $_POST['amount'];
    $user_id = $_SESSION['user_id'];

    // Insert the expense into the expenses table
    $stmt_expense = $conn->prepare("INSERT INTO expenses (user_id, description, amount) VALUES (:user_id, :description, :amount)");
    $stmt_expense->execute([
        ':user_id' => $user_id,
        ':description' => $description,
        ':amount' => $amount
    ]);

    // Redirect back to dashboard
    header("Location: dashboard.php");
    exit;
}
?>
