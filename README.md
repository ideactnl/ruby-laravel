
## Ruby-Laravel API Project

This is a Laravel API application, containerized with Docker and deployed on Render.com.

### Local Development
1. Copy `.env.example` to `.env` and configure your environment variables.
2. Start the application:
   ```sh
   docker-compose up --build
   ```
3. The app will be available at [http://localhost](http://localhost)

### API Documentation
- API docs are auto-generated with Scribe.
- To regenerate locally:
  ```sh
  php artisan scribe:generate
  ```

### CI/CD
- Continuous Integration and Deployment are managed with GitHub Actions.
- On each push or pull request, tests, linting, and static analysis are run automatically.
- Deployment scripts handle database migrations and API docs regeneration.

### Notes
- Do not commit `/public/docs/`, `/public/vendor/`, `.scribe/`, or sensitive files.
- Set all secrets and environment variables using your preferred environment management approach.