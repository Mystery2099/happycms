<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
require_request_method(['POST']);

if (!is_same_origin_request() || !verify_csrf_token($_POST['csrf_token'] ?? null)) {
    set_flash('error', 'Your session expired. Refresh the page and try again.');
    redirect_route('home');
}

logout_user();
set_flash('success', 'Signed out successfully.');
redirect_route('home');
