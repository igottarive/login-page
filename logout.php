<?php

/**
 * @file
 * User login form
 */
require 'includes/includes.php';

$user = new User($db);
$user->logout();
header('Location: /index.php');