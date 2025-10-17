<?php
// Sample transaction data for display
$transaction = [
    "transaction_id" => "TRX123456789", 
    "status" => "Confirmed",          
    "amount" => 250.50,               
    "fee" => 5.00,                   
    "source" => "Wallet",             
    "sender" => "Sender's Wallet Address", 
    "recipient" => "Recipient's Wallet Address", 
    "input" => 1,                     
    "input_value" => 250.50,        
    "output" => 1,                    
    "output_value" => 245.50,         
    "size" => 512                     
];
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Details</title>
    <style>
        /* Basic styles for the page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f4f4f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Page Title -->
        <h1>Transaction Details</h1>
 
        <!-- Transaction Details Table -->
        <table>
            <tr>
                <th>Field</th>
                <th>Value</th>
            </tr>
            <tr>
                <td>Transaction ID</td>
                <td><?= $transaction['transaction_id']; ?></td>
            </tr>
            <tr>
                <td>Status</td>
                <td><?= $transaction['status']; ?></td>
            </tr>
            <tr>
                <td>Amount</td>
                <td>$<?= number_format($transaction['amount'], 2); ?></td>
            </tr>
            <tr>
                <td>Fee</td>
                <td>$<?= number_format($transaction['fee'], 2); ?></td>
            </tr>
            <tr>
                <td>Source</td>
                <td><?= $transaction['source']; ?></td>
            </tr>
            <tr>
                <td>Sender</td>
                <td><?= $transaction['sender']; ?></td>
            </tr>
            <tr>
                <td>Recipient</td>
                <td><?= $transaction['recipient']; ?></td>
            </tr>
            
            <tr>
                <td>Input</td>
                <td><?= $transaction['input']; ?></td>
            </tr>
            <tr>
                <td>Input Value</td>
                <td>$<?= number_format($transaction['input_value'], 2); ?></td>
            </tr>
            <tr>
                <td>Output</td>
                <td><?= $transaction['output']; ?></td>
            </tr>
            <tr>
                <td>Output Value</td>
                <td>$<?= number_format($transaction['output_value'], 2); ?></td>
            </tr>
            <tr>
                <td>Size</td>
                <td><?= $transaction['size']; ?> bytes</td>
            </tr>
        </table>
        <p>Show transaction history => <a href="transaction_history.php">Go</a></p>
    </div>
</body>
</html>
 
 