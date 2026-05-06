<?php
session_start();
require_once __DIR__ . '/../classes/Auth.php';
$auth = new Auth();
$auth->logout();
header('Location: /VendorM/public/login.php');
exit;
