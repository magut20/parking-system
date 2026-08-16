<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Parking Booking') ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="site-header">
    <h1><?= e($title ?? 'Parking Booking') ?></h1>
    <nav>
        <a href="index.php">Book a slot</a>
        <a href="admin.php">Admin</a>
    </nav>
</header>
<main>
<?php foreach (($flashes ?? []) as $flash): ?>
    <div class="flash flash-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
<?php endforeach; ?>
