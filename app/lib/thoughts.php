<?php

declare(strict_types=1);

function all_thoughts(?string $search = null): array
{
    $pdo = get_pdo();
    $searchTerm = trim((string) $search);

    if ($searchTerm === '') {
        return $pdo->query(thought_select_query() . ' ORDER BY ht.created_at DESC, ht.id DESC')->fetchAll();
    }

    $statement = $pdo->prepare(
        thought_select_query() .
        ' WHERE ht.title LIKE :search
             OR ht.author LIKE :search
             OR ht.category LIKE :search
             OR ht.thought LIKE :search
          ORDER BY ht.created_at DESC, ht.id DESC'
    );
    $statement->execute(['search' => '%' . $searchTerm . '%']);

    return $statement->fetchAll();
}

function recent_thoughts(int $limit = 3): array
{
    $statement = get_pdo()->prepare(thought_select_query() . ' ORDER BY ht.created_at DESC, ht.id DESC LIMIT :limit');
    $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
    $statement->execute();

    return $statement->fetchAll();
}

function find_thought(int $id): ?array
{
    $statement = get_pdo()->prepare(thought_select_query() . ' WHERE ht.id = :id LIMIT 1');
    $statement->execute(['id' => $id]);
    $thought = $statement->fetch();

    return $thought ?: null;
}

function create_thought(array $data, ?int $userId = null): int
{
    $payload = [
        ...$data,
        'created_by_user_id' => $userId,
        'updated_by_user_id' => $userId,
    ];
    $statement = get_pdo()->prepare(
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
    $statement->execute($payload);

    return (int) get_pdo()->lastInsertId();
}

function update_thought(int $id, array $data, ?int $userId = null): void
{
    $payload = [...$data, 'id' => $id, 'updated_by_user_id' => $userId];
    $statement = get_pdo()->prepare(
        'UPDATE happy_thoughts
         SET title = :title,
             author = :author,
             category = :category,
             mood_score = :mood_score,
             thought = :thought,
             image_path = :image_path,
             updated_by_user_id = :updated_by_user_id,
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

function thought_select_query(): string
{
    return 'SELECT
                ht.id,
                ht.title,
                ht.author,
                ht.category,
                ht.mood_score,
                ht.thought,
                ht.image_path,
                ht.created_at,
                ht.updated_at,
                ht.created_by_user_id,
                ht.updated_by_user_id,
                creator.name AS created_by_name,
                creator.email AS created_by_email,
                editor.name AS updated_by_name,
                editor.email AS updated_by_email
            FROM happy_thoughts ht
            LEFT JOIN users creator ON creator.id = ht.created_by_user_id
            LEFT JOIN users editor ON editor.id = ht.updated_by_user_id';
}
