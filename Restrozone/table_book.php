<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "restrozone";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if restaurant_tables exists and create if needed
$check_tables_exist = mysqli_query($conn, "SHOW TABLES LIKE 'restaurant_tables'");
if(mysqli_num_rows($check_tables_exist) == 0) {
    // Create restaurant_tables table if it doesn't exist
    $create_tables_sql = "CREATE TABLE IF NOT EXISTS restaurant_tables (
        id INT AUTO_INCREMENT PRIMARY KEY, 
        rs_id INT NOT NULL, 
        table_name VARCHAR(50) NOT NULL, 
        capacity INT NOT NULL,
        FOREIGN KEY (rs_id) REFERENCES restaurant(rs_id) ON DELETE CASCADE
    )";
    mysqli_query($conn, $create_tables_sql);
    
    // Add sample data for restaurant tables if table was just created
    $check_data = mysqli_query($conn, "SELECT * FROM restaurant_tables LIMIT 1");
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
        mysqli_query($conn, $sample_data_sql);
    }
}

// Check if table_bookings exists and create if needed
$check_bookings_exist = mysqli_query($conn, "SHOW TABLES LIKE 'table_bookings'");
if(mysqli_num_rows($check_bookings_exist) == 0) {
    // Create table_bookings if it doesn't exist
    $create_bookings_sql = "CREATE TABLE IF NOT EXISTS table_bookings (
        id INT NOT NULL AUTO_INCREMENT,
        rs_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        date DATE NOT NULL,
        time TIME NOT NULL,
        people INT NOT NULL,
        status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
        table_id INT DEFAULT NULL,
        PRIMARY KEY (id),
        FOREIGN KEY (rs_id) REFERENCES restaurant(rs_id) ON DELETE CASCADE,
        FOREIGN KEY (table_id) REFERENCES restaurant_tables(id) ON DELETE SET NULL
    ) ENGINE=InnoDB";

    if(!mysqli_query($conn, $create_bookings_sql)) {
        die("Error creating table: " . mysqli_error($conn));
    }
}

$success = "";
$error = "";
$rs_id = isset($_GET['res_id']) ? intval($_GET['res_id']) : 0;

// Get restaurant information
$restaurant = null;
if ($rs_id > 0) {
    $rest_query = "SELECT * FROM restaurant WHERE rs_id = ?";
    $stmt = $conn->prepare($rest_query);
    $stmt->bind_param("i", $rs_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $restaurant = $result->fetch_assoc();
    } else {
        $error = "Restaurant not found!";
    }
}

