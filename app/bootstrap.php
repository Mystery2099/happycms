<?php

declare(strict_types=1);

ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);

require_once __DIR__ . '/lib/constants.php';
require_once __DIR__ . '/lib/database.php';
require_once __DIR__ . '/lib/paths.php';
require_once __DIR__ . '/lib/session.php';
require_once __DIR__ . '/lib/http.php';
require_once __DIR__ . '/lib/security.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/errors.php';
require_once __DIR__ . '/lib/validation.php';
require_once __DIR__ . '/lib/thoughts.php';
require_once __DIR__ . '/lib/famous-thoughts.php';
require_once __DIR__ . '/lib/rate-limit.php';
require_once __DIR__ . '/lib/shell.php';

boot_session();
install_error_handlers();
