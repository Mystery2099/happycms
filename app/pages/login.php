<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
require_request_method(['GET', 'POST']);

if (is_logged_in()) {
    redirect(login_redirect_target());
}

$formState = [
    'email' => '',
    'remember' => false,
];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$credentials, $validationErrors] = validate_login_input($_POST);
    $formState['email'] = $credentials['email'];
    $formState['remember'] = $credentials['remember'];

    if (!is_same_origin_request()) {
        $error = 'Invalid request origin. Refresh the page and try again.';
    } elseif (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $error = 'Your session expired. Refresh the page and try again.';
    } elseif ($validationErrors) {
        $error = reset($validationErrors) ?: 'Enter your login details and try again.';
    } else {
        $user = authenticate_user($credentials['email'], $credentials['password']);
        if ($user === null) {
            $error = 'Invalid email or password.';
        } else {
            login_user((int) $user['id'], $credentials['remember']);
            set_flash('success', 'Signed in successfully.');
            redirect(login_redirect_target());
        }
    }
}

$loginPageProps = [
    'loginUrl' => route_url('login'),
    'homeUrl' => route_url('home'),
    'csrfToken' => csrf_token(),
    'error' => $error,
    'redirectTo' => login_redirect_target(),
    'initialEmail' => $formState['email'],
    'rememberMe' => $formState['remember'],
];

$pageTitle = 'Sign In | Happy Thoughts';
$pageDescription = 'Sign in to access your happy thoughts collection.';
$currentPage = 'login';

require project_root_path() . '/app/views/layout/header.php';
?>

<div data-login-page></div>
<script id="login-page-props" type="application/json"><?= page_props_json($loginPageProps) ?></script>

<?php require project_root_path() . '/app/views/layout/footer.php'; ?>
