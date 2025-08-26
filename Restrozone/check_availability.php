<?php
include("connection/connect.php");
header('Content-Type: application/json');

if (!isset($_POST['check_availability'])) {
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

$rs_id = intval($_POST['rs_id']);
$date = $_POST['date'];
$time = $_POST['time'];
$people = intval($_POST['people']);

// Get booking time window
$time_obj = new DateTime($time);
$booking_start = $time_obj->format('H:i:s');

// Set fixed opening hours
$opening_time = new DateTime('11:00:00');
$closing_time = new DateTime('23:59:59');

// Check if booking is within operating hours
if ($time_obj < $opening_time || $time_obj > $closing_time) {
    echo json_encode([
        'available' => false,
        'total_capacity' => 0,
        'booked_seats' => 0,
        'available_seats' => 0,
        'message' => 'Restaurant is only open from 11:00 AM to 12:00 AM'
    ]);
    exit;
}

// Get total capacity and available tables
$total_capacity_query = "SELECT SUM(capacity) as total_capacity FROM restaurant_tables WHERE rs_id = ?";
$stmt = $db->prepare($total_capacity_query);
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
$stmt = $db->prepare($current_bookings_query);
$stmt->bind_param("iss", $rs_id, $date, $time);
$stmt->execute();
$booked_seats = $stmt->get_result()->fetch_assoc()['booked_seats'] ?: 0;

$available_seats = $total_capacity - $booked_seats;

// Check if there's enough capacity for the requested party size
$is_available = ($booked_seats + $people) <= $total_capacity;

// Get next available time slots if current slot is not available
$next_available = null;
if (!$is_available) {
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
                        LIMIT 1";
    $stmt = $db->prepare($next_slots_query);
    $stmt->bind_param("siis", $date, $rs_id, $people, $total_capacity);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $next_available = date('g:i A', strtotime($result->fetch_assoc()['time']));
    }
}

echo json_encode([
    'available' => $is_available,
    'total_capacity' => $total_capacity,
    'booked_seats' => $booked_seats,
    'available_seats' => $available_seats,
    'message' => $is_available ? null : 'All tables are currently booked for this time slot.',
    'nextAvailable' => $next_available
]);