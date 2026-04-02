-- 1. Create the new user_origins table
CREATE TABLE IF NOT EXISTS user_origins (
    id_user INT UNSIGNED NOT NULL,
    country_code CHAR(2) NOT NULL,
    PRIMARY KEY (id_user, country_code),
    INDEX (country_code),
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Migrate existing location data from users table
-- We only migrate if the location is exactly 2 characters (likely an ISO code)
INSERT INTO user_origins (id_user, country_code)
SELECT id_user, UPPER(location)
FROM users
WHERE location IS NOT NULL 
  AND LENGTH(location) = 2
ON DUPLICATE KEY UPDATE country_code = VALUES(country_code);

-- 3. (Optional) You can keep the location column for now but it's no longer used by the new code.
-- ALTER TABLE users DROP COLUMN location; 
