<?= render('partials/header', ['title' => $title, 'flashes' => $flashes]) ?>

<div class="slots">
    <?php foreach ($slots as $slot): ?>
        <?php $booked = (bool) $slot['is_booked']; ?>
        <div class="slot <?= $booked ? 'booked' : 'available' ?>">
            <div class="slot-number">Slot <?= e($slot['slot_number']) ?></div>
            <?php if ($booked): ?>
                <div class="slot-status">Booked by <?= e($slot['booked_by']) ?></div>
            <?php else: ?>
                <form method="POST" action="index.php">
                    <input type="hidden" name="action" value="book">
                    <input type="hidden" name="slot_id" value="<?= e($slot['id']) ?>">
                    <?= $csrfField ?>
                    <input type="text" name="name" placeholder="Your name" maxlength="100" required>
                    <button type="submit">Book</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<section class="release-box">
    <h3>Booked a slot and need to free it early?</h3>
    <form method="POST" action="index.php" class="release-form">
        <input type="hidden" name="action" value="release">
        <?= $csrfField ?>
        <label>Slot number
            <input type="number" name="slot_number" min="1" required>
        </label>
        <label>Release code
            <input type="text" name="release_code" maxlength="12" required>
        </label>
        <button type="submit">Release my slot</button>
    </form>
    <p class="hint">Use the slot number shown above and the release code from your booking confirmation.</p>
</section>

<?= render('partials/footer') ?>
