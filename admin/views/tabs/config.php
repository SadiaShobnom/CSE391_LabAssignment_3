<div class="card-form">
    <h3>Customer Daily Booking Limit</h3>
    <p>Maximum number of cars a single customer can book per day.</p>
    <form method="POST" action="index.php?tab=config" class="inline-form">
        <input type="hidden" name="action" value="update_config">
        <label><strong>Max cars per customer per day:</strong></label>
        <input type="number" name="user_daily_limit" value="<?php echo $userDailyLim; ?>"
            min="1" max="20" class="input-inline" style="width:80px;" required>
        <button type="submit" class="btn-primary">Save</button>
    </form>
</div>
