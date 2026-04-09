<?php

declare(strict_types=1);

function request_client_identifier(): string
{
    $remoteAddress = trim((string) ($_SERVER['REMOTE_ADDR'] ?? ''));

    return $remoteAddress !== '' ? $remoteAddress : 'unknown-client';
}

function rate_limit_directory(): string
{
    return app_data_root_path() . DIRECTORY_SEPARATOR . 'rate-limits';
}

/**
 * Track request timestamps in a small file-backed bucket so rate limits work
 * across PHP built-in server requests without requiring Redis or a database table.
 */
function consume_rate_limit(string $name, int $limit, int $windowSeconds, ?string $subject = null): ?int
{
    if ($limit < 1 || $windowSeconds < 1) {
        return null;
    }

    $directory = rate_limit_directory();
    if (!is_dir($directory)) {
        mkdir($directory, 0775, true);
    }

    $resolvedSubject = $subject !== null && trim($subject) !== ''
        ? trim($subject)
        : request_client_identifier();
    $bucketKey = hash('sha256', $name . '|' . strtolower($resolvedSubject));
    $bucketPath = $directory . DIRECTORY_SEPARATOR . $bucketKey . '.log';
    $handle = fopen($bucketPath, 'c+');

    if (!is_resource($handle)) {
        return null;
    }

    try {
        if (!flock($handle, LOCK_EX)) {
            return null;
        }

        $contents = stream_get_contents($handle);
        $now = time();
        $windowStart = $now - $windowSeconds;
        $timestamps = [];

        foreach (preg_split('/\R+/', trim((string) $contents)) as $line) {
            $timestamp = (int) $line;
            if ($timestamp > $windowStart) {
                $timestamps[] = $timestamp;
            }
        }

        if (count($timestamps) >= $limit) {
            $retryAfter = max(1, ($timestamps[0] + $windowSeconds) - $now);
            ftruncate($handle, 0);
            rewind($handle);
            fwrite($handle, implode(PHP_EOL, array_map('strval', $timestamps)));

            return $retryAfter;
        }

        $timestamps[] = $now;
        ftruncate($handle, 0);
        rewind($handle);
        fwrite($handle, implode(PHP_EOL, array_map('strval', $timestamps)));

        return null;
    } catch (Throwable $exception) {
        log_internal_error($exception);

        return null;
    } finally {
        flock($handle, LOCK_UN);
        fclose($handle);
    }
}
