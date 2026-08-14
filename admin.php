<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Unbook Slots</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Admin - Manage Bookings</h2>
<div class="slots">
    <?php
    $result = $conn->query("SELECT * FROM slots ORDER BY slot_number ASC");
    while ($row = $result->fetch_assoc()) {
        $class = $row['is_booked'] ? 'booked' : 'available';
        echo "<div class='slot $class'>";
        echo "Slot {$row['slot_number']}<br>";
        if ($row['is_booked']) {
            echo "Booked by: {$row['booked_by']}<br>";
            echo "<form method='POST' action='unbook.php'>
                    <input type='hidden' name='slot_id' value='{$row['id']}'>
                    <button type='submit'>Unbook</button>
                  </form>";
        } else {
            echo "Available";
        }
        echo "</div>";
    }
    ?>
</div>
</body>
</html>
