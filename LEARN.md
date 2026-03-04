# PHP OOP Login/Register System — Learning Guide

This project is a **learning codebase**: a simple login/register system built with PHP and OOP. Use it to see how sessions, validation, file uploads (avatars), and a small MVC-like structure work.

---

## Table of Contents

1. [Quick Start](#quick-start)
2. [Project Structure](#project-structure)
3. [How It Works (Flow)](#how-it-works-flow)
4. [Simple Examples](#simple-examples)
5. [Avatar Feature](#avatar-feature)
6. [Security Concepts](#security-concepts)
7. [Where to Read Next](#where-to-read-next)

---

## Quick Start

1. **Database**  
   Import `db.sql` in MySQL/MariaDB to create the `php_oop` database and tables.  
   For avatar support, also run `db_migration_avatar.sql` once.

2. **Config**  
   Edit `core/init.php` or use environment variables: `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`.

3. **Run**  
   Use PHP built-in server:  
   `php -S localhost:8000`  
   Then open `http://localhost:8000` and register a user, then log in.

---

## Project Structure

```
├── core/init.php          # Bootstrap: config, autoload, session, optional “remember me”
├── classes/               # OOP core
│   ├── Config.php         # Read config by path (e.g. mysql/host)
│   ├── DB.php             # Singleton PDO wrapper (get, insert, update, delete)
│   ├── User.php           # User model: login, register, permissions, session
│   ├── Session.php        # Session helpers (put, get, flash, delete)
│   ├── Cookie.php         # Cookie helpers
│   ├── Hash.php           # Password hashing (bcrypt)
│   ├── Token.php          # CSRF token generate/check
│   ├── Input.php          # Safe POST/GET access
│   ├── Validate.php       # Rule-based validation (required, min, max, unique, matches)
│   ├── Redirect.php      # Redirect helper
│   ├── Group.php         # Account types (groups table): getAll(), isValidId(), getById()
│   └── Admin.php         # Admin-only access: requireAdmin($user), getAllUsers()
├── functions/sanitize.php # escape(), avatar helpers (get_avatar_url, avatar_html)
├── includes/
│   ├── header.php         # Nav, avatar in nav, page title
│   └── footer.php         # Closing HTML
├── uploads/avatars/       # Uploaded profile pictures (run migration to add DB column)
├── index.php              # Home (welcome + avatar)
├── login.php              # Login form + validation
├── register.php           # Registration form + validation (with account type dropdown)
├── admin.php              # Admin dashboard (list users); OOP guard via Admin::requireAdmin()
├── update.php             # Update name + optional avatar upload
├── profile.php            # Public profile (avatar + name)
├── changepassword.php     # Change password
├── logout.php             # Logout
├── db.sql                 # Base schema
└── db_migration_avatar.sql # Adds optional `avatar` column to `users`
```

---

## How It Works (Flow)

- **Every page** includes `core/init.php` first (config, autoload, session start, optional “remember me” login).
- **Protected pages** (e.g. `update.php`, `changepassword.php`) create `$user = new User()` and redirect if `!$user->isLoggedIn()`.
- **Forms** use POST and a **CSRF token**: `Token::generate()` in the form, `Token::check(Input::get('token'))` before processing.
- **Validation** uses the `Validate` class with rules (required, min, max, unique, matches); then you check `$validate->passed()` and use `$validate->errors()` for messages.
- **Redirects** use `Redirect::to('index.php')` (or other URL) after success; one-time messages use `Session::flash('home', '...')` and are shown on the next page (e.g. index).

---

## Simple Examples

### Example 1: Logging in

1. User opens `login.php` and submits username + password (and optional “Remember me”).
2. Server checks CSRF token, then validates username and password (required).
3. If validation passes, `$user->login($username, $password, $remember)` is called.
4. On success, `Redirect::to('index.php')` sends the user home; a flash message can say “Welcome back”.

### Example 2: Registering a new user (with account type)

1. User opens `register.php` and submits name, username, **account type** (Standard User or Administrator), password, password again.
2. Server checks token, then validates (name length, username unique, password min length, passwords match). **Account type** is validated with `Group::isValidId()` (OOP) so only existing group ids (e.g. 1, 2) are accepted.
3. If validation passes, `$user->create([..., 'group' => $accountType])` inserts a row; password is stored via `Hash::encryptPassword()`.
4. Server sets a flash message and redirects to `index.php`. Users with group “Administrator” get an “Admin” link in the nav and can open `admin.php`.

### Example 3: Updating profile and avatar

1. User opens `update.php` (must be logged in).
2. Form has `enctype="multipart/form-data"` so the optional avatar file is sent.
3. Server validates the name (required). For avatar, it checks `$_FILES['avatar']` (type, size), then moves the file to `uploads/avatars/` with a safe name (e.g. `userid_random.ext`) and saves the filename in the `users.avatar` column.
4. If the user had an old avatar file, it can be deleted to avoid clutter. Then redirect to home with a success flash.

### Example 4: Admin dashboard (OOP)

1. Admin user clicks “Admin” in the nav (link only shown when `$user->hasPermission('admin')`).
2. `admin.php` runs `Admin::requireAdmin($user)` so non-admins are redirected with a flash message.
3. Page uses `$admin->getAllUsers()` to list all users (with group name from JOIN); table shows avatar, username, name, account type, joined date.

### Example 5: Showing the current user and avatar

- **Header:** `$user = new User()` then, if logged in, `avatar_html($user->data(), 32, 'nav-avatar')` and a link to profile.
- **Home (index.php):** If logged in, show `avatar_html($user->data(), 56)` and the username with a link to `profile.php?user=...`.
- **Profile (profile.php):** Load user by `Input::get('user')`, then show `avatar_html($profileUser->data(), 80)` and name/username.

---

## Avatar Feature

- **Storage:** Filename only is stored in the database (`users.avatar`). The file lives in `uploads/avatars/`.
- **Migration:** Run `db_migration_avatar.sql` once so the `users` table has an `avatar` column. If the column is missing, the app still runs; avatar upload will be skipped and initials will be used.
- **Display:** Use the helpers in `functions/sanitize.php`:
  - `get_avatar_url($userData)` — returns the path to the image file or `null`.
  - `get_avatar_initials($userData)` — returns 2 letters (e.g. “JD” for “John Doe”).
  - `avatar_html($userData, $size, $cssClass)` — outputs either an `<img>` or a circle with initials so you don’t need to write HTML in every page.
- **Upload:** Handled in `update.php`: validate type (JPEG/PNG/GIF) and size (e.g. 2 MB), then `move_uploaded_file()` and update the user’s `avatar` field.

---

## Account types and Admin (OOP)

- **Groups**  
  The `groups` table defines account types (e.g. id 1 = Standard User, 2 = Administrator). Permissions are stored as JSON (e.g. `{"admin":1,"moderator":1}`).

- **Group class**  
  `Group::getAll()` returns all groups for the registration dropdown. `Group::isValidId($id)` checks that the chosen id exists before saving. `Group::getById($id)` and `Group::hasPermission($id, $key)` support permission checks.

- **Choosing account type at register**  
  The registration form includes an “Account type” `<select>` filled from `$group->getAll()`. The submitted value is validated with `$group->isValidId($accountType)` and stored in `users.group`.

- **Admin class**  
  `Admin::requireAdmin($user)` is called at the top of `admin.php`; it redirects to home with a flash message if the user is not logged in or does not have the `admin` permission. `Admin::getAllUsers()` returns all users with their group name (JOIN) for the dashboard table.

---

## Security Concepts

- **Passwords:** Never stored in plain text; use `Hash::encryptPassword()` and `Hash::isValidPassword()` (bcrypt).
- **CSRF:** Every state-changing form uses a token stored in the session; the form sends it back and the server checks it before doing anything.
- **XSS:** All output that comes from the user or database is escaped with `escape()` (e.g. in `sanitize.php`) before printing in HTML.
- **SQL:** The DB layer uses prepared statements (e.g. in `DB::query()`), so parameters are bound and not concatenated into SQL.

---

## Where to Read Next

- **Flow:** Start with `core/init.php`, then `login.php` and `register.php` to see bootstrap, validation, and redirects.
- **User logic:** Read `classes/User.php` for login, session, remember-me, and permissions.
- **Avatar:** Read `functions/sanitize.php` for the helpers and `update.php` for the upload and validation logic.
- **Database:** Look at `classes/DB.php` and `db.sql` / `db_migration_avatar.sql` to see how tables and queries are used.

Use the **comments** in the PHP files for step-by-step guidance inside the code.
