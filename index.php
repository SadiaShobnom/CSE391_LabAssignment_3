<?php
require_once 'db.php';
$message = '';
$msgType = '';

// Read customer daily booking limit from config
$configRow    = $pdo->query("SELECT config_value FROM system_config WHERE config_key = 'user_daily_limit'")->fetch(PDO::FETCH_ASSOC);
$userDailyLim = $configRow ? (int)$configRow['config_value'] : 3;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name           = trim($_POST['client_name'] ?? '');
    $address        = trim($_POST['address'] ?? '');
    $phone          = trim($_POST['phone'] ?? '');
    $car_license    = trim($_POST['car_license'] ?? '');
    $car_engine     = trim($_POST['car_engine'] ?? '');
    $appointment_date = trim($_POST['appointment_date'] ?? '');
    $mechanic_id    = trim($_POST['mechanic_id'] ?? '');

    if (empty($name) || empty($address) || empty($phone) || empty($car_license) || empty($car_engine) || empty($appointment_date) || empty($mechanic_id)) {
        $message = "All fields are required.";
        $msgType = 'error';
    } elseif (!preg_match('/^[0-9]+$/', $phone)) {
        $message = "Phone number must contain only numbers.";
        $msgType = 'error';
    } elseif (!preg_match('/^[0-9]+$/', $car_engine)) {
        $message = "Car engine number must contain only numbers.";
        $msgType = 'error';
    } else {
        $d = DateTime::createFromFormat('Y-m-d', $appointment_date);
        if (!($d && $d->format('Y-m-d') === $appointment_date)) {
            $message = "Invalid appointment date.";
            $msgType = 'error';
        } else {
            // Check if this customer has already reached the daily limit
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE phone = ? AND appointment_date = ?");
            $stmt->execute([$phone, $appointment_date]);
            $customerBooked = (int)$stmt->fetchColumn();

            if ($customerBooked >= $userDailyLim) {
                $message = "You have reached the maximum booking limit of {$userDailyLim} car(s) per day.";
                $msgType = 'error';
            } else {
                // Check mechanic capacity (respecting day_type)
                $stmt = $pdo->prepare("SELECT max_cars, day_type, half_day_max FROM mechanics WHERE id = ?");
                $stmt->execute([$mechanic_id]);
                $mech = $stmt->fetch(PDO::FETCH_ASSOC);
                $cap  = ($mech['day_type'] === 'half') ? $mech['half_day_max'] : $mech['max_cars'];

                $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE mechanic_id = ? AND appointment_date = ?");
                $stmt->execute([$mechanic_id, $appointment_date]);
                $booked = (int)$stmt->fetchColumn();

                if ($booked >= $cap) {
                    $message = "The selected mechanic is fully occupied on this date. Please select another mechanic.";
                    $msgType = 'error';
                } else {
                    $stmt = $pdo->prepare("INSERT INTO appointments (client_name, address, phone, car_license, car_engine, appointment_date, mechanic_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    if ($stmt->execute([$name, $address, $phone, $car_license, $car_engine, $appointment_date, $mechanic_id])) {
                        $message = "Appointment successfully created!";
                        $msgType = 'success';
                        $name = $address = $phone = $car_license = $car_engine = $appointment_date = $mechanic_id = '';
                    } else {
                        $message = "Error creating appointment.";
                        $msgType = 'error';
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Workshop Appointment</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Book an Appointment</h1>
        <p style="text-align: center;"><a href="admin/index.php">Go to Admin Panel</a></p>
        <p style="text-align:center; color:#555; font-size:0.9rem;">
            ℹ️ Each customer can book a maximum of <strong><?php echo $userDailyLim; ?> car(s)</strong> per day.
        </p>

        <?php if ($message): ?>
            <div class="msg <?php echo $msgType; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php" id="appointmentForm">
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="client_name" id="client_name"
                    value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label>Address:</label>
                <textarea name="address" id="address"
                    required><?php echo htmlspecialchars($address ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label>Phone:</label>
                <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($phone ?? ''); ?>"
                    pattern="[0-9]+" title="Only numbers allowed" required>
            </div>

            <div class="form-group">
                <label>Car License Number:</label>
                <input type="text" name="car_license" id="car_license"
                    value="<?php echo htmlspecialchars($car_license ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label>Car Engine Number:</label>
                <input type="text" name="car_engine" id="car_engine"
                    value="<?php echo htmlspecialchars($car_engine ?? ''); ?>" pattern="[0-9]+"
                    title="Only numbers allowed" required>
            </div>

            <div class="form-group">
                <label>Appointment Date:</label>
                <input type="date" name="appointment_date" id="appointment_date"
                    value="<?php echo htmlspecialchars($appointment_date ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label>Select Mechanic:</label>
                <select name="mechanic_id" id="mechanic_id" required>
                    <option value="">-- Select a date first --</option>
                </select>
                <small id="mechanic_info" style="display:block; margin-top:5px;"></small>
            </div>

            <button type="submit" id="submitBtn">Book Appointment</button>
        </form>
    </div>

    <script>
        const dateInput = document.getElementById('appointment_date');
        const mechanicSelect = document.getElementById('mechanic_id');
        const mechanicInfo = document.getElementById('mechanic_info');
        const form = document.getElementById('appointmentForm');

        function fetchMechanics() {
            const dateVal = dateInput.value;
            if (!dateVal) {
                mechanicSelect.innerHTML = '<option value="">-- Select a date first --</option>';
                mechanicInfo.innerText = '';
                return;
            }

            mechanicSelect.innerHTML = '<option value="">Loading...</option>';

            fetch('api_mechanics.php?appointment_date=' + encodeURIComponent(dateVal))
                .then(res => res.json())
                .then(data => {
                    mechanicSelect.innerHTML = '<option value="">-- Select Mechanic --</option>';
                    let validMechanicSelected = false;
                    let selectedId = "<?php echo htmlspecialchars($mechanic_id ?? ''); ?>";

                    data.forEach(m => {
                        const opt = document.createElement('option');
                        opt.value = m.id;
                        let label = `${m.name} (${m.free_spots} spots available)`;
                        opt.textContent = label;
                        if (m.free_spots <= 0) {
                            opt.disabled = true;
                            label += ' - FULL';
                        }
                        if (m.id == selectedId && m.free_spots > 0) {
                            opt.selected = true;
                            validMechanicSelected = true;
                        }
                        mechanicSelect.appendChild(opt);
                    });
                })
                .catch(err => {
                    console.error('Error fetching mechanics', err);
                    mechanicSelect.innerHTML = '<option value="">Error loading mechanics</option>';
                });
        }

        dateInput.addEventListener('change', fetchMechanics);

        // Fetch on load if date is present
        if (dateInput.value) {
            fetchMechanics();
        }

        // JS Validation
        form.addEventListener('submit', function (e) {
            const phone = document.getElementById('phone').value;
            const engine = document.getElementById('car_engine').value;

            if (!/^[0-9]+$/.test(phone)) {
                alert('Phone must contain only numbers.');
                e.preventDefault();
                return;
            }
            if (!/^[0-9]+$/.test(engine)) {
                alert('Car engine must contain only numbers.');
                e.preventDefault();
                return;
            }
        });
    </script>
</body>

</html>