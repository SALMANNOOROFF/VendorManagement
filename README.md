# Vendor Management System (VMS) Portal 🛡️🏢

An enterprise-grade, role-based, and fully dynamic **Vendor Management, Registration, and Entry Request Approval System** built using modern web paradigms. 

The application is engineered to eliminate full-page browser reloads by leveraging high-speed **jQuery AJAX pipelines**, offering a seamless, interactive, and fluid user experience. It features vibrant HSL color tailoring, smooth CSS micro-animations, real-time notification feeds, and highly tactile UI validations.

---

## 🚀 Key Features

### 1. Multi-Step validated Vendor Registration
* **Tactile 2-Step wizard**: Grouped into Account, Company, Address, Contact, and Document uploads.
* **Smart Validation Redirection**: If you click submit with incomplete data on hidden steps, the form automatically transitions back to that step and scrolls to focus the field.
* **Giggle Shake & Glow effects**: Invalid fields wiggle dynamically using CSS keyframes and glow with a soft ambient red shadow for instant focus.
* **Password Instruction**: Displays dynamic requirements for password inputs directly inside the credentials interface.

### 2. Multi-Role Permission Matrices
* **Super Admin**: Custom form builders, toggle mandatory/optional fields globally, audit logging, and global configuration management.
* **Approver**: Multi-panel history dashboards, view and inspect resolved registrations/entry requests, and approve/reject entries with **mandatory remarks**.
* **Vendor**: Self-service profiles, document storage, workforce roster directory, and entry requests.
* **Worker**: Secure dashboard access to view active entry clearances.

### 3. AJAX-Powered Notification Pipeline
* **Dynamic Top Bell Badge**: Real-time count of unread system updates with responsive bell rotation hover effects.
* **Slide-Out Details Modal**: Read and mark alerts as read on the fly without page reloads.
* **Dashboard Feeds**: A designated feeds module rendering historical decisions, dates, and direct links to resolved documents.

### 4. High-End UI Layout & Centered Brand Logo
* **Perfect Balance Alignment**: Desktop navigation features absolute brand logo centering, with page options aligned to the left, and profile details on the right.
* **Tactile Page Tabs**: Compact circular tabs displaying only icons that gracefully expand to show text on mouse hover, preventing adjacent tabs from shifting.
* **Interactive Profile Modal**: Fast details-heavy click popup showing exact registration timestamps, emails, statuses, and approver details.

---

## 🛠️ Technology Stack

* **Core**: PHP 8.x (Structured OOP Architecture)
* **Database**: MySQL / MariaDB (Relational Integrity with Foreign Key Constraints & Audit Trails)
* **Frontend**: HTML5, Vanilla CSS3 (Custom Design Token variables), Bootstrap 5.x, jQuery (AJAX API bindings), and Bootstrap Icons.
* **Security & Best Practices**:
  * PDO prepared statements to block SQL Injection.
  * Password hashing (`password_hash()`) to protect user credentials.
  * Session-based role validation middleware.

---

## 📂 Project Directory Structure

```
VendorM/
│
├── api/                    # Core backend AJAX controllers
│   ├── approve_entry.php   # Handles approver actions & triggers notifications
│   └── notifications.php   # AJAX endpoints to mark notifications read
│
├── assets/                 # Global UI style sheets and JS
│   ├── css/
│   │   ├── bootstrap.min.css
│   │   └── custom.css      # Core premium design tokens & micro-animations
│   └── js/
│       ├── jquery.min.js
│       └── main.js         # AJAX loaders, toasts, modals, and shake validations
│
├── classes/                # Object-oriented core business models
│   ├── Database.php        # Singleton PDO connection wrapper
│   ├── User.php            # User authentication, profiles, and timestamps
│   ├── Vendor.php          # Company models & workflow state managers
│   ├── Worker.php          # Roster listings and counters
│   ├── EntryRequest.php    # Clearances & review history loaders
│   ├── Notification.php    # Real-time alert records and unread counts
│   └── FormConfig.php      # Dynamic DB fields configurator
│
├── includes/               # Global shared page modules
│   ├── header.php          # Centered navbar, hover links, and modals
│   └── footer.php          # Dynamic scripts and AJAX action triggers
│
├── public/                 # Client entry point pages
│   ├── index.php           # Welcome hub
│   ├── login.php           # Secure entry
│   ├── vendor/             # Vendor dashboards, rosters, & profile wizards
│   ├── approver/           # Approver queues, details, and tabs sheets
│   └── admin/              # Super Admin controls
│
├── sql/                    # Databases blueprints
│   └── seed_data.sql       # Baseline schema migrations and mock accounts
│
└── README.md               # Project documentation
```

---

## ⚙️ Local Installation & Setup

Follow these simple steps to run the VMS Portal locally on your machine using **XAMPP**:

### Prerequisites
* Install [XAMPP](https://www.apachefriends.org/) with **PHP 8.x** and **MySQL**.

### Step-by-Step Guide

1. **Clone or Copy the Project**:
   Copy the `VendorM` project folder and paste it into your local Apache root directory:
   ```bash
   C:\xampp\htdocs\VendorM
   ```

2. **Start XAMPP Control Panel**:
   * Open the XAMPP Control Panel and start the **Apache** and **MySQL** services.

3. **Import Database Schema**:
   * Open your web browser and navigate to `http://localhost/phpmyadmin/`.
   * Create a new database named **`vms_db`**.
   * Click on the **Import** tab.
   * Choose the SQL database file located at:
     ```bash
     C:\xampp\htdocs\VendorM\sql\seed_data.sql
     ```
   * Click **Go** to import all structured tables, schemas, relations, and initial admin credentials.

4. **Verify Database Configuration**:
   * If your local MySQL setup requires a specific password, update your configuration credentials in:
     `c:\xampp\htdocs\VendorM\config\database.php`

5. **Run the Application**:
   * Open your browser and navigate to the application entrance point:
     ```bash
     http://localhost/VendorM/public/
     ```

---

## 🔐 Default Login Accounts

For local demonstration and evaluation, you can log in using these preset credentials:

| Role | Username | Password |
| :--- | :--- | :--- |
| **Super Admin** | `admin` | `admin123` |
| **Approver** | `approver` | `approver123` |
| **Vendor** | `vendor` | `vendor123` |


Enjoy building with VMS Portal! 🛡️
