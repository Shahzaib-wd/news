<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

session_name(SESSION_NAME);
session_start();
session_destroy();

redirect(BASE_URL . '/admin/login.php');
