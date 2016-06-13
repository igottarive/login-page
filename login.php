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
	if($user->get_error()) {
		$user->display_errors();
	} else {
		header('Location: /index.php');
		exit();
	}
} elseif (isset($_POST['register'])) {
	$user->register();
	if($user->get_error()) {
		$user->display_errors();
	} else {
		header('Location: /index.php');
		exit();
	}
}

//Show the login page form if not logging in or registering
require 'template/login.html';
?>