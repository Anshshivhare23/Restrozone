<!DOCTYPE html>
<html lang="en">
<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../connection/connect.php");
session_start();

if (empty($_SESSION["adm_id"])) {
    header('location:index.php');
    exit;
}

// Get restaurant ID or set default value
$rs_id = isset($_SESSION['rs_id']) ? intval($_SESSION['rs_id']) : 0;

// Optimize queries by caching common data
$today = date('Y-m-d');

// Build query based on whether we have a restaurant ID
$restaurant_specific_stats = $rs_id > 0 ? ",
    (SELECT SUM(capacity) FROM restaurant_tables WHERE rs_id = $rs_id) as total_capacity,
    (SELECT SUM(people) FROM table_bookings WHERE rs_id = $rs_id AND date = '$today' AND status != 'cancelled') as booked_seats" 
    : ", 0 as total_capacity, 0 as booked_seats";

// Get all the counts in a single query
$dashboard_stats_query = "SELECT 
    (SELECT COUNT(*) FROM dishes) as total_dishes,
    (SELECT COUNT(*) FROM users_orders) as total_orders,
    (SELECT COUNT(*) FROM users_orders WHERE status = 'in process') as processing_orders,
    (SELECT COUNT(*) FROM users_orders WHERE status = 'closed') as delivered_orders,
    (SELECT COUNT(*) FROM users_orders WHERE status = 'rejected') as cancelled_orders,
    (SELECT COUNT(*) FROM table_bookings) as total_bookings,
    (SELECT COUNT(*) FROM table_bookings WHERE status = 'pending') as pending_bookings,
    (SELECT COUNT(*) FROM table_bookings WHERE status = 'confirmed') as confirmed_bookings,
    (SELECT COUNT(*) FROM table_bookings WHERE date = '$today') as today_bookings,
    (SELECT COUNT(*) FROM res_category) as total_categories" . $restaurant_specific_stats;

$stats_result = mysqli_query($db, $dashboard_stats_query);
if (!$stats_result) {
    die("Query failed: " . mysqli_error($db));
}
$stats = mysqli_fetch_assoc($stats_result);

// Calculate remaining capacity
$total_capacity = intval($stats['total_capacity']);
$booked_seats = intval($stats['booked_seats']);
$remaining_capacity = max(0, $total_capacity - $booked_seats);

