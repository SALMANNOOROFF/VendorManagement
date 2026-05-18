<?php
// Vendor navigation links — used by header.php
$appUrl = $appUrl ?? '/VendorM/public';
$navLinks = [
    ['label' => 'Dashboard', 'icon' => 'bi-grid-1x2-fill', 'href' => $appUrl.'/vendor/dashboard.php'],
    ['label' => 'Profile', 'icon' => 'bi-building', 'href' => $appUrl.'/vendor/profile.php'],
    ['label' => 'Documents', 'icon' => 'bi-folder2-open', 'href' => $appUrl.'/vendor/documents.php'],
    ['label' => 'Workers', 'icon' => 'bi-people-fill', 'href' => $appUrl.'/vendor/workers/list.php', 'match' => 'workers'],
    ['label' => 'Entry Requests', 'icon' => 'bi-shield-check', 'href' => $appUrl.'/vendor/entry/list.php', 'match' => '/entry/'],
];
?>
