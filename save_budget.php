<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $budget = $_POST['budget'];
    $user_id = $_SESSION['user_id'];

    // Check if the user already has a budget
    $stmt_check = $conn->prepare("SELECT id FROM budgets WHERE user_id = :user_id ORDER BY id DESC LIMIT 1");
    $stmt_check->execute([':user_id' => $user_id]);
    $existing_budget = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if ($existing_budget) {
        // Update the budget only if the user has not set it yet
        $stmt = $conn->prepare("UPDATE budgets SET budget = :budget WHERE user_id = :user_id");
        $stmt->execute([
            ':user_id' => $user_id,
            ':budget' => $budget
        ]);
    } else {
        // Insert the new budget if the user hasn't set one yet
        $stmt = $conn->prepare("INSERT INTO budgets (user_id, budget) VALUES (:user_id, :budget)");
        $stmt->execute([
            ':user_id' => $user_id,
            ':budget' => $budget
        ]);
    }

    // Redirect back to dashboard
    header("Location: dashboard.php");
    exit;
}
?>
