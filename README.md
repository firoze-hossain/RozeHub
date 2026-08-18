# RozeHub

RozeHub is a Laravel + Livewire software distribution portal for DBNavigator,
ThunderCall, StratosDB, Lumina, Roze, Roze OS, and Trackline.

## Included

- Searchable catalog with Windows, macOS, and Linux filters
- Release packages, per-package download counts, and release notes
- Community ratings and reviews
- Publisher Studio at `/studio` for uploading new version packages
- MySQL configuration through Laravel's `mysql` database driver

## Local setup

Create the MySQL database and a dedicated user, then put those credentials in
`.env`:

```sql
CREATE DATABASE rozehub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'rozehub'@'localhost' IDENTIFIED BY 'change-this-password';
GRANT ALL PRIVILEGES ON rozehub.* TO 'rozehub'@'localhost';
FLUSH PRIVILEGES;
```

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rozehub
DB_USERNAME=rozehub
DB_PASSWORD=change-this-password
```

Then run:

```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Open `http://127.0.0.1:8000`.
