# JCS - Judiciary Candidacy System or Digital Case Management and Referral System (DCMRS) for District Courts

A comprehensive system for managing the entire lifecycle of judicial candidate applications, from submission to final recommendation.

## System Objectives

- **Digitize Application Processes:** Build a secure online portal for candidates to submit applications and supporting documents.
- **Streamline Multi-Role Workflows:** Allow each user role to seamlessly review, annotate, forward, and approve applications within the system.
- **Enable Collaborative Committee Reviews:** Provide JTC members with an online workspace to review applications, comment, and vote with notification support.
- **Automate Recommendation Generation:** Develop a rules engine that aggregates votes and generates automated recommendations.
- **Provide Real-Time Reporting and Auditing:** Implement dashboards for key performance indicators and ensure full traceability with immutable audit logs.
- **Ensure System Security and Performance:** Secure the system using modern encryption, authentication (Laravel Sanctum), and ensure system responsiveness for up to 5,000 concurrent users.

## Functional Requirements

1.  **User Management:**
    -   Roles: Candidate, District Court Clerk, Training Officer, JTC Member, Chairperson, System Administrator.
    -   Functions: User registration, login, password reset (Laravel Sanctum for API auth), Role-based access control (middleware).
2.  **Application Intake Module:**
    -   Document upload (PDF, JPEG) with validation.
    -   Form validation and automated acknowledgment emails.
    -   Laravel file storage structure for managing uploads.
3.  **Review Module:**
    -   Application queue management per user role.
    -   Status tracking (Pending, Under Review, Awaiting Additional Info, Forwarded, Completed).
    -   Annotation tools for adding comments and requesting additional documents.
    -   Escalation path (forwarding to the next role).
4.  **Committee Module:**
    -   View complete application packet with downloadable documents.
    -   Collaborative commenting.
    -   Voting interface with threshold logic.
    -   Automated meeting minutes capture.
5.  **Recommendation Engine:**
    -   Aggregate committee votes using customizable rules (weighting, majority, etc.).
    -   Generate system-based recommendation reports.
    -   Chairperson decision interface to approve, reject, or request revisions.
6.  **Notification System:**
    -   Real-time notifications (email, dashboard alerts) for new submissions, required actions, and final decisions.
7.  **Reporting & Dashboards:**
    -   KPI tracking: Average review time, backlog size, workload per officer, decision trends.
    -   Custom report builder.
    -   Role-specific dashboards.
8.  **Audit & Access Logs:**
    -   Track all actions (viewed, commented, approved, forwarded).
    -   Immutable logs for security and accountability.
9.  **Administrative Tools:**
    -   Manage system users and roles.
    -   Configuration settings (voting rules, document size limits, etc.).
    -   System maintenance and data backup options.

## Requirements

Before you begin, ensure you have met the following requirements:

*   PHP >= 8.2
*   Composer
*   Node.js & npm (or Yarn)
*   MySQL Database
*   Git

## Installation

Follow these steps to get the project up and running on your local machine:

1.  **Clone the repository:**
    ```bash
    git clone <repository_url>
    cd jcs
    ```
    *(Replace `<repository_url>` with the actual repository URL)*

2.  **Install Composer dependencies:**
    ```bash
    composer install
    ```

3.  **Create a copy of the environment file:**
    ```bash
    cp .env.example .env
    ```

4.  **Generate an application key:**
    ```bash
    php artisan key:generate
    ```

5.  **Configure your database:**
    Open the `.env` file and update the database connection details:
    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database_name
    DB_USERNAME=your_database_user
    DB_PASSWORD=your_database_password
    ```
    *(Replace `your_database_name`, `your_database_user`, and `your_database_password` with your actual database credentials)*

6.  **Run database migrations and seeders:**
    ```bash
    php artisan migrate:fresh --seed
    ```
    *This command will create all necessary tables and populate them with initial data (including roles and users).*

7.  **Install Node.js dependencies:**
    ```bash
    npm install
    # OR
    yarn install
    ```

8.  **Compile assets:**
    ```bash
    npm run dev
    # OR
    yarn dev
    ```

9.  **Start the development server:**
    ```bash
    php artisan serve
    ```

10. **Access the application:**
    Open your web browser and visit `http://127.0.0.1:8000`.

    **Default User Credentials (from seeding):**
    *   **Admin:** `admin@example.com` / `root`
    *   **Chairperson:** `chairperson@example.com` / `root`
    *   **JTC Member:** `jtc1@example.com` / `root` (and jtc2, jtc3)
    *   **Training Officer:** `officer1@example.com` / `root` (and officer2)
    *   **District Court Clerk:** `clerk1@example.com` / `root` (and clerk2)
    *   **Candidate:** `candidate1@example.com` / `root` (and candidate2-5)

## Tech Stack

-   **Backend:** Laravel
-   **Frontend:** Livewire
-   **Database:** MySQL
-   **Authentication:** Laravel Sanctum
-   **Queue Management:** Laravel Queues with Redis

---

Developed by [TechLink360](https://www.techlink360.net)
