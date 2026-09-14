# 🐳 RESCO POS - Docker Quick Start Guide

This project includes a complete Docker environment with **PHP 8.2-FPM**, **Nginx**, **MySQL 8.0**, and **phpMyAdmin**.

---

## 🚀 1. How to Start the Application

To build and start all containers in the background, run:

```bash
docker compose up -d --build
```

*(Or on older docker versions: `docker-compose up -d --build`)*

Once started, the entrypoint script will automatically:
1. Wait for MySQL Database to become healthy.
2. Run database migrations and initial seeders.
3. Fix file storage permissions.
4. Clear and optimize Laravel caches.

---

## 🌐 2. Application URLs

| Service | URL | Description |
|---|---|---|
| **RESCO POS App** | [http://localhost:8000](http://localhost:8000) | Main Restaurant Management System |
| **phpMyAdmin** | [http://localhost:8081](http://localhost:8081) | Database Visual GUI Management |
| **MySQL Host Port** | `localhost:3307` | Direct DB connection (avoids XAMPP port 3306 conflict) |

### 🔑 Database Credentials:
- **Host**: `db` (inside docker network) or `127.0.0.1:3307` (from host tools like HeidiSQL/DBeaver)
- **Database**: `resco_db`
- **Username**: `resco_user`
- **Password**: `secret`
- **Root Password**: `secret`

---

## 🛠️ 3. Useful Docker Commands

### Stop All Containers:
```bash
docker compose down
```

### View Live Logs:
```bash
docker compose logs -f app
```

### Run Artisan Commands inside Container:
```bash
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
docker compose exec app php artisan route:list
```

### Run Composer or NPM:
```bash
docker compose exec app composer update
docker compose exec app npm run build
```

### Access Container Shell (Bash/Sh):
```bash
docker compose exec app bash
```

### Restart All Services:
```bash
docker compose restart
```
