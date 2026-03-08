<!-- Add mechanic -->
<div class="card-form">
    <h3>Add New Mechanic</h3>
    <form method="POST" action="index.php?tab=mechanics" class="inline-form">
        <input type="hidden" name="action" value="add_mechanic">
        <input type="text" name="mech_name" placeholder="Mechanic name" required class="input-inline">
        <button type="submit" class="btn-primary">Add Mechanic</button>
    </form>
</div>

<!-- Mechanics table -->
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Day Type</th>
            <th>Full-Day Cap</th>
            <th>Half-Day Cap</th>
            <th>Total Booked</th>
            <th>Edit Name</th>
            <th>Workload Config</th>
            <th>Delete</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($mechanics as $i => $m): ?>
        <tr>
            <td><?php echo $i + 1; ?></td>
            <td><?php echo htmlspecialchars($m['name']); ?></td>
            <td>
                <span class="badge <?php echo $m['day_type'] === 'half' ? 'badge-orange' : 'badge-blue'; ?>">
                    <?php echo ucfirst($m['day_type']); ?>-Day
                </span>
            </td>
            <td><?php echo $m['max_cars']; ?></td>
            <td><?php echo $m['half_day_max']; ?></td>
            <td><?php echo $m['total_booked']; ?></td>

            <!-- Edit name -->
            <td>
                <form method="POST" action="index.php?tab=mechanics" class="inline-form">
                    <input type="hidden" name="action" value="edit_mechanic">
                    <input type="hidden" name="mech_id" value="<?php echo $m['id']; ?>">
                    <input type="text" name="mech_name"
                        value="<?php echo htmlspecialchars($m['name']); ?>"
                        class="input-inline" required>
                    <button type="submit" class="btn-primary btn-sm">Save</button>
                </form>
            </td>

            <!-- Workload config -->
            <td>
                <form method="POST" action="index.php?tab=mechanics" class="workload-form">
                    <input type="hidden" name="action" value="update_workload">
                    <input type="hidden" name="mech_id" value="<?php echo $m['id']; ?>">
                    <div class="workload-row">
                        <label>Day:</label>
                        <select name="day_type" class="edit-select" onchange="toggleHalfDay(this)">
                            <option value="full" <?php echo $m['day_type'] === 'full' ? 'selected' : ''; ?>>Full</option>
                            <option value="half" <?php echo $m['day_type'] === 'half' ? 'selected' : ''; ?>>Half</option>
                        </select>
                    </div>
                    <div class="workload-row">
                        <label>Max (full):</label>
                        <input type="number" name="max_cars" value="<?php echo $m['max_cars']; ?>"
                            min="1" max="20" class="edit-input num-input">
                    </div>
                    <div class="workload-row half-day-field" style="<?php echo $m['day_type'] === 'half' ? '' : 'display:none'; ?>">
                        <label>Max (half):</label>
                        <input type="number" name="half_day_max" value="<?php echo $m['half_day_max']; ?>"
                            min="1" max="20" class="edit-input num-input">
                    </div>
                    <button type="submit" class="btn-warning btn-sm" style="margin-top:4px;">Update</button>
                </form>
            </td>

            <!-- Delete -->
            <td>
                <form method="POST" action="index.php?tab=mechanics"
                    onsubmit="return confirm('Delete this mechanic? Only possible if no bookings exist.')">
                    <input type="hidden" name="action" value="delete_mechanic">
                    <input type="hidden" name="mech_id" value="<?php echo $m['id']; ?>">
                    <button type="submit" class="btn-danger btn-sm">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
