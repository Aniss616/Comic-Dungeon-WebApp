# Comic Dungeon

A comic book web application built with Laravel, powered by the [Comic Vine API](https://comicvine.gamespot.com/api/). Designed to help new readers discover characters, find where to start reading, and track their progress as they explore the world of comics.

---

## Features

### For All Visitors
- Browse characters, volumes, issues, and story arcs
- Search locally or live against the Comic Vine API
- View character details including powers, teams, allies, enemies, and first appearance
- View issue and volume details with structured descriptions
- View story arcs with issues listed in release order

### For Registered Users
- Favourite characters and issues
- Mark issues as read and track reading history
- Get personalised reading recommendations based on history

### For Admins
- Import data from Comic Vine (characters, volumes, issues, publishers, people)
- Search and import directly from the dashboard

---

## Tech Stack

- **Backend:** Laravel (PHP 8.2)
- **Frontend:** Blade + Tailwind CSS v4 + Vite
- **Database:** MySQL
- **API:** Comic Vine API
- **Auth:** Laravel session-based (custom)

---

## Prerequisites

- PHP 8.2+
- Composer
- Node.js + npm
- MySQL
- [Comic Vine API key](https://comicvine.gamespot.com/api/)

---

## Installation

```bash
git clone https://github.com/your-username/comic-dungeon.git
cd comic-dungeon
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Update `.env` with your database credentials and API key:

```env
DB_DATABASE=comic_dungeon
DB_USERNAME=root
DB_PASSWORD=

COMIC_VINE_API_KEY=your_api_key_here
```

```bash
php artisan migrate
```

---

## Running the App

```bash
# Terminal 1
npm run dev

# Terminal 2
php artisan serve
```

Visit: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## Admin Account

Create an admin user via Tinker:

```bash
php artisan tinker
```

```php
App\Models\User::create([
    'username'      => 'admin',
    'email'         => 'admin@example.com',
    'password_hash' => bcrypt('password'),
    'is_admin'      => true,
]);
```

---

## Importing Data

Use the admin dashboard at `/dashboard` to search Comic Vine and import characters, volumes, issues, publishers, and people. Imports are idempotent.

After importing new issues, sync story arcs:

```bash
php artisan story-arcs:sync
```

> The Comic Vine API enforces a rate limit of 200 requests/hour. The app applies a 1.1-second delay between requests automatically.

---

## Key Notes

- The `/issues` list endpoint on Comic Vine does **not** return `character_credits` — each issue is fetched individually to retrieve full data.
- The `issue_people` pivot uses `people_id` (not `person_id`) — foreign keys are specified explicitly in relationships.
- Comic Vine descriptions are raw HTML, parsed into structured sections by heading level for display.
