<?php
/**
 * @file
 * Includes for all php pages
 */

session_start();
echo "includes.php";print_r($_SESSION);

require_once 'class/User.class.php';
try {
	$db = new PDO('sqlite:/tmp/users.db'); //success
} catch (PDOException $e) {
	echo 'Connection failed: ' . $e->getMessage();
	exit;
}

?>