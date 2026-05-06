# Vendor Management System — Complete Architecture
**Tech Stack:** PHP 8.x | MySQL 8.x | Bootstrap 5 | jQuery  
**Theme:** Navy Blue `#0A1628` | Cyan `#00BCD4` | White `#FFFFFF`

---

## TABLE OF CONTENTS

1. [System Overview](#1-system-overview)
2. [Roles & Access Control](#2-roles--access-control)
3. [Database Architecture](#3-database-architecture)
   - 3.1 roles
   - 3.2 users
   - 3.3 company_types
   - 3.4 company_subtypes
   - 3.5 vendors
   - 3.6 workers
   - 3.7 form_fields_config
   - 3.8 approval_workflow
   - 3.9 audit_logs
4. [Table Relationships (ERD Summary)](#4-table-relationships-erd-summary)
5. [Important SQL Queries](#5-important-sql-queries)
6. [Folder Structure](#6-folder-structure)
7. [PHP Classes](#7-php-classes)
8. [Role-Based Access — Middleware](#8-role-based-access--middleware)
9. [UI Design Guide](#9-ui-design-guide)
10. [Page-by-Page UI & Code](#10-page-by-page-ui--code)
11. [Business Flow (Step by Step)](#11-business-flow-step-by-step)
12. [Security Checklist](#12-security-checklist)
13. [Deployment Guide](#13-deployment-guide)

---

## 1. SYSTEM OVERVIEW

```
[Vendor]  →  Self Register  →  [Approver Reviews]  →  Approved/Rejected
                                                              ↓
                                                    [Vendor adds Workers]
                                                              ↓
                                              [Super Admin sees everything]
```

**4 Main Roles:**
| Role | Who | What they do |
|---|---|---|
| `super_admin` | System owner | Full control, manages users/roles/config |
| `approver` | Internal staff | Reviews & approves vendor registrations |
| `vendor` | Company/Supplier | Self-registers, adds workers |
| `worker` | Vendor employee | Basic profile only |

---

## 2. ROLES & ACCESS CONTROL

### Access Matrix

| Page/Feature | super_admin | approver | vendor | worker |
|---|:---:|:---:|:---:|:---:|
| Admin Dashboard | ✅ | ❌ | ❌ | ❌ |
| Manage Roles | ✅ | ❌ | ❌ | ❌ |
| Manage Users | ✅ | ❌ | ❌ | ❌ |
| Form Config (toggle fields) | ✅ | ❌ | ❌ | ❌ |
| View All Vendors | ✅ | ✅ | ❌ | ❌ |
| Approve/Reject Vendor | ✅ | ✅ | ❌ | ❌ |
| Vendor Dashboard | ❌ | ❌ | ✅ | ❌ |
| Add/Edit Workers | ❌ | ❌ | ✅ | ❌ |
| View Own Profile | ❌ | ❌ | ✅ | ✅ |
| Audit Logs | ✅ | ❌ | ❌ | ❌ |

---

## 3. DATABASE ARCHITECTURE

### 3.1 — `roles` Table

Defines all system roles and their permissions.

```sql
CREATE TABLE roles (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    role_name       VARCHAR(50) UNIQUE NOT NULL,   -- 'super_admin', 'approver', 'vendor', 'worker'
    role_display    VARCHAR(100) NOT NULL,          -- 'Super Administrator'
    description     TEXT,
    permissions     JSON,                           -- {"can_approve": true, "can_add_workers": false}
    is_active       BOOLEAN DEFAULT TRUE,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**Seed Data:**
```sql
INSERT INTO roles (role_name, role_display, description, permissions) VALUES
('super_admin', 'Super Administrator', 'Full system access', '{"all": true}'),
('approver',    'Vendor Approver',     'Reviews vendor registrations', '{"can_approve_vendors": true, "view_vendors": true}'),
('vendor',      'Vendor',              'Registered company/supplier',  '{"can_add_workers": true, "view_own_profile": true}'),
('worker',      'Worker',              'Vendor employee',              '{"view_own_profile": true}');
```

---

### 3.2 — `users` Table

Main authentication table. Every person in the system has one record here.

```sql
CREATE TABLE users (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    username        VARCHAR(100) UNIQUE NOT NULL,
    email           VARCHAR(150) UNIQUE NOT NULL,
    password_hash   VARCHAR(255) NOT NULL,
    role_id         INT NOT NULL,
    status          ENUM('pending','active','suspended','rejected') DEFAULT 'pending',
    created_by      INT NULL,        -- NULL if self-registered
    approved_by     INT NULL,        -- Approver user_id
    approved_at     TIMESTAMP NULL,
    last_login      TIMESTAMP NULL,
    login_attempts  INT DEFAULT 0,
    locked_until    TIMESTAMP NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (role_id)     REFERENCES roles(id),
    FOREIGN KEY (created_by)  REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id)
);
```

---

### 3.3 — `company_types` Table

Top-level company category (e.g. Manufacturing, Services).

```sql
CREATE TABLE company_types (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    type_name   VARCHAR(150) UNIQUE NOT NULL,    -- 'Manufacturing', 'IT Services', 'Construction'
    description TEXT,
    is_active   BOOLEAN DEFAULT TRUE,
    sort_order  INT DEFAULT 0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**Seed Data:**
```sql
INSERT INTO company_types (type_name, description, sort_order) VALUES
('Manufacturing',        'Companies that produce goods',                    1),
('IT & Technology',      'Software, hardware, IT services',                 2),
('Construction',         'Building, civil engineering, infrastructure',      3),
('Trading & Distribution', 'Import/export, wholesale, retail distribution', 4),
('Professional Services','Consulting, legal, accounting, HR',               5),
('Healthcare & Pharma',  'Hospitals, clinics, pharmaceutical companies',    6),
('Logistics & Transport','Freight, courier, warehousing',                   7),
('Food & Beverage',      'Food processing, restaurants, catering',          8),
('Energy & Utilities',   'Oil & gas, electricity, water, renewable energy', 9),
('Other',                'Any other business type',                         10);
```

---

### 3.4 — `company_subtypes` Table

Sub-category linked to parent company_type.

```sql
CREATE TABLE company_subtypes (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    company_type_id INT NOT NULL,                   -- FK to company_types
    subtype_name    VARCHAR(150) NOT NULL,
    description     TEXT,
    is_active       BOOLEAN DEFAULT TRUE,
    sort_order      INT DEFAULT 0,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (company_type_id) REFERENCES company_types(id) ON DELETE CASCADE,
    UNIQUE KEY unique_subtype (company_type_id, subtype_name)
);
```

**Seed Data (Examples):**
```sql
-- Manufacturing subtypes
INSERT INTO company_subtypes (company_type_id, subtype_name, sort_order) VALUES
(1, 'Textile & Garments',        1),
(1, 'Chemical Manufacturing',    2),
(1, 'Food Processing',           3),
(1, 'Plastic & Rubber Products', 4),
(1, 'Steel & Metal Works',       5),
(1, 'Electronics Manufacturing', 6),

-- IT & Technology subtypes
(2, 'Software Development',  1),
(2, 'IT Consulting',         2),
(2, 'Cybersecurity',         3),
(2, 'Cloud Services',        4),
(2, 'Hardware & Networking', 5),

-- Construction subtypes
(3, 'Residential Construction', 1),
(3, 'Commercial Construction',  2),
(3, 'Road & Infrastructure',    3),
(3, 'Interior Design',          4),
(3, 'Electrical Contracting',   5),
(3, 'Plumbing & HVAC',          6),

-- Trading subtypes
(4, 'Import/Export',       1),
(4, 'Wholesale',           2),
(4, 'Retail Distribution', 3),

-- Professional Services subtypes
(5, 'Management Consulting', 1),
(5, 'Legal Services',        2),
(5, 'Accounting & Audit',    3),
(5, 'HR & Recruitment',      4);
```

---

### 3.5 — `vendors` Table

Detailed vendor/company profile. Linked to `users` (1-to-1).

```sql
CREATE TABLE vendors (
    id                      INT PRIMARY KEY AUTO_INCREMENT,
    user_id                 INT UNIQUE NOT NULL,

    -- Company Identity
    company_name            VARCHAR(200) NOT NULL,
    company_registration_no VARCHAR(100),
    ntn_number              VARCHAR(50),             -- National Tax Number
    strn_number             VARCHAR(50),             -- Sales Tax Registration
    company_type_id         INT NOT NULL,            -- FK → company_types
    company_subtype_id      INT,                     -- FK → company_subtypes
    years_in_business       INT,
    number_of_employees     INT,
    annual_revenue          DECIMAL(15,2),

    -- Primary Contact
    primary_contact_name    VARCHAR(150) NOT NULL,
    primary_contact_phone   VARCHAR(20) NOT NULL,
    primary_contact_email   VARCHAR(150) NOT NULL,
    primary_contact_cnic    VARCHAR(20),

    -- Secondary Contact
    secondary_contact_name  VARCHAR(150),
    secondary_contact_phone VARCHAR(20),
    secondary_contact_email VARCHAR(150),

    -- Address
    address_line1           VARCHAR(255) NOT NULL,
    address_line2           VARCHAR(255),
    city                    VARCHAR(100) NOT NULL,
    state_province          VARCHAR(100),
    postal_code             VARCHAR(20),
    country                 VARCHAR(100) DEFAULT 'Pakistan',

    -- Banking
    bank_name               VARCHAR(150),
    bank_account_title      VARCHAR(200),
    bank_account_no         VARCHAR(50),
    bank_branch             VARCHAR(150),
    iban                    VARCHAR(50),

    -- Certifications & Details
    certifications          JSON,       -- ['ISO 9001', 'PSEB', 'SECP']
    business_description    TEXT,

    -- Documents (file paths)
    registration_certificate VARCHAR(255),
    ntn_certificate          VARCHAR(255),
    tax_certificate          VARCHAR(255),
    bank_statement           VARCHAR(255),
    company_profile_doc      VARCHAR(255),
    other_documents          JSON,       -- [{'name':'License','path':'...'}]

    -- Approval Status
    verification_status     ENUM('pending','under_review','verified','rejected') DEFAULT 'pending',
    rejection_reason        TEXT,
    reviewer_notes          TEXT,

    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)            REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (company_type_id)    REFERENCES company_types(id),
    FOREIGN KEY (company_subtype_id) REFERENCES company_subtypes(id)
);
```

---

### 3.6 — `workers` Table

Vendor employees. Linked to both `users` and `vendors`.

```sql
CREATE TABLE workers (
    id                      INT PRIMARY KEY AUTO_INCREMENT,
    user_id                 INT UNIQUE NOT NULL,
    vendor_id               INT NOT NULL,

    -- Personal
    first_name              VARCHAR(100) NOT NULL,
    last_name               VARCHAR(100) NOT NULL,
    cnic                    VARCHAR(20) UNIQUE NOT NULL,
    date_of_birth           DATE,
    gender                  ENUM('male','female','other'),
    nationality             VARCHAR(100) DEFAULT 'Pakistani',
    profile_photo           VARCHAR(255),

    -- Contact
    phone                   VARCHAR(20) NOT NULL,
    email                   VARCHAR(150),
    address                 TEXT,
    emergency_contact_name  VARCHAR(150),
    emergency_contact_phone VARCHAR(20),
    emergency_relation      VARCHAR(50),

    -- Employment
    designation             VARCHAR(100) NOT NULL,
    department              VARCHAR(100),
    employee_code           VARCHAR(50),
    join_date               DATE NOT NULL,
    employment_type         ENUM('permanent','contract','temporary','daily_wage'),
    monthly_salary          DECIMAL(10,2),

    -- Skills & Qualifications
    education_level         ENUM('matric','intermediate','bachelor','master','phd','other'),
    skills                  JSON,           -- ['welding','electrical','plumbing']
    certifications          JSON,
    experience_years        INT DEFAULT 0,

    -- Documents
    cnic_front              VARCHAR(255),
    cnic_back               VARCHAR(255),
    police_verification     VARCHAR(255),
    medical_certificate     VARCHAR(255),

    -- Status
    is_active               BOOLEAN DEFAULT TRUE,
    deactivation_reason     TEXT,
    deactivated_at          TIMESTAMP NULL,

    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)   REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (vendor_id) REFERENCES vendors(id) ON DELETE CASCADE
);
```

---

### 3.7 — `form_fields_config` Table

Admin controls which fields are mandatory/visible without code changes.

```sql
CREATE TABLE form_fields_config (
    id               INT PRIMARY KEY AUTO_INCREMENT,
    form_type        ENUM('vendor_registration','worker_registration','user_profile') NOT NULL,
    field_name       VARCHAR(100) NOT NULL,
    field_label      VARCHAR(150) NOT NULL,
    field_type       ENUM('text','email','number','date','select','textarea','file','checkbox','radio'),
    is_mandatory     BOOLEAN DEFAULT FALSE,
    is_visible       BOOLEAN DEFAULT TRUE,
    validation_rules JSON,       -- {"min": 5, "max": 100, "pattern": "^[0-9]{13}$"}
    default_value    VARCHAR(255),
    placeholder      VARCHAR(255),
    help_text        TEXT,
    field_order      INT DEFAULT 0,
    field_group      VARCHAR(100),    -- 'company_info', 'contact', 'banking', 'documents'

    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY unique_field (form_type, field_name)
);
```

**Seed Data:**
```sql
INSERT INTO form_fields_config (form_type, field_name, field_label, field_type, is_mandatory, field_group, field_order) VALUES
-- Vendor Registration Fields
('vendor_registration', 'company_name',            'Company Name',                 'text',     TRUE,  'company_info', 1),
('vendor_registration', 'company_registration_no', 'Registration Number',          'text',     TRUE,  'company_info', 2),
('vendor_registration', 'ntn_number',              'NTN Number',                   'text',     FALSE, 'company_info', 3),
('vendor_registration', 'company_type_id',         'Company Type',                 'select',   TRUE,  'company_info', 4),
('vendor_registration', 'company_subtype_id',      'Company Sub-Type',             'select',   FALSE, 'company_info', 5),
('vendor_registration', 'years_in_business',       'Years in Business',            'number',   FALSE, 'company_info', 6),
('vendor_registration', 'annual_revenue',          'Annual Revenue (PKR)',          'number',   FALSE, 'company_info', 7),
('vendor_registration', 'primary_contact_name',    'Primary Contact Name',         'text',     TRUE,  'contact',      8),
('vendor_registration', 'primary_contact_phone',   'Primary Contact Phone',        'text',     TRUE,  'contact',      9),
('vendor_registration', 'primary_contact_email',   'Primary Contact Email',        'email',    TRUE,  'contact',      10),
('vendor_registration', 'address_line1',           'Address Line 1',               'text',     TRUE,  'address',      11),
('vendor_registration', 'city',                    'City',                         'text',     TRUE,  'address',      12),
('vendor_registration', 'country',                 'Country',                      'text',     FALSE, 'address',      13),
('vendor_registration', 'bank_name',               'Bank Name',                    'text',     FALSE, 'banking',      14),
('vendor_registration', 'bank_account_no',         'Account Number',               'text',     FALSE, 'banking',      15),
('vendor_registration', 'registration_certificate','Registration Certificate',      'file',     TRUE,  'documents',    16),
('vendor_registration', 'ntn_certificate',         'NTN Certificate',              'file',     FALSE, 'documents',    17),

-- Worker Registration Fields
('worker_registration', 'first_name',              'First Name',                   'text',     TRUE,  'personal',     1),
('worker_registration', 'last_name',               'Last Name',                    'text',     TRUE,  'personal',     2),
('worker_registration', 'cnic',                    'CNIC Number',                  'text',     TRUE,  'personal',     3),
('worker_registration', 'date_of_birth',           'Date of Birth',                'date',     FALSE, 'personal',     4),
('worker_registration', 'gender',                  'Gender',                       'select',   FALSE, 'personal',     5),
('worker_registration', 'phone',                   'Phone Number',                 'text',     TRUE,  'contact',      6),
('worker_registration', 'designation',             'Designation',                  'text',     TRUE,  'employment',   7),
('worker_registration', 'join_date',               'Joining Date',                 'date',     TRUE,  'employment',   8),
('worker_registration', 'employment_type',         'Employment Type',              'select',   FALSE, 'employment',   9),
('worker_registration', 'cnic_front',              'CNIC Front Image',             'file',     TRUE,  'documents',    10),
('worker_registration', 'cnic_back',               'CNIC Back Image',              'file',     FALSE, 'documents',    11);
```

---

### 3.8 — `approval_workflow` Table

Tracks vendor approval process with history.

```sql
CREATE TABLE approval_workflow (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    vendor_user_id  INT NOT NULL,
    approver_id     INT NULL,
    status          ENUM('pending','under_review','approved','rejected') DEFAULT 'pending',
    comments        TEXT,
    rejection_reason TEXT,
    reviewed_at     TIMESTAMP NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (vendor_user_id) REFERENCES users(id),
    FOREIGN KEY (approver_id)    REFERENCES users(id)
);
```

---

### 3.9 — `audit_logs` Table

Complete system activity log.

```sql
CREATE TABLE audit_logs (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    user_id     INT NULL,
    action      VARCHAR(100) NOT NULL,   -- 'vendor_registered', 'vendor_approved', 'worker_added'
    entity_type VARCHAR(50),             -- 'vendor', 'worker', 'user'
    entity_id   INT,
    old_values  JSON,
    new_values  JSON,
    ip_address  VARCHAR(45),
    user_agent  TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

---

## 4. TABLE RELATIONSHIPS (ERD SUMMARY)

```
roles (1) ─────────────── (many) users
                                   │
                    ┌──────────────┼──────────────┐
                    │                             │
              vendors (1:1)                  workers (1:1)
                    │                             │
         ┌──────────┤                     vendor_id (FK)
         │          │
  company_types    company_subtypes
  (1) ──── (many) company_subtypes

users ─── approval_workflow
users ─── audit_logs
```

---

## 5. IMPORTANT SQL QUERIES

### Get all vendors with company type and approval status
```sql
SELECT 
    v.id,
    v.company_name,
    ct.type_name AS company_type,
    cs.subtype_name AS company_subtype,
    v.verification_status,
    u.email,
    u.status AS account_status,
    u.created_at AS registered_on
FROM vendors v
JOIN users u ON v.user_id = u.id
JOIN company_types ct ON v.company_type_id = ct.id
LEFT JOIN company_subtypes cs ON v.company_subtype_id = cs.id
ORDER BY v.created_at DESC;
```

### Get subtypes for a given company type (for AJAX dropdown)
```sql
SELECT id, subtype_name 
FROM company_subtypes 
WHERE company_type_id = ? AND is_active = 1
ORDER BY sort_order ASC;
```

### Get all workers for a vendor (vendor dashboard)
```sql
SELECT 
    w.id,
    CONCAT(w.first_name, ' ', w.last_name) AS worker_name,
    w.cnic,
    w.designation,
    w.employment_type,
    w.is_active,
    w.join_date
FROM workers w
JOIN users u ON w.user_id = u.id
WHERE w.vendor_id = ?
ORDER BY w.first_name ASC;
```

### Get pending vendor registrations (for approver)
```sql
SELECT 
    v.id AS vendor_id,
    v.company_name,
    ct.type_name,
    v.primary_contact_name,
    v.primary_contact_phone,
    aw.status AS approval_status,
    aw.created_at AS submitted_on
FROM approval_workflow aw
JOIN users u ON aw.vendor_user_id = u.id
JOIN vendors v ON v.user_id = u.id
JOIN company_types ct ON v.company_type_id = ct.id
WHERE aw.status = 'pending'
ORDER BY aw.created_at ASC;
```

### Get dynamic form fields (mandatory + visible) for a form
```sql
SELECT field_name, field_label, field_type, is_mandatory, 
       validation_rules, placeholder, help_text, field_order, field_group
FROM form_fields_config
WHERE form_type = ? AND is_visible = 1
ORDER BY field_order ASC;
```

### Toggle field mandatory status (admin panel)
```sql
UPDATE form_fields_config 
SET is_mandatory = ?, updated_at = NOW()
WHERE form_type = ? AND field_name = ?;
```

### Super admin dashboard stats
```sql
SELECT
    (SELECT COUNT(*) FROM vendors WHERE verification_status = 'pending')   AS pending_vendors,
    (SELECT COUNT(*) FROM vendors WHERE verification_status = 'verified')  AS approved_vendors,
    (SELECT COUNT(*) FROM vendors WHERE verification_status = 'rejected')  AS rejected_vendors,
    (SELECT COUNT(*) FROM workers WHERE is_active = 1)                     AS total_workers,
    (SELECT COUNT(*) FROM users WHERE status = 'active')                   AS active_users;
```

---

## 6. FOLDER STRUCTURE

```
vendor-management-system/
│
├── config/
│   ├── database.php          # PDO connection singleton
│   ├── config.php            # APP_URL, UPLOAD_PATH, mail config
│   └── roles.php             # Role constants: ROLE_SUPER_ADMIN, etc.
│
├── classes/
│   ├── Database.php          # PDO wrapper with prepare/execute
│   ├── Auth.php              # login, logout, session, brute-force lock
│   ├── User.php              # createUser, updateStatus, getById
│   ├── Vendor.php            # register, approve, reject, getAll, getById
│   ├── Worker.php            # add, edit, deactivate, getByVendor
│   ├── CompanyType.php       # getAll, getSubtypes(type_id)
│   ├── FormConfig.php        # getFields(form_type), toggleMandatory
│   ├── AuditLog.php          # log(user_id, action, entity, id, old, new)
│   ├── FileUpload.php        # validate, move, getPath
│   └── Mailer.php            # sendApprovalEmail, sendRejectionEmail
│
├── middleware/
│   ├── auth.php              # redirect to login if not logged in
│   └── role_check.php        # checkRole(['super_admin', 'approver'])
│
├── public/
│   ├── index.php             # Landing page with login/register links
│   ├── login.php             # Login form
│   ├── logout.php
│   │
│   ├── vendor/
│   │   ├── register.php      # 4-step registration wizard
│   │   ├── dashboard.php     # Vendor home
│   │   ├── profile.php       # Edit company profile
│   │   ├── documents.php     # Upload/manage documents
│   │   └── workers/
│   │       ├── list.php      # Paginated workers list
│   │       ├── add.php       # Add new worker form
│   │       ├── edit.php      # Edit worker
│   │       └── view.php      # View worker details
│   │
│   ├── approver/
│   │   ├── dashboard.php     # Stats + pending count
│   │   ├── pending.php       # Table of pending vendors
│   │   ├── review.php        # Single vendor review page
│   │   └── history.php       # Past approvals/rejections
│   │
│   ├── admin/
│   │   ├── dashboard.php     # Super admin home
│   │   ├── users/
│   │   │   ├── list.php
│   │   │   ├── create.php    # Create approver/admin user
│   │   │   └── edit.php
│   │   ├── roles/
│   │   │   ├── list.php
│   │   │   └── manage.php
│   │   ├── vendors/
│   │   │   └── list.php      # All vendors (read-only with override)
│   │   ├── form_config/
│   │   │   └── manage.php    # Toggle mandatory/visible fields
│   │   ├── company_types/
│   │   │   ├── list.php
│   │   │   └── manage.php    # Add/edit company types & subtypes
│   │   └── audit_logs.php
│   │
│   └── worker/
│       └── dashboard.php     # Worker reads own profile only
│
├── api/                      # AJAX endpoints (return JSON)
│   ├── get_subtypes.php      # GET company subtypes by type_id
│   ├── vendor_register.php   # POST vendor registration
│   ├── worker_add.php        # POST add worker
│   ├── approve_vendor.php    # POST approve/reject
│   ├── toggle_field.php      # POST toggle form field config
│   └── upload_document.php   # POST file upload handler
│
├── assets/
│   ├── css/
│   │   ├── bootstrap.min.css
│   │   └── custom.css        # Navy/Cyan theme variables
│   ├── js/
│   │   ├── jquery.min.js
│   │   ├── bootstrap.bundle.min.js
│   │   └── main.js           # Form wizards, AJAX, validation
│   └── uploads/
│       ├── vendor_docs/
│       └── worker_docs/
│
└── sql/
    ├── schema.sql            # All CREATE TABLE statements
    └── seed_data.sql         # Roles, company types, form_fields_config, admin user
```

---

## 7. PHP CLASSES

### `config/database.php`
```php
<?php
class Database {
    private static ?PDO $instance = null;

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $dsn = 'mysql:host=localhost;dbname=vms_db;charset=utf8mb4';
            self::$instance = new PDO($dsn, 'vms_user', 'vms_password', [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
        return self::$instance;
    }
}
```

### `classes/Auth.php`
```php
<?php
class Auth {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function login(string $email, string $password): array {
        $stmt = $this->db->prepare("
            SELECT u.*, r.role_name 
            FROM users u 
            JOIN roles r ON u.role_id = r.id 
            WHERE u.email = ? AND u.status = 'active'
        ");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        session_regenerate_id(true);
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['role_name'] = $user['role_name'];
        $_SESSION['username']  = $user['username'];

        // Update last login
        $this->db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")
                 ->execute([$user['id']]);

        return ['success' => true, 'role' => $user['role_name']];
    }

    public function logout(): void {
        session_destroy();
        header('Location: /login.php');
        exit();
    }

    public static function check(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login.php');
            exit();
        }
    }
}
```

### `classes/CompanyType.php`
```php
<?php
class CompanyType {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll(): array {
        return $this->db->query("
            SELECT * FROM company_types WHERE is_active = 1 ORDER BY sort_order
        ")->fetchAll();
    }

    public function getSubtypes(int $typeId): array {
        $stmt = $this->db->prepare("
            SELECT * FROM company_subtypes 
            WHERE company_type_id = ? AND is_active = 1 
            ORDER BY sort_order
        ");
        $stmt->execute([$typeId]);
        return $stmt->fetchAll();
    }
}
```

### `classes/FormConfig.php`
```php
<?php
class FormConfig {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getFields(string $formType): array {
        $stmt = $this->db->prepare("
            SELECT * FROM form_fields_config 
            WHERE form_type = ? AND is_visible = 1 
            ORDER BY field_order ASC
        ");
        $stmt->execute([$formType]);
        return $stmt->fetchAll();
    }

    public function toggleMandatory(string $formType, string $fieldName, bool $mandatory): bool {
        $stmt = $this->db->prepare("
            UPDATE form_fields_config 
            SET is_mandatory = ?, updated_at = NOW()
            WHERE form_type = ? AND field_name = ?
        ");
        return $stmt->execute([$mandatory, $formType, $fieldName]);
    }

    public function toggleVisible(string $formType, string $fieldName, bool $visible): bool {
        $stmt = $this->db->prepare("
            UPDATE form_fields_config 
            SET is_visible = ?, updated_at = NOW()
            WHERE form_type = ? AND field_name = ?
        ");
        return $stmt->execute([$visible, $formType, $fieldName]);
    }
}
```

---

## 8. ROLE-BASED ACCESS — MIDDLEWARE

### `middleware/role_check.php`
```php
<?php
function requireAuth(): void {
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login.php');
        exit();
    }
}

function checkRole(array $allowedRoles): void {
    requireAuth();
    if (!in_array($_SESSION['role_name'], $allowedRoles)) {
        http_response_code(403);
        include '../includes/403.php';
        exit();
    }
}

// Usage Examples:
// checkRole(['super_admin']);                        // Admin only
// checkRole(['super_admin', 'approver']);            // Admin + Approver
// checkRole(['vendor']);                             // Vendor only
// checkRole(['super_admin', 'approver', 'vendor']);  // All except worker
```

### Usage in pages:
```php
<?php
// At the very top of every protected page
require_once '../../middleware/role_check.php';
checkRole(['vendor']);  // Only vendor can access this page
?>
```

---

## 9. UI DESIGN GUIDE

### CSS Theme Variables (`assets/css/custom.css`)
```css
:root {
    --navy:       #0A1628;
    --navy-light: #132040;
    --navy-mid:   #1C3058;
    --cyan:       #00BCD4;
    --cyan-light: #4DD0E1;
    --cyan-dark:  #0097A7;
    --white:      #FFFFFF;
    --gray-light: #F0F4F8;
    --gray-mid:   #B0BEC5;
    --text-light: #CFD8DC;
    --danger:     #EF5350;
    --success:    #66BB6A;
    --warning:    #FFA726;
}

body {
    background-color: var(--navy);
    color: var(--white);
    font-family: 'Segoe UI', sans-serif;
}

/* Navbar */
.navbar-vms {
    background-color: var(--navy-light);
    border-bottom: 2px solid var(--cyan);
    padding: 0.8rem 1.5rem;
}
.navbar-brand { color: var(--cyan) !important; font-weight: 700; font-size: 1.4rem; }

/* Sidebar */
.sidebar {
    background-color: var(--navy-light);
    min-height: 100vh;
    width: 260px;
    border-right: 1px solid var(--navy-mid);
}
.sidebar .nav-link {
    color: var(--text-light);
    padding: 0.75rem 1.25rem;
    border-left: 3px solid transparent;
    transition: all 0.2s;
}
.sidebar .nav-link:hover,
.sidebar .nav-link.active {
    color: var(--cyan);
    background-color: var(--navy-mid);
    border-left-color: var(--cyan);
}

/* Cards */
.card-vms {
    background-color: var(--navy-light);
    border: 1px solid var(--navy-mid);
    border-radius: 10px;
}
.card-vms .card-header {
    background-color: var(--navy-mid);
    border-bottom: 1px solid var(--cyan);
    color: var(--cyan);
    font-weight: 600;
}

/* Buttons */
.btn-cyan    { background-color: var(--cyan);  color: var(--navy);  border: none; font-weight: 600; }
.btn-cyan:hover { background-color: var(--cyan-dark); color: var(--white); }
.btn-outline-cyan { border: 1px solid var(--cyan); color: var(--cyan); background: transparent; }
.btn-outline-cyan:hover { background-color: var(--cyan); color: var(--navy); }

/* Tables */
.table-vms { color: var(--white); }
.table-vms thead { background-color: var(--navy-mid); color: var(--cyan); }
.table-vms tbody tr:hover { background-color: var(--navy-mid); }

/* Form Inputs */
.form-control-vms {
    background-color: var(--navy-mid);
    border: 1px solid var(--navy-mid);
    color: var(--white);
    border-radius: 6px;
}
.form-control-vms:focus {
    border-color: var(--cyan);
    box-shadow: 0 0 0 0.2rem rgba(0,188,212,0.25);
    background-color: var(--navy-mid);
    color: var(--white);
}
.form-label { color: var(--text-light); font-size: 0.875rem; }

/* Badges */
.badge-pending  { background-color: var(--warning); color: var(--navy); }
.badge-approved { background-color: var(--success); color: var(--navy); }
.badge-rejected { background-color: var(--danger);  color: var(--white); }

/* Stat Cards */
.stat-card {
    background: linear-gradient(135deg, var(--navy-light), var(--navy-mid));
    border: 1px solid var(--navy-mid);
    border-top: 3px solid var(--cyan);
    border-radius: 10px;
    padding: 1.5rem;
    text-align: center;
}
.stat-card .stat-number { font-size: 2.5rem; font-weight: 700; color: var(--cyan); }
.stat-card .stat-label  { color: var(--text-light); font-size: 0.875rem; }
```

---

## 10. PAGE-BY-PAGE UI & CODE

### Vendor Self-Registration — Multi-Step Form (`public/vendor/register.php`)

Multi-step wizard with 4 steps:

| Step | Title | Fields |
|---|---|---|
| 1 | Company Info | company_name, type, subtype, registration_no, years |
| 2 | Contact & Address | primary_contact, secondary_contact, address |
| 3 | Banking & Certs | bank_name, account_no, certifications |
| 4 | Documents Upload | registration_cert, ntn_cert, bank_statement |

```html
<!-- Step Indicator -->
<div class="steps-indicator d-flex mb-4">
    <div class="step active" data-step="1">
        <span class="step-number">1</span>
        <span class="step-title">Company Info</span>
    </div>
    <div class="step-divider"></div>
    <div class="step" data-step="2">
        <span class="step-number">2</span>
        <span class="step-title">Contact</span>
    </div>
    <div class="step-divider"></div>
    <div class="step" data-step="3">
        <span class="step-number">3</span>
        <span class="step-title">Banking</span>
    </div>
    <div class="step-divider"></div>
    <div class="step" data-step="4">
        <span class="step-number">4</span>
        <span class="step-title">Documents</span>
    </div>
</div>

<!-- Step 1: Company Info -->
<div class="form-step" id="step-1">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Company Name *</label>
            <input type="text" name="company_name" class="form-control form-control-vms" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Registration Number *</label>
            <input type="text" name="company_registration_no" class="form-control form-control-vms" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Company Type *</label>
            <select name="company_type_id" id="company_type" class="form-select form-control-vms" required>
                <option value="">-- Select Type --</option>
                <!-- Populated from DB -->
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Company Sub-Type</label>
            <select name="company_subtype_id" id="company_subtype" class="form-select form-control-vms">
                <option value="">-- Select Type First --</option>
            </select>
        </div>
    </div>
</div>
```

**AJAX Subtype Loading (`assets/js/main.js`):**
```javascript
$('#company_type').on('change', function() {
    const typeId = $(this).val();
    if (!typeId) return;

    $.get('/api/get_subtypes.php', { type_id: typeId }, function(res) {
        $('#company_subtype').html('<option value="">-- Select Sub-Type --</option>');
        res.subtypes.forEach(s => {
            $('#company_subtype').append(`<option value="${s.id}">${s.subtype_name}</option>`);
        });
    }, 'json');
});
```

**`api/get_subtypes.php`:**
```php
<?php
header('Content-Type: application/json');
require_once '../classes/CompanyType.php';

$typeId = (int)($_GET['type_id'] ?? 0);
if (!$typeId) { echo json_encode(['subtypes' => []]); exit; }

$ct = new CompanyType();
echo json_encode(['subtypes' => $ct->getSubtypes($typeId)]);
```

---

### Approver Review Page (`public/approver/review.php`)

```php
<?php
require_once '../../middleware/role_check.php';
checkRole(['approver', 'super_admin']);

$vendorId = (int)($_GET['id'] ?? 0);
// Fetch vendor details with joins...
?>

<div class="row">
    <!-- Vendor Details Column -->
    <div class="col-md-8">
        <div class="card card-vms mb-4">
            <div class="card-header">Company Information</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6"><strong>Company:</strong> <?= htmlspecialchars($vendor['company_name']) ?></div>
                    <div class="col-6"><strong>Type:</strong> <?= htmlspecialchars($vendor['type_name']) ?></div>
                    <div class="col-6"><strong>Sub-Type:</strong> <?= htmlspecialchars($vendor['subtype_name'] ?? 'N/A') ?></div>
                    <div class="col-6"><strong>Reg No:</strong> <?= htmlspecialchars($vendor['company_registration_no']) ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Column -->
    <div class="col-md-4">
        <div class="card card-vms">
            <div class="card-header">Decision</div>
            <div class="card-body">
                <textarea name="comments" class="form-control form-control-vms mb-3" placeholder="Comments (required for rejection)"></textarea>
                <button class="btn btn-cyan w-100 mb-2" onclick="submitDecision('approve')">✅ Approve Vendor</button>
                <button class="btn btn-danger w-100" onclick="submitDecision('reject')">❌ Reject Vendor</button>
            </div>
        </div>
    </div>
</div>
```

---

### Admin — Form Config Page (`public/admin/form_config/manage.php`)

```php
<?php
require_once '../../../middleware/role_check.php';
checkRole(['super_admin']);

$formConfig = new FormConfig();
$fields = $formConfig->getFields('vendor_registration'); // also 'worker_registration'
?>

<div class="card card-vms">
    <div class="card-header">Vendor Registration — Field Configuration</div>
    <div class="card-body p-0">
        <table class="table table-vms mb-0">
            <thead>
                <tr>
                    <th>Field Name</th>
                    <th>Label</th>
                    <th>Type</th>
                    <th class="text-center">Mandatory</th>
                    <th class="text-center">Visible</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fields as $field): ?>
                <tr>
                    <td><code><?= $field['field_name'] ?></code></td>
                    <td><?= htmlspecialchars($field['field_label']) ?></td>
                    <td><span class="badge bg-secondary"><?= $field['field_type'] ?></span></td>
                    <td class="text-center">
                        <input type="checkbox" 
                               class="toggle-field form-check-input" 
                               data-form="vendor_registration"
                               data-field="<?= $field['field_name'] ?>"
                               data-key="mandatory"
                               <?= $field['is_mandatory'] ? 'checked' : '' ?>>
                    </td>
                    <td class="text-center">
                        <input type="checkbox" 
                               class="toggle-field form-check-input" 
                               data-form="vendor_registration"
                               data-field="<?= $field['field_name'] ?>"
                               data-key="visible"
                               <?= $field['is_visible'] ? 'checked' : '' ?>>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$('.toggle-field').on('change', function() {
    $.post('/api/toggle_field.php', {
        form_type:  $(this).data('form'),
        field_name: $(this).data('field'),
        key:        $(this).data('key'),
        value:      $(this).is(':checked') ? 1 : 0
    }, function(res) {
        if (!res.success) alert('Error saving change');
    }, 'json');
});
</script>
```

---

## 11. BUSINESS FLOW (STEP BY STEP)

```
Step 1: Vendor visits /vendor/register.php
         ↓
Step 2: Fills 4-step form (company info, contact, banking, docs)
         ↓
Step 3: On submit → api/vendor_register.php
         - Creates users record (status = 'pending')
         - Creates vendors record (verification_status = 'pending')
         - Saves uploaded documents
         - Creates approval_workflow record (status = 'pending')
         - Logs to audit_logs
         - Sends email to all approvers
         ↓
Step 4: Approver logs in → sees pending count on dashboard
         ↓
Step 5: Approver opens vendor → reviews details + documents
         ↓
Step 6: Approver clicks Approve or Reject with comments
         - api/approve_vendor.php called
         - Updates users.status = 'active' (or 'rejected')
         - Updates vendors.verification_status = 'verified' (or 'rejected')
         - Updates approval_workflow
         - Logs to audit_logs
         - Sends email to vendor
         ↓
Step 7: Vendor receives approval email → can now login
         ↓
Step 8: Vendor logs in → Dashboard shows profile & workers section
         ↓
Step 9: Vendor adds workers → /vendor/workers/add.php
         - api/worker_add.php creates users + workers records
         - worker linked to vendor_id
         ↓
Step 10: Super Admin can view everything, toggle form fields,
         manage users/roles, view audit logs
```

---

## 12. SECURITY CHECKLIST

| # | Security Measure | Implementation |
|---|---|---|
| 1 | Password Hashing | `password_hash($pass, PASSWORD_BCRYPT)` |
| 2 | SQL Injection | PDO prepared statements everywhere |
| 3 | XSS Prevention | `htmlspecialchars()` on all output |
| 4 | CSRF Tokens | Token in every form, validated on submit |
| 5 | Session Security | `session_regenerate_id(true)` on login |
| 6 | File Upload Validation | Check extension, MIME type, file size |
| 7 | Role Enforcement | `checkRole()` on every protected page |
| 8 | Input Validation | Server-side validation, never trust client |
| 9 | Directory Protection | `.htaccess` deny on uploads folder |
| 10 | Error Handling | Never show raw PHP errors in production |

### File Upload Validation (`classes/FileUpload.php`)
```php
<?php
class FileUpload {
    private array $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
    private int $maxSize = 5 * 1024 * 1024; // 5MB

    public function validate(array $file): array {
        if ($file['size'] > $this->maxSize) {
            return ['valid' => false, 'error' => 'File too large. Max 5MB.'];
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $this->allowedTypes)) {
            return ['valid' => false, 'error' => 'Only PDF, JPG, PNG allowed.'];
        }

        return ['valid' => true];
    }

    public function move(array $file, string $destination): string {
        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('doc_', true) . '.' . $ext;
        $path     = $destination . '/' . $filename;
        move_uploaded_file($file['tmp_name'], $path);
        return $path;
    }
}
```

### `.htaccess` for uploads folder
```apache
# assets/uploads/.htaccess
Options -Indexes
<FilesMatch "\.(php|php3|php4|php5|phtml|pl|py|cgi)$">
    Order Deny,Allow
    Deny from all
</FilesMatch>
```

---

## 13. DEPLOYMENT GUIDE

### Step 1 — Database Setup
```bash
mysql -u root -p -e "CREATE DATABASE vms_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p -e "CREATE USER 'vms_user'@'localhost' IDENTIFIED BY 'StrongPass!123';"
mysql -u root -p -e "GRANT ALL PRIVILEGES ON vms_db.* TO 'vms_user'@'localhost';"
mysql -u root -p vms_db < sql/schema.sql
mysql -u root -p vms_db < sql/seed_data.sql
```

### Step 2 — Apache Virtual Host
```apache
<VirtualHost *:80>
    ServerName vms.yourdomain.com
    DocumentRoot /var/www/vms/public
    DirectoryIndex index.php

    <Directory /var/www/vms/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/vms_error.log
    CustomLog ${APACHE_LOG_DIR}/vms_access.log combined
</VirtualHost>
```

### Step 3 — File Permissions
```bash
chmod 755 /var/www/vms
chmod -R 644 /var/www/vms/assets/css
chmod -R 755 /var/www/vms/assets/uploads
chown -R www-data:www-data /var/www/vms/assets/uploads
```

### Step 4 — Create Super Admin
```sql
INSERT INTO users (username, email, password_hash, role_id, status, created_at)
VALUES (
    'superadmin',
    'admin@yourdomain.com',
    '$2y$10$HASH_GENERATED_BY_password_hash()',
    1,
    'active',
    NOW()
);
```

### Step 5 — PHP `password_hash` for seeding
```php
<?php echo password_hash('Admin@123!', PASSWORD_BCRYPT); ?>
```

---

## SUMMARY TABLE

| Module | Tables | Pages | Role |
|---|---|---|---|
| Auth | users, roles | login, logout | All |
| Vendor Registration | vendors, company_types, company_subtypes | register | Public |
| Approval Workflow | approval_workflow | pending, review | approver, super_admin |
| Worker Management | workers | list, add, edit | vendor |
| Form Config | form_fields_config | manage | super_admin |
| Audit Trail | audit_logs | audit_logs | super_admin |
| User Management | users | list, create, edit | super_admin |
| Company Types | company_types, company_subtypes | manage | super_admin |

---

*VMS Architecture Document — Navy/Cyan Theme — PHP MySQL*
