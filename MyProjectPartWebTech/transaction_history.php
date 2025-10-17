<?php

session_start();
 
// Database connection

$conn = new mysqli('localhost', 'root', '', 'library');
 
// Check for database connection errors

if ($conn->connect_error) {

    die("Database connection failed: " . $conn->connect_error);

}
 
// Initialize variables

$errors = [];

$transactions = [];

$user_id = $_SESSION['user_id'] ?? null;
 
// Fetch transactions based on filter criteria

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['filter'])) {

        $start_date = $_POST['start_date'] ?? null;

        $end_date = $_POST['end_date'] ?? null;
 
        if ($start_date && $end_date) {

            $stmt = $conn->prepare("SELECT * FROM transactions WHERE user_id = ? AND transaction_time BETWEEN ? AND ?");

            $stmt->bind_param("iss", $user_id, $start_date, $end_date);

            $stmt->execute();

            $transactions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        } else {

            $errors[] = "Please select both start and end dates.";

        }

    }
 
    if (isset($_POST['last_five'])) {

        $stmt = $conn->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY transaction_time DESC LIMIT 5");

        $stmt->bind_param("i", $user_id);

        $stmt->execute();

        $transactions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    }
 
    if (isset($_POST['download'])) {

        $stmt = $conn->prepare("SELECT * FROM transactions WHERE user_id = ?");

        $stmt->bind_param("i", $user_id);

        $stmt->execute();

        $transactions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
 
        $file_content = "Transaction ID, Amount, Status, Date\n";

        foreach ($transactions as $transaction) {

            $file_content .= implode(", ", [

                $transaction['transaction_id'],

                $transaction['amount'],

                $transaction['status'],

                $transaction['transaction_time']

            ]) . "\n";

        }
 
        header('Content-Type: text/plain');

        header('Content-Disposition: attachment; filename="transaction_history.txt"');

        echo $file_content;

        exit();

    }

}

?>
 
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Transaction History</title>
<style>

        body {

            font-family: Arial, sans-serif;

            background-color: #f4f4f9;

            margin: 0;

            padding: 20px;

        }

        .container {

            max-width: 800px;

            margin: 0 auto;

            background: #fff;

            padding: 20px;

            border-radius: 5px;

            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);

        }

        h1 {

            text-align: center;

        }

        form {

            margin-bottom: 20px;

        }

        label {

            display: block;

            margin-bottom: 5px;

            font-weight: bold;

        }

        input, button {

            width: 100%;

            padding: 10px;

            margin-bottom: 15px;

            border: 1px solid #ddd;

            border-radius: 5px;

        }

        button {

            background: #4CAF50;

            color: #fff;

            cursor: pointer;

        }

        button:hover {

            background: #45a049;

        }

        table {

            width: 100%;

            border-collapse: collapse;

        }

        th, td {

            border: 1px solid #ddd;

            padding: 10px;

            text-align: left;

        }

        th {

            background: #f4f4f9;

        }

        .error {

            color: red;

        }
</style>
</head>
<body>
<div class="container">
<h1>Transaction History</h1>
 
        <!-- Error messages -->
<?php foreach ($errors as $error): ?>
<p class="error"><?= htmlspecialchars($error) ?></p>
<?php endforeach; ?>
 
        <!-- Filter form -->
<form method="POST">
<label for="start_date">Start Date</label>
<input type="date" id="start_date" name="start_date">
<label for="end_date">End Date</label>
<input type="date" id="end_date" name="end_date">
 
            <button type="submit" name="filter">Filter Transactions</button>
<button type="submit" name="last_five">Last Five Transactions</button>
<button type="submit" name="download">Download Statement</button>
</form>
 
        <!-- Transaction table -->
<?php if ($transactions): ?>
<table>
<thead>
<tr>
<th>Transaction ID</th>
<th>Amount</th>
<th>Status</th>
<th>Date</th>
</tr>
</thead>
<tbody>
<?php foreach ($transactions as $transaction): ?>
<tr>
<td><?= htmlspecialchars($transaction['transaction_id']) ?></td>
<td>$<?= htmlspecialchars(number_format($transaction['amount'], 2)) ?></td>
<td><?= htmlspecialchars($transaction['status']) ?></td>
<td><?= htmlspecialchars($transaction['transaction_time']) ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php else: ?>
<p>No transactions found.</p>
<?php endif; ?>
</div>
</body>
</html>

 