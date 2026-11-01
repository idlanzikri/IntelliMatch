# IntelliMatch

Vue 3 + TypeScript + Inertia + Laravel, with PostgreSQL and Docker.

## First run (PowerShell)

Start Docker Desktop with Linux containers, then run from this folder:

```powershell
docker compose build app
docker compose run --rm app composer install
docker compose run --rm app php artisan key:generate
docker compose up -d db
docker compose run --rm app php artisan migrate
docker compose run --rm --no-deps node npm install
docker compose run --rm --no-deps node npm run build
docker compose up -d app
docker compose --profile worker up -d worker
```

Open http://localhost:8000. Register your own account; no default credentials are created.
If `.env` is missing, copy `.env.example` to `.env` before setup and change the database password.

## Development

After frontend changes, repeat the node build command. The application source is mounted into Docker.
Use container PHP commands: the installed Laragon PHP 8.2 is below the starter requirement.
Resume uploads must remain in private storage. AI integrations and role-specific pages are planned, not implemented.

## Checks

```powershell
docker compose run --rm app php artisan test
docker compose run --rm --no-deps node npm run types:check
```

## Stop

```powershell
docker compose down
```

Database data persists in a Docker volume. The database is not exposed on a host port.
The worker is optional and should be started after migrations are complete.

## Planned feature areas

Student profiles, career catalogue, matching, resume analysis, learning plans, institution reporting, and administration.
Keep matching rules in backend services and enforce institution boundaries using policies.

## IntelliMatch implementation

- `routes/web.php` maps student endpoints to `Student/CareerController`.
- `app/Http/Requests/Career` authorises and validates requests.
- Controllers delegate to `app/Actions/Career/*Action::execute()`.
- `resources/js/pages` contains screens; `components/career` contains reusable career UI; `types/career.ts` defines the shared frontend data shape.
- `/student/profile` saves the authenticated student's profile. Ownership is assigned by the server.
- `/dashboard` ranks four sample career profiles using self-reported skill coverage. It is not a live job feed or an AI assessment.
- Original supplied branding is in `public/brand/intellimatch.jpeg`.

AI resume parsing, live jobs, learning-resource integration, and institution/admin workflows remain future work. Existing authentication follows the starter's Fortify flow.
