# Authentication

HappyCMS now uses PHP session-based authentication with role-based authorization.

## Current Behavior

- Reading content remains public.
- Creating, editing, and deleting thoughts requires an authenticated `admin`.
- The header sign-in menu is powered by server props from PHP.
- Logout is handled as a CSRF-protected `POST`.

## Default Admin Account

On first run, the app seeds a default admin user into SQLite:

- Email: `admin@happycms.local`
- Password: `ChangeMe123!`
- Role: `admin`

For a different seeded admin, set these environment variables before starting the app:

- `HAPPYCMS_ADMIN_NAME`
- `HAPPYCMS_ADMIN_EMAIL`
- `HAPPYCMS_ADMIN_PASSWORD`

Example:

```bash
HAPPYCMS_ADMIN_NAME="Professor Demo" \
HAPPYCMS_ADMIN_EMAIL="prof@example.com" \
HAPPYCMS_ADMIN_PASSWORD="BetterPassword123!" \
./run.sh
```

If the seeded admin already exists in the database, the app will not overwrite it.

## Implementation Notes

- Passwords are hashed with PHP's `password_hash()` and checked with `password_verify()`.
- Session IDs are regenerated on login and logout.
- Authenticated user data is loaded from the database on each request.
- Role checks currently use `require_role('admin')`.
