# Docker Setup

Dokumentasi untuk menjalankan aplikasi menggunakan Docker.

## Prerequisites

- Docker Engine 20.10+
- Docker Compose 2.0+

## Quick Start

### 1. Clone Repository

```bash
git clone https://github.com/Arfan9swn/lks-kaltim-2026-ARF-284A63A0.git
cd lks-kaltim-2026-ARF-284A63A0
```

### 2. Setup Environment

```bash
# copy env file
cp .env.example .env

# generate APP_KEY
docker-compose run --rm app php artisan key:generate

# gnerate JWT Secret
docker-compose run --rm app php artisan jwt:secret
```

### 3. Start Application

```bash
# start all services (app + mysql + redis)
docker-compose up -d

# Check status
docker-compose ps

# View logs
docker-compose logs -f
```

### 4. Setup Database

```bash
# Run migrations
docker-compose exec app php artisan migrate

```

### 5. Access Application

- **API:** http://localhost:8000
- **MySQL:** localhost:3306
  - Username: laravel
  - Password: laravel
- **Redis:** localhost:6379
  - Password: (from .env REDIS_PASSWORD)

## Docker Commands

### Build and Start

```bash
# Build images
docker-compose build

# Start services
docker-compose up -d

# Stop services
docker-compose down

# Stop and remove volumes (WARNING: deletes data)
docker-compose down -v
```

### Application Commands

```bash
# Run artisan commands
docker-compose exec app php artisan <command>

# Example:
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan route:list
docker-compose exec app php artisan config:clear
```

### Logs

```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f app
docker-compose logs -f mysql
docker-compose logs -f redis
```

### Shell Access

```bash
# Access app container
docker-compose exec app sh

# Access MySQL
docker-compose exec mysql mysql -u laravel -p laravel

# Access Redis
docker-compose exec redis redis-cli -a your_redis_password_here
```

## Architecture

### Multi-Stage Build

1. **Builder Stage:** Uses `composer:2` to install PHP dependencies
2. **Production Stage:** Uses `php:8.2-fpm-alpine` with nginx and supervisor

### Services

- **app:** Laravel application with nginx + php-fpm
- **mysql:** MySQL 8.0 database
- **redis:** Redis 7 for cache, session, and queue

### Network

- **app-network:** Bridge network untuk komunikasi antar service
  - app → mysql (port 3306)
  - app → redis (port 6379)

### Volumes

- **mysql_data:** Persistence untuk MySQL data
- **redis_data:** Persistence untuk Redis data
- **storage:** Laravel public storage

### Security

- Runs as non-root user (laravel:laravel)
- Environment variables not committed to repository
- .dockerignore excludes sensitive files
- Redis password protected

## Environment Variables

Key environment variables in `.env`:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=laravel

REDIS_HOST=redis
REDIS_PASSWORD=your_redis_password_here

JWT_SECRET=your_jwt_secret_key_here

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

## Ports

- **8000:** Application (HTTP)
- **3306:** MySQL Database
- **6379:** Redis Cache

## Volumes

- `mysql_data:` - MySQL data persistence
- `redis_data:` - Redis data persistence
- `storage:` - Laravel storage (public files)

## Network

- **app-network:** Bridge network untuk komunikasi antar service
  - app dapat mengakses mysql dan redis
  - mysql dan redis dapat diakses dari app

## Troubleshooting

### Permission Issues

```bash
# Fix storage permissions
docker-compose exec app chown -R laravel:laravel storage bootstrap/cache
```

### Clear Cache

```bash
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```

### Rebuild After Changes

```bash
# Rebuild and restart
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

## Production Deployment

For production deployment:

1. Set `APP_ENV=production` and `APP_DEBUG=false`
2. Use strong passwords in `.env`
3. Configure SSL/TLS certificates
4. Set up proper backup strategy for MySQL
5. Use Docker secrets for sensitive data
6. Configure firewall rules
7. Set up monitoring and logging

## License

MIT