// Form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name   = $conn->real_escape_string(trim($_POST['name']));
    $email  = $conn->real_escape_string(trim($_POST['email']));
    $phone  = $conn->real_escape_string(trim($_POST['phone']));
    $date   = $conn->real_escape_string($_POST['date']);
    $time   = $conn->real_escape_string($_POST['time']);
    $people = intval($_POST['people']);
    $rs_id  = intval($_POST['rs_id']);
    
    // Check if booking time is within operating hours
    if ($restaurant) {
        $booking_time = strtotime($time);
        $opening_time = strtotime('11:00');
        $closing_time = strtotime('23:59');
        
        if ($booking_time < $opening_time || $booking_time > $closing_time) {
            $error = "Sorry, the restaurant is closed at this time. Our hours are 11:00 AM to 12:00 AM";
        } else {
            // Check number of tables already booked
            $booked_tables_query = "SELECT COUNT(*) as booked_tables 
                                       FROM table_bookings 
                                       WHERE rs_id = ? 
                                       AND date = ? 
                                       AND time = ?
                                       AND status != 'cancelled'";
            $stmt = $conn->prepare($booked_tables_query);
            $stmt->bind_param("iss", $rs_id, $date, $time);
            $stmt->execute();
            $booked_tables = $stmt->get_result()->fetch_assoc()['booked_tables'] ?: 0;

            if ($booked_tables >= 10) {
                $error = "Sorry, all tables are booked for this time slot. Maximum 10 tables can be booked at once.";
                
                // Find next available time
                $next_slots_query = "SELECT time 
                                          FROM (
                                              SELECT TIME_FORMAT(TIME('11:00:00') + INTERVAL (n*15) MINUTE, '%H:%i:%s') as time
                                              FROM (
                                                  SELECT 0 as n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4
                                                  UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8
                                              ) numbers
                                              WHERE TIME('11:00:00') + INTERVAL (n*15) MINUTE <= '23:59:59'
                                          ) slots
                                          LEFT JOIN table_bookings b ON b.date = ? 
                                              AND b.time = slots.time 
                                              AND b.rs_id = ?
                                              AND b.status != 'cancelled'
                                          GROUP BY slots.time
                                          HAVING COUNT(*) < 10
                                          ORDER BY time
                                          LIMIT 3";
                $stmt = $conn->prepare($next_slots_query);
                $stmt->bind_param("si", $date, $rs_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $error .= "\n\nNext available time slots:\n";
                    while ($row = $result->fetch_assoc()) {
                        $error .= date('g:i A', strtotime($row['time'])) . "\n";
                    }
                }
            } else {
                // Check total capacity for this restaurant
                $capacity_query = "SELECT SUM(capacity) as total_capacity FROM restaurant_tables WHERE rs_id = ?";
                $stmt = $conn->prepare($capacity_query);
                $stmt->bind_param("i", $rs_id);
                $stmt->execute();
                $total_capacity = $stmt->get_result()->fetch_assoc()['total_capacity'];

                // Get current bookings for this time slot
                $current_bookings_query = "SELECT SUM(people) as booked_seats 
                                  FROM table_bookings 
                                  WHERE rs_id = ? 
                                  AND date = ? 
                                  AND time = ?
                                  AND status != 'cancelled'";
                $stmt = $conn->prepare($current_bookings_query);
                $stmt->bind_param("iss", $rs_id, $date, $time);
                $stmt->execute();
                $booked_seats = $stmt->get_result()->fetch_assoc()['booked_seats'] ?: 0;

                // Check if adding these people would exceed capacity
                if (($booked_seats + $people) > $total_capacity) {
                    $error = "Sorry, we are at full capacity for this time slot. The restaurant can accommodate $total_capacity people at once, and we already have $booked_seats seats booked.";
                    
                    // Suggest alternative times
                    $next_slots_query = "SELECT time 
                                  FROM (
                                      SELECT TIME_FORMAT(TIME('11:00:00') + INTERVAL (n*15) MINUTE, '%H:%i:%s') as time
                                      FROM (
                                          SELECT 0 as n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4
                                          UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8
                                      ) numbers
                                      WHERE TIME('11:00:00') + INTERVAL (n*15) MINUTE <= '23:59:59'
                                  ) slots
                                  LEFT JOIN table_bookings b ON b.date = ? 
                                      AND b.time = slots.time 
                                      AND b.rs_id = ?
                                      AND b.status != 'cancelled'
                                  GROUP BY slots.time
                                  HAVING COALESCE(SUM(b.people), 0) + ? <= ?
                                  ORDER BY time
                                  LIMIT 3";
                    $stmt = $conn->prepare($next_slots_query);
                    $stmt->bind_param("siis", $date, $rs_id, $people, $total_capacity);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        $error .= "\n\nAvailable time slots for your party size:\n";
                        while ($row = $result->fetch_assoc()) {
                            $error .= date('g:i A', strtotime($row['time'])) . "\n";
                        }
                    }
                } else {
                    // Check specific table availability
                    $available = checkTableAvailability($conn, $rs_id, $date, $time, $people);
                    
                    if ($available) {
                        $table_id = findAvailableTable($conn, $rs_id, $date, $time, $people);
                        
                        if ($table_id) {
                            // Get table details
                            $table_query = "SELECT table_name, capacity FROM restaurant_tables WHERE id = ?";
                            $stmt = $conn->prepare($table_query);
                            $stmt->bind_param("i", $table_id);
                            $stmt->execute();
                            $table_info = $stmt->get_result()->fetch_assoc();
                            
                            // Insert booking
                            $sql = "INSERT INTO table_bookings (rs_id, name, email, phone, date, time, people, status, table_id) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?)";
                            
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("isssssii", $rs_id, $name, $email, $phone, $date, $time, $people, $table_id);
                            
                            if ($stmt->execute()) {
                                $success = "Table booked successfully!\n\n";
                                $success .= "Table: " . $table_info['table_name'] . "\n";
                                $success .= "Date: " . date('F j, Y', strtotime($date)) . "\n";
                                $success .= "Time: " . date('g:i A', strtotime($time)) . "\n";
                                $success .= "Party Size: " . $people . " people\n";
                                $success .= "\nPlease arrive 10 minutes before your booking time.";
                            } else {
                                $error = "Error processing your booking: " . $stmt->error;
                            }
                            $stmt->close();
                        } else {
                            $error = "Sorry, we couldn't find a suitable table for your party size at this time.";
                        }
                    } else {
                        $error = "All tables are currently booked for this time slot. Please try a different time.";
                    }
                }
            }
        }
    }
}

