<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/src/bootstrap.php';

use App\Csrf;
use App\Database;
use App\Env;
use App\Flash;
use App\SlotRepository;

$repo = new SlotRepository(Database::connection());
$repo->expireStale((int) Env::get('BOOKING_EXPIRY_HOURS', '24'));

$action = $_SERVER['REQUEST_METHOD'] === 'POST' ? ($_POST['action'] ?? '') : '';

if ($action === 'book') {
    if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
        Flash::set('error', 'Your session expired — please try again.');
        redirect('index.php');
    }

    $slotId = (int) ($_POST['slot_id'] ?? 0);
    $name = (string) ($_POST['name'] ?? '');
    $result = $repo->book($slotId, $name);

    if ($result['ok']) {
        Flash::set(
            'success',
            "Slot booked! Save this release code to free it yourself later: {$result['code']}"
        );
    } else {
        Flash::set('error', $result['error']);
    }
    redirect('index.php');
}

if ($action === 'release') {
    if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
        Flash::set('error', 'Your session expired — please try again.');
        redirect('index.php');
    }

    $slotNumber = (int) ($_POST['slot_number'] ?? 0);
    $code = (string) ($_POST['release_code'] ?? '');
    if ($repo->releaseWithCode($slotNumber, $code)) {
        Flash::set('success', 'Slot released. Thanks!');
    } else {
        Flash::set('error', 'Slot number and release code did not match.');
    }
    redirect('index.php');
}

$slots = $repo->all();
echo render('index', [
    'title' => 'Select a Parking Slot',
    'flashes' => Flash::pull(),
    'slots' => $slots,
    'csrfField' => Csrf::field(),
]);
