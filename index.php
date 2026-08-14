<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Parking Slot Booking</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Select a Parking Slot</h2>
<div class="slots">
    <?php
    $result = $conn->query("SELECT * FROM slots ORDER BY slot_number ASC");
    while ($row = $result->fetch_assoc()) {
        $class = $row['is_booked'] ? 'booked' : 'available';
        echo "<div class='slot $class'>";
        echo "Slot {$row['slot_number']}<br>";
        if (!$row['is_booked']) {
            echo "<form method='POST' action='book.php'>
                    <input type='hidden' name='slot_id' value='{$row['id']}'>
                    <input type='text' name='name' placeholder='Your Name' required>
                    <button type='submit'>Book</button>
                  </form>";
        } else {
            echo "Booked by: {$row['booked_by']}";
        }
        echo "</div>";
    }
    ?>
</div>
</body>
</html>
