<?php foreach ($mechanics as $m):
    $mechApps = array_filter($appointments, fn($a) => $a['mechanic_id'] == $m['id']);
    $cap      = ($m['day_type'] === 'half') ? $m['half_day_max'] : $m['max_cars'];
?>
<div class="mechanic-section">
    <h3>
        <?php echo htmlspecialchars($m['name']); ?>
        <span class="badge <?php echo $m['day_type'] === 'half' ? 'badge-orange' : 'badge-blue'; ?>">
            <?php echo ucfirst($m['day_type']); ?>-Day
        </span>
        <small style="font-weight:normal; font-size:0.85rem;">
            Capacity: <?php echo $cap; ?> cars/day &nbsp;|&nbsp; Total booked: <?php echo count($mechApps); ?>
        </small>
    </h3>

    <?php if (count($mechApps) > 0): ?>
    <table>
        <thead>
            <tr><th>#</th><th>Client</th><th>Car License</th><th>Date</th></tr>
        </thead>
        <tbody>
            <?php $idx = 1; foreach ($mechApps as $a): ?>
            <tr>
                <td><?php echo $idx++; ?></td>
                <td><?php echo htmlspecialchars($a['client_name']); ?></td>
                <td><?php echo htmlspecialchars($a['car_license']); ?></td>
                <td><?php echo htmlspecialchars($a['appointment_date']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p class="no-data">No cars assigned to this mechanic.</p>
    <?php endif; ?>
</div>
<?php endforeach; ?>
