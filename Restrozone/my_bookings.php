<?php
include("connection/connect.php");
error_reporting(0);
session_start();

// Check if user is logged in
if(empty($_SESSION['user_id'])) {
    header('location:login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Handle booking cancellation
if(isset($_GET['cancel_id']) && !empty($_GET['cancel_id'])) {
    $booking_id = intval($_GET['cancel_id']);
    
    // Verify this booking belongs to the user
    $check_sql = "SELECT * FROM table_bookings WHERE id = $booking_id AND customer_id = $user_id";
    $check_result = mysqli_query($db, $check_sql);
    
    if(mysqli_num_rows($check_result) > 0) {
        // Update booking status to cancelled
        $cancel_sql = "UPDATE table_bookings SET status = 'cancelled' WHERE id = $booking_id";
        if(mysqli_query($db, $cancel_sql)) {
            $success_message = "Your booking has been cancelled successfully.";
        } else {
            $error_message = "Error cancelling booking: " . mysqli_error($db);
        }
    } else {
        $error_message = "Invalid booking or you don't have permission to cancel it.";
    }
}

// Fetch user's bookings
$bookings_sql = "SELECT b.*, r.title as restaurant_name, t.table_name 
                FROM table_bookings b
                LEFT JOIN restaurant r ON b.rs_id = r.rs_id
                LEFT JOIN restaurant_tables t ON b.table_id = t.id
                WHERE b.customer_id = $user_id
                ORDER BY b.date DESC, b.time DESC";
$bookings_result = mysqli_query($db, $bookings_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="#">
    <title>My Table Bookings</title>
    <link rel="stylesheet" href="css/dropdown.css">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animsition.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .booking-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }
        .booking-header {
            padding: 15px;
            border-bottom: 1px solid #eee;
            background-color: #f8f9fa;
        }
        .booking-body {
            padding: 20px;
        }
        .booking-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
            text-align: center;
        }
        .status-pending {
            background-color: #ffc107;
            color: #000;
        }
        .status-confirmed {
            background-color: #28a745;
            color: #fff;
        }
        .status-completed {
            background-color: #007bff;
            color: #fff;
        }
        .status-cancelled {
            background-color: #dc3545;
            color: #fff;
        }
        .booking-actions {
            margin-top: 15px;
            text-align: right;
        }
        .no-bookings {
            background-color: #f8f9fa;
            text-align: center;
            padding: 40px;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <header id="header" class="header-scroll top-header headrom">
        <nav class="navbar navbar-dark">
            <div class="container">
                <button class="navbar-toggler hidden-lg-up" type="button" data-toggle="collapse" data-target="#mainNavbarCollapse">&#9776;</button>
                <a class="navbar-brand" href="index.php"> <img class="img-rounded" src="images/logo restrozone.png" alt=""> </a>
                <div class="collapse navbar-toggleable-md  float-lg-right" id="mainNavbarCollapse">
                    <ul class="nav navbar-nav">
                        <li class="nav-item"> <a class="nav-link active" href="index.php">Home <span class="sr-only">(current)</span></a> </li>
                        <li class="nav-item"> <a class="nav-link active" href="restaurants.php">Restaurants <span class="sr-only"></span></a> </li>
                        
                        <?php
                        if(empty($_SESSION["user_id"])) {
                            echo '<li class="nav-item"><a href="login.php" class="nav-link active">Login</a> </li>
                                <li class="nav-item"><a href="registration.php" class="nav-link active">Register</a> </li>';
                        } else {
                            echo '<li class="nav-item"><a href="your_orders.php" class="nav-link active">My Orders</a> </li>';
                            echo '<li class="nav-item"><a href="my_bookings.php" class="nav-link active">My Table Bookings</a> </li>';
                            echo '<li class="nav-item"><a href="logout.php" class="nav-link active">Logout</a> </li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    
    <div class="page-wrapper">
        <div class="top-links">
            <div class="container">
                <ul class="row links">
                    <li class="col-xs-12 col-sm-4 link-item"><span>1</span><a href="index.php">Home</a></li>
                    <li class="col-xs-12 col-sm-4 link-item"><span>2</span><a href="restaurants.php">Restaurants</a></li>
                    <li class="col-xs-12 col-sm-4 link-item active"><span>3</span><a href="my_bookings.php">My Table Bookings</a></li>
                </ul>
            </div>
        </div>
        
        <div class="container m-t-30">
            <div class="row">
                <div class="col-xs-12">
                    <div class="bg-gray">
                        <div class="row">
                            <div class="col-xs-12">
                                <h2 class="title">My Table Bookings</h2>
                            </div>
                            
                            <?php if(!empty($success_message)): ?>
                            <div class="col-xs-12">
                                <div class="alert alert-success">
                                    <?php echo $success_message; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if(!empty($error_message)): ?>
                            <div class="col-xs-12">
                                <div class="alert alert-danger">
                                    <?php echo $error_message; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if(mysqli_num_rows($bookings_result) == 0): ?>
                            <div class="col-xs-12">
                                <div class="no-bookings">
                                    <h4>You don't have any table bookings yet.</h4>
                                    <p>Visit our <a href="restaurants.php">restaurants page</a> to book a table!</p>
                                </div>
                            </div>
                            <?php else: ?>
                                <?php while($booking = mysqli_fetch_assoc($bookings_result)): ?>
                                <div class="col-xs-12 col-sm-6 col-md-6">
                                    <div class="booking-card">
                                        <div class="booking-header">
                                            <div class="row">
                                                <div class="col-xs-8">
                                                    <h4><?php echo $booking['restaurant_name']; ?></h4>
                                                </div>
                                                <div class="col-xs-4 text-xs-right">
                                                    <?php 
                                                    $status_class = !empty($booking['status']) ? 'status-' . $booking['status'] : 'status-pending';
                                                    $status_text = !empty($booking['status']) ? ucfirst($booking['status']) : 'Pending';
                                                    ?>
                                                    <span class="booking-status <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="booking-body">
                                            <div class="row">
                                                <div class="col-xs-6">
                                                    <p><strong>Date:</strong> <?php echo date('M d, Y', strtotime($booking['date'])); ?></p>
                                                    <p><strong>Time:</strong> <?php echo date('h:i A', strtotime($booking['time'])); ?></p>
                                                    <p><strong>Party Size:</strong> <?php echo $booking['people']; ?> people</p>
                                                </div>
                                                <div class="col-xs-6">
                                                    <p><strong>Table:</strong> <?php echo !empty($booking['table_name']) ? $booking['table_name'] : 'To be assigned'; ?></p>
                                                    <p><strong>Booking ID:</strong> #<?php echo $booking['id']; ?></p>
                                                    <p><strong>Booked On:</strong> <?php echo date('M d, Y', strtotime($booking['date'])); ?></p>
                                                </div>
                                            </div>
                                            
                                            <?php if($booking['status'] != 'cancelled' && $booking['status'] != 'completed'): ?>
                                            <div class="booking-actions">
                                                <a href="my_bookings.php?cancel_id=<?php echo $booking['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel Booking</a>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include "include/footer.php"; ?>
    
    <script src="js/jquery.min.js"></script>
    <script src="js/tether.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/animsition.min.js"></script>
    <script src="js/bootstrap-slider.min.js"></script>
    <script src="js/jquery.isotope.min.js"></script>
    <script src="js/headroom.js"></script>
    <script src="js/foodpicky.min.js"></script>
</body>
</html>