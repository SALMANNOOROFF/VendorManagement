-- =============================================
-- Vendor Management System — Seed Data
-- =============================================

SET NAMES utf8mb4;

-- Roles
INSERT INTO roles (role_name, role_display, description, permissions) VALUES
('super_admin', 'Super Administrator', 'Full system access', '{"all": true}'),
('approver',    'Vendor Approver',     'Reviews vendor registrations', '{"can_approve_vendors": true, "view_vendors": true}'),
('vendor',      'Vendor',              'Registered company/supplier',  '{"can_add_workers": true, "view_own_profile": true}'),
('worker',      'Worker',              'Vendor employee',              '{"view_own_profile": true}');

-- Super Admin User (password: Admin@123!)
INSERT INTO users (username, email, password_hash, role_id, status) VALUES
('superadmin', 'admin@vms.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'active');

-- Approver User (password: Admin@123!)
INSERT INTO users (username, email, password_hash, role_id, status, created_by) VALUES
('approver1', 'approver@vms.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'active', 1);

-- Company Types
INSERT INTO company_types (type_name, description, sort_order) VALUES
('Manufacturing',          'Companies that produce goods',                    1),
('IT & Technology',        'Software, hardware, IT services',                 2),
('Construction',           'Building, civil engineering, infrastructure',      3),
('Trading & Distribution', 'Import/export, wholesale, retail distribution',   4),
('Professional Services',  'Consulting, legal, accounting, HR',               5),
('Healthcare & Pharma',    'Hospitals, clinics, pharmaceutical companies',     6),
('Logistics & Transport',  'Freight, courier, warehousing',                   7),
('Food & Beverage',        'Food processing, restaurants, catering',           8),
('Energy & Utilities',     'Oil & gas, electricity, water, renewable energy', 9),
('Other',                  'Any other business type',                         10);

-- Company Subtypes
INSERT INTO company_subtypes (company_type_id, subtype_name, sort_order) VALUES
-- Manufacturing
(1, 'Textile & Garments',        1),
(1, 'Chemical Manufacturing',    2),
(1, 'Food Processing',           3),
(1, 'Plastic & Rubber Products', 4),
(1, 'Steel & Metal Works',       5),
(1, 'Electronics Manufacturing', 6),
-- IT & Technology
(2, 'Software Development',  1),
(2, 'IT Consulting',         2),
(2, 'Cybersecurity',         3),
(2, 'Cloud Services',        4),
(2, 'Hardware & Networking',  5),
-- Construction
(3, 'Residential Construction', 1),
(3, 'Commercial Construction',  2),
(3, 'Road & Infrastructure',    3),
(3, 'Interior Design',          4),
(3, 'Electrical Contracting',   5),
(3, 'Plumbing & HVAC',          6),
-- Trading
(4, 'Import/Export',       1),
(4, 'Wholesale',           2),
(4, 'Retail Distribution', 3),
-- Professional Services
(5, 'Management Consulting', 1),
(5, 'Legal Services',        2),
(5, 'Accounting & Audit',    3),
(5, 'HR & Recruitment',      4);

-- Form Fields Config — Vendor Registration
INSERT INTO form_fields_config (form_type, field_name, field_label, field_type, is_mandatory, field_group, field_order, placeholder) VALUES
('vendor_registration', 'company_name',            'Company Name',                 'text',     TRUE,  'company_info', 1,  'Enter company name'),
('vendor_registration', 'company_registration_no', 'Registration Number',          'text',     TRUE,  'company_info', 2,  'Company registration number'),
('vendor_registration', 'ntn_number',              'NTN Number',                   'text',     FALSE, 'company_info', 3,  'National Tax Number'),
('vendor_registration', 'strn_number',             'STRN Number',                  'text',     FALSE, 'company_info', 4,  'Sales Tax Registration Number'),
('vendor_registration', 'company_type_id',         'Company Type',                 'select',   TRUE,  'company_info', 5,  NULL),
('vendor_registration', 'company_subtype_id',      'Company Sub-Type',             'select',   FALSE, 'company_info', 6,  NULL),
('vendor_registration', 'years_in_business',       'Years in Business',            'number',   FALSE, 'company_info', 7,  'e.g. 10'),
('vendor_registration', 'number_of_employees',     'Number of Employees',          'number',   FALSE, 'company_info', 8,  'e.g. 50'),
('vendor_registration', 'annual_revenue',          'Annual Revenue (PKR)',          'number',   FALSE, 'company_info', 9,  'e.g. 5000000'),
('vendor_registration', 'primary_contact_name',    'Primary Contact Name',         'text',     TRUE,  'contact',      10, 'Full name'),
('vendor_registration', 'primary_contact_phone',   'Primary Contact Phone',        'text',     TRUE,  'contact',      11, '+92-XXX-XXXXXXX'),
('vendor_registration', 'primary_contact_email',   'Primary Contact Email',        'email',    TRUE,  'contact',      12, 'email@example.com'),
('vendor_registration', 'primary_contact_cnic',    'Primary Contact CNIC',         'text',     FALSE, 'contact',      13, 'XXXXX-XXXXXXX-X'),
('vendor_registration', 'secondary_contact_name',  'Secondary Contact Name',       'text',     FALSE, 'contact',      14, 'Full name'),
('vendor_registration', 'secondary_contact_phone', 'Secondary Contact Phone',      'text',     FALSE, 'contact',      15, '+92-XXX-XXXXXXX'),
('vendor_registration', 'secondary_contact_email', 'Secondary Contact Email',      'email',    FALSE, 'contact',      16, 'email@example.com'),
('vendor_registration', 'address_line1',           'Address Line 1',               'text',     TRUE,  'address',      17, 'Street address'),
('vendor_registration', 'address_line2',           'Address Line 2',               'text',     FALSE, 'address',      18, 'Suite, floor, etc.'),
('vendor_registration', 'city',                    'City',                         'text',     TRUE,  'address',      19, 'City'),
('vendor_registration', 'state_province',          'State/Province',               'text',     FALSE, 'address',      20, 'State or Province'),
('vendor_registration', 'postal_code',             'Postal Code',                  'text',     FALSE, 'address',      21, 'Postal code'),
('vendor_registration', 'country',                 'Country',                      'text',     FALSE, 'address',      22, 'Pakistan'),
('vendor_registration', 'bank_name',               'Bank Name',                    'text',     FALSE, 'banking',      23, 'e.g. HBL, MCB'),
('vendor_registration', 'bank_account_title',      'Account Title',                'text',     FALSE, 'banking',      24, 'Account holder name'),
('vendor_registration', 'bank_account_no',         'Account Number',               'text',     FALSE, 'banking',      25, 'Bank account number'),
('vendor_registration', 'bank_branch',             'Branch',                       'text',     FALSE, 'banking',      26, 'Branch name'),
('vendor_registration', 'iban',                    'IBAN',                         'text',     FALSE, 'banking',      27, 'International Bank Account Number'),
('vendor_registration', 'business_description',    'Business Description',         'textarea', FALSE, 'company_info', 28, 'Describe your business'),
('vendor_registration', 'registration_certificate','Registration Certificate',     'file',     TRUE,  'documents',    29, NULL),
('vendor_registration', 'ntn_certificate',         'NTN Certificate',              'file',     FALSE, 'documents',    30, NULL),
('vendor_registration', 'tax_certificate',         'Tax Certificate',              'file',     FALSE, 'documents',    31, NULL),
('vendor_registration', 'bank_statement',          'Bank Statement',               'file',     FALSE, 'documents',    32, NULL),
('vendor_registration', 'company_profile_doc',     'Company Profile Document',     'file',     FALSE, 'documents',    33, NULL);

-- Form Fields Config — Worker Registration
INSERT INTO form_fields_config (form_type, field_name, field_label, field_type, is_mandatory, field_group, field_order, placeholder) VALUES
('worker_registration', 'first_name',              'First Name',                   'text',     TRUE,  'personal',     1,  'First name'),
('worker_registration', 'last_name',               'Last Name',                    'text',     TRUE,  'personal',     2,  'Last name'),
('worker_registration', 'cnic',                    'CNIC Number',                  'text',     TRUE,  'personal',     3,  'XXXXX-XXXXXXX-X'),
('worker_registration', 'date_of_birth',           'Date of Birth',                'date',     FALSE, 'personal',     4,  NULL),
('worker_registration', 'gender',                  'Gender',                       'select',   FALSE, 'personal',     5,  NULL),
('worker_registration', 'phone',                   'Phone Number',                 'text',     TRUE,  'contact',      6,  '+92-XXX-XXXXXXX'),
('worker_registration', 'email',                   'Email Address',                'email',    FALSE, 'contact',      7,  'email@example.com'),
('worker_registration', 'address',                 'Address',                      'textarea', FALSE, 'contact',      8,  'Full address'),
('worker_registration', 'emergency_contact_name',  'Emergency Contact Name',       'text',     FALSE, 'contact',      9,  'Emergency contact'),
('worker_registration', 'emergency_contact_phone', 'Emergency Contact Phone',      'text',     FALSE, 'contact',      10, '+92-XXX-XXXXXXX'),
('worker_registration', 'designation',             'Designation',                  'text',     TRUE,  'employment',   11, 'Job title'),
('worker_registration', 'department',              'Department',                   'text',     FALSE, 'employment',   12, 'Department'),
('worker_registration', 'join_date',               'Joining Date',                 'date',     TRUE,  'employment',   13, NULL),
('worker_registration', 'employment_type',         'Employment Type',              'select',   FALSE, 'employment',   14, NULL),
('worker_registration', 'monthly_salary',          'Monthly Salary (PKR)',         'number',   FALSE, 'employment',   15, 'e.g. 50000'),
('worker_registration', 'education_level',         'Education Level',              'select',   FALSE, 'employment',   16, NULL),
('worker_registration', 'experience_years',        'Experience (Years)',            'number',   FALSE, 'employment',   17, 'Years of experience'),
('worker_registration', 'cnic_front',              'CNIC Front Image',             'file',     TRUE,  'documents',    18, NULL),
('worker_registration', 'cnic_back',               'CNIC Back Image',              'file',     FALSE, 'documents',    19, NULL);
