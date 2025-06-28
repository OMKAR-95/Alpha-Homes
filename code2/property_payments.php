<?php
// Database connection
$servername = "localhost";
$username = "root"; // replace with your database username
$password = ""; // replace with your database password
$dbname = "realestatephp";

// Create connection
$conn = mysqli_connect("localhost","root","","realestatephp");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process payment form if submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $pid = $_POST['pid']; // You'll need to include this in your form
    $uname = $_POST['uname']; // You'll need to add this field to your form
    $uemail = $_POST['uemail']; // You'll need to add this field to your form
    $uphone = $_POST['uphone'] ?? ''; // Optional
    
    // Card details (in a real application, use a payment gateway instead of handling directly)
    $card_number = $_POST['card_number'];
    $expiry_date = $_POST['expiry_date'];
    $cvv = $_POST['cvv'];
    
    // For security, only store the last 4 digits of the card
    $masked_card = '****' . substr($card_number, -4);
    
    // Generate a transaction ID
    $transaction_id = 'TXN' . date('ymd') . rand(10000, 99999);
    
    // Get property details and payment amount
    $stmt = $conn->prepare("SELECT price FROM property WHERE pid = ?");
    $stmt->bind_param("i", $pid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $payment_amount = $row['price'];
        
        // In a real application, you would process payment through a gateway here
        // For this example, we'll assume payment is successful
        
        // Insert payment record
        $insert_stmt = $conn->prepare("INSERT INTO payment_transactions 
            (transaction_id, pid, uname, uemail, uphone, 
            card_number, payment_amount, payment_status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'completed')");
            
        $insert_stmt->bind_param("sisssdd", 
            $transaction_id, 
            $pid, 
            $uname, 
            $uemail, 
            $uphone, 
            $masked_card, 
            $payment_amount
        );
        
        if ($insert_stmt->execute()) {
            // Update property status
            $update_stmt = $conn->prepare("UPDATE property SET stype = 'sold' WHERE pid = ?");
            $update_stmt->bind_param("i", $pid);
            $update_stmt->execute();
            
            // Redirect to success page
            header("Location: payment-success.php?txn=" . $transaction_id);
            exit();
        } else {
            echo "Error: " . $insert_stmt->error;
        }
    } else {
        echo "Property not found";
    }
}
?>