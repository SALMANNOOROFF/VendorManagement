-- =============================================
-- Vendor Management System — Database Schema
-- MySQL 8.x | UTF8MB4
-- =============================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Roles
CREATE TABLE IF NOT EXISTS roles (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    role_name       VARCHAR(50) UNIQUE NOT NULL,
    role_display    VARCHAR(100) NOT NULL,
    description     TEXT,
    permissions     JSON,
    is_active       BOOLEAN DEFAULT TRUE,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Users
CREATE TABLE IF NOT EXISTS users (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    username        VARCHAR(100) UNIQUE NOT NULL,
    email           VARCHAR(150) UNIQUE NOT NULL,
    password_hash   VARCHAR(255) NOT NULL,
    role_id         INT NOT NULL,
    status          ENUM('pending','active','suspended','rejected') DEFAULT 'pending',
    created_by      INT NULL,
    approved_by     INT NULL,
    approved_at     TIMESTAMP NULL,
    last_login      TIMESTAMP NULL,
    login_attempts  INT DEFAULT 0,
    locked_until    TIMESTAMP NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (role_id)     REFERENCES roles(id),
    FOREIGN KEY (created_by)  REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Company Types
CREATE TABLE IF NOT EXISTS company_types (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    type_name   VARCHAR(150) UNIQUE NOT NULL,
    description TEXT,
    is_active   BOOLEAN DEFAULT TRUE,
    sort_order  INT DEFAULT 0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Company Subtypes
CREATE TABLE IF NOT EXISTS company_subtypes (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    company_type_id INT NOT NULL,
    subtype_name    VARCHAR(150) NOT NULL,
    description     TEXT,
    is_active       BOOLEAN DEFAULT TRUE,
    sort_order      INT DEFAULT 0,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (company_type_id) REFERENCES company_types(id) ON DELETE CASCADE,
    UNIQUE KEY unique_subtype (company_type_id, subtype_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Vendors
CREATE TABLE IF NOT EXISTS vendors (
    id                      INT PRIMARY KEY AUTO_INCREMENT,
    user_id                 INT UNIQUE NOT NULL,

    -- Company Identity
    company_name            VARCHAR(200) NOT NULL,
    company_registration_no VARCHAR(100),
    ntn_number              VARCHAR(50),
    strn_number             VARCHAR(50),
    company_type_id         INT NOT NULL,
    company_subtype_id      INT,
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
    certifications          JSON,
    business_description    TEXT,

    -- Documents (file paths)
    registration_certificate VARCHAR(255),
    ntn_certificate          VARCHAR(255),
    tax_certificate          VARCHAR(255),
    bank_statement           VARCHAR(255),
    company_profile_doc      VARCHAR(255),
    other_documents          JSON,

    -- Approval Status
    verification_status     ENUM('pending','under_review','verified','rejected') DEFAULT 'pending',
    rejection_reason        TEXT,
    reviewer_notes          TEXT,

    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)            REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (company_type_id)    REFERENCES company_types(id),
    FOREIGN KEY (company_subtype_id) REFERENCES company_subtypes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Workers
CREATE TABLE IF NOT EXISTS workers (
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
    skills                  JSON,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Form Fields Config
CREATE TABLE IF NOT EXISTS form_fields_config (
    id               INT PRIMARY KEY AUTO_INCREMENT,
    form_type        ENUM('vendor_registration','worker_registration','user_profile') NOT NULL,
    field_name       VARCHAR(100) NOT NULL,
    field_label      VARCHAR(150) NOT NULL,
    field_type       ENUM('text','email','number','date','select','textarea','file','checkbox','radio'),
    is_mandatory     BOOLEAN DEFAULT FALSE,
    is_visible       BOOLEAN DEFAULT TRUE,
    validation_rules JSON,
    default_value    VARCHAR(255),
    placeholder      VARCHAR(255),
    help_text        TEXT,
    field_order      INT DEFAULT 0,
    field_group      VARCHAR(100),

    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY unique_field (form_type, field_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Approval Workflow
CREATE TABLE IF NOT EXISTS approval_workflow (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Audit Logs
CREATE TABLE IF NOT EXISTS audit_logs (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    user_id     INT NULL,
    action      VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50),
    entity_id   INT,
    old_values  JSON,
    new_values  JSON,
    ip_address  VARCHAR(45),
    user_agent  TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
