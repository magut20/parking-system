<?php

declare(strict_types=1);

require_once __DIR__ . '/Env.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Csrf.php';
require_once __DIR__ . '/Flash.php';
require_once __DIR__ . '/SlotRepository.php';
require_once __DIR__ . '/helpers.php';

use App\Env;

Env::load(dirname(__DIR__) . '/.env');

$appEnv = Env::get('APP_ENV', 'production');
if ($appEnv === 'local') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
}

session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
]);