/**
 * Check if tables are available for the given date, time, and party size
 */
function checkTableAvailability($conn, $rs_id, $date, $time, $people) {
    // Get booking time window (2 hours per booking)
    $time_obj = new DateTime($time);
    $end_time = clone $time_obj;
    $end_time->modify('+2 hours');
    
    $booking_start = $time_obj->format('H:i:s');
    $booking_end = $end_time->format('H:i:s');
    
    // Get all tables for this restaurant that can accommodate the party size
    $tables_query = "SELECT * FROM restaurant_tables 
                    WHERE rs_id = $rs_id AND capacity >= $people 
                    ORDER BY capacity ASC";
    $tables_result = $conn->query($tables_query);
    
    if ($tables_result->num_rows == 0) {
        return false; // No tables can accommodate this party size
    }
    
    // Check if table_id column exists
    $check_table_id = mysqli_query($conn, "SHOW COLUMNS FROM table_bookings LIKE 'table_id'");
    
    if(mysqli_num_rows($check_table_id) > 0) {
        // Column exists, proceed with checking availability
        // Check each table for availability
        while ($table = $tables_result->fetch_assoc()) {
            $table_id = $table['id'];
            
            // Check if this table is already booked during the requested time
            $booking_query = "SELECT * FROM table_bookings 
                             WHERE date = '$date' 
                             AND table_id = $table_id 
                             AND status != 'cancelled'
                             AND ((time <= '$booking_start' AND ADDTIME(time, '02:00:00') > '$booking_start') 
                                  OR (time < '$booking_end' AND ADDTIME(time, '02:00:00') >= '$booking_end')
                                  OR (time >= '$booking_start' AND time < '$booking_end'))";
            
            $booking_result = $conn->query($booking_query);
            
            if ($booking_result->num_rows == 0) {
                // This table is available
                return true;
            }
        }
    } else {
        // table_id column doesn't exist yet, just check if we have enough tables
        $existing_bookings = "SELECT COUNT(*) as count FROM table_bookings 
                             WHERE date = '$date' 
                             AND rs_id = $rs_id 
                             AND ((time <= '$booking_start' AND ADDTIME(time, '02:00:00') > '$booking_start') 
                                  OR (time < '$booking_end' AND ADDTIME(time, '02:00:00') >= '$booking_end')
                                  OR (time >= '$booking_start' AND time < '$booking_end'))";
        
        $result = $conn->query($existing_bookings);
        $row = $result->fetch_assoc();
        $booked_tables = $row['count'];
        
        // Count total tables with sufficient capacity
        $total_tables_query = "SELECT COUNT(*) as count FROM restaurant_tables 
                              WHERE rs_id = $rs_id AND capacity >= $people";
        $total_result = $conn->query($total_tables_query);
        $total_row = $total_result->fetch_assoc();
        $total_tables = $total_row['count'];
        
        // If we have at least one table available, return true
        if($booked_tables < $total_tables) {
            return true;
        }
    }
    
    return false; // No available tables found
}

/**
 * Find an available table for the given parameters
 */
