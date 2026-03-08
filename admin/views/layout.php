<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Car Workshop</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="admin-wrap">

    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-logo">🔧 Car Workshop</div>
        <ul>
            <li><a href="index.php?tab=dashboard"         class="<?php echo $tab==='dashboard'          ? 'active' : ''; ?>">📊 Dashboard</a></li>
            <li><a href="index.php?tab=bookings"          class="<?php echo $tab==='bookings'           ? 'active' : ''; ?>">📋 All Bookings</a></li>
            <li><a href="index.php?tab=mechanics"         class="<?php echo $tab==='mechanics'          ? 'active' : ''; ?>">🔩 Mechanics</a></li>
            <li><a href="index.php?tab=cars_per_mechanic" class="<?php echo $tab==='cars_per_mechanic'  ? 'active' : ''; ?>">🚗 Cars per Mechanic</a></li>
            <li><a href="index.php?tab=config"            class="<?php echo $tab==='config'             ? 'active' : ''; ?>">⚙️ Settings</a></li>
        </ul>
        <div class="sidebar-footer">
            <span>👤 <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            <a href="index.php?logout=1" class="logout-btn">Logout</a>
        </div>
    </nav>

    <!-- Main content -->
    <main class="admin-main">
        <div class="admin-topbar">
            <h2>
                <?php
                $titles = [
                    'dashboard'         => '📊 Dashboard',
                    'bookings'          => '📋 All Bookings',
                    'mechanics'         => '🔩 Mechanic Management',
                    'cars_per_mechanic' => '🚗 Cars Booked per Mechanic',
                    'config'            => '⚙️ Settings',
                ];
                echo $titles[$tab] ?? 'Admin Panel';
                ?>
            </h2>
            <a href="../index.php" class="link-btn">Go to Booking Page →</a>
        </div>

        <?php if (!empty($message)): ?>
            <div class="msg <?php echo $msgType; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php
        $viewFile = __DIR__ . '/tabs/' . $tab . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            require __DIR__ . '/tabs/dashboard.php';
        }
        ?>

    </main>
</div>

<script>
function toggleHalfDay(sel) {
    const row = sel.closest('form').querySelector('.half-day-field');
    row.style.display = sel.value === 'half' ? '' : 'none';
}
</script>

</body>
</html>
