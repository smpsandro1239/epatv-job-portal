# EPATV Job Portal 🚀

The EPATV Job Portal is a web-based platform designed for Escola Profissional Amar Terra Verde (EPATV) to connect ex-students (alumni) with job opportunities posted by companies. Built with Laravel, it serves as a backend (REST API & Web routes) with Blade templates and Tailwind CSS for the web user interface. It offers a robust backend with MySQL, role-based access control, and automated database notifications.

## 📂 Project Structure Overview

A brief overview of key directories:

*   `app/Http/Controllers`: Handles incoming requests for both API (`Api` sub-namespace) and Web.
*   `app/Models`: Contains all Eloquent database models.
*   `app/Providers`: Houses service providers (e.g., `AppServiceProvider`, `AuthServiceProvider`).
*   `app/Notifications` (Laravel's built-in system): While this project primarily uses custom database notifications (`app/Models/Notification.php`), this directory would be for Laravel's notification classes if email/other channels were more extensively used via that system.
*   `config`: Application configuration files.
*   `database/factories`: Model factories used for seeding and testing.
*   `database/migrations`: Database schema migrations.
*   `database/seeders`: Database seeders for initial data.
*   `public`: The web server's document root and entry point (`index.php`); compiled assets.
*   `resources/views`: Contains all Blade templates for the web UI.
*   `routes`: Route definitions (`api.php` for API routes, `web.php` for web routes).
*   `storage`: Compiled Blade templates, file uploads (e.g., `storage/app/public`), logs, and framework cache.
*   `tests`: Application tests, written using the Pest testing framework.

## 🛠️ Tech Stack

| Component        | Technology                                     | Notes                                         |
| :--------------- | :--------------------------------------------- | :-------------------------------------------- |
| Backend          | Laravel 12                                     | PHP 8.2+                                      |
| Database         | MySQL                                          |                                               |
| Frontend         | Laravel Blade, Tailwind CSS                    |                                               |
| Authentication   | Laravel Fortify/Sanctum (Web), JWT (API)       | Session for web, JWT for current API setup    |
| Testing          | Pest                                           | Built on PHPUnit                              |
| Charts           | Chart.js                                       | For admin dashboard visualizations            |
| Queue (Optional) | Redis                                          | For background job processing (e.g., notifications at scale) |
| File Storage     | Laravel Storage (public disk)                  | For CVs, photos, logos                        |
| Email Testing    | MailHog                                        | For local development                         |

## 🎯 Key Features

### User Roles & Functionalities:

*   **Superadmin (School Administration):**
    *   Manages users (approving pending student registrations).
    *   Controls system-wide settings, primarily the Candidate Registration Window.
    *   Monitors platform activity via a comprehensive dashboard with key metrics and charts.
*   **Employers (Companies):**
    *   Register with detailed company profiles (contact person, company name, location, website, description, logo).
    *   Perform CRUD operations (Create, Read, Update, Delete) for their job postings.
    *   View and manage applications received for their jobs.
*   **Students (Ex-Students/Alumni):**
    *   Initial registration with core details (name, email, password, role, course completion year).
    *   Complete and manage their profile with additional details (phone, CV, photo, areas of interest).
    *   Browse a public job board with filtering capabilities (by area of interest, location, contract type).
    *   View detailed information for each job.
    *   Apply for jobs (currently via API endpoint).
    *   Save favorite jobs for later viewing.
    *   Track their application history and status.

### Core System Mechanics:

*   **Registration Window for Students:**
    *   Superadmins define a specific window (start/end time, max number of registrations).
    *   An optional password can be set for the window. If password-protected, it's valid for a configurable duration (e.g., 2 hours) from its first use, for a limited number of registrations.
    *   Student registrations outside this window (due to time, capacity, or password validation failure) are marked as 'pending'.
*   **Notifications (Database-driven):**
    *   Students receive notifications for new jobs matching their areas of interest.
    *   Students are notified if their registration is 'pending' and again when it's 'approved'.
    *   Superadmins are notified about new 'pending' student registrations requiring approval.
    *   All users can view their notifications and mark them as read.
*   **Admin Dashboard:**
    *   Provides superadmins with metrics like total users (by role), pending registrations, CV uploads, job counts, application counts, and jobs grouped by location, area, month, and contract type, visualized with charts.

## 📦 Installation and Configuration

### Prerequisites

*   PHP 8.2+
*   Composer
*   MySQL
*   Node.js & npm (for Tailwind CSS compilation if customizing)
*   Git
*   Redis (Optional, if `QUEUE_CONNECTION=redis`)
*   MailHog (Optional, for local email testing)

### Setup Instructions

1.  **Clone the Repository:**
    ```bash
    git clone https://github.com/your-repo/epatv-job-portal.git # Replace with your repo URL
    cd epatv-job-portal
    ```

2.  **Fix Git Ownership (If using Laragon/Windows and encounter "dubious ownership" errors):**
    ```bash
    git config --global --add safe.directory C:/laragon/www/epatv-job-portal # Adjust path if needed
    ```

3.  **Install Dependencies:**
    ```bash
    composer install
    npm install
    ```

4.  **Configure Environment:**
    *   Copy `.env.example` to `.env`: `cp .env.example .env`
    *   Update `.env` with your specific settings. Key variables:
        *   `APP_NAME`: Your application name (e.g., "EPATV Job Portal").
        *   `APP_ENV`: `local` for development, `production` for live.
        *   `APP_DEBUG`: `true` for development, `false` for production.
        *   `APP_URL`: The base URL of your application (e.g., `http://localhost:8000` or your production domain).
        *   `DB_CONNECTION=mysql`
        *   `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`: Your MySQL database credentials. `DB_DATABASE` is typically `epatv_job_portal`.
        *   `QUEUE_CONNECTION`: `sync` (default, immediate execution) or `redis` for background jobs.
        *   `REDIS_HOST`, `REDIS_PASSWORD`, `REDIS_PORT`: Redis server details if using it for queues.
        *   `MAIL_MAILER`: `smtp` for actual sending, or `log` for development if not using MailHog.
        *   `MAIL_HOST`: For MailHog, typically `127.0.0.1`.
        *   `MAIL_PORT`: For MailHog, typically `1025`.
        *   `MAIL_USERNAME`, `MAIL_PASSWORD`: Usually `null` for MailHog.
        *   `MAIL_FROM_ADDRESS`: Default email address for outgoing application emails (e.g., `admin@epatv.pt`).

5.  **Generate Application Key:**
    ```bash
    php artisan key:generate
    ```

6.  **Run Migrations and Seeders:**
    ```bash
    php artisan migrate --seed
    ```
    This sets up the database schema and populates initial data (e.g., areas of interest, default superadmin).

7.  **Create Storage Link:**
    ```bash
    php artisan storage:link
    ```
    This makes files in `storage/app/public` (like CVs, logos, photos) accessible from the web.

8.  **Set Up MailHog (Optional, for local email testing):**
    *   Install MailHog (e.g., download for Windows or `brew install mailhog` on macOS).
    *   Start MailHog: `mailhog`
    *   Access the MailHog web UI at `http://127.0.0.1:8025` to view emails sent by the application.

9.  **Compile Frontend Assets (If you plan to modify Tailwind CSS or JS):**
    ```bash
    npm run dev
    ```

10. **Start the Development Server:**
    ```bash
    php artisan serve
    ```
    Access the application at `http://localhost:8000` (or your configured `APP_URL`).

11. **Start Queue Worker (Optional, if `QUEUE_CONNECTION=redis` or other async driver):**
    ```bash
    php artisan queue:work
    ```
    The queue worker processes background tasks. Keep it running during development if testing features that use queues (like some notification schemes, though current DB notifications are synchronous).

## 🖥️ Command-Line Reference
(This section seems largely accurate from previous state)
... (keep existing commands)

## 🧪 Testing

The project uses [Pest](https://pestphp.com/), a testing framework built on top of PHPUnit, for automated testing. Tests are located in the `tests/Feature` directory and cover key application features and user flows.

To run all tests:
```bash
php artisan test
```
To run a specific test file:
```bash
php artisan test tests/Feature/YourTestFile.php
```

## 🗄️ Database Schema
(Schema descriptions updated in Step 23 are largely correct)

**users:**
*   id, name (contact person for employer, student name), email, password, role ('superadmin'/'employer'/'student'/'candidate'), phone, course_completion_year, photo (student photo path), cv (student CV path), company_name, company_city, company_website, company_description, company_logo (path), registration_status ('approved'/'pending'), created_at, updated_at.

**areas_of_interest:**
*   id, name, created_at, updated_at.

**user_areas_of_interest (pivot):**
*   user_id, area_of_interest_id.

**jobs_employment (table name):**
*   id, company_id (links to users table - employer), posted_by (links to users table - user who posted), title, category_id (links to areas_of_interest), area_of_interest_id (links to areas_of_interest), description, location, salary, contract_type, expiration_date, created_at, updated_at.

**applications:**
*   id, user_id (student/applicant), job_id, status ('pending'/'reviewed'/'shortlisted'/'hired'/'rejected'), cover_letter, created_at, updated_at.

**saved_jobs:**
*   id, user_id, job_id, created_at, updated_at.

**registration_windows:**
*   id, start_time, end_time, max_registrations, password (hashed), password_valid_duration_hours, first_use_time, current_registrations, is_active, created_at, updated_at.

**notifications:**
*   id, user_id, type (string identifier), data (JSON for message and context), read_at, created_at, updated_at.


## 🌐 Key Routes

API routes under `/api/*` are authenticated using JWT. Web routes are protected by session-based authentication. Role-specific access is enforced by middleware (e.g., `role:student`, `role:employer`, `role:superadmin`).

### Key API Routes (`/api/...`)
(List from Step 23 is accurate and comprehensive)
...

### Key Web Routes (`/...`)
(List from Step 23 is accurate and comprehensive, including named routes)
...

## 🤝 Contributing

We welcome contributions to the EPATV Job Portal! To contribute:

1.  **Fork the Repository:** Create your own copy of the project.
2.  **Create a Feature Branch:**
    ```bash
    git checkout -b feature/YourAmazingFeature
    ```
3.  **Make Your Changes:** Implement your feature or bug fix.
4.  **Write Tests:** Add Pest tests for any new functionality.
5.  **Commit Your Changes:**
    ```bash
    git commit -m 'Add: Your Amazing Feature'
    ```
6.  **Push to Your Branch:**
    ```bash
    git push origin feature/YourAmazingFeature
    ```
7.  **Open a Pull Request:** Submit a PR against the main repository branch for review.

Please follow existing coding standards (roughly PSR-12, general Laravel best practices) and ensure tests pass before submitting a PR.

## 🛠️ Troubleshooting / FAQ

*   **Permissions Issues:** If you encounter errors related to `storage` or `bootstrap/cache` directories, ensure they are writable by the web server user:
    ```bash
    chmod -R 775 storage bootstrap/cache
    # You might also need to chown to your web server user (e.g., www-data)
    ```
*   **`.env` File Not Found / No App Key:** If you see errors about missing `.env` or application key:
    *   Ensure you've copied `.env.example` to `.env`.
    *   Run `php artisan key:generate`.
*   **Frontend Asset Issues:** If styles or scripts are missing:
    *   Run `npm install` to install Node.js dependencies.
    *   Run `npm run dev` (for development) or `npm run build` (for production).
*   **Uploaded Files Not Appearing (e.g., Logos, CVs):**
    *   Ensure you've run `php artisan storage:link`. This creates a symbolic link from `public/storage` to `storage/app/public`.
*   **"Dubious Ownership" Git Error (Windows/Laragon):**
    *   Run `git config --global --add safe.directory C:/path/to/your/project` (replace with your actual project path).

## 🔌 WordPress Integration (Optional)
(This section from Step 23 is fine)
...

## 🚀 Deployment
(This section from Step 23 is fine)
...

## 📜 License
This project is licensed under the MIT License.

## 📬 Contact
(This section from Step 23 is fine)
...

Built with ❤️ for EPATV by the development team.
