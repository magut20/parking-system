<?php

declare(strict_types=1);

namespace App;

final class Flash
{
    private const SESSION_KEY = '_flash';

    public static function set(string $type, string $message): void
    {
        $_SESSION[self::SESSION_KEY][] = ['type' => $type, 'message' => $message];
    }

    /** Returns and clears all pending flash messages. */
    public static function pull(): array
    {
        $messages = $_SESSION[self::SESSION_KEY] ?? [];
        unset($_SESSION[self::SESSION_KEY]);
        return $messages;
    }
}
