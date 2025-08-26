<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

// Check if the database structure is updated
$check_tables_exist = mysqli_query($db, "SHOW TABLES LIKE 'restaurant_tables'");
if(mysqli_num_rows($check_tables_exist) == 0) {
    // Create restaurant_tables table if it doesn't exist
    $create_tables_sql = "CREATE TABLE IF NOT EXISTS restaurant_tables (
        id INT AUTO_INCREMENT PRIMARY KEY, 
        rs_id INT NOT NULL, 
        table_name VARCHAR(50) NOT NULL, 
        capacity INT NOT NULL,
        FOREIGN KEY (rs_id) REFERENCES restaurant(rs_id) ON DELETE CASCADE
    )";
    mysqli_query($db, $create_tables_sql);
}

// Handle form submissions
if(isset($_POST['add_table'])) {
    $rs_id = intval($_POST['restaurant_id']);
    $table_name = mysqli_real_escape_string($db, $_POST['table_name']);
    $capacity = intval($_POST['capacity']);
    
    if($rs_id > 0 && !empty($table_name) && $capacity > 0) {
        $insert_sql = "INSERT INTO restaurant_tables (rs_id, table_name, capacity) 
                      VALUES ($rs_id, '$table_name', $capacity)";
        
        if(mysqli_query($db, $insert_sql)) {
            $success_message = "Table added successfully!";
        } else {
            $error_message = "Error adding table: " . mysqli_error($db);
        }
    } else {
        $error_message = "Please fill in all fields correctly.";
    }
}

// Handle table deletion
if(isset($_GET['delete_table'])) {
    $table_id = intval($_GET['delete_table']);
    
    // Check if the table is used in any bookings
    $check_sql = "SELECT COUNT(*) as count FROM table_bookings WHERE table_id = $table_id";
    $check_result = mysqli_query($db, $check_sql);
    $row = mysqli_fetch_assoc($check_result);
    
    if($row['count'] > 0) {
        $error_message = "Cannot delete this table as it has existing bookings.";
    } else {
        $delete_sql = "DELETE FROM restaurant_tables WHERE id = $table_id";
        if(mysqli_query($db, $delete_sql)) {
            $success_message = "Table deleted successfully!";
        } else {
            $error_message = "Error deleting table: " . mysqli_error($db);
        }
    }
}

// Get restaurant list
$restaurant_query = "SELECT * FROM restaurant ORDER BY title";
$restaurant_result = mysqli_query($db, $restaurant_query);

// Get selected restaurant ID from dropdown or URL
$selected_restaurant = isset($_GET['restaurant_id']) ? intval($_GET['restaurant_id']) : 
                      (isset($_POST['restaurant_id']) ? intval($_POST['restaurant_id']) : 0);

// Get tables for the selected restaurant
$tables_sql = "SELECT t.*, r.title as restaurant_name 
              FROM restaurant_tables t
              JOIN restaurant r ON t.rs_id = r.rs_id";
              
if($selected_restaurant > 0) {
    $tables_sql .= " WHERE t.rs_id = $selected_restaurant";
}

