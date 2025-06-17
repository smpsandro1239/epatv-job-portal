EPATV Job Portal 🚀

The EPATV Job Portal is a web-based platform designed for Escola Profissional Amar Terra Verde (EPATV) to connect ex-students (alumni) with job opportunities posted by companies. Built with Laravel 12, it offers a robust backend with MySQL, role-based access control, and automated notifications. The frontend uses Laravel Blade with Tailwind CSS. The system supports three primary user types: superadmin (school administration), employers (companies), and students (ex-students/alumni), with features like temporary registration windows, job filtering, and an analytics dashboard.
🎯 Features
👥 User Types and Roles

**Superadmin (School Administration):**
*   Manages users (including approving pending registrations), overall system settings like registration windows, and has oversight of platform activity via the dashboard. Job postings themselves are directly managed by companies/employers.
*   Approves registrations outside the defined time window.
*   Accesses a dashboard with metrics (e.g., total users, jobs, applications).

**Employers (Companies/Admins):**
*   Register with company details (contact person name, company name, city, website, logo, etc.).
*   Create and manage job postings (CRUD).
*   View and manage applications received for their jobs.

**Students (Ex-Students/Alumni):**
*   Initial registration with core details (name, email, password, role, and course completion year if applicable). Additional details like phone, CV, photo, and areas of interest are completed via the student's profile management section after registration.
*   Browse, filter, and apply to job listings.
*   Save jobs for later viewing.
*   Receive notifications for new jobs in their interest areas.
*   Track application history and status.

⚙️ Core Functionalities

**Registration Window:**
*   Students register within a time window (e.g., start/end datetime) set by the superadmin.
*   Window can have a maximum number of registrations (0 for unlimited).
*   Optional password protection for the window. Password-based registration allows a set number of registrations within a configurable duration (e.g., 2 hours) from the first time the password is used.
*   Out-of-window (time, capacity, or password validation failure) registrations are marked as 'pending', notifying the superadmin and the student.

**Job Postings:**
*   Companies create listings with title, area of interest, description, location, salary, contract type, and expiration date.
*   Public job board with filters for area of interest, location, and contract type.
*   Detailed view for each job.

**Applications:**
*   Students apply via an API endpoint, potentially with a cover letter.
*   Companies view applications with candidate details (name, email, phone, CV link).
*   Students can view their application history and status.

**Notifications (Database-driven):**
*   Students receive notifications for new jobs posted in their selected areas of interest.
*   Superadmins and students are notified when a student's registration is 'pending'.
*   Students are notified when their pending registration is approved.
*   Users can view their notifications and mark them as read (individually or all).

**Admin Dashboard:**
*   Displays key metrics: total users (by role), pending registrations, CV uploads, total jobs, total applications.
*   Includes charts (Chart.js) for: jobs by location, jobs by area of interest, jobs by month, and jobs by contract type.
*   Superadmin manages registration window settings.

**Security:**
*   Role-based access control via Laravel middleware.
*   Laravel Sanctum for API authentication (if used, default is session/JWT for API in this project).
*   Secure file uploads (CVs, photos, logos; max 2MB) with validation.

🛠️ Tech Stack
(This section seems largely accurate, no changes needed unless specific versions need update)
Component
Technology

Backend
Laravel 12 (PHP 8.2+)

Database
MySQL

Frontend
Laravel Blade, Tailwind CSS

Authentication
Laravel Fortify/Sanctum (Web), JWT/Sanctum (API)

Testing
Pest (PHPUnit-based)

Charts
Chart.js

Queue
Redis

File Storage
Laravel Storage (public disk)

Email Testing
MailHog

📦 Installation
(This section seems largely accurate, no changes needed)

🖥️ Command-Line Reference
(This section seems largely accurate, no changes needed)

🗄️ Database Schema

**users:**
*   id, name (contact person for employer/admin, student name), email, password, role ('superadmin'/'employer'/'student'/'admin'/'candidate'), phone, course_completion_year, photo (student photo path), cv (student CV path), company_name, company_city, company_website, company_description, company_logo (path), registration_status ('approved'/'pending'), created_at, updated_at.

**areas_of_interest:**
*   id, name (e.g., Programação Informática), created_at, updated_at.

**user_areas_of_interest (pivot):**
*   user_id, area_of_interest_id.

**jobs_employment (table name):**
*   id, company_id (links to users table - employer), posted_by (links to users table - user who posted), title, category_id (links to areas_of_interest), area_of_interest_id (links to areas_of_interest), description, location, salary, contract_type, expiration_date, created_at, updated_at.

**applications (table name `job_applications` in old schema, now `applications`):**
*   id, user_id (student/applicant), job_id, status ('pending'/'reviewed'/'shortlisted'/'hired'/'rejected'), cover_letter, created_at, updated_at. (Removed redundant user details like name, email as they are on the user model).

**saved_jobs:**
*   id, user_id, job_id, created_at, updated_at.

**registration_windows:**
*   id, start_time, end_time, max_registrations, password (hashed), password_valid_duration_hours, first_use_time, current_registrations, is_active, created_at, updated_at.

