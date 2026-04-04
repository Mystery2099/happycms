# HappyCMS

HappyCMS is a small PHP + SQLite content management system where PHP is used for backend responsibilities and Svelte is used as aggressively as possible for the frontend.

## Architecture

- PHP handles routing, request validation, sessions, security headers, and SQLite persistence.
- Svelte handles the client-side UI layer, including the home page shell, theme controls, list controls, and Fetch API quote browser.
- The split is intended to satisfy the PHP assignment requirements in [docs/assignment-requirements.md](docs/assignment-requirements.md) without treating PHP templates as the primary frontend technology.

## Quick Start

From the project root:

```bash
./run.sh
```

Then open:

```text
http://127.0.0.1:8000
```

This is the main recommended way to run the project.

## Requirements

- PHP
- Bun

Runtime SQLite data is stored outside the web root by default in a sibling `happycms-data/database/` directory, so no separate database server is required.

## Project URLs

When the app is running, the main pages are:

- `/`
- `/create/`
- `/search/`
- `/thoughts/`
- `/thoughts/edit/?id=1`
- `/thoughts/delete/?id=1`

## Run It The Simple Way

From the project root:

```bash
./run.sh
```

This script:

1. builds the Svelte frontend with `bun run build`
2. starts PHP's built-in server

By default, it runs at:

```text
http://127.0.0.1:8000
```

To use a different port:

```bash
./run.sh 8080
```

You can also change the host or port with environment variables:

```bash
HOST=0.0.0.0 PORT=9000 ./run.sh
```

## Run It On WAMP

There are two reasonable ways to do this.

### Option 1: Use the project directly in your WAMP web root

1. Copy the project folder into your WAMP web root, usually something like:

```text
C:\wamp64\www\happycms
```

2. Open a terminal in the project folder and build the frontend:

```bash
bun install
bun run build
```

3. Start WAMP.

4. Open the site in a browser:

```text
http://localhost/happycms/
```

### Option 2: Use a VirtualHost

If your instructor or environment uses WAMP virtual hosts, point the host to this project folder and browse to that hostname instead.

Important notes for WAMP:

- The built frontend files must exist in `public/assets/`, so run `bun run build` before opening the site.
- Runtime SQLite data is written outside the web root by default in a sibling `happycms-data/database/` directory. You can override this with `HAPPYCMS_DATA_DIR`.
- This project does not require MySQL.

## Run It With Docker

This project does not include a `Dockerfile`, but it can still be run with the official Node and PHP images.

### Step 1: Build the frontend assets

From the project root:

```bash
docker run --rm -v "${PWD}:/app" -w /app oven/bun:1 bun install
docker run --rm -v "${PWD}:/app" -w /app oven/bun:1 bun run build
```

### Step 2: Run PHP's built-in server in a container

```bash
docker run --rm -it -p 8000:8000 -v "${PWD}:/app" -w /app php:8.2-cli php -S 0.0.0.0:8000 -t /app
```

Then open:

```text
http://127.0.0.1:8000
```

### Docker note for Windows PowerShell

If `${PWD}` does not behave correctly in PowerShell, use the full project path instead. Example:

```powershell
docker run --rm -v "C:\path\to\happycms:/app" -w /app oven/bun:1 bun run build
docker run --rm -it -p 8000:8000 -v "C:\path\to\happycms:/app" -w /app php:8.2-cli php -S 0.0.0.0:8000 -t /app
```

## Notes

- Runtime SQLite data is stored outside the web root by default in a sibling `happycms-data/database/` directory. You can override this with `HAPPYCMS_DATA_DIR`.
- Seed quote data for the API is read from `storage/database/famous-thoughts.txt` unless an override file exists in the external data directory.
- Static site assets live under `public/`, with icons in `public/icons/` and demo images in `public/images/`.
- If the page loads without styling, the frontend probably has not been built yet. Run `bun run build`.
- Unknown routes are redirected back to `/`.
