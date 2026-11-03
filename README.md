# IntelliMatch

## TVET directory and maps

- `docker compose exec app php artisan migrate --force` adds agencies, institution courses and nullable campus/workplace coordinates without resetting records.
- `docker compose exec app php artisan tvet:import` imports the bundled 3 November 2026 TVET Madani snapshot: 14 agency groups, 1,324 institutions and 5,444 course records. It can be rerun; existing account memberships and manually confirmed campus coordinates are preserved. Existing demo institutions remain separate.
- Students browse `/training` by course keyword, agency and state; each campus page lists source-linked courses. Administrators access the same directory and confirm campus pins on the detail page. No coordinates are guessed from institution names or town centres.
- `/opportunities` has a map of all filtered, open, approved, non-remote workplaces. Roles without pins remain in the list. Employers select a workplace on the vacancy form; publishing on-site/hybrid roles requires a valid coordinate pair. Remote roles have no workplace pin. Existing vacancies retain their status and need a pin on their next publication edit.
- MapLibre GL JS 6.16 is loaded on the client with a separately bundled Vite worker. Maps require WebGL2; a list and coordinate inputs remain available if it cannot initialise. Maps default to attributed OpenStreetMap raster tiles. Set `VITE_MAP_STYLE_URL` to your GIS provider's MapLibre style URL and rebuild to use your company's basemap. Follow the tile provider's usage terms for deployment.
- Campus coordinates are initially pending: the public snapshot provides addresses, not verified geolocation. Admin confirmation is required before a campus appears on the map. Matching courses to skill gaps and distance-based career ranking are future work.

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

## Account seeders

Run from the project folder while the Docker `app` service is running:

```powershell
docker compose exec app php artisan migrate:fresh --seed
```

**This deletes all existing database tables**, runs the migrations, and seeds the accounts and demo data. To seed without resetting the database, run:

```powershell
docker compose exec app php artisan db:seed
```

The default seeder runs SuperAdminSeeder, InstitutionSeeder, StudentSeeder, and IndustrySeeder in that order:

- Superadmin: `idlan.skyrem@gmail.com` (verified, all current admin access).
- 5 institutions and staff accounts: `institution1@intellimatch.test` through `institution5@intellimatch.test`.
- 30 students: `student1@intellimatch.test` through `student30@intellimatch.test`. Each institution has 6 linked students with populated career profiles.
- 15 industry accounts: `industry1@intellimatch.test` through `industry15@intellimatch.test`. Each has an approved company and 3–5 published vacancies (60 total), with future closing dates.
- All seeded accounts are email verified. Demo data is only seeded in `local`/`testing` environments.
- The default password is `P@ssw0rd`. Override `SEED_SUPERADMIN_PASSWORD` and `SEED_DEMO_PASSWORD` in `.env` as needed. Demo email addresses use fixed numbered ranges so each account has a unique email.
- Rerunning seeders resets passwords to the configured values and refreshes demo profiles, institution links, companies, and vacancies while preserving existing account names. It does not duplicate the seeded records.
- Public registration always creates students; role and admin flags are not mass assignable.

## Institution workspace

- `/institution` provides scoped totals, common self-reported skills, and a searchable student directory.
- `/institution/students/{id}` shows a linked student's profile and published career alignment.
- Institution access requires a verified institution-role account with a server-assigned `institution_id`.
- Profile institution text never grants membership. Cross-institution student URLs return 404.
- Demo institution codes are `DEMO-TVET-1` through `DEMO-TVET-5`; each has one staff account and six linked students.
- Link existing students or institution staff with `docker compose exec app php artisan app:link-institution email@example.com DEMO-TVET-1`.
- Institution logins redirect from `/dashboard` to `/institution`.
- Programme administration, exports, and counselling notes are not implemented in this release.

## Industry workspace

Register as Industry / Employer, verify email, and open /industry. Submit company details, then an administrator approves the company at /admin/companies. Changes to company details require renewed approval. Approved employers can publish jobs and internships; other statuses remain hidden from /opportunities. All employer writes are scoped to their company. Students apply through HTTP/HTTPS employer links; no student resumes are exposed.

Run `docker compose exec app php artisan db:seed --class=IndustrySeeder` to seed only the 15 demo industry accounts using `SEED_DEMO_PASSWORD`. Their companies are approved and contain 3–5 sample published vacancies each. Logo upload, application tracking, and vacancy-specific AI scoring are not implemented.



## Career paths and applications

- Students can search `/opportunities` by keyword/company and location, filter job type and work arrangement, and browse one vacancy per row with 10 results per page.
- Applications are submitted inside IntelliMatch using the student's current career profile. One application per student/vacancy is allowed. Closed, expired and unapproved-company vacancies cannot receive applications.
- `/student/applications` tracks submitted, shortlisted and rejected applications.
- Industry users open **Applicants** under each vacancy. Only the owning employer can view applicants or change their status.
- Match scores use 70% case-insensitive required skill coverage and 30% minimum qualification level (certificate, diploma, degree). Missing qualification information contributes no qualification points and is labelled insufficient data. Scores use self-reported current profiles; employers must review programme relevance and evidence themselves.
- `/student/career-paths` lets a student select one active target career, view skill gaps, track learning tasks and discover linked published vacancies. Completing a task does not automatically add a skill. The dashboard highlights the target and related jobs.
- Student profiles record the highest qualification held; employers can set a minimum qualification level when editing vacancies. Existing records without these fields show insufficient qualification data until updated.
- After pulling these changes, run `docker compose exec app php artisan migrate` and `docker compose run --rm --no-deps node npm run build`.
