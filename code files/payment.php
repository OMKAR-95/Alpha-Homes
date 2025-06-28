<?php
session_start();
include("config.php");

if (isset($_GET['pid'])) {
    $pid = $_GET['pid'];
    // Retrieve property details based on the passed ID.
    $query = mysqli_query($conn, "SELECT * FROM property WHERE pid='$pid'");

    
    if (mysqli_num_rows($query) > 0) {
        $property = mysqli_fetch_array($query);
    } else {
        echo "Property not found!";
        exit;
    }
} else {
    echo "No property selected.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Page - <?php echo $property['1']; ?></title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Payment for: <?php echo $property['1']; ?></h2>
    <p>Price: Rs.<?php echo $property['13']; ?></p>
    <!-- Display one of the property images (adjust the index if needed) -->
    <img src="admin/property/<?php echo $property['18']; ?>" alt="<?php echo $property['1']; ?>" style="max-width:300px;" class="mb-4">
    
    <!-- Payment Form -->
        <form action="process-payment.php" method="POST">
        <input type="hidden" name="property_id" value="1">
        <input type="hidden" name="amount" value="3500000">
        
        <label for="uname">Full Name:</label>
        <input type="text" id="uname" name="uname" required>
        
        <label for="uemail">Email:</label>
        <input type="email" id="uemail" name="uemail" required>
        
        <label for="card_number">Card Number:</label>
        <input type="text" id="card_number" name="card_number" required>
        
        <label for="expiry_date">Expiry Date:</label>
        <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/YY" required>
        
        <label for="cvv">CVV:</label>
        <input type="text" id="cvv" name="cvv" required>
        
        <a href="payment-success.php" class="btn btn-lg btn-success">Payment</a>
         </form>
        
        
        
    
</div>
</body>
</html>
