<?php
include("connection/connect.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Function to check if a column exists in a table
function columnExists($db, $table, $column) {
    $result = mysqli_query($db, "SHOW COLUMNS FROM $table LIKE '$column'");
    return mysqli_num_rows($result) > 0;
}

// Array to store messages
$messages = [];

// Create restaurant_tables table if it doesn't exist
$check_tables_exist = mysqli_query($db, "SHOW TABLES LIKE 'restaurant_tables'");
if(mysqli_num_rows($check_tables_exist) == 0) {
    $create_tables_sql = "CREATE TABLE IF NOT EXISTS restaurant_tables (
        id INT AUTO_INCREMENT PRIMARY KEY, 
        rs_id INT NOT NULL, 
        table_name VARCHAR(50) NOT NULL, 
        capacity INT NOT NULL,
        is_available BOOLEAN DEFAULT TRUE,
        FOREIGN KEY (rs_id) REFERENCES restaurant(rs_id) ON DELETE CASCADE
    )";
    
    if(mysqli_query($db, $create_tables_sql)) {
        $messages[] = "Created restaurant_tables table successfully.";
        
        // Add sample data for restaurant tables if table was just created
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
            
        if(mysqli_query($db, $sample_data_sql)) {
            $messages[] = "Added sample table data successfully.";
        } else {
            $messages[] = "Error adding sample table data: " . mysqli_error($db);
        }
    } else {
        $messages[] = "Error creating restaurant_tables table: " . mysqli_error($db);
    }
}

// Create table_bookings table if doesn't exist
$check_bookings_exist = mysqli_query($db, "SHOW TABLES LIKE 'table_bookings'");
if(mysqli_num_rows($check_bookings_exist) == 0) {
    $create_bookings_sql = "CREATE TABLE table_bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        rs_id INT NOT NULL,
        table_id INT DEFAULT NULL,
        customer_id INT DEFAULT NULL,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        date DATE NOT NULL,
        time TIME NOT NULL,
        people INT NOT NULL,
        status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
        FOREIGN KEY (rs_id) REFERENCES restaurant(rs_id) ON DELETE CASCADE,
        FOREIGN KEY (table_id) REFERENCES restaurant_tables(id) ON DELETE SET NULL,
        FOREIGN KEY (customer_id) REFERENCES users(u_id) ON DELETE SET NULL
    )";
    
    if(mysqli_query($db, $create_bookings_sql)) {
        $messages[] = "Created table_bookings table successfully.";
    } else {
        $messages[] = "Error creating table_bookings table: " . mysqli_error($db);
    }
}

// Add missing columns and foreign keys
if(!columnExists($db, "table_bookings", "customer_id")) {
    mysqli_query($db, "ALTER TABLE table_bookings ADD COLUMN customer_id INT DEFAULT NULL");
    mysqli_query($db, "ALTER TABLE table_bookings ADD FOREIGN KEY (customer_id) REFERENCES users(u_id) ON DELETE SET NULL");
}

if(!columnExists($db, "restaurant_tables", "is_available")) {
    mysqli_query($db, "ALTER TABLE restaurant_tables ADD COLUMN is_available BOOLEAN DEFAULT TRUE");
}

// Now let's check the current structure of table_bookings
$table_structure = [];
$structure_query = mysqli_query($db, "DESCRIBE table_bookings");
while($row = mysqli_fetch_assoc($structure_query)) {
    $table_structure[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Update</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 30px;
        }
        .alert {
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Database Update Results</h2>
        
        <?php foreach($messages as $message): ?>
            <div class="alert alert-info">
                <?php echo $message; ?>
            </div>
        <?php endforeach; ?>
        
        <h3>Current table_bookings Structure</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Field</th>
                    <th>Type</th>
                    <th>Null</th>
                    <th>Key</th>
                    <th>Default</th>
                    <th>Extra</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($table_structure as $column): ?>
                <tr>
                    <td><?php echo $column['Field']; ?></td>
                    <td><?php echo $column['Type']; ?></td>
                    <td><?php echo $column['Null']; ?></td>
                    <td><?php echo $column['Key']; ?></td>
                    <td><?php echo $column['Default']; ?></td>
                    <td><?php echo $column['Extra']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="mt-4">
            <a href="index.php" class="btn btn-primary">Return to Home</a>
        </div>
    </div>
</body>
</html>