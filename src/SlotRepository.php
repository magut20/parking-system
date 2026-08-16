<?php

declare(strict_types=1);

namespace App;

use PDO;

final class SlotRepository
{
    public function __construct(private PDO $db)
    {
    }

    /** @return array<int, array<string, mixed>> */
    public function all(): array
    {
        $stmt = $this->db->query('SELECT * FROM slots ORDER BY slot_number ASC');
        return $stmt->fetchAll();
    }

    /**
     * Books a free slot atomically. Returns a 6-character release code the
     * booker can use later to free their own slot without admin access.
     *
     * @return array{ok: bool, code?: string, error?: string}
     */
    public function book(int $slotId, string $name): array
    {
        $name = trim($name);
        if ($name === '') {
            return ['ok' => false, 'error' => 'Please enter your name.'];
        }
        if (mb_strlen($name) > 100) {
            return ['ok' => false, 'error' => 'Name is too long (max 100 characters).'];
        }

        $code = strtoupper(bin2hex(random_bytes(3))); // 6 hex chars

        $stmt = $this->db->prepare(
            'UPDATE slots
             SET is_booked = 1, booked_by = ?, release_code = ?, booked_at = ?
             WHERE id = ? AND is_booked = 0'
        );
        $stmt->execute([$name, $code, self::now(), $slotId]);

        if ($stmt->rowCount() === 0) {
            return ['ok' => false, 'error' => 'That slot was just taken — please pick another.'];
        }

        return ['ok' => true, 'code' => $code];
    }

    /** Self-service release: the booker proves ownership with their code. */
    public function releaseWithCode(int $slotNumber, string $code): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE slots
             SET is_booked = 0, booked_by = NULL, release_code = NULL, booked_at = NULL
             WHERE slot_number = ? AND is_booked = 1 AND release_code = ?'
        );
        $stmt->execute([$slotNumber, strtoupper(trim($code))]);
        return $stmt->rowCount() > 0;
    }

    /** Admin override: releases a slot regardless of the code. */
    public function adminRelease(int $slotId): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE slots
             SET is_booked = 0, booked_by = NULL, release_code = NULL, booked_at = NULL
             WHERE id = ?'
        );
        $stmt->execute([$slotId]);
        return $stmt->rowCount() > 0;
    }

    /** Auto-frees bookings older than the configured expiry window. */
    public function expireStale(int $hours): void
    {
        if ($hours <= 0) {
            return;
        }
        $cutoff = date('Y-m-d H:i:s', time() - $hours * 3600);
        $stmt = $this->db->prepare(
            'UPDATE slots
             SET is_booked = 0, booked_by = NULL, release_code = NULL, booked_at = NULL
             WHERE is_booked = 1 AND booked_at IS NOT NULL AND booked_at < ?'
        );
        $stmt->execute([$cutoff]);
    }

    private static function now(): string
    {
        return date('Y-m-d H:i:s');
    }
}
