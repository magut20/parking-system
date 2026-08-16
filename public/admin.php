<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/src/bootstrap.php';

use App\Csrf;
use App\Database;
use App\Flash;
use App\SlotRepository;

// NOTE: This admin panel is intentionally left without a login screen,
// per current project requirements. Anyone with the URL can release any
// slot from here. If that changes, add a session-based auth check at the
// top of this file before it's exposed publicly.

$repo = new SlotRepository(Database::connection());

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'unbook') {
    if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
        Flash::set('error', 'Your session expired — please try again.');
        redirect('admin.php');
    }

    $slotId = (int) ($_POST['slot_id'] ?? 0);
    if ($repo->adminRelease($slotId)) {
        Flash::set('success', 'Slot released.');
    } else {
        Flash::set('error', 'Could not release that slot.');
    }
    redirect('admin.php');
}

$slots = $repo->all();
echo render('admin', [
    'title' => 'Admin - Manage Bookings',
    'flashes' => Flash::pull(),
    'slots' => $slots,
    'csrfField' => Csrf::field(),
]);
