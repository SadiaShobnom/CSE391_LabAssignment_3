<?php
require_once 'db.php';
header('Content-Type: application/json');

$date = $_GET['appointment_date'] ?? date('Y-m-d');
if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
    $date = date('Y-m-d');
}

$stmt = $pdo->prepare("
    SELECT m.id, m.name, m.max_cars, m.day_type, m.half_day_max,
           (SELECT COUNT(*) FROM appointments a WHERE a.mechanic_id = m.id AND a.appointment_date = ?) AS booked
    FROM mechanics m
");
$stmt->execute([$date]);
$mechanics = $stmt->fetchAll(PDO::FETCH_ASSOC);

$result = [];
foreach ($mechanics as $row) {
    // Use half-day capacity if mechanic is set to half-day
    $capacity = ($row['day_type'] === 'half') ? (int)$row['half_day_max'] : (int)$row['max_cars'];
    $free     = max(0, $capacity - (int)$row['booked']);
    $result[] = [
        'id'         => $row['id'],
        'name'       => $row['name'],
        'day_type'   => $row['day_type'],
        'capacity'   => $capacity,
        'free_spots' => $free,
    ];
}

echo json_encode($result);