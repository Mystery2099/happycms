<?php

declare(strict_types=1);

function all_thoughts(?string $search = null): array
{
    $pdo = get_pdo();
    $searchTerm = trim((string) $search);

    if ($searchTerm === '') {
        return $pdo->query('SELECT * FROM happy_thoughts ORDER BY created_at DESC, id DESC')->fetchAll();
    }

    $statement = $pdo->prepare(
        'SELECT * FROM happy_thoughts
         WHERE title LIKE :search
            OR author LIKE :search
            OR category LIKE :search
            OR thought LIKE :search
         ORDER BY created_at DESC, id DESC'
    );
    $statement->execute(['search' => '%' . $searchTerm . '%']);

    return $statement->fetchAll();
}

function recent_thoughts(int $limit = 3): array
{
    $statement = get_pdo()->prepare('SELECT * FROM happy_thoughts ORDER BY created_at DESC, id DESC LIMIT :limit');
    $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
    $statement->execute();

    return $statement->fetchAll();
}

function find_thought(int $id): ?array
{
    $statement = get_pdo()->prepare('SELECT * FROM happy_thoughts WHERE id = :id LIMIT 1');
    $statement->execute(['id' => $id]);
    $thought = $statement->fetch();

    return $thought ?: null;
}

function create_thought(array $data): int
{
    $statement = get_pdo()->prepare(
        'INSERT INTO happy_thoughts (title, author, category, mood_score, thought, image_path)
         VALUES (:title, :author, :category, :mood_score, :thought, :image_path)'
    );
    $statement->execute($data);

    return (int) get_pdo()->lastInsertId();
}

function update_thought(int $id, array $data): void
{
    $payload = [...$data, 'id' => $id];
    $statement = get_pdo()->prepare(
        'UPDATE happy_thoughts
         SET title = :title,
             author = :author,
             category = :category,
             mood_score = :mood_score,
             thought = :thought,
             image_path = :image_path,
             updated_at = CURRENT_TIMESTAMP
         WHERE id = :id'
    );
    $statement->execute($payload);
}

function remove_thought(int $id): void
{
    $statement = get_pdo()->prepare('DELETE FROM happy_thoughts WHERE id = :id');
    $statement->execute(['id' => $id]);
}

function dashboard_stats(): array
{
    $pdo = get_pdo();

    return [
        'total' => (int) $pdo->query('SELECT COUNT(*) FROM happy_thoughts')->fetchColumn(),
        'categories' => (int) $pdo->query('SELECT COUNT(DISTINCT category) FROM happy_thoughts')->fetchColumn(),
        'high_mood' => (int) $pdo->query('SELECT COUNT(*) FROM happy_thoughts WHERE mood_score >= 4')->fetchColumn(),
        'with_images' => (int) $pdo->query('SELECT COUNT(*) FROM happy_thoughts WHERE image_path IS NOT NULL')->fetchColumn(),
    ];
}