**notifications:**
*   id, user_id, type (string identifier), data (JSON for message and context), read_at, created_at, updated_at.

🌐 Key Routes

### Key API Routes (`/api/...`)

**Authentication (Public)**
- `POST /register`: Register a new user (student or employer).
- `POST /login`: Authenticate user and receive JWT token.
- `POST /logout`: Invalidate user's session (requires auth token).
- `GET /email/verify/{id}/{hash}`: Verify email address.
- `POST /forgot-password`: Request password reset link.
- `POST /reset-password`: Reset password with token.

**Student Routes (Requires Authentication & 'student' role)**
- `GET /student/profile`: View own student profile.
- `PUT /student/profile`: Update own student profile (supports file uploads for photo/CV).
- `POST /student/jobs/{job}/save`: Toggle save/unsave a job.
- `GET /student/applications`: List own job applications.

**Job Application (Requires Authentication - typically student)**
- `POST /apply`: Submit a job application (uses `ApplicationController`).

**Employer Routes (Requires Authentication & 'employer' role)**
- `GET /employer/profile`: View own company profile.
- `PUT /employer/profile`: Update own company profile (supports file upload for logo).
- `GET /employer/jobs`: List jobs posted by the employer.
- `POST /employer/jobs`: Create a new job posting.
- `GET /employer/jobs/{job}`: View a specific job posting owned by the employer.
- `PUT /employer/jobs/{job}`: Update an owned job posting.
- `DELETE /employer/jobs/{job}`: Delete an owned job posting.
- `GET /employer/applications`: List applications received for the employer's jobs.

**Superadmin Routes (Requires Authentication & 'superadmin' role)**
- `GET /admin/dashboard`: View comprehensive dashboard metrics.
- `GET /admin/users`: List users with filters (role, status).
- `POST /admin/users/{user}/approve`: Approve a pending user registration.
- `GET /admin/registration-window`: Get current registration window settings.
- `PUT /admin/registration-window`: Update registration window settings.

**Authenticated User Routes (Any role)**
- `GET /user`: Get current authenticated user details (typically from auth token).
- `GET /notifications`: List user's database notifications.
- `POST /notifications/{notification}/read`: Mark a specific notification as read.
- `POST /notifications/mark-all-read`: Mark all user's notifications as read.

### Key Web Routes (`/...`)

**Public Routes**
- `GET /`: Welcome page.
- `GET /login`: Show login page.
- `POST /login`: Process login (`login.store`).
- `GET /register`: Show registration page.
- `POST /register`: Process registration (`register.store`).
- `POST /logout`: Process logout.
- `GET /jobs`: Public job listing page with filters.
- `GET /jobs/{job}`: Public job details page.

**Student Routes (Requires Authentication & 'student' role)**
- `GET /student/profile`: View student profile page (`student.profile.show`).
- `GET /student/profile/edit`: Show edit student profile form (`student.profile.edit`).
- `PUT /student/profile`: Update student profile (`student.profile.update`).
- `GET /student/applications`: View student's list of applications (`student.applications.index`).

**Employer Routes (Requires Authentication & 'employer' role)**
- `GET /employer/profile`: View employer profile page (`employer.profile.show`).
- `GET /employer/profile/edit`: Show edit employer profile form (`employer.profile.edit`).
- `PUT /employer/profile`: Update employer profile (`employer.profile.update`).
- `Route::resource('/employer/jobs', EmployerJobController::class)` (Handles index, create, store, show, edit, update, destroy for employer's jobs).
- `GET /employer/applications`: View applications for employer's jobs (`employer.applications.index`).

**Superadmin Routes (Requires Authentication & 'superadmin' role)**
- `GET /admin/dashboard`: Show superadmin dashboard (`admin.dashboard`).
- `GET /admin/users`: Show user management page (`admin.users.index`).
- `POST /admin/users/{user}/approve`: Approve a pending user (`admin.users.approve`).
- `GET /admin/registration-window`: Show edit form for registration window (`admin.regwindow.edit`).
- `PUT /admin/registration-window`: Update registration window settings (`admin.regwindow.update`).

**Authenticated User Routes (Any role)**
- `GET /dashboard`: Generic dashboard (often a redirect target or simple landing page after login).
- `GET /notifications`: Show user's notifications page (`notifications.index`).
- `POST /notifications/{notification}/read`: Mark a specific notification as read (`notifications.read`).
- `POST /notifications/mark-all-read`: Mark all user's notifications as read (`notifications.markallasread`).

🧪 Testing
(This section seems largely accurate, no changes needed)

🚀 Deployment
(This section seems largely accurate, no changes needed)

🔌 WordPress Integration (Optional)
(This section seems largely accurate, no changes needed)

🤝 Contributing
(This section seems largely accurate, no changes needed)

📜 License
This project is licensed under the MIT License.
📬 Contact

EPATV Administration: admin@epatv.pt
Developers: smpsandro1239@gmail.pt

Built with ❤️ for EPATV by the development team.
