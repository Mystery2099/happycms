<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/constants.php';
require_once __DIR__ . '/lib/database.php';
require_once __DIR__ . '/lib/paths.php';
require_once __DIR__ . '/lib/session.php';
require_once __DIR__ . '/lib/http.php';
require_once __DIR__ . '/lib/security.php';
require_once __DIR__ . '/lib/errors.php';
require_once __DIR__ . '/lib/validation.php';
require_once __DIR__ . '/lib/thoughts.php';
require_once __DIR__ . '/lib/famous-thoughts.php';

boot_session();
install_error_handlers();
