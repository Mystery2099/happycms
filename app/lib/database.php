<?php

declare(strict_types=1);

function app_data_root_path(): string
{
    /**
     * Resolve the writable runtime data directory, defaulting outside the web root
     * so SQLite files are not exposed by a direct static file serving setup.
     */
    $configuredPath = getenv('HAPPYCMS_DATA_DIR');
    if (is_string($configuredPath) && trim($configuredPath) !== '') {
        return rtrim($configuredPath, DIRECTORY_SEPARATOR);
    }

    return dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'happycms-data';
}

function database_path(): string
{
    return app_data_root_path() . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'happy.sqlite';
}

function get_pdo(): PDO
{
    /**
     * Reuse a single PDO instance per request so schema initialization and seed
     * checks run once while every caller sees consistent connection settings.
     */
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $databasePath = database_path();
    $directory = dirname($databasePath);
    if (!is_dir($directory)) {
        mkdir($directory, 0775, true);
    }

    $pdo = new PDO('sqlite:' . $databasePath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    initialize_database($pdo);

    return $pdo;
}

function initialize_database(PDO $pdo): void
{
    /**
     * Keep schema creation and first-run seed data together so a fresh install is
     * immediately usable and older databases are migrated forward on boot.
     */
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            name TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT \'guest\',
            is_active INTEGER NOT NULL DEFAULT 1,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )'
    );

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS happy_thoughts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            author TEXT NOT NULL,
            category TEXT NOT NULL,
            mood_score INTEGER NOT NULL DEFAULT 3,
            thought TEXT NOT NULL,
            image_path TEXT DEFAULT NULL,
            created_by_user_id INTEGER DEFAULT NULL REFERENCES users(id) ON DELETE SET NULL,
            updated_by_user_id INTEGER DEFAULT NULL REFERENCES users(id) ON DELETE SET NULL,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )'
    );

    ensure_happy_thought_audit_columns($pdo);
    ensure_happy_thought_audit_indexes($pdo);

    $seededUsers = seed_default_users($pdo);
    $defaultAdminUserId = $seededUsers['admin'];
    backfill_happy_thought_audit_users($pdo, $defaultAdminUserId);

    $count = (int) $pdo->query('SELECT COUNT(*) FROM happy_thoughts')->fetchColumn();
    if ($count > 0) {
        return;
    }

    $seedThoughts = [
        [
            'title' => 'Morning Garden Walk',
            'author' => 'Avery',
            'category' => 'Nature',
            'mood_score' => 5,
            'thought' => 'The tulips outside the library looked impossible this morning, like the campus decided to start over in color.',
            'image_path' => 'public/images/spring-hero.jpg',
        ],
        [
            'title' => 'Coffee with Friends',
            'author' => 'Jordan',
            'category' => 'Community',
            'mood_score' => 4,
            'thought' => 'A short coffee break with the right people can make a difficult week feel manageable again.',
            'image_path' => null,
        ],
        [
            'title' => 'Unexpected Sunshine',
            'author' => 'Morgan',
            'category' => 'Weather',
            'mood_score' => 5,
            'thought' => 'When the clouds finally moved, the sunlight hit every puddle on campus like a spotlight.',
            'image_path' => 'public/images/happy-sun.png',
        ],
        [
            'title' => 'Fresh Start',
            'author' => 'Taylor',
            'category' => 'Growth',
            'mood_score' => 4,
            'thought' => 'Finishing one hard assignment means the next challenge starts with proof that I can do difficult work.',
            'image_path' => null,
        ],
        [
            'title' => 'Weekend Call Home',
            'author' => 'Casey',
            'category' => 'Family',
            'mood_score' => 5,
            'thought' => 'Hearing laughter through the phone still counts as being surrounded by the people who know you best.',
            'image_path' => null,
        ],
    ];

    $statement = $pdo->prepare(
        'INSERT INTO happy_thoughts (
            title,
            author,
            category,
            mood_score,
            thought,
            image_path,
            created_by_user_id,
            updated_by_user_id
         ) VALUES (
            :title,
            :author,
            :category,
            :mood_score,
            :thought,
            :image_path,
            :created_by_user_id,
            :updated_by_user_id
         )'
    );

    foreach ($seedThoughts as $thought) {
        $statement->execute([
            ...$thought,
            'created_by_user_id' => $defaultAdminUserId,
            'updated_by_user_id' => $defaultAdminUserId,
        ]);
    }
}

function happy_thought_columns(PDO $pdo): array
{
    $columns = [];
    $statement = $pdo->query('PRAGMA table_info(happy_thoughts)');
    foreach ($statement->fetchAll() as $column) {
        $name = $column['name'] ?? null;
        if (is_string($name) && $name !== '') {
            $columns[] = $name;
        }
    }

    return $columns;
}

function ensure_happy_thought_audit_columns(PDO $pdo): void
{
    /**
     * Add ownership columns lazily for databases created before audit metadata
     * existed, allowing deployments to self-migrate without a separate step.
     */
    $columns = happy_thought_columns($pdo);
    if (!in_array('created_by_user_id', $columns, true)) {
        $pdo->exec(
            'ALTER TABLE happy_thoughts
             ADD COLUMN created_by_user_id INTEGER DEFAULT NULL REFERENCES users(id) ON DELETE SET NULL'
        );
    }

    if (!in_array('updated_by_user_id', $columns, true)) {
        $pdo->exec(
            'ALTER TABLE happy_thoughts
             ADD COLUMN updated_by_user_id INTEGER DEFAULT NULL REFERENCES users(id) ON DELETE SET NULL'
        );
    }
}

