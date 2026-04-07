# Authentication UI Implementation

This document describes the authentication UI components that have been added to the project and outlines the backend integration required.

## What Was Added

### Frontend Components

1. **LoginPage.svelte** (`frontend/src/pages/LoginPage.svelte`)
   - Clean, centered login form with email/password fields
   - Password visibility toggle
   - "Remember me" checkbox
   - Loading state with spinner animation
   - Error message display area
   - Dark mode support

2. **UserMenu.svelte** (`frontend/src/components/UserMenu.svelte`)
   - Shows user avatar and name when logged in
   - Dropdown menu with logout option
   - Shows "Sign in" button when logged out
   - Click-outside-to-close behavior

3. **Updated SiteHeader.svelte**
   - Integrated UserMenu into the header navigation
   - Added vertical separator between theme selector and user menu

### PHP Template

4. **login.php** (`app/pages/login.php`)
   - Page template following the existing pattern
   - Includes TODO comments for backend integration

### Configuration Updates

5. **main.ts**
   - Added new Lucide icons: LogIn, Lock, Eye, EyeOff, AlertCircle, LogOut, Settings
   - Registered LoginPage component

6. **types.ts**
   - Added 'login' to ShellRouteKey type
   - Added commented auth type definitions for future use

7. **shell.php**
   - Added commented login route (uncomment when backend is ready)

## Backend Integration Checklist

To complete the authentication system, implement the following on the backend:

### 1. Database Schema

Add a `users` table to your SQLite database:

```sql
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### 2. Session Management

Create or update `app/lib/session.php`:

```php
function start_session(string $email, bool $remember = false): void {
    // Initialize session
    // Set session cookie parameters
    // Store user data in session
}

function end_session(): void {
    // Clear session data
    // Destroy session
}

function is_logged_in(): bool {
    // Check if valid session exists
}

function current_user(): ?array {
    // Return current user data from session
}
```

### 3. Authentication Functions

Add to `app/lib/security.php` or create `app/lib/auth.php`:

```php
function authenticate_user(string $email, string $password): bool {
    // Verify email/password against database
    // Use password_verify() for checking hashes
}

function require_auth(): void {
    // Redirect to login if not authenticated
}
```

### 4. CSRF Protection

Ensure CSRF tokens are generated and validated:

```php
function generate_csrf_token(): string {
    // Generate and store token in session
}

function validate_csrf_token(string $token): bool {
    // Verify token matches session
}
```

### 5. Update Routes

Add the login route to your router (`router.php` or routing configuration):

```php
// In your route definitions
'login' => '/login/',
'logout' => '/logout/',
```

### 6. Update login.php

Remove placeholder comments and implement:

```php
// At the top of app/pages/login.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf_token($_POST['csrf_token'] ?? '');
    
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (authenticate_user($email, $password)) {
        start_session($email, $remember);
        redirect(route_url('home'));
    } else {
        set_flash('error', 'Invalid email or password');
    }
}

// Redirect if already logged in
if (is_logged_in()) {
    redirect(route_url('home'));
}
```

### 7. Update SiteHeader Props

Modify `app/lib/shell.php` or pass auth data to the header:

```php
function shell_component_props(string $currentPage): array {
    $user = current_user();
    
    return [
        'currentPage' => $currentPage,
        'routes' => shell_routes(),
        // Add these when auth is ready:
        'isLoggedIn' => is_logged_in(),
        'userName' => $user['name'] ?? '',
        'userEmail' => $user['email'] ?? '',
    ];
}
```

Then update `SiteHeader.svelte` to remove the placeholder `authProps` and use the actual props.

### 8. Logout Handler

Create a simple logout endpoint or add to your router:

```php
// In router or logout.php
if ($path === '/logout') {
    end_session();
    redirect(route_url('home'));
}
```

### 9. Protect Routes (Optional)

Add authentication checks to protected pages:

```php
// At the top of protected pages
require_auth();
```

## Optional Enhancements

Once basic auth is working, consider adding:

1. **Registration page** - Uncomment the registration link in LoginPage.svelte
2. **Password reset** - Uncomment "Forgot password?" link in LoginPage.svelte  
3. **Profile page** - Uncomment profile link in UserMenu.svelte
4. **Settings page** - Uncomment settings link in UserMenu.svelte

## Security Considerations

- Always use HTTPS in production
- Store passwords with `password_hash()` and verify with `password_verify()`
- Use prepared statements for all database queries
- Regenerate session ID after login
- Set appropriate session cookie flags (HttpOnly, Secure, SameSite)
- Rate limit login attempts to prevent brute force attacks
- Consider adding account lockout after failed attempts