$tables_sql .= " ORDER BY r.title, t.table_name";
$tables_result = mysqli_query($db, $tables_sql);
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
    <title>Manage Restaurant Tables</title>
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body class="fix-header fix-sidebar">
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
        </svg>
    </div>

    <div id="main-wrapper">
        <div class="header">
            <nav class="navbar top-navbar navbar-expand-md navbar-light">
                <div class="navbar-header">
                    <a class="navbar-brand" href="dashboard.php">
                        <span><img src="./images/logo restrozone.png" alt="homepage" class="dark-logo" width="80%"/></span>
                    </a>
                </div>
                <div class="navbar-collapse">
                    <ul class="navbar-nav mr-auto mt-md-0">
                    </ul>
                    <ul class="navbar-nav my-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-muted" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="images/bookingSystem/logo.jpg" alt="user" class="profile-pic" /></a>
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
                        <li><a href="dashboard.php"><i class="fa fa-tachometer"></i><span>Dashboard</span></a></li>
                        <li class="nav-label">Log</li>
                        <li><a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-archive f-s-20 color-warning"></i><span class="hide-menu">Restaurant</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="all_restaurant.php">All Restaurants</a></li>
                                <li><a href="add_category.php">Add Category</a></li>
                                <li><a href="add_restaurant.php">Add Restaurant</a></li>
                            </ul>
                        </li>
                        <li><a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-cutlery" aria-hidden="true"></i><span class="hide-menu">Menu</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="all_menu.php">All Menues</a></li>
                                <li><a href="add_menu.php">Add Menu</a></li>
                            </ul>
                        </li>
                        <li><a href="all_orders.php"><i class="fa fa-shopping-cart" aria-hidden="true"></i><span>Orders</span></a></li>
                        <li><a href="all_booking.php"><i class="fa fa-calendar" aria-hidden="true"></i><span>Table Bookings</span></a></li>
                        <li><a href="manage_tables.php"><i class="fa fa-table" aria-hidden="true"></i><span>Manage Tables</span></a></li>
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
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Add New Table</h4>
                                <form method="POST" action="manage_tables.php">
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label for="restaurant_id">Restaurant</label>
                                            <select class="form-control" id="restaurant_id" name="restaurant_id" required>
                                                <option value="">-- Select Restaurant --</option>
                                                <?php while($restaurant = mysqli_fetch_assoc($restaurant_result)): ?>
                                                <option value="<?php echo $restaurant['rs_id']; ?>" <?php echo ($selected_restaurant == $restaurant['rs_id']) ? 'selected' : ''; ?>>
                                                    <?php echo $restaurant['title']; ?>
                                                </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="table_name">Table Name</label>
                                            <input type="text" class="form-control" id="table_name" name="table_name" placeholder="e.g. Table A1" required>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="capacity">Capacity</label>
                                            <input type="number" class="form-control" id="capacity" name="capacity" min="1" max="20" required>
                                        </div>
                                        <div class="form-group col-md-1">
                                            <label>&nbsp;</label>
                                            <button type="submit" name="add_table" class="btn btn-primary form-control">Add</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Filter Tables</h4>
                                <form method="GET" action="manage_tables.php">
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label for="filter_restaurant">Filter by Restaurant</label>
                                            <select class="form-control" id="filter_restaurant" name="restaurant_id" onchange="this.form.submit()">
                                                <option value="0">All Restaurants</option>
                                                <?php 
                                                // Reset the pointer to the beginning of the result set
                                                mysqli_data_seek($restaurant_result, 0);
                                                while($restaurant = mysqli_fetch_assoc($restaurant_result)): 
                                                ?>
                                                <option value="<?php echo $restaurant['rs_id']; ?>" <?php echo ($selected_restaurant == $restaurant['rs_id']) ? 'selected' : ''; ?>>
                                                    <?php echo $restaurant['title']; ?>
                                                </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Restaurant Tables</h4>
                                <div class="table-responsive">
                                    <table id="tablesTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Restaurant</th>
                                                <th>Table Name</th>
                                                <th>Capacity</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(mysqli_num_rows($tables_result) == 0): ?>
                                            <tr>
                                                <td colspan="5" class="text-center">No tables found</td>
                                            </tr>
                                            <?php else: ?>
                                                <?php while($table = mysqli_fetch_assoc($tables_result)): ?>
                                                <tr>
                                                    <td><?php echo $table['id']; ?></td>
                                                    <td><?php echo $table['restaurant_name']; ?></td>
                                                    <td><?php echo $table['table_name']; ?></td>
                                                    <td><?php echo $table['capacity']; ?> people</td>
                                                    <td>
                                                        <a href="manage_tables.php?delete_table=<?php echo $table['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this table?')">Delete</a>
                                                    </td>
                                                </tr>
                                                <?php endwhile; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include "include/footer.php" ?>
        </div>
    </div>

    <script src="js/lib/jquery/jquery.min.js"></script>
    <script src="js/lib/bootstrap/js/popper.min.js"></script>
    <script src="js/lib/bootstrap/js/bootstrap.min.js"></script>
    <script src="js/jquery.slimscroll.js"></script>
    <script src="js/sidebarmenu.js"></script>
    <script src="js/lib/sticky-kit-master/dist/sticky-kit.min.js"></script>
    <script src="js/custom.min.js"></script>
    <script src="js/lib/datatables/datatables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#tablesTable').DataTable();
        });
    </script>
</body>
</html>