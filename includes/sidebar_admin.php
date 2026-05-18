<?php
// Admin navigation links — used by header.php
$appUrl = $appUrl ?? '/VendorM/public';
$navLinks = [
    ['label' => 'Dashboard', 'icon' => 'bi-grid-1x2-fill', 'href' => $appUrl.'/admin/dashboard.php'],
    ['label' => 'Vendors', 'icon' => 'bi-building', 'href' => $appUrl.'/admin/vendors/list.php', 'match' => 'vendors'],
    ['label' => 'Users', 'icon' => 'bi-people-fill', 'href' => $appUrl.'/admin/users/list.php', 'match' => 'users'],
    ['label' => 'Form Fields', 'icon' => 'bi-sliders', 'href' => $appUrl.'/admin/form_config/manage.php', 'match' => 'form_config'],
    ['label' => 'Company Types', 'icon' => 'bi-tags-fill', 'href' => $appUrl.'/admin/company_types/list.php', 'match' => 'company_types'],
    ['label' => 'Roles', 'icon' => 'bi-shield-lock-fill', 'href' => $appUrl.'/admin/roles/list.php', 'match' => 'roles'],
    ['label' => 'Audit Logs', 'icon' => 'bi-clock-history', 'href' => $appUrl.'/admin/audit_logs.php'],
];
?>
