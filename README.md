EPATV Job Portal
Overview
The EPATV Job Portal is a web-based platform developed for Escola Profissional Amar Terra Verde (EPATV) to connect ex-students (alumni) with job opportunities posted by companies. The system supports three user types—superadmin (school administration), admins (companies), and normal users (ex-students)—with role-based access control. Key features include a temporary registration window for ex-students, job postings with filtering, application management, automated notifications, and an admin dashboard with metrics.
The backend is built with Laravel 11, using MySQL for the database, Sanctum or JWT for authentication, and Pest for testing. The frontend uses Laravel Blade templates with Tailwind CSS for styling, with optional WordPress integration (e.g., via Elementor). Notifications are handled via Laravel’s notification system, supporting email and in-app alerts, with MailHog for local email testing during development.
Features
User Types and Roles

Superadmin (School Administration):
Manages users, job postings, and registration windows.
Approves registrations outside the defined time window.
Accesses a dashboard with metrics (e.g., total users, jobs, applications).

Admins (Companies):
Register with company details (name, city, website, logo, etc.).
Create and manage job postings.
View and contact applicants.

Normal Users (Ex-Students):
Register with personal details (name, email, phone, CV, photo, course completion year, areas of interest).
Browse and filter job listings, save jobs, and apply to opportunities.
Receive notifications for new jobs in their areas of interest.

Core Functionalities

Registration Window:
Ex-students can register within a time window (e.g., 23/01/2025 10:00 to 12:00) set by the superadmin, with a default limit of 30 registrations.
Alternative password-based registration allows 30 registrations within 2 hours from first use.
Out-of-window registrations are marked as pending, notifying the superadmin and student.

Job Postings:
Companies create job listings with title, category, description, location, salary, contract type (full-time/part-time), and expiration date.
Public job board with filters for category, location, and contract type.

Applications:
Students apply to jobs with pre-filled data (editable) and optional messages.
Companies view applications with candidate details and CVs.
Student dashboard shows application history and status.

Notifications:
Students receive notifications for new jobs in their interest areas.
Superadmin and students are notified for out-of-window registrations.

Admin Dashboard:
Displays metrics: total registrations, CV uploads, job postings by location/category/month, and contract types.
Includes Chart.js visualizations (bar, pie, line charts).
Manages registration windows (toggle on/off, set dates, max registrations, password).

Security:
Role-based access control using Laravel middleware.
JWT or Sanctum for API authentication.
Secure file uploads (CVs, photos, logos; max 2MB).

Tech Stack

Backend: Laravel 11 (PHP 8.1+)
Database: MySQL
Frontend: Laravel Blade with Tailwind CSS (optional WordPress integration with Elementor)
Authentication: Laravel Sanctum or JWT
Testing: Pest (built on PHPUnit)
Charts: Chart.js
Queue: Redis for notification processing
File Storage: Laravel Storage (public disk for uploads)
Email Testing: MailHog (local email capture)

Installation
Prerequisites

PHP 8.1+
Composer
MySQL
Node.js and npm (for Tailwind CSS)
Redis (optional, for queues)
Git
MailHog (optional, for email testing)

Setup Instructions

Clone the Repository:
git clone https://github.com/your-repo/epatv-job-portal.git
cd epatv-job-portal

Install Dependencies:
composer install
npm install

Configure Environment:

Copy .env.example to .env:cp .env.example .env

Update .env with database, mail, and Redis settings:DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=epatv_job_portal
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS=admin@epatv.pt

Generate Application Key:
php artisan key:generate

Run Migrations and Seeders:
php artisan migrate --seed

Set Up MailHog (Optional):

Install MailHog (e.g., via Homebrew on macOS: brew install mailhog).
Start MailHog:mailhog

Access the MailHog UI at http://127.0.0.1:8025 to view captured emails.
Ensure .env mail settings point to MailHog (MAIL_HOST=127.0.0.1, MAIL_PORT=1025).

Compile Frontend Assets:
npm run dev

Start the Development Server:
php artisan serve

