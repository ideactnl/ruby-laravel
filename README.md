## Ruby-Laravel API Project

This is a Laravel API application, containerized with Docker and deployable to Render or any cloud provider.

---

### 1. Local Development with Laravel Sail (Recommended)

Laravel Sail provides a full-featured local dev environment with MySQL, Redis, etc., all via Docker Compose.

**Setup:**
1. Copy `.env.example` to `.env` and configure your environment variables (especially DB settings for MySQL).
2. Install Sail (if not already):
   ```sh
   composer require laravel/sail --dev
   php artisan sail:install
   ```
3. Start Sail:
   ```sh
   ./vendor/bin/sail up -d
   # Or, if you have Sail aliased:
   sail up -d
   ```
4. Access your app at [http://localhost](http://localhost)

**Common Sail Commands:**
- Run Artisan: `sail artisan <command>`
- Run Composer: `sail composer <command>`
- Run tests: `sail test`
- Stop services: `sail down`

> **Note:** Sail uses MySQL by default. Update your `.env` to use MySQL for Sail. Do not use SQLite for production.

---

### 2. Local Development with Standalone Docker

You can also build and run the project using plain Docker (with SQLite for local only):

#### Build the Docker image
```sh
docker build -t ruby-laravel-local .
```

#### Run the container

> **Note:** You must provide a valid Laravel APP_KEY. Generate it locally with:
> ```sh
> php artisan key:generate --show
> ```
> Copy the output and use it as the value for `APP_KEY` in the command below.

```sh
docker run -d -p 8082:80 \
  -e DB_CONNECTION=sqlite \
  -e DB_DATABASE=/var/www/html/storage/database.sqlite \
  -e APP_KEY=<YOUR_APP_KEY> \
  -e APP_DEBUG=true \
  -e APP_URL=http://localhost:8082 \
  ruby-laravel-local
```

#### Set correct permissions for SQLite (replace `<container_id>` with the ID from `docker ps`)
```sh
docker exec -it <container_id> touch /var/www/html/storage/database.sqlite

docker exec -it <container_id> chown nginx:nginx /var/www/html/storage/database.sqlite

docker exec -it <container_id> chmod 666 /var/www/html/storage/database.sqlite
```

- The API will be available at [http://localhost:8082](http://localhost:8082)
- The Scribe API documentation will be available at [http://localhost:8082/docs](http://localhost:8082/docs)

> **Note:** For local dev, SQLite is used for convenience. **Never use SQLite for production!**

---

### 3. Deployment & CI/CD (Branching and Automation)

#### Branching Strategy
- **main**: Production branch. All code merged here is considered production-ready.
- **staging**: Used for pre-production testing and QA.
- **development**: Active development branch for ongoing features and integration.
- **feature/***: Short-lived branches for individual features or fixes.

#### CI (Continuous Integration)
- On every push or pull request to `main`, `staging`, `development`, or any `feature/*` branch, the CI workflow runs:
  - Composer install
  - Static analysis (PHPStan)
  - Code style checks (Laravel Pint)
  - All Laravel tests
- This ensures code quality and prevents regressions before merging.

#### CD (Continuous Deployment)
- **Automatic deployment is triggered only for the `main` branch.**
- When code is pushed to `main`, the CD workflow sends a deploy hook to Render using a secure secret.
- Render then builds and deploys the latest code from `main`.
- All environment variables and secrets are managed in the Render dashboard.

#### Recommended Workflow
1. Develop features or fixes in `feature/*` branches.
2. Merge into `development` for integration/testing.
3. Promote to `staging` for QA.
4. Merge into `main` to trigger production deployment.

- **Environment Variables:** Set all secrets (e.g., `APP_KEY`, DB credentials) in your Render dashboard or cloud provider. Do **not** rely on `.env` files in production.
- **Database:** Use a dedicated, secure database (MySQL/PostgreSQL). Never use SQLite in production or share a local dev DB file.
- **Build & Deploy:** Render will run your deploy script automatically. Ensure your Dockerfile and scripts do not depend on `.env` files.
- **API Docs:** Scribe docs are generated and served at `/docs`.

> **Render Deployment Note:**
> - When deploying to Render with Docker, set all secrets and environment variables (including `APP_KEY`, `APP_URL`, etc.) in the Render dashboard.
> - Use `DB_URL` (not `DATABASE_URL`) for your database connection string—Render sets this automatically for PostgreSQL/MySQL.
> - See the [official Render guide](https://render.com/docs/deploy-php-laravel-docker) for more details.

---

### API Documentation
- API docs are auto-generated with Scribe.
- To regenerate locally:
  ```sh
  php artisan scribe:generate
  ```

---

### Troubleshooting

- **Permission errors with SQLite?**
  Ensure you have set the correct permissions as shown in the Docker section above.
- **Laravel key errors?**
  Make sure you generate and provide a valid `APP_KEY` as an environment variable.
- **API docs not updating?**
  Regenerate with `php artisan scribe:generate` and ensure config cache is cleared before docs generation.

For more help, see [Laravel Docs](https://laravel.com/docs) or [Render’s PHP guide](https://render.com/docs/deploy-php-laravel-docker).