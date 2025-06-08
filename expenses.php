<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// Get the user's budget
$stmt_budget = $conn->prepare("SELECT SUM(budget) as total_budget FROM budgets WHERE user_id = :user_id");
$stmt_budget->execute([':user_id' => $_SESSION['user_id']]);
$budget_row = $stmt_budget->fetch(PDO::FETCH_ASSOC);
$total_budget = $budget_row['total_budget'] ?? 0;

// Get the user's expenses
$stmt_expenses = $conn->prepare("SELECT description, amount, created_at FROM expenses WHERE user_id = :user_id");
$stmt_expenses->execute([':user_id' => $_SESSION['user_id']]);
$expenses = $stmt_expenses->fetchAll();

// Calculate total expenses
$total_expenses = 0;
foreach ($expenses as $expense) {
    $total_expenses += $expense['amount'];
}

// Calculate remaining savings
$remaining_savings = $total_budget - $total_expenses;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense List</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Your Expenses</h1>
        <a href="dashboard.php">Back to Dashboard</a>
        
        <table>
            <tr><th>Description</th><th>Amount ($)</th><th>Date</th></tr>
            <?php foreach ($expenses as $expense): ?>
                <tr>
                    <td><?= htmlspecialchars($expense['description']) ?></td>
                    <td><?= htmlspecialchars($expense['amount']) ?></td>
                    <td><?= htmlspecialchars($expense['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td><strong>Total Expenses:</strong></td>
                <td><strong>$<?= number_format($total_expenses, 2) ?></strong></td>
                <td></td>
            </tr>
            <tr>
                <td><strong>Remaining Savings:</strong></td>
                <td><strong>$<?= number_format($remaining_savings, 2) ?></strong></td>
                <td></td>
            </tr>
        </table>
    </div>
</body>
</html>
