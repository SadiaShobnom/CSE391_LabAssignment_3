<?php
// ─── DATA FETCHING ────────────────────────────────────────────────────────────
// Runs after actions.php so counts reflect any just-made changes.

$totalAppointments = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
$totalMechanics    = $pdo->query("SELECT COUNT(*) FROM mechanics")->fetchColumn();
$todayCount        = $pdo->query("SELECT COUNT(*) FROM appointments WHERE appointment_date = CURDATE()")->fetchColumn();

$appointments = $pdo->query("
    SELECT a.id, a.client_name, a.phone, a.car_license, a.car_engine,
           a.address, a.appointment_date, m.name AS mechanic_name, a.mechanic_id
    FROM appointments a
    JOIN mechanics m ON a.mechanic_id = m.id
    ORDER BY a.appointment_date DESC
")->fetchAll(PDO::FETCH_ASSOC);

$mechanics = $pdo->query("
    SELECT m.id, m.name, m.max_cars, m.day_type, m.half_day_max,
           COUNT(a.id) AS total_booked
    FROM mechanics m
    LEFT JOIN appointments a ON a.mechanic_id = m.id
    GROUP BY m.id
    ORDER BY m.name
")->fetchAll(PDO::FETCH_ASSOC);

$mechToday = $pdo->query("
    SELECT m.id, m.name, m.day_type, m.max_cars, m.half_day_max,
           COALESCE(SUM(a.appointment_date = CURDATE()), 0) AS today_booked
    FROM mechanics m
    LEFT JOIN appointments a ON a.mechanic_id = m.id
    GROUP BY m.id
    ORDER BY m.name
")->fetchAll(PDO::FETCH_ASSOC);

$configRow    = $pdo->query("SELECT config_value FROM system_config WHERE config_key = 'user_daily_limit'")->fetch(PDO::FETCH_ASSOC);
$userDailyLim = $configRow ? (int)$configRow['config_value'] : 3;

$allMechanics = $pdo->query("SELECT id, name FROM mechanics ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
