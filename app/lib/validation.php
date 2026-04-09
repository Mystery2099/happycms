<?php

declare(strict_types=1);

function normalized_text_input(mixed $value, int $maxLength): string
{
    /**
     * Normalize free-form text by trimming, removing control characters, and
     * enforcing a maximum length with multibyte-safe truncation when available.
     */
    $text = trim((string) $value);
    $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text) ?? '';

    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($text) > $maxLength) {
            $text = mb_substr($text, 0, $maxLength);
        }

        return $text;
    }

    if (strlen($text) > $maxLength) {
        return substr($text, 0, $maxLength);
    }

    return $text;
}

function text_length(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
}

function normalized_search_query(mixed $value): string
{
    return normalized_text_input($value, 100);
}

function normalize_local_image_path(?string $path): ?string
{
    /**
     * Accept only project-local images from whitelisted directories, then resolve
     * them against the real filesystem to block traversal and broken references.
     */
    $path = trim((string) $path);
    if ($path === '') {
        return null;
    }

    $normalizedPath = str_replace('\\', '/', ltrim($path, '/'));
    if (!preg_match('#^(images|public/assets)/[A-Za-z0-9._/-]+$#', $normalizedPath)) {
        return null;
    }

    if (str_contains($normalizedPath, '../') || str_contains($normalizedPath, '/..')) {
        return null;
    }

    $extension = strtolower(pathinfo($normalizedPath, PATHINFO_EXTENSION));
    if ($extension === '' || !in_array($extension, ALLOWED_IMAGE_EXTENSIONS, true)) {
        return null;
    }

    $resolvedPath = realpath(project_root_path() . '/' . $normalizedPath);
    if ($resolvedPath === false) {
        return null;
    }

    $projectRoot = project_root_path();
    if (!str_starts_with($resolvedPath, $projectRoot . DIRECTORY_SEPARATOR)) {
        return null;
    }

    if (!is_file($resolvedPath) || !is_readable($resolvedPath)) {
        return null;
    }

    return str_replace(DIRECTORY_SEPARATOR, '/', substr($resolvedPath, strlen($projectRoot) + 1));
}

function validate_thought_input(array $input): array
{
    /**
     * Return a normalized thought payload alongside field-level validation errors
     * so page handlers can re-render the form without duplicating normalization.
     */
    $data = [
        'title' => normalized_text_input($input['title'] ?? '', 80),
        'author' => normalized_text_input($input['author'] ?? '', 60),
        'category' => normalized_text_input($input['category'] ?? 'Nature', 30),
        'mood_score' => (int) ($input['mood_score'] ?? 3),
        'thought' => normalized_text_input($input['thought'] ?? '', 400),
        'image_path' => normalized_text_input($input['image_path'] ?? '', 255),
    ];

    $errors = [];

    if ($data['title'] === '' || text_length($data['title']) < 3) {
        $errors['title'] = 'Use a title with at least 3 characters.';
    } elseif (text_length($data['title']) > 80) {
        $errors['title'] = 'Title must stay under 80 characters.';
    }

    if ($data['author'] === '' || text_length($data['author']) < 2) {
        $errors['author'] = 'Author is required.';
    } elseif (text_length($data['author']) > 60) {
        $errors['author'] = 'Author must stay under 60 characters.';
    }

    if (!in_array($data['category'], THOUGHT_CATEGORIES, true)) {
        $errors['category'] = 'Choose one of the listed categories.';
    }

    if ($data['mood_score'] < 1 || $data['mood_score'] > 5) {
        $errors['mood_score'] = 'Mood score must be between 1 and 5.';
    }

    if ($data['thought'] === '' || text_length($data['thought']) < 12) {
        $errors['thought'] = 'Thought text must be at least 12 characters.';
    } elseif (text_length($data['thought']) > 400) {
        $errors['thought'] = 'Thought text must stay under 400 characters.';
    }

    $normalizedImagePath = normalize_local_image_path($data['image_path']);
    if ($data['image_path'] !== '' && $normalizedImagePath === null) {
        $errors['image_path'] = 'Use an existing local image in images/ or public/assets/ with a standard image extension.';
    }

    $data['image_path'] = $normalizedImagePath;

    return [$data, $errors];
}

function validate_login_input(array $input): array
{
    /**
     * Normalize login form input into a predictable auth payload before the
     * authentication layer decides whether the credentials are valid.
     */
    $email = strtolower(normalized_text_input($input['email'] ?? '', 255));
    $password = (string) ($input['password'] ?? '');
    $remember = isset($input['remember']) && (string) $input['remember'] !== '0';

    $errors = [];

    if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $errors['email'] = 'Enter a valid email address.';
    }

    if ($password === '') {
        $errors['password'] = 'Enter your password.';
    }

    return [[
        'email' => $email,
        'password' => $password,
        'remember' => $remember,
    ], $errors];
}
