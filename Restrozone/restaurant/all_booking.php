<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Correct path to connection file
include("../connection/connect.php");
session_start();

// Check if restaurant_tables table exists and create if needed
$check_tables_exist = mysqli_query($db, "SHOW TABLES LIKE 'restaurant_tables'");
if(mysqli_num_rows($check_tables_exist) == 0) {
    $create_tables_sql = "CREATE TABLE IF NOT EXISTS restaurant_tables (
        id INT AUTO_INCREMENT PRIMARY KEY, 
        rs_id INT NOT NULL, 
        table_name VARCHAR(50) NOT NULL, 
        capacity INT NOT NULL,
        FOREIGN KEY (rs_id) REFERENCES restaurant(rs_id) ON DELETE CASCADE
    )";
    mysqli_query($db, $create_tables_sql);
    
    // Add sample data for restaurant tables if table was just created
    $check_data = mysqli_query($db, "SELECT * FROM restaurant_tables LIMIT 1");
    if(mysqli_num_rows($check_data) == 0) {
        $sample_data_sql = "INSERT INTO restaurant_tables (rs_id, table_name, capacity) VALUES
            (1, 'Table A1', 2),
            (1, 'Table A2', 4),
            (1, 'Table A3', 6),
            (1, 'Table A4', 8),
            (2, 'Table B1', 2),
            (2, 'Table B2', 4),
            (2, 'Table B3', 6),
            (3, 'Table C1', 2),
            (3, 'Table C2', 4),
            (3, 'Table C3', 6),
            (4, 'Table D1', 2),
            (4, 'Table D2', 4),
            (4, 'Table D3', 6)";
        mysqli_query($db, $sample_data_sql);
    }
}

