<?= render('partials/header', ['title' => $title, 'flashes' => $flashes]) ?>

<p class="admin-note">No login is required for this page in the current setup — anyone with the link can release slots here.</p>

<div class="slots">
    <?php foreach ($slots as $slot): ?>
        <?php $booked = (bool) $slot['is_booked']; ?>
        <div class="slot <?= $booked ? 'booked' : 'available' ?>">
            <div class="slot-number">Slot <?= e($slot['slot_number']) ?></div>
            <?php if ($booked): ?>
                <div class="slot-status">
                    Booked by <?= e($slot['booked_by']) ?><br>
                    <?php if (!empty($slot['booked_at'])): ?>
                        <small>since <?= e($slot['booked_at']) ?></small>
                    <?php endif; ?>
                </div>
                <form method="POST" action="admin.php">
                    <input type="hidden" name="action" value="unbook">
                    <input type="hidden" name="slot_id" value="<?= e($slot['id']) ?>">
                    <?= $csrfField ?>
                    <button type="submit">Release</button>
                </form>
            <?php else: ?>
                <div class="slot-status">Available</div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<?= render('partials/footer') ?>
