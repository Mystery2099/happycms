<?php

declare(strict_types=1);

function log_internal_error(Throwable $exception): void
{
    error_log(sprintf(
        '[happycms] %s in %s:%d',
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine()
    ));
}

function render_error_response(int $statusCode = 500): never
{
    http_response_code($statusCode);

    if (expects_json_response()) {
        send_json_security_headers();
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'success' => false,
            'error' => 'A server error occurred.',
        ], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

        exit;
    }

    send_page_security_headers();

    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Server Error</title></head><body><main><h1>Something went wrong.</h1><p>The request could not be completed. Please refresh the page and try again.</p></main></body></html>';
    exit;
}

function install_error_handlers(): void
{
    static $installed = false;

    if ($installed) {
        return;
    }

    set_exception_handler(static function (Throwable $exception): void {
        log_internal_error($exception);
        render_error_response(500);
    });

    register_shutdown_function(static function (): void {
        $error = error_get_last();
        if (!is_array($error)) {
            return;
        }

        $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR];
        if (!in_array($error['type'] ?? null, $fatalTypes, true)) {
            return;
        }

        error_log(sprintf(
            '[happycms] Fatal error: %s in %s:%d',
            $error['message'] ?? 'Unknown fatal error',
            $error['file'] ?? 'unknown',
            $error['line'] ?? 0
        ));

        render_error_response(500);
    });

    $installed = true;
}
