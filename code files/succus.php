<?php
session_start();
include("config.php");

if (!isset($_SESSION['payment_success']) || !isset($_SESSION['property_id'])) {
    header("Location: index.php");
    exit;
}

$pid = $_SESSION['property_id'];
$query = mysqli_query($conn, "SELECT * FROM property WHERE pid='$pid'");
$property = mysqli_fetch_array($query);

// Clear the session variables after use
unset($_SESSION['payment_success']);
unset($_SESSION['property_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Successful</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include("include/header.php"); ?>

<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-md-8 mx-auto text-center">
            <div class="card">
                <div class="card-body">
                    <i class="fa fa-check-circle text-success" style="font-size: 64px;"></i>
                    <h2 class="mt-3">Payment Successful!</h2>
                    <p>Thank you for purchasing <?php echo $property['1']; ?>.</p>
                    <p>A confirmation email has been sent to your registered email address.</p>
                    <a href="index.php" class="btn btn-primary mt-3">Return to Home</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("include/footer.php"); ?>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>













