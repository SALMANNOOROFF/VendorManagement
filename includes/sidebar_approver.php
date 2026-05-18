<?php
// Approver navigation links — used by header.php
$appUrl = $appUrl ?? '/VendorM/public';
$navLinks = [
    ['label' => 'Dashboard', 'icon' => 'bi-grid-1x2-fill', 'href' => $appUrl.'/approver/dashboard.php'],
    ['label' => 'Pending', 'icon' => 'bi-hourglass-split', 'href' => $appUrl.'/approver/pending.php'],
    ['label' => 'Entry Requests', 'icon' => 'bi-door-open', 'href' => $appUrl.'/approver/entry_requests/list.php'],
    ['label' => 'History', 'icon' => 'bi-clock-history', 'href' => $appUrl.'/approver/history.php'],
];
?>
