<?php

/**
 * @file
 * User login form
 */
require_once 'includes/includes.php';
require_once 'class/User.class.php';

$user = new User($db);

if (isset($_POST['login'])) {
	$user->login();
} elseif (isset($_POST['register'])) {
	$user->register();
}

header('Location: /index.php');