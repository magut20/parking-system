<?php
include 'db.php';

$slot_id = $_POST['slot_id'];

$stmt = $conn->prepare("UPDATE slots SET is_booked = 0, booked_by = NULL WHERE id = ?");
$stmt->bind_param("i", $slot_id);
$stmt->execute();

header("Location: admin.php");
?>
