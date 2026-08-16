CREATE TABLE IF NOT EXISTS slots (
  id            INTEGER PRIMARY KEY AUTOINCREMENT,
  slot_number   INTEGER NOT NULL UNIQUE,
  is_booked     INTEGER NOT NULL DEFAULT 0,
  booked_by     TEXT DEFAULT NULL,
  release_code  TEXT DEFAULT NULL,
  booked_at     TEXT DEFAULT NULL
);
