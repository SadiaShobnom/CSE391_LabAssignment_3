<div class="dashboard-cards">
    <div class="card">
        <div class="card-icon">📋</div>
        <div class="card-num"><?php echo $totalAppointments; ?></div>
        <div class="card-label">Total Appointments</div>
    </div>
    <div class="card">
        <div class="card-icon">📅</div>
        <div class="card-num"><?php echo $todayCount; ?></div>
        <div class="card-label">Today's Bookings</div>
    </div>
    <div class="card">
        <div class="card-icon">🔩</div>
        <div class="card-num"><?php echo $totalMechanics; ?></div>
        <div class="card-label">Mechanics</div>
    </div>
    <div class="card">
        <div class="card-icon">🚗</div>
        <div class="card-num"><?php echo $userDailyLim; ?></div>
        <div class="card-label">Customer Daily Car Limit</div>
    </div>
</div>

<h3>Today's Mechanic Load</h3>
<table>
    <thead>
        <tr>
            <th>Mechanic</th>
            <th>Day Type</th>
            <th>Capacity</th>
            <th>Booked Today</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($mechToday as $m):
            $cap    = ($m['day_type'] === 'half') ? $m['half_day_max'] : $m['max_cars'];
            $booked = (int)$m['today_booked'];
            $free   = max(0, $cap - $booked);
            $status = $free === 0
                ? '<span class="badge badge-red">FULL</span>'
                : '<span class="badge badge-green">' . $free . ' free</span>';
        ?>
        <tr>
            <td><?php echo htmlspecialchars($m['name']); ?></td>
            <td><?php echo ucfirst($m['day_type']); ?>-Day</td>
            <td><?php echo $cap; ?></td>
            <td><?php echo $booked; ?></td>
            <td><?php echo $status; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