function ensure_happy_thought_audit_indexes(PDO $pdo): void
{
    /**
     * Index audit ownership columns because admin listings and joins resolve
     * creator/editor names on nearly every thought read.
     */
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_happy_thoughts_created_by_user_id ON happy_thoughts(created_by_user_id)');
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_happy_thoughts_updated_by_user_id ON happy_thoughts(updated_by_user_id)');
}

function backfill_happy_thought_audit_users(PDO $pdo, int $defaultAdminUserId): void
{
    /**
     * Older databases predate audit ownership columns. When those rows are
     * migrated forward, attribute them to the seeded admin so admin-only edit
     * history remains internally consistent.
     */
    $statement = $pdo->prepare(
        'UPDATE happy_thoughts
         SET created_by_user_id = COALESCE(created_by_user_id, :user_id),
             updated_by_user_id = COALESCE(updated_by_user_id, :user_id)
         WHERE created_by_user_id IS NULL
            OR updated_by_user_id IS NULL'
    );
    $statement->execute(['user_id' => $defaultAdminUserId]);
}

function env_seed_user_value(string $key, string $defaultValue, bool $normalizeEmail = false): string
{
    $value = trim((string) getenv($key));
    $resolvedValue = $value !== '' ? $value : $defaultValue;

    return $normalizeEmail ? strtolower($resolvedValue) : $resolvedValue;
}

function default_seed_user_definitions(): array
{
    /**
     * Build the initial user set from environment overrides. After the first
     * seed, the database owns those identities and subsequent boots preserve them.
     */
    return [
        'admin' => [
            'email' => env_seed_user_value('HAPPYCMS_ADMIN_EMAIL', 'admin@happycms.local', true),
            'password' => env_seed_user_value('HAPPYCMS_ADMIN_PASSWORD', 'ChangeMe123!'),
            'name' => env_seed_user_value('HAPPYCMS_ADMIN_NAME', 'Happy Admin'),
            'role' => AUTH_ROLE_ADMIN,
            'is_active' => 1,
        ],
        'guest' => [
            'email' => env_seed_user_value('HAPPYCMS_GUEST_EMAIL', 'guest@happycms.local', true),
            'password' => env_seed_user_value('HAPPYCMS_GUEST_PASSWORD', 'Guest123!'),
            'name' => env_seed_user_value('HAPPYCMS_GUEST_NAME', 'Happy Guest'),
            'role' => AUTH_ROLE_GUEST,
            'is_active' => 1,
        ],
    ];
}

function seed_default_users(PDO $pdo): array
{
    /** @var array<string, int> $seededUsers */
    $seededUsers = [];

    foreach (default_seed_user_definitions() as $key => $user) {
        $seededUsers[$key] = seed_database_user($pdo, $user);
    }

    return $seededUsers;
}

function seed_database_user(PDO $pdo, array $user): int
{
    /**
     * Seed users idempotently by email so local databases keep any existing
     * password or role changes instead of being reset on every application boot.
     */
    $statement = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $statement->execute(['email' => $user['email']]);

    $existingId = $statement->fetchColumn();
    if ($existingId !== false) {
        return (int) $existingId;
    }

    $insertStatement = $pdo->prepare(
        'INSERT INTO users (email, password_hash, name, role, is_active)
         VALUES (:email, :password_hash, :name, :role, :is_active)'
    );

    $insertStatement->execute([
        'email' => $user['email'],
        'password_hash' => password_hash($user['password'], PASSWORD_DEFAULT, auth_password_options()),
        'name' => $user['name'],
        'role' => $user['role'],
        'is_active' => $user['is_active'],
    ]);

    return (int) $pdo->lastInsertId();
}

function normalize_database_user(array $user): array
{
    /**
     * Convert raw SQLite values into the stable shape expected by auth and page
     * rendering code, especially for boolean-like integer fields.
     */
    return [
        'id' => (int) $user['id'],
        'email' => (string) $user['email'],
        'password_hash' => (string) $user['password_hash'],
        'name' => (string) $user['name'],
        'role' => (string) $user['role'],
        'is_active' => ((int) ($user['is_active'] ?? 0)) === 1,
        'created_at' => (string) $user['created_at'],
        'updated_at' => (string) $user['updated_at'],
    ];
}

function find_user_by_id(int $id): ?array
{
    $statement = get_pdo()->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
    $statement->execute(['id' => $id]);
    $user = $statement->fetch();

    return is_array($user) ? normalize_database_user($user) : null;
}

function find_user_by_email(string $email): ?array
{
    $normalizedEmail = strtolower(trim($email));
    if ($normalizedEmail === '') {
        return null;
    }

    $statement = get_pdo()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
    $statement->execute(['email' => $normalizedEmail]);
    $user = $statement->fetch();

    return is_array($user) ? normalize_database_user($user) : null;
}

function rehash_user_password(int $id, string $password): void
{
    $statement = get_pdo()->prepare(
        'UPDATE users
         SET password_hash = :password_hash,
             updated_at = CURRENT_TIMESTAMP
         WHERE id = :id'
    );

    $statement->execute([
        'id' => $id,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT, auth_password_options()),
    ]);
}