function findAvailableTable($conn, $rs_id, $date, $time, $people) {
    // Get booking time window (2 hours per booking)
    $time_obj = new DateTime($time);
    $end_time = clone $time_obj;
    $end_time->modify('+2 hours');
    
    $booking_start = $time_obj->format('H:i:s');
    $booking_end = $end_time->format('H:i:s');
    
    // Get all tables for this restaurant that can accommodate the party size
    $tables_query = "SELECT * FROM restaurant_tables 
                    WHERE rs_id = $rs_id AND capacity >= $people 
                    ORDER BY capacity ASC";
    $tables_result = $conn->query($tables_query);
    
    // Check if table_id column exists
    $check_table_id = mysqli_query($conn, "SHOW COLUMNS FROM table_bookings LIKE 'table_id'");
    
    if(mysqli_num_rows($check_table_id) > 0) {
        // Column exists, proceed with checking availability
        // Check each table for availability
        while ($table = $tables_result->fetch_assoc()) {
            $table_id = $table['id'];
            
            // Check if this table is already booked during the requested time
            $booking_query = "SELECT * FROM table_bookings 
                             WHERE date = '$date' 
                             AND table_id = $table_id 
                             AND status != 'cancelled'
                             AND ((time <= '$booking_start' AND ADDTIME(time, '02:00:00') > '$booking_start') 
                                  OR (time < '$booking_end' AND ADDTIME(time, '02:00:00') >= '$booking_end')
                                  OR (time >= '$booking_start' AND time < '$booking_end'))";
            
            $booking_result = $conn->query($booking_query);
            
            if ($booking_result->num_rows == 0) {
                // This table is available
                return $table_id;
            }
        }
    } else {
        // table_id column doesn't exist yet, just return the first table's ID
        if($tables_result->num_rows > 0) {
            $table = $tables_result->fetch_assoc();
            return $table['id'];
        }
    }
    
    return null; // No available tables found
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book a Table - <?php echo $restaurant ? $restaurant['title'] : 'Restrozone'; ?></title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animsition.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dropdown.css">
    <style>
        body {
            font-family: "Source Sans Pro", sans-serif;
            background: url('images/img/bg.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #ffffff;
            padding: 30px;
        }
        .booking-container {
            max-width: 800px;
            margin: 20px auto;
            background: rgba(0, 0, 0, 0.7);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
        }
        .booking-form {
            background: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 8px;
        }
        .availability-status {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            display: none;
        }
        .booking-status {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            font-size: 14px;
        }
        .capacity-info {
            margin-top: 10px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }
        .capacity-info p {
            margin: 5px 0;
        }
        .available {
            background-color: rgba(46, 204, 113, 0.2);
            border: 1px solid #2ecc71;
            color: #2ecc71;
        }
        .unavailable {
            background-color: rgba(231, 76, 60, 0.2);
            border: 1px solid #e74c3c;
            color: #e74c3c;
        }
        .time-slots {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
            margin: 15px 0;
        }
        .time-slot {
            padding: 8px;
            text-align: center;
            border-radius: 4px;
            cursor: pointer;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        .time-slot:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .time-slot.available {
            border-color: #2ecc71;
        }
        .time-slot.unavailable {
            border-color: #e74c3c;
            opacity: 0.5;
            cursor: not-allowed;
        }
        .selected-time {
            background: #2ecc71 !important;
            color: white;
        }
        h2 {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }
        .message {
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
        }
        .success-message {
            background-color: rgba(46, 204, 113, 0.7);
        }
        .error-message {
            background-color: rgba(231, 76, 60, 0.7);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .restaurant-select {
            margin-bottom: 20px;
        }
        .stat-box {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 4px;
            text-align: center;
            margin: 10px 0;
        }
        .stat-box h4 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #fff;
        }
        .stat-box span {
            font-size: 24px;
            font-weight: bold;
            color: #2ecc71;
        }
        .status-message {
            margin-top: 15px;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
            display: none;
        }
        .booking-stats {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .detail-item {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        .label {
            font-weight: bold;
        }
        .value {
            color: #2ecc71;
        }
    </style>
</head>
<body>

<div class="booking-container">
    <h2><?php echo $restaurant ? 'Book a Table at ' . $restaurant['title'] : 'Book a Table'; ?></h2>
    
    <?php if (!empty($success)): ?>
    <div class="message success-message">
        <p><?php echo nl2br($success); ?></p>
        <a href="index.php" class="btn btn-secondary mt-3">Return to Home</a>
    </div>
    <?php elseif (!empty($error)): ?>
    <div class="message error-message">
        <p><?php echo nl2br($error); ?></p>
    </div>
    <?php else: ?>
    
    <?php if ($restaurant): ?>
    <div class="restaurant-info">
        <div class="row">
            <div class="col-md-4">
                <img src="admin/Res_img/<?php echo $restaurant['image']; ?>" alt="<?php echo $restaurant['title']; ?>" class="img-fluid" style="border-radius: 5px;">
            </div>
            <div class="col-md-8">
                <h3><?php echo $restaurant['title']; ?></h3>
                <p><i class="fa fa-map-marker"></i> <?php echo $restaurant['address']; ?></p>
                <p><i class="fa fa-phone"></i> <?php echo $restaurant['phone']; ?></p>
                <p><i class="fa fa-clock-o"></i> Open: <?php echo $restaurant['o_hr']; ?> - Close: <?php echo $restaurant['c_hr']; ?></p>
                <p><i class="fa fa-calendar"></i> Open Days: <?php echo $restaurant['o_days']; ?></p>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="restaurant-select">
        <form method="GET" action="table_book.php">
            <div class="form-group">
                <label>Select a Restaurant:</label>
                <select name="res_id" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Select Restaurant --</option>
                    <?php
                    $rest_query = "SELECT * FROM restaurant ORDER BY title";
                    $rest_result = $conn->query($rest_query);
                    while ($rest = $rest_result->fetch_assoc()) {
                        echo '<option value="' . $rest['rs_id'] . '">' . $rest['title'] . '</option>';
                    }
                    ?>
                </select>
            </div>
        </form>
    </div>
    <?php endif; ?>
    
    <?php if ($restaurant): ?>
    <div class="booking-form">
        <div class="capacity-info">
            <?php
            // Get total capacity for this restaurant
            $capacity_query = "SELECT SUM(capacity) as total_capacity FROM restaurant_tables WHERE rs_id = ?";
            $stmt = $conn->prepare($capacity_query);
            $stmt->bind_param("i", $rs_id);
            $stmt->execute();
            $total_capacity = $stmt->get_result()->fetch_assoc()['total_capacity'];
            ?>
            <div class="capacity-details">
                <div class="detail-item">
                    <span class="label">Restaurant Total Capacity:</span>
                    <span class="value"><?php echo $total_capacity; ?> seats</span>
                </div>
                <div class="detail-item">
                    <span class="label">Booked For Selected Date:</span>
                    <span class="value booked-seats-display">Select a date to see availability</span>
                </div>
                <div class="detail-item">
                    <span class="label">Available For Selected Date:</span>
                    <span class="value available-seats-display">Select a date to see availability</span>
                </div>
            </div>

            <div class="booked-slots-info" style="margin-top: 20px; display: none;">
                <h4>Existing Bookings for Selected Date:</h4>
                <div class="booked-slots-list" style="margin-top: 10px;">
                </div>
            </div>
        </div>
        
        <form action="table_book.php?res_id=<?php echo $rs_id; ?>" method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Your Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Number of People</label>
                        <select name="people" class="form-control" required>
                            <option value="">-- Select --</option>
                            <option value="2">2 People</option>
                            <option value="4">4 People</option>
                            <option value="6">6 People</option>
                            <option value="8">8 People</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Date</label>
                        <?php
                        // Set minimum date to today
                        $min_date = date('Y-m-d');
                        // Set maximum date to 3 months from today
                        $max_date = date('Y-m-d', strtotime('+3 months'));
                        ?>
                        <input type="date" name="date" class="form-control" min="<?php echo $min_date; ?>" max="<?php echo $max_date; ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Time</label>
                        <input type="time" name="time" class="form-control" required>
                        <small class="text-muted">Restaurant hours: <?php echo $restaurant['o_hr']; ?> - <?php echo $restaurant['c_hr']; ?></small>
                    </div>
                </div>
            </div>
            
            <input type="hidden" name="rs_id" value="<?php echo $rs_id; ?>">
            <button type="submit" class="btn-book">Book My Table</button>
            
            <div class="availability-status"></div>
            
            <div class="booking-stats" style="margin: 20px 0;">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stat-box">
                            <h4>Total Capacity</h4>
                            <span class="total-capacity">-</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-box">
                            <h4>Booked Seats</h4>
                            <span class="booked-seats">-</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-box">
                            <h4>Available Seats</h4>
                            <span class="available-seats">-</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="time-slots">
                <!-- Time slots will be populated by JavaScript -->
            </div>
        </form>
    </div>
    <?php endif; ?>
    
    <?php endif; ?>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[method="POST"]');
    const timeInput = document.querySelector('input[name="time"]');
    const dateInput = document.querySelector('input[name="date"]');
    const peopleSelect = document.querySelector('select[name="people"]');
    const bookedSeatsDisplay = document.querySelector('.booked-seats-display');
    const availableSeatsDisplay = document.querySelector('.available-seats-display');
    const bookingStats = document.querySelector('.booking-stats');
    const statusMessage = document.querySelector('.availability-status');
    const totalCapacitySpan = document.querySelector('.total-capacity');
    const bookedSeatsSpan = document.querySelector('.booked-seats');
    const availableSeatsSpan = document.querySelector('.available-seats');

    function updateCapacityInfo(data) {
        bookingStats.style.display = 'block';
        
        // Update the capacity details section
        bookedSeatsDisplay.textContent = data.booked_seats + ' seats';
        availableSeatsDisplay.textContent = data.available_seats + ' seats';

        // Update the statistics boxes
        totalCapacitySpan.textContent = data.total_capacity + ' seats';
        bookedSeatsSpan.textContent = data.booked_seats + ' seats';
        availableSeatsSpan.textContent = data.available_seats + ' seats';

        // Update colors based on availability
        if (data.available_seats >= parseInt(peopleSelect.value || 0)) {
            availableSeatsDisplay.style.color = '#2ecc71';
            availableSeatsSpan.style.color = '#2ecc71';
            bookedSeatsDisplay.style.color = '#ffffff';
        } else {
            availableSeatsDisplay.style.color = '#e74c3c';
            availableSeatsSpan.style.color = '#e74c3c';
            bookedSeatsDisplay.style.color = '#e74c3c';
        }

        // Update status message
        statusMessage.style.display = 'block';
        if (data.available) {
            statusMessage.innerHTML = '<div class="alert alert-success"><i class="fa fa-check-circle"></i> Tables are available for your party size!</div>';
        } else {
            let message = '<div class="alert alert-danger"><i class="fa fa-times-circle"></i> ' + 
                         (data.message || 'No tables available at this time.');
            if (data.nextAvailable) {
                message += '<br>Next available time: ' + data.nextAvailable;
            }
            message += '</div>';
            statusMessage.innerHTML = message;
        }
    }
    
    function checkAvailability() {
        if (!dateInput.value || !timeInput.value || !peopleSelect.value) {
            bookingStats.style.display = 'none';
            statusMessage.style.display = 'none';
            return;
        }

        const formData = new FormData();
        formData.append('check_availability', '1');
        formData.append('date', dateInput.value);
        formData.append('time', timeInput.value);
        formData.append('people', peopleSelect.value);
        formData.append('rs_id', <?php echo $rs_id; ?>);
        
        fetch('check_availability.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Server error:', data.error);
                statusMessage.innerHTML = '<div class="alert alert-danger">Error checking availability. Please try again.</div>';
                return;
            }
            updateCapacityInfo(data);
        })
        .catch(error => {
            console.error('Error checking availability:', error);
            statusMessage.innerHTML = '<div class="alert alert-danger">Error checking availability. Please try again.</div>';
            bookingStats.style.display = 'none';
        });
    }
    
    // Check availability when inputs change
    dateInput?.addEventListener('change', checkAvailability);
    timeInput?.addEventListener('change', checkAvailability);
    peopleSelect?.addEventListener('change', checkAvailability);
});
</script>

<?php $conn->close(); ?>
</body>
</html>