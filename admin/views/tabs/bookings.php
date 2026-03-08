<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Client</th>
            <th>Phone</th>
            <th>Car License</th>
            <th>Engine No.</th>
            <th>Date</th>
            <th>Mechanic</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($appointments) > 0): ?>
            <?php foreach ($appointments as $i => $app): ?>
            <tr>
                <td><?php echo $i + 1; ?></td>
                <td><?php echo htmlspecialchars($app['client_name']); ?></td>
                <td><?php echo htmlspecialchars($app['phone']); ?></td>
                <td><?php echo htmlspecialchars($app['car_license']); ?></td>
                <td><?php echo htmlspecialchars($app['car_engine']); ?></td>
                <form method="POST" action="index.php?tab=bookings">
                    <input type="hidden" name="action" value="update_appointment">
                    <input type="hidden" name="appointment_id" value="<?php echo $app['id']; ?>">
                    <td>
                        <input type="date" name="new_date"
                            value="<?php echo htmlspecialchars($app['appointment_date']); ?>"
                            class="edit-input" required>
                    </td>
                    <td>
                        <select name="new_mechanic" class="edit-select" required>
                            <?php foreach ($allMechanics as $m): ?>
                                <option value="<?php echo $m['id']; ?>"
                                    <?php echo $m['id'] == $app['mechanic_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($m['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td class="action-cell">
                        <button type="submit" class="btn-primary btn-sm">Save</button>
                </form>
                        <form method="POST" action="index.php?tab=bookings" style="display:inline;"
                            onsubmit="return confirm('Delete this appointment?')">
                            <input type="hidden" name="action" value="delete_appointment">
                            <input type="hidden" name="appointment_id" value="<?php echo $app['id']; ?>">
                            <button type="submit" class="btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="8" style="text-align:center;">No appointments found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
