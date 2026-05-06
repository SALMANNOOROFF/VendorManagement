-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 06, 2026 at 01:23 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `approval_workflow`
--

CREATE TABLE `approval_workflow` (
  `id` int(11) NOT NULL,
  `vendor_user_id` int(11) NOT NULL,
  `approver_id` int(11) DEFAULT NULL,
  `status` enum('pending','under_review','approved','rejected') DEFAULT 'pending',
  `comments` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `approval_workflow`
--

INSERT INTO `approval_workflow` (`id`, `vendor_user_id`, `approver_id`, `status`, `comments`, `rejection_reason`, `reviewed_at`, `created_at`) VALUES
(3, 6, 2, 'approved', '', NULL, '2026-05-06 05:10:06', '2026-05-06 05:07:13'),
(4, 7, NULL, 'pending', NULL, NULL, NULL, '2026-05-06 06:41:03'),
(5, 8, NULL, 'pending', NULL, NULL, NULL, '2026-05-06 06:55:45'),
(6, 9, 2, 'approved', '', NULL, '2026-05-06 08:17:25', '2026-05-06 08:08:00');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity_type`, `entity_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, NULL, 'vendor_registered', 'vendor', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 04:56:29'),
(2, 6, 'vendor_registered', 'vendor', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 05:07:13'),
(3, 2, 'vendor_approved', 'vendor', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 05:10:06'),
(4, 7, 'vendor_registered', 'vendor', 4, NULL, NULL, '172.16.4.92', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 06:41:03'),
(5, 8, 'vendor_registered', 'vendor', 5, NULL, NULL, '172.16.4.5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 06:55:45'),
(6, 9, 'vendor_registered', 'vendor', 6, NULL, NULL, '172.16.4.86', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 08:08:00'),
(7, 2, 'vendor_approved', 'vendor', 6, NULL, NULL, '172.16.4.86', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 08:17:25'),
(8, 6, 'worker_added', 'worker', 1, NULL, NULL, '172.16.64.58', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 11:01:44'),
(11, 6, 'worker_bulk_added', 'worker', 4, NULL, NULL, '172.16.64.58', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 11:15:10'),
(12, 6, 'worker_bulk_added', 'worker', 5, NULL, NULL, '172.16.64.58', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 11:15:10'),
(13, 6, 'worker_bulk_added', 'worker', 6, NULL, NULL, '172.16.64.58', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 11:15:10'),
(14, 6, 'worker_bulk_added', 'worker', 7, NULL, NULL, '172.16.64.58', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 11:15:10'),
(15, 6, 'worker_bulk_added', 'worker', 8, NULL, NULL, '172.16.64.58', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 11:15:10'),
(16, 6, 'worker_bulk_added', 'worker', 9, NULL, NULL, '172.16.64.58', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 11:15:10'),
(17, 6, 'worker_bulk_added', 'worker', 10, NULL, NULL, '172.16.64.58', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-06 11:18:22');

-- --------------------------------------------------------

--
-- Table structure for table `company_subtypes`
--

CREATE TABLE `company_subtypes` (
  `id` int(11) NOT NULL,
  `company_type_id` int(11) NOT NULL,
  `subtype_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company_subtypes`
--

INSERT INTO `company_subtypes` (`id`, `company_type_id`, `subtype_name`, `description`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'Textile & Garments', NULL, 1, 1, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(2, 1, 'Chemical Manufacturing', NULL, 1, 2, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(3, 1, 'Food Processing', NULL, 1, 3, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(4, 1, 'Plastic & Rubber Products', NULL, 1, 4, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(5, 1, 'Steel & Metal Works', NULL, 1, 5, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(6, 1, 'Electronics Manufacturing', NULL, 1, 6, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(7, 2, 'Software Development', NULL, 1, 1, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(8, 2, 'IT Consulting', NULL, 1, 2, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(9, 2, 'Cybersecurity', NULL, 1, 3, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(10, 2, 'Cloud Services', NULL, 1, 4, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(11, 2, 'Hardware & Networking', NULL, 1, 5, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(12, 3, 'Residential Construction', NULL, 1, 1, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(13, 3, 'Commercial Construction', NULL, 1, 2, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(14, 3, 'Road & Infrastructure', NULL, 1, 3, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(15, 3, 'Interior Design', NULL, 1, 4, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(16, 3, 'Electrical Contracting', NULL, 1, 5, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(17, 3, 'Plumbing & HVAC', NULL, 1, 6, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(18, 4, 'Import/Export', NULL, 1, 1, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(19, 4, 'Wholesale', NULL, 1, 2, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(20, 4, 'Retail Distribution', NULL, 1, 3, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(21, 5, 'Management Consulting', NULL, 1, 1, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(22, 5, 'Legal Services', NULL, 1, 2, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(23, 5, 'Accounting & Audit', NULL, 1, 3, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(24, 5, 'HR & Recruitment', NULL, 1, 4, '2026-05-06 04:30:36', '2026-05-06 04:30:36');

-- --------------------------------------------------------

--
-- Table structure for table `company_types`
--

CREATE TABLE `company_types` (
  `id` int(11) NOT NULL,
  `type_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company_types`
--

INSERT INTO `company_types` (`id`, `type_name`, `description`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Manufacturing', 'Companies that produce goods', 1, 1, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(2, 'IT & Technology', 'Software, hardware, IT services', 1, 2, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(3, 'Construction', 'Building, civil engineering, infrastructure', 1, 3, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(4, 'Trading & Distribution', 'Import/export, wholesale, retail distribution', 1, 4, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(5, 'Professional Services', 'Consulting, legal, accounting, HR', 1, 5, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(6, 'Healthcare & Pharma', 'Hospitals, clinics, pharmaceutical companies', 1, 6, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(7, 'Logistics & Transport', 'Freight, courier, warehousing', 1, 7, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(8, 'Food & Beverage', 'Food processing, restaurants, catering', 1, 8, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(9, 'Energy & Utilities', 'Oil & gas, electricity, water, renewable energy', 1, 9, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(10, 'Other', 'Any other business type', 1, 10, '2026-05-06 04:30:36', '2026-05-06 04:30:36');

-- --------------------------------------------------------

--
-- Table structure for table `form_fields_config`
--

CREATE TABLE `form_fields_config` (
  `id` int(11) NOT NULL,
  `form_type` enum('vendor_registration','worker_registration','user_profile') NOT NULL,
  `field_name` varchar(100) NOT NULL,
  `field_label` varchar(150) NOT NULL,
  `field_type` enum('text','email','number','date','select','textarea','file','checkbox','radio') DEFAULT NULL,
  `is_mandatory` tinyint(1) DEFAULT 0,
  `is_visible` tinyint(1) DEFAULT 1,
  `validation_rules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`validation_rules`)),
  `default_value` varchar(255) DEFAULT NULL,
  `placeholder` varchar(255) DEFAULT NULL,
  `help_text` text DEFAULT NULL,
  `field_order` int(11) DEFAULT 0,
  `field_group` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `form_fields_config`
--

INSERT INTO `form_fields_config` (`id`, `form_type`, `field_name`, `field_label`, `field_type`, `is_mandatory`, `is_visible`, `validation_rules`, `default_value`, `placeholder`, `help_text`, `field_order`, `field_group`, `created_at`, `updated_at`) VALUES
(1, 'vendor_registration', 'company_name', 'Company Name', 'text', 1, 1, NULL, NULL, 'Enter company name', NULL, 1, 'company_info', '2026-05-06 04:30:36', '2026-05-06 10:37:42'),
(2, 'vendor_registration', 'company_registration_no', 'Registration Number', 'text', 0, 1, NULL, NULL, 'Company registration number', NULL, 2, 'company_info', '2026-05-06 04:30:36', '2026-05-06 10:00:28'),
(3, 'vendor_registration', 'ntn_number', 'NTN Number', 'text', 0, 0, NULL, NULL, 'National Tax Number', NULL, 3, 'company_info', '2026-05-06 04:30:36', '2026-05-06 10:00:31'),
(4, 'vendor_registration', 'strn_number', 'STRN Number', 'text', 0, 0, NULL, NULL, 'Sales Tax Registration Number', NULL, 4, 'company_info', '2026-05-06 04:30:36', '2026-05-06 10:00:34'),
(5, 'vendor_registration', 'company_type_id', 'Company Type', 'select', 1, 1, NULL, NULL, NULL, NULL, 5, 'company_info', '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(6, 'vendor_registration', 'company_subtype_id', 'Company Sub-Type', 'select', 0, 0, NULL, NULL, NULL, NULL, 6, 'company_info', '2026-05-06 04:30:36', '2026-05-06 10:00:37'),
(7, 'vendor_registration', 'years_in_business', 'Years in Business', 'number', 0, 0, NULL, NULL, 'e.g. 10', NULL, 7, 'company_info', '2026-05-06 04:30:36', '2026-05-06 10:00:40'),
(8, 'vendor_registration', 'number_of_employees', 'Number of Employees', 'number', 0, 0, NULL, NULL, 'e.g. 50', NULL, 8, 'company_info', '2026-05-06 04:30:36', '2026-05-06 10:00:42'),
(9, 'vendor_registration', 'annual_revenue', 'Annual Revenue (PKR)', 'number', 0, 0, NULL, NULL, 'e.g. 5000000', NULL, 9, 'company_info', '2026-05-06 04:30:36', '2026-05-06 10:00:43'),
(10, 'vendor_registration', 'primary_contact_name', 'Primary Contact Name', 'text', 1, 1, NULL, NULL, 'Full name', NULL, 10, 'contact', '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(11, 'vendor_registration', 'primary_contact_phone', 'Primary Contact Phone', 'text', 1, 1, NULL, NULL, '+92-XXX-XXXXXXX', NULL, 11, 'contact', '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(12, 'vendor_registration', 'primary_contact_email', 'Primary Contact Email', 'email', 1, 1, NULL, NULL, 'email@example.com', NULL, 12, 'contact', '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(13, 'vendor_registration', 'primary_contact_cnic', 'Primary Contact CNIC', 'text', 0, 1, NULL, NULL, 'XXXXX-XXXXXXX-X', NULL, 13, 'contact', '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(14, 'vendor_registration', 'secondary_contact_name', 'Secondary Contact Name', 'text', 0, 1, NULL, NULL, 'Full name', NULL, 14, 'contact', '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(15, 'vendor_registration', 'secondary_contact_phone', 'Secondary Contact Phone', 'text', 0, 1, NULL, NULL, '+92-XXX-XXXXXXX', NULL, 15, 'contact', '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(16, 'vendor_registration', 'secondary_contact_email', 'Secondary Contact Email', 'email', 0, 1, NULL, NULL, 'email@example.com', NULL, 16, 'contact', '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(17, 'vendor_registration', 'address_line1', 'Address Line 1', 'text', 1, 1, NULL, NULL, 'Street address', NULL, 17, 'address', '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(18, 'vendor_registration', 'address_line2', 'Address Line 2', 'text', 0, 1, NULL, NULL, 'Suite, floor, etc.', NULL, 18, 'address', '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(19, 'vendor_registration', 'city', 'City', 'text', 1, 1, NULL, NULL, 'City', NULL, 19, 'address', '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(20, 'vendor_registration', 'state_province', 'State/Province', 'text', 0, 1, NULL, NULL, 'State or Province', NULL, 20, 'address', '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(21, 'vendor_registration', 'postal_code', 'Postal Code', 'text', 0, 1, NULL, NULL, 'Postal code', NULL, 21, 'address', '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(22, 'vendor_registration', 'country', 'Country', 'text', 0, 1, NULL, NULL, 'Pakistan', NULL, 22, 'address', '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(23, 'vendor_registration', 'bank_name', 'Bank Name', 'text', 0, 1, NULL, NULL, 'e.g. HBL, MCB', NULL, 23, 'banking', '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(24, 'vendor_registration', 'bank_account_title', 'Account Title', 'text', 0, 1, NULL, NULL, 'Account holder name', NULL, 24, 'banking', '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(25, 'vendor_registration', 'bank_account_no', 'Account Number', 'text', 0, 1, NULL, NULL, 'Bank account number', NULL, 25, 'banking', '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(26, 'vendor_registration', 'bank_branch', 'Branch', 'text', 0, 1, NULL, NULL, 'Branch name', NULL, 26, 'banking', '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(27, 'vendor_registration', 'iban', 'IBAN', 'text', 0, 1, NULL, NULL, 'International Bank Account Number', NULL, 27, 'banking', '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(28, 'vendor_registration', 'business_description', 'Business Description', 'textarea', 0, 1, NULL, NULL, 'Describe your business', NULL, 28, 'company_info', '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(29, 'vendor_registration', 'registration_certificate', 'Documents upload', 'file', 1, 1, NULL, NULL, NULL, NULL, 29, 'documents', '2026-05-06 04:30:36', '2026-05-06 10:39:42'),
(30, 'vendor_registration', 'ntn_certificate', 'NTN Certificate', 'file', 0, 0, NULL, NULL, NULL, NULL, 30, 'documents', '2026-05-06 04:30:36', '2026-05-06 10:38:02'),
(31, 'vendor_registration', 'tax_certificate', 'Tax Certificate', 'file', 0, 0, NULL, NULL, NULL, NULL, 31, 'documents', '2026-05-06 04:30:36', '2026-05-06 10:38:01'),
(32, 'vendor_registration', 'bank_statement', 'Bank Statement', 'file', 0, 0, NULL, NULL, NULL, NULL, 32, 'documents', '2026-05-06 04:30:36', '2026-05-06 10:38:00'),
(33, 'vendor_registration', 'company_profile_doc', 'Company Profile Document', 'file', 0, 0, NULL, NULL, NULL, NULL, 33, 'documents', '2026-05-06 04:30:36', '2026-05-06 10:37:59'),
(34, 'worker_registration', 'first_name', 'Full Name', 'text', 1, 1, NULL, NULL, 'First name', NULL, 1, 'personal', '2026-05-06 04:30:36', '2026-05-06 10:43:16'),
(35, 'worker_registration', 'last_name', 'Last Name', 'text', 0, 0, NULL, NULL, 'Last name', NULL, 2, 'personal', '2026-05-06 04:30:36', '2026-05-06 10:43:18'),
(36, 'worker_registration', 'cnic', 'CNIC Number', 'text', 1, 1, NULL, NULL, 'XXXXX-XXXXXXX-X', NULL, 3, 'personal', '2026-05-06 04:30:36', '2026-05-06 10:52:21'),
(37, 'worker_registration', 'date_of_birth', 'Date of Birth', 'date', 0, 0, NULL, NULL, NULL, NULL, 4, 'personal', '2026-05-06 04:30:36', '2026-05-06 09:54:37'),
(38, 'worker_registration', 'gender', 'Gender', 'select', 0, 0, NULL, NULL, NULL, NULL, 5, 'personal', '2026-05-06 04:30:36', '2026-05-06 09:54:38'),
(39, 'worker_registration', 'phone', 'Phone Number', 'text', 1, 1, NULL, NULL, '+92-XXX-XXXXXXX', NULL, 6, 'contact', '2026-05-06 04:30:36', '2026-05-06 10:52:32'),
(40, 'worker_registration', 'email', 'Email Address', 'email', 0, 0, NULL, NULL, 'email@example.com', NULL, 7, 'contact', '2026-05-06 04:30:36', '2026-05-06 09:54:41'),
(41, 'worker_registration', 'address', 'Address', 'textarea', 0, 1, NULL, NULL, 'Full address', NULL, 8, 'contact', '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(42, 'worker_registration', 'emergency_contact_name', 'Emergency Contact Name', 'text', 0, 0, NULL, NULL, 'Emergency contact', NULL, 9, 'contact', '2026-05-06 04:30:36', '2026-05-06 10:43:33'),
(43, 'worker_registration', 'emergency_contact_phone', 'Emergency Contact Phone', 'text', 0, 0, NULL, NULL, '+92-XXX-XXXXXXX', NULL, 10, 'contact', '2026-05-06 04:30:36', '2026-05-06 10:43:34'),
(44, 'worker_registration', 'designation', 'Designation', 'text', 1, 1, NULL, NULL, 'Job title', NULL, 11, 'employment', '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(45, 'worker_registration', 'department', 'Department', 'text', 0, 1, NULL, NULL, 'Department', NULL, 12, 'employment', '2026-05-06 04:30:36', '2026-05-06 10:43:40'),
(46, 'worker_registration', 'join_date', 'Joining Date', 'date', 0, 0, NULL, NULL, NULL, NULL, 13, 'employment', '2026-05-06 04:30:36', '2026-05-06 10:43:42'),
(47, 'worker_registration', 'employment_type', 'Employment Type', 'select', 0, 1, NULL, NULL, NULL, NULL, 14, 'employment', '2026-05-06 04:30:36', '2026-05-06 11:16:04'),
(48, 'worker_registration', 'monthly_salary', 'Monthly Salary (PKR)', 'number', 0, 0, NULL, NULL, 'e.g. 50000', NULL, 15, 'employment', '2026-05-06 04:30:36', '2026-05-06 10:43:44'),
(49, 'worker_registration', 'education_level', 'Education Level', 'select', 0, 0, NULL, NULL, NULL, NULL, 16, 'employment', '2026-05-06 04:30:36', '2026-05-06 10:43:45'),
(50, 'worker_registration', 'experience_years', 'Experience (Years)', 'number', 0, 0, NULL, NULL, 'Years of experience', NULL, 17, 'employment', '2026-05-06 04:30:36', '2026-05-06 10:43:47'),
(51, 'worker_registration', 'cnic_front', 'CNIC Front Image', 'file', 0, 0, NULL, NULL, NULL, NULL, 18, 'documents', '2026-05-06 04:30:36', '2026-05-06 10:43:50'),
(52, 'worker_registration', 'cnic_back', 'CNIC Back Image', 'file', 0, 0, NULL, NULL, NULL, NULL, 19, 'documents', '2026-05-06 04:30:36', '2026-05-06 10:43:49');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `role_display` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `role_display`, `description`, `permissions`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'super_admin', 'Super Administrator', 'Full system access', '{\"all\": true}', 1, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(2, 'approver', 'Vendor Approver', 'Reviews vendor registrations', '{\"can_approve_vendors\": true, \"view_vendors\": true}', 1, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(3, 'vendor', 'Vendor', 'Registered company/supplier', '{\"can_add_workers\": true, \"view_own_profile\": true}', 1, '2026-05-06 04:30:36', '2026-05-06 04:30:36'),
(4, 'worker', 'Worker', 'Vendor employee', '{\"view_own_profile\": true}', 1, '2026-05-06 04:30:36', '2026-05-06 04:30:36');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `status` enum('pending','active','suspended','rejected') DEFAULT 'pending',
  `created_by` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `login_attempts` int(11) DEFAULT 0,
  `locked_until` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role_id`, `status`, `created_by`, `approved_by`, `approved_at`, `last_login`, `login_attempts`, `locked_until`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', 'admin@vms.local', '$2y$10$gVprkXL4wWqAuGkRJM3JA.YcRwJYQPDp1cdJdE5tG..PI48JrkwTq', 1, 'active', NULL, NULL, NULL, '2026-05-06 10:52:11', 0, NULL, '2026-05-06 04:30:36', '2026-05-06 10:52:11'),
(2, 'approver1', 'approver@vms.local', '$2y$10$gVprkXL4wWqAuGkRJM3JA.YcRwJYQPDp1cdJdE5tG..PI48JrkwTq', 2, 'active', 1, NULL, NULL, '2026-05-06 08:34:59', 0, NULL, '2026-05-06 04:30:36', '2026-05-06 08:34:59'),
(6, 'admin@vms.localnewvendor', 'newvendor@test.com', '$2y$10$8I.BGnSA1UxdHOWnaxi56OTQTWGyj1OFtNajDsEIEAZzYIF.bc3qi', 3, 'active', NULL, 2, '2026-05-06 05:10:06', '2026-05-06 10:45:36', 0, NULL, '2026-05-06 05:07:13', '2026-05-06 10:45:36'),
(7, 'perovibox', 'kuwonixude@mailinator.com', '$2y$10$oTCJsR/aVoNfz45SZglr8en4RAdnLpSJ5ugDmyoLgEUX2NJGQmilW', 3, 'pending', NULL, NULL, NULL, NULL, 0, NULL, '2026-05-06 06:41:03', '2026-05-06 06:41:03'),
(8, 'vupyt', 'nirinokevy@mailinator.com', '$2y$10$EODe.rDKiiXSQporiu5SvOgzot5Q1lfaLcRfWHipLMqr65sUzNyMK', 3, 'pending', NULL, NULL, NULL, NULL, 0, NULL, '2026-05-06 06:55:45', '2026-05-06 06:55:45'),
(9, 'testvendor123', 'testvendor@example.com', '$2y$10$OOVLHM13mhb8siorfk6jL.ND80QaJtcOXY9EhUQwKoUsZDBho4b9m', 3, 'active', NULL, 2, '2026-05-06 08:17:25', '2026-05-06 08:17:34', 1, NULL, '2026-05-06 08:08:00', '2026-05-06 10:44:45'),
(13, 'w_1111111111111', 'w_1111111111111@worker.vms.local', '$2y$10$o7o6fel2yWx6meMyeqvQU.ROtIRgk3XbtKuKRUk5Gps8pqEK70EeO', 4, 'active', 6, NULL, NULL, NULL, 0, NULL, '2026-05-06 11:01:44', '2026-05-06 11:01:44'),
(16, 'w_121564654', 'w_121564654@worker.vms.local', '$2y$10$vuDMYwvOjFuLH8xfHpMpF.83ZT7j386FhLK71Gb95IA7ZH5epuHI6', 4, 'active', 6, NULL, NULL, NULL, 0, NULL, '2026-05-06 11:15:10', '2026-05-06 11:15:10'),
(17, 'w_16456464', 'w_16456464@worker.vms.local', '$2y$10$5b8X2D4oxmSQ4ak1LSPEh.VuW0NbQ4ilr0XhLCuDWrthVWsbxbXKy', 4, 'active', 6, NULL, NULL, NULL, 0, NULL, '2026-05-06 11:15:10', '2026-05-06 11:15:10'),
(18, 'w_213213123', 'w_213213123@worker.vms.local', '$2y$10$pIEORNO6SwzTatiN5aXsaOtishXZeV1Oj7p88Rz8r.swnZWnxX.N6', 4, 'active', 6, NULL, NULL, NULL, 0, NULL, '2026-05-06 11:15:10', '2026-05-06 11:15:10'),
(19, 'w_23123412432', 'w_23123412432@worker.vms.local', '$2y$10$ksXTI2l/G8uEAGroOLThJ.ovzMuD5Cd1MIFjujPy3ZGu3pYvHsyh2', 4, 'active', 6, NULL, NULL, NULL, 0, NULL, '2026-05-06 11:15:10', '2026-05-06 11:15:10'),
(20, 'w_3563453254', 'w_3563453254@worker.vms.local', '$2y$10$f2DKYkWisSdcNKb4Ox3a5OY8CaHik2Jq7L9ayz3MijDilcVILlaoO', 4, 'active', 6, NULL, NULL, NULL, 0, NULL, '2026-05-06 11:15:10', '2026-05-06 11:15:10'),
(21, 'w_1634534534', 'w_1634534534@worker.vms.local', '$2y$10$COFrM5GU/c7RTrDYPnJmZuCnwbFKOaHVchTRpsyi4.PbhSsifd1MK', 4, 'active', 6, NULL, NULL, NULL, 0, NULL, '2026-05-06 11:15:10', '2026-05-06 11:15:10'),
(22, 'w_', 'w_@worker.vms.local', '$2y$10$hP.dxd1s3w8DNzmeNTcxxe7/sBeK8d9c/WstYmVlJ5c69eMxVEZmu', 4, 'active', 6, NULL, NULL, NULL, 0, NULL, '2026-05-06 11:18:22', '2026-05-06 11:18:22');

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_name` varchar(200) NOT NULL,
  `company_registration_no` varchar(100) DEFAULT NULL,
  `ntn_number` varchar(50) DEFAULT NULL,
  `strn_number` varchar(50) DEFAULT NULL,
  `company_type_id` int(11) NOT NULL,
  `company_subtype_id` int(11) DEFAULT NULL,
  `years_in_business` int(11) DEFAULT NULL,
  `number_of_employees` int(11) DEFAULT NULL,
  `annual_revenue` decimal(15,2) DEFAULT NULL,
  `primary_contact_name` varchar(150) NOT NULL,
  `primary_contact_phone` varchar(20) NOT NULL,
  `primary_contact_email` varchar(150) NOT NULL,
  `primary_contact_cnic` varchar(20) DEFAULT NULL,
  `secondary_contact_name` varchar(150) DEFAULT NULL,
  `secondary_contact_phone` varchar(20) DEFAULT NULL,
  `secondary_contact_email` varchar(150) DEFAULT NULL,
  `address_line1` varchar(255) NOT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `state_province` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'Pakistan',
  `bank_name` varchar(150) DEFAULT NULL,
  `bank_account_title` varchar(200) DEFAULT NULL,
  `bank_account_no` varchar(50) DEFAULT NULL,
  `bank_branch` varchar(150) DEFAULT NULL,
  `iban` varchar(50) DEFAULT NULL,
  `certifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`certifications`)),
  `business_description` text DEFAULT NULL,
  `registration_certificate` varchar(255) DEFAULT NULL,
  `ntn_certificate` varchar(255) DEFAULT NULL,
  `tax_certificate` varchar(255) DEFAULT NULL,
  `bank_statement` varchar(255) DEFAULT NULL,
  `company_profile_doc` varchar(255) DEFAULT NULL,
  `other_documents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`other_documents`)),
  `verification_status` enum('pending','under_review','verified','rejected') DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `reviewer_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`id`, `user_id`, `company_name`, `company_registration_no`, `ntn_number`, `strn_number`, `company_type_id`, `company_subtype_id`, `years_in_business`, `number_of_employees`, `annual_revenue`, `primary_contact_name`, `primary_contact_phone`, `primary_contact_email`, `primary_contact_cnic`, `secondary_contact_name`, `secondary_contact_phone`, `secondary_contact_email`, `address_line1`, `address_line2`, `city`, `state_province`, `postal_code`, `country`, `bank_name`, `bank_account_title`, `bank_account_no`, `bank_branch`, `iban`, `certifications`, `business_description`, `registration_certificate`, `ntn_certificate`, `tax_certificate`, `bank_statement`, `company_profile_doc`, `other_documents`, `verification_status`, `rejection_reason`, `reviewer_notes`, `created_at`, `updated_at`) VALUES
(3, 6, 'Alpha Systems', 'ALPHA-001', '', NULL, 1, 3, 0, 0, NULL, 'Ahmed Khan', '03001234567', 'ahmed@alpha.com', '', NULL, NULL, NULL, '45 Industrial Area', '', 'Islamabad', '', NULL, 'Pakistan', 'HBL', '', '', '', '', NULL, '', 'C:\\xampp\\htdocs\\VendorM/assets/uploads/vendor_docs/doc_69facc81a04d41.61261749.pdf', NULL, NULL, NULL, NULL, NULL, 'verified', NULL, '', '2026-05-06 05:07:13', '2026-05-06 05:10:06'),
(4, 7, 'Maddox and Fitzgerald Traders', 'Harris Schwartz Co', '141', NULL, 4, 19, 1989, 367, NULL, 'Aspen Ellis', '+1 (568) 181-7742', 'fame@mailinator.com', 'Ipsa et sint explic', NULL, NULL, NULL, '57 Cowley Street', 'Eiusmod est dolorem ', 'Sit eos placeat di', 'Et tenetur vel aliqu', NULL, 'Qui sed quo ut ipsa', 'Keegan Higgins', 'Fugiat tempora fugia', 'Dignissimos architec', 'Sed fugit quaerat a', 'Est provident at ad', NULL, 'Facilis illo quisqua', 'C:\\xampp\\htdocs\\VendorM/assets/uploads/vendor_docs/doc_69fae27f39e762.56506880.png', NULL, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2026-05-06 06:41:03', '2026-05-06 06:41:03'),
(5, 8, 'Hogan Bartlett LLC', 'Buchanan Wagner Traders', '406', NULL, 3, 13, 1977, 874, NULL, 'Solomon Santiago', '+1 (326) 711-4705', 'suwuvycyxy@mailinator.com', 'Reprehenderit ea no', NULL, NULL, NULL, '781 East Rocky Milton Avenue', 'Aspernatur irure omn', 'Officiis et esse est', 'Adipisicing quia ab ', NULL, 'Quaerat optio ipsum', 'Hollee Nicholson', 'Aut amet nihil dolo', 'Sed eiusmod eos qua', 'Atque in culpa iste ', 'Ad est quas ut minim', NULL, 'Dignissimos atque pr', 'C:\\xampp\\htdocs\\VendorM/assets/uploads/vendor_docs/doc_69fae5f11f7645.02018420.png', NULL, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, '2026-05-06 06:55:45', '2026-05-06 06:55:45'),
(6, 9, 'Mayo Dalton Trading', 'Randolph and Dudley Plc', '124', NULL, 2, 8, 2006, 830, NULL, 'new', '+1 (567) 547-5202', 'new@mailinator.com', 'Quia reprehenderit ', NULL, NULL, NULL, '184 West Fabien Road', 'Non velit non aute ', 'Et magnam voluptate ', 'Occaecat Nam cum ut ', NULL, 'Facere porro iste do', 'Gemma Bonner', 'Nulla commodo aliqui', '23333343535465', 'Ut sunt eos expedit', 'Qui eos enim ut quae', NULL, 'Et dolorum voluptate', 'C:\\xampp\\htdocs\\VendorM/assets/uploads/vendor_docs/doc_69faf6e0c04674.51810779.png', 'C:\\xampp\\htdocs\\VendorM/assets/uploads/vendor_docs/doc_69faf6e0c09110.73946207.png', 'C:\\xampp\\htdocs\\VendorM/assets/uploads/vendor_docs/doc_69faf6e0c0bae1.51979273.png', 'C:\\xampp\\htdocs\\VendorM/assets/uploads/vendor_docs/doc_69faf6e0c0e1f6.33684596.png', NULL, NULL, 'verified', NULL, '', '2026-05-06 08:08:00', '2026-05-06 08:17:25');

-- --------------------------------------------------------

--
-- Table structure for table `workers`
--

CREATE TABLE `workers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `cnic` varchar(20) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `nationality` varchar(100) DEFAULT 'Pakistani',
  `profile_photo` varchar(255) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `emergency_contact_name` varchar(150) DEFAULT NULL,
  `emergency_contact_phone` varchar(20) DEFAULT NULL,
  `emergency_relation` varchar(50) DEFAULT NULL,
  `designation` varchar(100) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `employee_code` varchar(50) DEFAULT NULL,
  `join_date` date NOT NULL,
  `employment_type` enum('permanent','contract','temporary','daily_wage') DEFAULT NULL,
  `monthly_salary` decimal(10,2) DEFAULT NULL,
  `education_level` enum('matric','intermediate','bachelor','master','phd','other') DEFAULT NULL,
  `skills` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`skills`)),
  `certifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`certifications`)),
  `experience_years` int(11) DEFAULT 0,
  `cnic_front` varchar(255) DEFAULT NULL,
  `cnic_back` varchar(255) DEFAULT NULL,
  `police_verification` varchar(255) DEFAULT NULL,
  `medical_certificate` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `deactivation_reason` text DEFAULT NULL,
  `deactivated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `workers`
--

INSERT INTO `workers` (`id`, `user_id`, `vendor_id`, `first_name`, `last_name`, `cnic`, `date_of_birth`, `gender`, `nationality`, `profile_photo`, `phone`, `email`, `address`, `emergency_contact_name`, `emergency_contact_phone`, `emergency_relation`, `designation`, `department`, `employee_code`, `join_date`, `employment_type`, `monthly_salary`, `education_level`, `skills`, `certifications`, `experience_years`, `cnic_front`, `cnic_back`, `police_verification`, `medical_certificate`, `is_active`, `deactivation_reason`, `deactivated_at`, `created_at`, `updated_at`) VALUES
(1, 13, 3, 'salman', '', '1111111111111', NULL, NULL, 'Pakistani', NULL, '12432532532', NULL, 'asfsadadsa', NULL, NULL, NULL, 'dsadasdasd', 'dsadadsasd', NULL, '2026-05-06', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-05-06 11:01:44', '2026-05-06 11:01:44'),
(4, 16, 3, 'asda', '', '121564654', NULL, NULL, 'Pakistani', NULL, '16456464', NULL, '4dsadsa', NULL, NULL, NULL, 'sadasd', 'sadads', NULL, '2026-05-06', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-05-06 11:15:10', '2026-05-06 11:15:10'),
(5, 17, 3, 'sdads', '', '16456464', NULL, NULL, 'Pakistani', NULL, '16456464', NULL, 'wsdaa', NULL, NULL, NULL, 'sadasd', 'dsadads', NULL, '2026-05-06', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-05-06 11:15:10', '2026-05-06 11:15:10'),
(6, 18, 3, 'asda', '', '213213123', NULL, NULL, 'Pakistani', NULL, '16456464', NULL, 'sdads', NULL, NULL, NULL, 'aasdasdas', 'adsadssa', NULL, '2026-05-06', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-05-06 11:15:10', '2026-05-06 11:15:10'),
(7, 19, 3, 'dsadsasd', '', '23123412432', NULL, NULL, 'Pakistani', NULL, '16456464', NULL, 'aadsads', NULL, NULL, NULL, 'dasdad', 'asdasda', NULL, '2026-05-06', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-05-06 11:15:10', '2026-05-06 11:15:10'),
(8, 20, 3, 'adsas', '', '3563453254', NULL, NULL, 'Pakistani', NULL, '16456464', NULL, 'dsadsa', NULL, NULL, NULL, 'dasdsadas', 'asdads', NULL, '2026-05-06', 'permanent', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-05-06 11:15:10', '2026-05-06 11:18:29'),
(9, 21, 3, 'dasdads', '', '1634534534', NULL, NULL, 'Pakistani', NULL, '16456464', NULL, 'sdads', NULL, NULL, NULL, 'dasdas', 'adsasdas', NULL, '2026-05-06', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-05-06 11:15:10', '2026-05-06 11:15:10'),
(10, 22, 3, 'sadads', '', 'asdads', NULL, NULL, 'Pakistani', NULL, 'asdasd', NULL, 'asdadsa', NULL, NULL, NULL, 'adsd', 'adsads', NULL, '2026-05-06', 'contract', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-05-06 11:18:22', '2026-05-06 11:18:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `approval_workflow`
--
ALTER TABLE `approval_workflow`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_user_id` (`vendor_user_id`),
  ADD KEY `approver_id` (`approver_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `company_subtypes`
--
ALTER TABLE `company_subtypes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_subtype` (`company_type_id`,`subtype_name`);

--
-- Indexes for table `company_types`
--
ALTER TABLE `company_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type_name` (`type_name`);

--
-- Indexes for table `form_fields_config`
--
ALTER TABLE `form_fields_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_field` (`form_type`,`field_name`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `company_type_id` (`company_type_id`),
  ADD KEY `company_subtype_id` (`company_subtype_id`);

--
-- Indexes for table `workers`
--
ALTER TABLE `workers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `cnic` (`cnic`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `approval_workflow`
--
ALTER TABLE `approval_workflow`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `company_subtypes`
--
ALTER TABLE `company_subtypes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `company_types`
--
ALTER TABLE `company_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `form_fields_config`
--
ALTER TABLE `form_fields_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `workers`
--
ALTER TABLE `workers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `approval_workflow`
--
ALTER TABLE `approval_workflow`
  ADD CONSTRAINT `approval_workflow_ibfk_1` FOREIGN KEY (`vendor_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `approval_workflow_ibfk_2` FOREIGN KEY (`approver_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `company_subtypes`
--
ALTER TABLE `company_subtypes`
  ADD CONSTRAINT `company_subtypes_ibfk_1` FOREIGN KEY (`company_type_id`) REFERENCES `company_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `vendors`
--
ALTER TABLE `vendors`
  ADD CONSTRAINT `vendors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vendors_ibfk_2` FOREIGN KEY (`company_type_id`) REFERENCES `company_types` (`id`),
  ADD CONSTRAINT `vendors_ibfk_3` FOREIGN KEY (`company_subtype_id`) REFERENCES `company_subtypes` (`id`);

--
-- Constraints for table `workers`
--
ALTER TABLE `workers`
  ADD CONSTRAINT `workers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `workers_ibfk_2` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
