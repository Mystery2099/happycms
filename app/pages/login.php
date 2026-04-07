<?php
/**
 * Login Page Template
 * 
 * Backend TODO:
 * 1. Add login route to your router (e.g., /login)
 * 2. Implement POST handler for form submission
 * 3. Add CSRF token generation and validation
 * 4. Add session-based authentication
 * 5. Add flash message support for errors
 * 6. Redirect authenticated users away from this page
 */

declare(strict_types=1);

require_request_method(['GET', 'POST']);

// Backend: Add your authentication logic here
// Example:
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $email = $_POST['email'] ?? '';
//     $password = $_POST['password'] ?? '';
//     $remember = isset($_POST['remember']);
//     
//     if (authenticate_user($email, $password)) {
//         start_session($email, $remember);
//         redirect(route_url('home'));
//     } else {
//         set_flash('error', 'Invalid email or password');
//     }
// }

// Backend: Check if user is already logged in, redirect if so
// if (is_logged_in()) {
//     redirect(route_url('home'));
// }

$flash = get_flash();
$loginPageProps = [
    'loginUrl' => route_url('login'),  // Backend: Add this route
    'homeUrl' => route_url('home'),
    'csrfToken' => '',  // Backend: Generate and include CSRF token
    'error' => $flash['message'] ?? '',  // Backend: Pass error message from flash
];

$loginPagePropsJson = json_encode(
    $loginPageProps,
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR
);

$pageTitle = 'Sign In | Happy Thoughts';
$pageDescription = 'Sign in to access your happy thoughts collection.';
$currentPage = 'login';

require project_root_path() . '/app/views/layout/header.php';
?>

<div data-login-page></div>
<script id="login-page-props" type="application/json">
<?= $loginPagePropsJson ?>
</script>

<?php require project_root_path() . '/app/views/layout/footer.php'; ?>