?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Restaurant Panel</title>
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="fix-header">

    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
        </svg>
    </div>

    <div id="main-wrapper">

        <!-- Header -->
        <div class="header">
            <nav class="navbar top-navbar navbar-expand-md navbar-light">
                <div class="navbar-header">
                    <a class="navbar-brand" href="dashboard.php">
                        <span><img src="./images/logo restrozone.png" alt="homepage" class="dark-logo" width="80%"/></span>
                    </a>
                </div>

                <div class="navbar-collapse">
                    <ul class="navbar-nav mr-auto mt-md-0"></ul>
                    <ul class="navbar-nav my-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-muted" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="images/bookingSystem/logo.jpg" alt="user" class="profile-pic"/>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right animated zoomIn">
                                <ul class="dropdown-user">
                                    <li><a href="logout.php"><i class="fa fa-power-off"></i> Logout</a></li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>

        <!-- Sidebar -->
        <div class="left-sidebar">
            <div class="scroll-sidebar">
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <li class="nav-devider"></li>
                        <li class="nav-label">Home</li>
                        <li><a href="dashboard.php"><i class="fa fa-tachometer"></i><span>Dashboard</span></a></li>

                        <li class="nav-label">Log</li>
                        <li><a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-archive f-s-20 color-warning"></i><span class="hide-menu">Restaurant</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="add_category.php">Add Category</a></li>
                                <li><a href="add_restaurant.php">Add </a></li>
                               </ul>
                        </li>

                        <li><a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-cutlery" aria-hidden="true"></i><span class="hide-menu">Menu</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="all_menu.php">All Menus</a></li>
                                <li><a href="add_menu.php">Add Menu</a></li>
                            </ul>
                        </li>

                        <li><a href="all_orders.php"><i class="fa fa-shopping-cart" aria-hidden="true"></i><span>Orders</span></a></li>
                        <li><a href="all_booking.php"><i class="fa fa-shopping-cart" aria-hidden="true"></i><span>Table Booking</span></a></li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <div class="container-fluid">
                <div class="col-lg-12">
                    <div class="card card-outline-primary">
                        <div class="card-header">
                            <h4 class="m-b-0 text-white">Restaurant Dashboard</h4>
                        </div>

                        <div class="card-body">
                            <div class="row">

                                <!-- Dishes -->
                                <div class="col-md-3">
                                    <div class="card p-30">
                                        <div class="media">
                                            <div class="media-left media-middle">
                                                <span><i class="fa fa-cutlery f-s-40" aria-hidden="true"></i></span>
                                            </div>
                                            <div class="media-body media-text-right">
                                                <h2>
                                                    <?php echo $stats['total_dishes']; ?>
                                                </h2>
                                                <p class="m-b-0">Dishes</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Total Orders -->
                                <div class="col-md-3">
                                    <div class="card p-30">
                                        <div class="media">
                                            <div class="media-left media-middle">
                                                <span><i class="fa fa-shopping-cart f-s-40" aria-hidden="true"></i></span>
                                            </div>
                                            <div class="media-body media-text-right">
                                                <h2>
                                                    <?php echo $stats['total_orders']; ?>
                                                </h2>
                                                <p class="m-b-0">Total Orders</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- In Process -->
                                <div class="col-md-3">
                                    <div class="card p-30">
                                        <div class="media">
                                            <div class="media-left media-middle">
                                                <span><i class="fa fa-spinner f-s-40" aria-hidden="true"></i></span>
                                            </div>
                                            <div class="media-body media-text-right">
                                                <h2>
                                                    <?php echo $stats['processing_orders']; ?>
                                                </h2>
                                                <p class="m-b-0">Processing Orders</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delivered Orders -->
                                <div class="col-md-3">
                                    <div class="card p-30">
                                        <div class="media">
                                            <div class="media-left media-middle">
                                                <span><i class="fa fa-check f-s-40" aria-hidden="true"></i></span>
                                            </div>
                                            <div class="media-body media-text-right">
                                                <h2>
                                                    <?php echo $stats['delivered_orders']; ?>
                                                </h2>
                                                <p class="m-b-0">Delivered Orders</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cancelled Orders -->
                                <div class="col-md-3">
                                    <div class="card p-30">
                                        <div class="media">
                                            <div class="media-left media-middle">
                                                <span><i class="fa fa-times f-s-40" aria-hidden="true"></i></span>
                                            </div>
                                            <div class="media-body media-text-right">
                                                <h2>
                                                    <?php echo $stats['cancelled_orders']; ?>
                                                </h2>
                                                <p class="m-b-0">Cancelled Orders</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Table Booking -->
                                <div class="col-md-3">
                                    <div class="card p-30">
                                        <div class="media">
                                            <div class="media-left media-middle">
                                                <span><i class="fa fa-check f-s-40" aria-hidden="true"></i></span>
                                            </div>
                                            <div class="media-body media-text-right">
                                                <h2>
                                                    <?php echo $stats['total_bookings']; ?>
                                                </h2>
                                                <p class="m-b-0">Table Booking</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            <div class="col-md-4">
                                <div class="card p-30">
                                    <div class="media">
                                        <div class="media-left meida media-middle">
                                            <span><i class="fa fa-th-large f-s-40" aria-hidden="true"></i></span>
                                        </div>
                                        <div class="media-body media-text-right">
                                            <h2><?php echo $stats['total_categories']; ?></h2>
                                            <p class="m-b-0">Restro Categories</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                                <!-- Pending Bookings -->
                                <div class="col-md-3">
                                    <div class="card p-30">
                                        <div class="media">
                                            <div class="media-left media-middle">
                                                <span><i class="fa fa-clock-o f-s-40" aria-hidden="true"></i></span>
                                            </div>
                                            <div class="media-body media-text-right">
                                                <h2>
                                                    <?php echo $stats['pending_bookings']; ?>
                                                </h2>
                                                <p class="m-b-0">Pending Bookings</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Confirmed Bookings -->
                                <div class="col-md-3">
                                    <div class="card p-30">
                                        <div class="media">
                                            <div class="media-left media-middle">
                                                <span><i class="fa fa-check-circle f-s-40" aria-hidden="true"></i></span>
                                            </div>
                                            <div class="media-body media-text-right">
                                                <h2>
                                                    <?php echo $stats['confirmed_bookings']; ?>
                                                </h2>
                                                <p class="m-b-0">Confirmed Bookings</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Today's Bookings -->
                                <div class="col-md-3">
                                    <div class="card p-30">
                                        <div class="media">
                                            <div class="media-left media-middle">
                                                <span><i class="fa fa-calendar-check-o f-s-40" aria-hidden="true"></i></span>
                                            </div>
                                            <div class="media-body media-text-right">
                                                <h2>
                                                    <?php echo $stats['today_bookings']; ?>
                                                </h2>
                                                <p class="m-b-0">Today's Bookings</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div> <!-- End Row -->
                        </div> <!-- End Card Body -->

                    </div>
                </div>
            </div>

            <?php include "include/footer.php" ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="js/lib/jquery/jquery.min.js"></script>
    <script src="js/lib/bootstrap/js/popper.min.js"></script>
    <script src="js/lib/bootstrap/js/bootstrap.min.js"></script>
    <script src="js/jquery.slimscroll.js"></script>
    <script src="js/sidebarmenu.js"></script>
    <script src="js/lib/sticky-kit-master/dist/sticky-kit.min.js"></script>
    <script src="js/custom.min.js"></script>

</body>
</html>
