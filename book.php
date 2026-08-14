<?php
include 'db.php';

$slot_id = $_POST['slot_id'];
$name = $_POST['name'];

$stmt = $conn->prepare("UPDATE slots SET is_booked = 1, booked_by = ? WHERE id = ? AND is_booked = 0");
$stmt->bind_param("si", $name, $slot_id);
$stmt->execute();

header("Location: index.php");
?>
