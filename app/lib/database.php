<?php

declare(strict_types=1);

function app_data_root_path(): string
{
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
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS happy_thoughts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            author TEXT NOT NULL,
            category TEXT NOT NULL,
            mood_score INTEGER NOT NULL DEFAULT 3,
            thought TEXT NOT NULL,
            image_path TEXT DEFAULT NULL,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )'
    );

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            name TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT \'admin\',
            is_active INTEGER NOT NULL DEFAULT 1,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )'
    );

    seed_default_admin_user($pdo);

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
        'INSERT INTO happy_thoughts (title, author, category, mood_score, thought, image_path)
         VALUES (:title, :author, :category, :mood_score, :thought, :image_path)'
    );

    foreach ($seedThoughts as $thought) {
        $statement->execute($thought);
    }
}

function seed_default_admin_user(PDO $pdo): void
{
    $email = auth_default_admin_email();
    $statement = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $statement->execute(['email' => $email]);

    if ($statement->fetchColumn() !== false) {
        return;
    }

    $insertStatement = $pdo->prepare(
        'INSERT INTO users (email, password_hash, name, role, is_active)
         VALUES (:email, :password_hash, :name, :role, :is_active)'
    );

    $insertStatement->execute([
        'email' => $email,
        'password_hash' => password_hash(auth_default_admin_password(), PASSWORD_DEFAULT, auth_password_options()),
        'name' => auth_default_admin_name(),
        'role' => AUTH_ROLE_ADMIN,
        'is_active' => 1,
    ]);
}

function normalize_database_user(array $user): array
{
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
