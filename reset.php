<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];

// Delete user's expenses
$stmt_expenses = $conn->prepare("DELETE FROM expenses WHERE user_id = :user_id");
$stmt_expenses->execute([':user_id' => $user_id]);

// Delete user's budget
$stmt_budget = $conn->prepare("DELETE FROM budgets WHERE user_id = :user_id");
$stmt_budget->execute([':user_id' => $user_id]);

// Redirect to dashboard
header("Location: dashboard.php");
exit;
?>