Access the application at http://localhost:8000.

Set Up Queues (Optional):

Ensure Redis is installed and running.
Start the queue worker:php artisan queue:work

Command-Line Reference

php artisan serve: Starts the Laravel development server at http://localhost:8000.
npm run dev: Compiles and serves frontend assets (e.g., Tailwind CSS) with hot reloading for development.
php artisan queue:work: Processes background jobs (e.g., notifications) using Redis. Requires Redis to be running.
php artisan migrate --seed: Runs database migrations to create tables and seeds initial data (e.g., areas of interest, superadmin user).
php artisan test: Executes Pest tests to verify functionality (e.g., registration, job creation).
npm run build: Compiles frontend assets for production, creating optimized files.
php artisan key:generate: Generates a unique application key for encryption, stored in .env.
mailhog: Starts MailHog to capture outgoing emails for testing. Access the UI at http://127.0.0.1:8025.

Database Schema

users:
id, name, email, password, role (superadmin/admin/student), phone, course_completion_year, photo, cv, company_name, company_city, company_website, company_description, company_logo, registration_status (approved/pending), created_at, updated_at.

areas_of_interest:
id, name (e.g., Programação Informática), created_at, updated_at.

user_areas_of_interest (pivot):
user_id, area_id.

jobs:
id, company_id, title, category_id, description, location, salary, contract_type (full-time/part-time), expiration_date, created_at, updated_at.

job_applications:
id, user_id, job_id, name, email, phone, course_completion_year, cv, message, status (pending/accepted/rejected), created_at, updated_at.

saved_jobs:
id, user_id, job_id, created_at, updated_at.

registration_windows:
id, start_time, end_time, max_registrations, password, first_use_time, current_registrations, is_active, created_at, updated_at.

notifications:
id, user_id, type, data (JSON), read_at, created_at, updated_at.

Key Routes

Public:
GET /jobs: List all jobs with filters.
GET /jobs/{id}: View job details.
POST /register: Register a user (student/company).
POST /login: Authenticate user.

Student (middleware: auth:api,role:student):
GET /student/profile: View profile.
PUT /student/profile: Update profile.
POST /student/jobs/{id}/apply: Apply to a job.
GET /student/applications: View applications.
POST /student/jobs/{id}/save: Save a job.

Company (middleware: auth:api,role:admin):
POST /company/jobs: Create a job.
GET /company/jobs: View own jobs.
GET /company/applications: View applications.

Superadmin (middleware: auth:api,role:superadmin):
GET /admin/dashboard: View dashboard metrics.
POST /admin/registration-window: Update registration window.
GET /admin/users: Manage users.
POST /admin/users/{id}/approve: Approve pending registrations.

Testing
The project uses Pest for automated testing. Key test cases:

User registration (within/outside window, password-based).
Job creation and filtering.
Job applications (student submission, company viewing).
Notifications (job postings, pending registrations).
Admin dashboard metrics.

Run tests:
php artisan test

Deployment

Deploy to a server (e.g., Laravel Forge, Heroku, or a VPS).
Configure Nginx/Apache, PHP, MySQL, and Redis.
Set up .env with production settings (e.g., APP_ENV=production, real mail server settings).
Run migrations:php artisan migrate --seed

Compile assets:npm run build

Start queue workers:php artisan queue:work

WordPress Integration (Optional)
If WordPress is required (e.g., for Elementor-based UI):

Use Laravel as a REST API backend (/api/\* routes).
Develop a WordPress plugin to consume API endpoints.
Design pages with Elementor, replicating the job board, dashboards, and forms.
Alternatively, use Laravel Blade with Tailwind CSS for a fully Laravel-based solution.

Contributing

Fork the repository and create a feature branch.
Follow Laravel coding standards (PSR-12).
Write Pest tests for new features.
Submit pull requests with clear descriptions.

License
This project is licensed under the MIT License.
Contact
For support or inquiries, contact:

EPATV Administration: admin@epatv.pt
Developers: smpsandro1239@gmail.pt