// Check if table_bookings exists, if not create it
$check_bookings_exist = mysqli_query($db, "SHOW TABLES LIKE 'table_bookings'");
if(mysqli_num_rows($check_bookings_exist) == 0) {
    // Create table_bookings from scratch with all needed columns
    $create_bookings_sql = "CREATE TABLE table_bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        rs_id INT NOT NULL,
        table_id INT DEFAULT NULL,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        date DATE NOT NULL,
        time TIME NOT NULL,
        people INT NOT NULL,
        status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
        FOREIGN KEY (rs_id) REFERENCES restaurant(rs_id) ON DELETE CASCADE,
        FOREIGN KEY (table_id) REFERENCES restaurant_tables(id) ON DELETE SET NULL
    )";
    mysqli_query($db, $create_bookings_sql);
} else {
    // Table exists, check and add missing columns
    
    // Check if rs_id column exists
    $check_rs_id = mysqli_query($db, "SHOW COLUMNS FROM table_bookings LIKE 'rs_id'");
    if(mysqli_num_rows($check_rs_id) == 0) {
        mysqli_query($db, "ALTER TABLE table_bookings ADD COLUMN rs_id INT NOT NULL DEFAULT 1");
    }

    // Check if table_id column exists
    $check_table_id = mysqli_query($db, "SHOW COLUMNS FROM table_bookings LIKE 'table_id'");
    if(mysqli_num_rows($check_table_id) == 0) {
        mysqli_query($db, "ALTER TABLE table_bookings ADD COLUMN table_id INT DEFAULT NULL");
    }

    // Check if status column exists
    $check_status = mysqli_query($db, "SHOW COLUMNS FROM table_bookings LIKE 'status'");
    if(mysqli_num_rows($check_status) == 0) {
        mysqli_query($db, "ALTER TABLE table_bookings ADD COLUMN status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending'");
    }

    // Check existing foreign keys
    $check_fk_restaurant = mysqli_query($db, "SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
        WHERE REFERENCED_TABLE_NAME = 'restaurant' 
        AND TABLE_NAME = 'table_bookings' 
        AND COLUMN_NAME = 'rs_id'
        AND CONSTRAINT_SCHEMA = '" . $db->real_escape_string($dbname) . "'");

    $check_fk_table = mysqli_query($db, "SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
        WHERE REFERENCED_TABLE_NAME = 'restaurant_tables' 
        AND TABLE_NAME = 'table_bookings' 
        AND COLUMN_NAME = 'table_id'
        AND CONSTRAINT_SCHEMA = '" . $db->real_escape_string($dbname) . "'");

    // Drop existing foreign keys if they exist (to avoid duplicates)
    if(mysqli_num_rows($check_fk_restaurant) > 0) {
        $row = mysqli_fetch_assoc($check_fk_restaurant);
        mysqli_query($db, "ALTER TABLE table_bookings DROP FOREIGN KEY " . $row['CONSTRAINT_NAME']);
    }

    if(mysqli_num_rows($check_fk_table) > 0) {
        $row = mysqli_fetch_assoc($check_fk_table);
        mysqli_query($db, "ALTER TABLE table_bookings DROP FOREIGN KEY " . $row['CONSTRAINT_NAME']);
    }

    // Add foreign keys with new names
    mysqli_query($db, "ALTER TABLE table_bookings 
        ADD CONSTRAINT fk_booking_restaurant FOREIGN KEY (rs_id) REFERENCES restaurant(rs_id) ON DELETE CASCADE");
    
    mysqli_query($db, "ALTER TABLE table_bookings 
        ADD CONSTRAINT fk_booking_table FOREIGN KEY (table_id) REFERENCES restaurant_tables(id) ON DELETE SET NULL");
}

// Handle status updates
if(isset($_GET['booking_id']) && isset($_GET['status'])) {
    $booking_id = intval($_GET['booking_id']);
    $status = mysqli_real_escape_string($db, $_GET['status']);
    
    if(in_array($status, ['pending', 'confirmed', 'completed', 'cancelled'])) {
        $update_sql = "UPDATE table_bookings SET status = '$status' WHERE id = $booking_id";
        if(mysqli_query($db, $update_sql)) {
            $success_message = "Booking status updated successfully!";
        } else {
            $error_message = "Error updating booking status: " . mysqli_error($db);
        }
    }
}

// Get current restaurant ID (for restaurant-specific admin)
$restaurant_id = isset($_SESSION['rs_id']) ? $_SESSION['rs_id'] : 0;

// Handle date filter
$date_filter = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <title>Table Bookings</title>
    <!-- Bootstrap Core CSS -->
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <style>
        .booking-status {
            padding: 5px 10px;
            border-radius: 4px;
            color: white;
        }
        .status-pending { background-color: #ffc107; }
        .status-confirmed { background-color: #28a745; }
        .status-completed { background-color: #007bff; }
        .status-cancelled { background-color: #dc3545; }
    </style>
</head>

<body class="fix-header fix-sidebar">
    <div class="preloader">
        <div class="loader">
            <div class="loader__figure"></div>
            <p class="loader__label">Loading...</p>
        </div>
    </div>

    <div id="main-wrapper">
        <div class="header">
            <nav class="navbar top-navbar navbar-expand-md navbar-light">
                <div class="navbar-header">
                    <a class="navbar-brand" href="dashboard.php">
                        <span><img src="../images/logo restrozone.png" alt="homepage" class="dark-logo" width="80%"/></span>
                    </a>
                </div>
                <div class="navbar-collapse">
                    <ul class="navbar-nav mr-auto mt-md-0">
                    </ul>

                    <ul class="navbar-nav my-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-muted" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="../images/bookingSystem/logo.jpg" alt="user" class="profile-pic" /></a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <ul class="dropdown-user">
                                    <li><a href="logout.php"><i class="fa fa-power-off"></i> Logout</a></li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>

        <div class="left-sidebar">
            <div class="scroll-sidebar">
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <li class="nav-devider"></li>
                        <li class="nav-label">Home</li>
                        <li> <a href="dashboard.php"><i class="fa fa-tachometer"></i><span>Dashboard</span></a></li>
                        <li class="nav-label">Log</li>
                        <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-archive f-s-20 color-warning"></i><span class="hide-menu">Restaurant</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="all_restaurant.php">All Restaurants</a></li>
                                <li><a href="add_category.php">Add Category</a></li>
                                <li><a href="add_restaurant.php">Add Restaurant</a></li>
                            </ul>
                        </li>
                        <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-cutlery" aria-hidden="true"></i><span class="hide-menu">Menu</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="all_menu.php">All Menus</a></li>
                                <li><a href="add_menu.php">Add Menu</a></li>
                            </ul>
                        </li>
                        <li> <a href="all_orders.php"><i class="fa fa-shopping-cart" aria-hidden="true"></i><span>Orders</span></a></li>
                        <li> <a href="all_booking.php"><i class="fa fa-calendar" aria-hidden="true"></i><span>Table Bookings</span></a></li>
                        <li> <a href="manage_tables.php"><i class="fa fa-table" aria-hidden="true"></i><span>Manage Tables</span></a></li>
                    </ul>
                </nav>
            </div>
        </div>

        <div class="page-wrapper">
            <div class="container-fluid">
                <?php if(isset($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
                <?php endif; ?>
                
                <?php if(isset($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-12">
                        <div class="col-lg-12">
                            <div class="card card-outline-primary">
                                <div class="card-header">
                                    <h4 class="m-b-0 text-white">Table Bookings Management</h4>
                                </div>
                                
                                <div class="card-body">
                                    <!-- Date filter -->
                                    <div class="date-filter">
                                        <form method="GET" action="" class="form-inline">
                                            <div class="form-group mx-sm-3 mb-2">
                                                <label for="date" class="mr-2">Filter by Date:</label>
                                                <input type="date" class="form-control" id="date" name="date" value="<?php echo $date_filter; ?>">
                                            </div>
                                            <button type="submit" class="btn btn-primary mb-2">Filter</button>
                                            <a href="all_booking.php" class="btn btn-secondary mb-2 ml-2">Reset</a>
                                        </form>
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table id="bookingTable" class="table table-bordered table-striped">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Restaurant</th>
                                                    <th>Table</th>
                                                    <th>Customer</th>
                                                    <th>Contact</th>
                                                    <th>Date</th>
                                                    <th>Time</th>
                                                    <th>People</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Build query based on filters
                                                $where_conditions = [];
                                                
                                                if ($restaurant_id > 0) {
                                                    $where_conditions[] = "b.rs_id = $restaurant_id";
                                                }
                                                
                                                if (!empty($date_filter)) {
                                                    $where_conditions[] = "b.date = '$date_filter'";
                                                }
                                                
                                                $where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";
                                                
                                                $sql = "SELECT b.*, 
                                                        r.title as restaurant_name, 
                                                        t.table_name, 
                                                        t.capacity
                                                      FROM table_bookings b
                                                      LEFT JOIN restaurant r ON b.rs_id = r.rs_id
                                                      LEFT JOIN restaurant_tables t ON b.table_id = t.id
                                                      $where_clause
                                                      ORDER BY b.date DESC, b.time DESC";
                                                
                                                $query = mysqli_query($db, $sql);
                                                
                                                if (!$query || mysqli_num_rows($query) == 0) {
                                                    echo '<tr><td colspan="10"><center>No Bookings Found</center></td></tr>';
                                                } else {
                                                    while ($row = mysqli_fetch_array($query)) {
                                                        $status_class = isset($row['status']) ? "status-" . $row['status'] : "status-pending";
                                                        $status_text = isset($row['status']) ? ucfirst($row['status']) : "Pending";

                                                        echo '<tr>
                                                            <td>' . $row['id'] . '</td>
                                                            <td>' . $row['restaurant_name'] . '</td>
                                                            <td>' . ($row['table_name'] ? $row['table_name'] . ' (Capacity: ' . $row['capacity'] . ')' : 'Not assigned') . '</td>
                                                            <td>' . $row['name'] . '</td>
                                                            <td>' . $row['email'] . '<br>' . $row['phone'] . '</td>
                                                            <td>' . date('M d, Y', strtotime($row['date'])) . '</td>
                                                            <td>' . date('h:i A', strtotime($row['time'])) . '</td>
                                                            <td>' . $row['people'] . '</td>
                                                            <td><span class="booking-status ' . $status_class . '">' . $status_text . '</span></td>
                                                            <td>
                                                                <div class="dropdown">
                                                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton' . $row['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                        Update Status
                                                                    </button>
                                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton' . $row['id'] . '">
                                                                        <a class="dropdown-item" href="all_booking.php?booking_id=' . $row['id'] . '&status=pending">Pending</a>
                                                                        <a class="dropdown-item" href="all_booking.php?booking_id=' . $row['id'] . '&status=confirmed">Confirm</a>
                                                                        <a class="dropdown-item" href="all_booking.php?booking_id=' . $row['id'] . '&status=completed">Complete</a>
                                                                        <a class="dropdown-item" href="all_booking.php?booking_id=' . $row['id'] . '&status=cancelled">Cancel</a>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>';
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="footer"> © 2025 Restrozone Table Booking System </footer>
        </div>
    </div>

    <!-- All Scripts -->
    <script src="js/lib/jquery/jquery.min.js"></script>
    <script src="js/lib/bootstrap/js/popper.min.js"></script>
    <script src="js/lib/bootstrap/js/bootstrap.min.js"></script>
    <script src="js/jquery.slimscroll.js"></script>
    <script src="js/sidebarmenu.js"></script>
    <script src="js/custom.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize dropdowns
            $('.dropdown-toggle').dropdown();
            
            // Hide preloader with a delay if it hasn't been hidden yet
            setTimeout(function() {
                $('.preloader').fadeOut('slow');
            }, 1000);
            
            // Initialize DataTable if available
            if ($.fn.DataTable) {
                $('#bookingTable').DataTable({
                    "order": [[ 5, "desc" ], [ 6, "desc" ]],
                    "pageLength": 10
                });
            }
        });
    </script>
</body>
</html>