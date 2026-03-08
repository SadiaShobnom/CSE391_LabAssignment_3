<?php
// ─── POST ACTIONS (authenticated) ────────────────────────────────────────────
// $pdo and $tab are available from index.php

$message = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    // ── Update appointment ────────────────────────────────────────────────────
    if ($action === 'update_appointment') {
        $appointment_id = (int)$_POST['appointment_id'];
        $new_date       = $_POST['new_date'];
        $new_mechanic   = (int)$_POST['new_mechanic'];

        $stmt = $pdo->prepare("SELECT max_cars, day_type, half_day_max FROM mechanics WHERE id = ?");
        $stmt->execute([$new_mechanic]);
        $mech = $stmt->fetch(PDO::FETCH_ASSOC);
        $cap  = ($mech['day_type'] === 'half') ? $mech['half_day_max'] : $mech['max_cars'];

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE mechanic_id = ? AND appointment_date = ? AND id != ?");
        $stmt->execute([$new_mechanic, $new_date, $appointment_id]);

        if ($stmt->fetchColumn() >= $cap) {
            $message = "The selected mechanic is fully occupied on the new date.";
            $msgType  = 'error';
        } else {
            $pdo->prepare("UPDATE appointments SET appointment_date = ?, mechanic_id = ? WHERE id = ?")
                ->execute([$new_date, $new_mechanic, $appointment_id]);
            $message = "Appointment updated successfully!";
            $msgType  = 'success';
        }
        $tab = 'bookings';
    }

    // ── Delete appointment ────────────────────────────────────────────────────
    if ($action === 'delete_appointment') {
        $pdo->prepare("DELETE FROM appointments WHERE id = ?")->execute([(int)$_POST['appointment_id']]);
        $message = "Appointment deleted.";
        $msgType  = 'success';
        $tab = 'bookings';
    }

    // ── Add mechanic ──────────────────────────────────────────────────────────
    if ($action === 'add_mechanic') {
        $name = trim($_POST['mech_name']);
        if ($name !== '') {
            $pdo->prepare("INSERT INTO mechanics (name) VALUES (?)")->execute([$name]);
            $message = "Mechanic added successfully.";
            $msgType  = 'success';
        }
        $tab = 'mechanics';
    }

    // ── Edit mechanic name ────────────────────────────────────────────────────
    if ($action === 'edit_mechanic') {
        $mid  = (int)$_POST['mech_id'];
        $name = trim($_POST['mech_name']);
        if ($name !== '') {
            $pdo->prepare("UPDATE mechanics SET name = ? WHERE id = ?")->execute([$name, $mid]);
            $message = "Mechanic name updated.";
            $msgType  = 'success';
        }
        $tab = 'mechanics';
    }

    // ── Delete mechanic ───────────────────────────────────────────────────────
    if ($action === 'delete_mechanic') {
        $mid  = (int)$_POST['mech_id'];
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE mechanic_id = ?");
        $stmt->execute([$mid]);
        if ($stmt->fetchColumn() > 0) {
            $message = "Cannot delete: this mechanic has existing appointments.";
            $msgType  = 'error';
        } else {
            $pdo->prepare("DELETE FROM mechanics WHERE id = ?")->execute([$mid]);
            $message = "Mechanic deleted.";
            $msgType  = 'success';
        }
        $tab = 'mechanics';
    }

    // ── Update workload config ────────────────────────────────────────────────
    if ($action === 'update_workload') {
        $mid          = (int)$_POST['mech_id'];
        $max_cars     = (int)$_POST['max_cars'];
        $day_type     = in_array($_POST['day_type'], ['full', 'half']) ? $_POST['day_type'] : 'full';
        $half_day_max = (int)$_POST['half_day_max'];
        $pdo->prepare("UPDATE mechanics SET max_cars = ?, day_type = ?, half_day_max = ? WHERE id = ?")
            ->execute([$max_cars, $day_type, $half_day_max, $mid]);
        $message = "Workload settings updated.";
        $msgType  = 'success';
        $tab = 'mechanics';
    }

    // ── Update system config ──────────────────────────────────────────────────
    if ($action === 'update_config') {
        $limit = max(1, (int)$_POST['user_daily_limit']);
        $pdo->prepare("UPDATE system_config SET config_value = ? WHERE config_key = 'user_daily_limit'")->execute([$limit]);
        $message = "Customer daily booking limit updated.";
        $msgType  = 'success';
        $tab = 'config';
    }
}
