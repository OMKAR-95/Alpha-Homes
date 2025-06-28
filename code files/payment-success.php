<?php
// Debugging
echo "URL parameters: ";
print_r($_GET);
echo "<br>";

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "realestatephp"; // Make sure this exactly matches your database name

// Create connection
$conn = mysqli_connect("localhost","root","","realestatephp");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    echo "Database connection established successfully<br>";
}

// Get transaction ID from URL
$transaction_id = $_GET['txn'] ?? '';

if (empty($transaction_id)) {
    echo "Invalid transaction - No transaction ID provided";
    exit();
}

// Debugging - add this to see actual transaction ID
echo "Transaction ID: " . $transaction_id . "<br>";

// Check if the payment_transactions table exists
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'payment_transactions'");
if (mysqli_num_rows($table_check) == 0) {
    echo "Error: payment_transactions table does not exist";
    exit();
}

// Get transaction details
$stmt = $conn->prepare("
    SELECT t.*, p.title as property_name, p.city as property_location
    FROM payment_transactions t
    JOIN property p ON t.pid = p.pid
    WHERE t.transaction_id = ?
");

// If the prepare statement fails
if (!$stmt) {
    echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
    exit();
}

$stmt->bind_param("s", $transaction_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Transaction not found in database";
    exit();
}

$transaction = $result->fetch_assoc();

// Debug - print transaction details
echo "Transaction details found:<br>";
print_r($transaction);
echo "<br>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <!-- Add your CSS here or link an external stylesheet -->
    <style>
        /* Copy the CSS from the previous confirmation page */
    </style>
</head>
<body>
    <div class="confirmation-container">
        <div class="success-icon">
            <span class="checkmark">✓</span>
        </div>
        
        <h1>Payment Successful!</h1>
        <p>Your payment for the property in <?php echo htmlspecialchars($transaction['property_location']); ?> has been processed successfully. A confirmation email with details has been sent to your registered email address.</p>
        
        <div class="divider"></div>
        
        <div class="details">
            <div class="detail-row">
                <span class="label">Property:</span>
                <span class="value"><?php echo htmlspecialchars($transaction['property_name']); ?></span>
            </div>
            <div class="detail-row">
                <span class="label">Transaction Date:</span>
                <span class="value"><?php echo date('F j, Y', strtotime($transaction['payment_date'])); ?></span>
            </div>
            <div class="detail-row">
                <span class="label">Payment Method:</span>
                <span class="value">Credit Card (<?php echo htmlspecialchars($transaction['card_number']); ?>)</span>
            </div>
            
            <div class="divider"></div>
            
            <div class="total-row">
                <span class="total-label">Total Amount Paid:</span>
                <span class="total-value">₹<?php echo number_format($transaction['payment_amount'], 2); ?></span>
            </div>
        </div>
        
        <div class="receipt-id">
            Transaction ID: <?php echo htmlspecialchars($transaction_id); ?>
        </div>
        
        <a href="index.php" class="back-button">Back to Dashboard</a>
        
        <p class="footer-text">If you have any questions or concerns, please contact our support at support@propertyname.com</p>
    </div>
</body>
</html>