<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$user_name = $_SESSION['username'];
include 'db_connection.php';

// Fetch the user's original budget
$stmt_budget = $conn->prepare("SELECT budget FROM budgets WHERE user_id = :user_id ORDER BY id DESC LIMIT 1");
$stmt_budget->execute([':user_id' => $_SESSION['user_id']]);
$budget_row = $stmt_budget->fetch(PDO::FETCH_ASSOC);

// If no budget exists yet, initialize it to zero
$original_budget = $budget_row['budget'] ?? 0;

// Fetch total expenses
$stmt_expenses = $conn->prepare("SELECT SUM(amount) AS total_expenses FROM expenses WHERE user_id = :user_id");
$stmt_expenses->execute([':user_id' => $_SESSION['user_id']]);
$expense_row = $stmt_expenses->fetch(PDO::FETCH_ASSOC);
$total_expenses = $expense_row['total_expenses'] ?? 0;

// Calculate remaining budget (original budget minus total expenses)
$remaining_budget = $original_budget - $total_expenses;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div style="text-align: right;">
            <a href="logout.php" style="color: red;">Logout</a>
        </div>
        <h1>Welcome, <?= htmlspecialchars($user_name) ?></h1>
        
        <!-- Show the budget form or Set Budget button -->
        <?php if ($original_budget == 0): ?>
            <h2>Set Your Budget</h2>
            <form action="save_budget.php" method="POST">
                <input type="number" step="0.01" name="budget" placeholder="Enter your budget" required>
                <button type="submit">Set Budget</button>
            </form>
        <?php else: ?>
            <h2>Original Budget: $<?= number_format($original_budget, 2) ?></h2>
            <!-- Allow the user to set a new budget -->
            <form action="save_budget.php" method="POST">
                <input type="number" step="0.01" name="budget" placeholder="Enter a new budget" required>
                <button type="submit">Set New Budget</button>
            </form>
        <?php endif; ?>

        <h3>Remaining Budget: $<?= number_format($remaining_budget, 2) ?></h3>
        
        <!-- Form to add an expense -->
        <form action="save_expense.php" method="POST">
            <input type="text" name="description" placeholder="Expense Description" required>
            <input type="number" step="0.01" name="amount" placeholder="Amount in USD" required>
            <button type="submit">Add Expense</button>
        </form>

        <h3>Expense List:</h3>
        <table>
            <tr><th>Description</th><th>Amount ($)</th><th>Date</th></tr>
            <?php
            $stmt = $conn->prepare("SELECT description, amount, created_at FROM expenses WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $_SESSION['user_id']]);
            $expenses = $stmt->fetchAll();
            foreach ($expenses as $expense):
            ?>
                <tr>
                    <td><?= htmlspecialchars($expense['description']) ?></td>
                    <td><?= number_format($expense['amount'], 2) ?></td>
                    <td><?= htmlspecialchars($expense['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <!-- Reset Budget and Expenses -->
        <form action="reset.php" method="POST" onsubmit="return confirm('Are you sure you want to reset all data? This action cannot be undone.');">
            <button type="submit" style="color: white; background-color: red;">Reset All Data</button>
        </form>

    </div>
</body>
</html>
