<?php

/**
 * @file
 * The user class used for logging in and storing user information
 */

class User {

	//id field
	public $id;

	//username field
	public $username;

	//Email field
	public $email;

	//firstName field
	public $firstName;

	//lastName field
	public $lastName;

	//created field
	private $created;

	//lastLogin field
	private $lastLogin;

	//Database connection
	private $db;

	//Has user been logged in?
	private $is_logged = false;

	//Message to display to user
	private $msg = array();

	//Error to log
	private $error = array();

	// Create a new user object
	public function __construct($db) {
		if(!$db) return NULL;
		$this->db = $db;
		$this->update_messages();
		return $this;
	}

	// Get username
	public function get_username() { return $this->username; }

	// Get email
	public function get_email() { return $this->email; }

	// Check if the user is logged
	public function is_logged() { return $this->is_logged; }

	// Get info messages
	public function get_info() { return $this->msg; }

	// Get errors
	public function get_error() { return $this->error; }


	/* MOVE TO JS for a js error effect */
	// Copy error & info messages from $_SESSION to the user object
	private function update_messages() {
		if (isset($_SESSION['msg']) && $_SESSION['msg'] != '') {
			$this->msg = array_merge($this->msg, $_SESSION['msg']);
			$_SESSION['msg'] = '';
		}
		if (isset($_SESSION['error']) && $_SESSION['error'] != '') {
			$this->error = array_merge($this->error, $_SESSION['error']);
			$_SESSION['error'] = '';
		}
	}

	// Login
	public function login() {
		if (!empty($_POST['username']) && !empty($_POST['password'])) {
			$this->username = $_POST['username'];
			$this->password = md5($_POST['password']);
			$user = $this->verify_password();
			if ($user) {
				$this->get_user_info($user->id);

				session_regenerate_id(true);
				$_SESSION['id'] = session_id();
				$_SESSION['user_id'] = $this->id;
				$_SESSION['email'] = $this->email;
				$_SESSION['username'] = $this->username;
				$_SESSION['is_logged'] = true;
				$this->is_logged = true;

				// Set a cookie that expires in one week
				if (isset($_POST['remember'])) {
					setcookie('username', $this->username, time() + 604800);
				}

				// To avoid resending the form on refreshing
				header('Location: ' . $_SERVER['REQUEST_URI']);
				exit();

			} else {
				$this->error[] = 'Wrong username/password combination.';
			}
		} elseif (empty($_POST['username'])) {
			$this->error[] = 'Username field was empty.';
		} elseif (empty($_POST['password'])) {
			$this->error[] = 'Password field was empty.';
		}
	}

	// Check if username and password match
	// Returns id of the user it found
	private function verify_password() {
		$query = $this->db->prepare('SELECT id FROM users
			WHERE username = :username AND password = :password');
		$query->bindParam(':username', $this->username);
		$query->bindParam(':password', md5($this->password));

		$query->execute();
		return  $query->fetch(PDO::FETCH_OBJ);
	}

	// Logout function
	public function logout() {
		session_unset();
		session_destroy();
		$this->is_logged = false;
		setcookie('username', '', time()-3600);
	}

	// Register a new user
	public function register() {
		if (!empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['passwordConfirm'])) {
			if ($_POST['password'] == $_POST['passwordConfirm']) {
                $query = $this->db->prepare('INSERT INTO users (username, password, email)
                VALUES ( :username, :password, :email)');
                $query->bindParam(':username', $username);
                $query->bindParam(':password', $password);
                $query->bindParam(':email', $email);

                $username = $_POST['username'];
				$password = md5($_POST['password']);
				$email = $_POST['email'];

				if ($query->execute()) {
					$this->msg[] = 'User created.';
					$_SESSION['msg'] = $this->msg;

					// To avoid resending the form on refreshing
					header('Location: ' . $_SERVER['REQUEST_URI']);
					exit();
				} else {
					$this->error[] = 'Username already exists.';
				}
			} else {
				$this->error[] = 'Passwords don\'t match.';
			}
		//Empty fields
		} elseif (empty($_POST['username'])) {
			$this->error[] = 'Username field was empty.';
		} elseif (empty($_POST['password'])) {
			$this->error[] = 'Password field was empty.';
		} elseif (empty($_POST['passwordConfirm'])) {
			$this->error[] = 'You need to confirm the password.';
		}

	}

	// Get info about a user
	public function get_user_info($id) {
		$query = $this->db->prepare('SELECT * FROM users WHERE id = :id');
		$query->bindParam(':id', $id);
		$query->execute();
		$result = $query->fetch(PDO::FETCH_OBJ);

		//Set the fields in $this to the resulting db result
		foreach($result as $k => $v) $this->$k = $v;

		print_r($result);
		return;
	}

	// Get all the existing users
	public function get_users() {
		$query = $this->db->prepare('SELECT * FROM users');
		$query->execute();
		return $query;
	}

	// Print info messages in screen
	public function display_info() {
		foreach ($this->msg as $msg) {
			echo '<p class="msg">' . $msg . '</p>';
		}
	}

	// Print errors in screen
	public function display_errors() {
		foreach ($this->error as $error) {
			echo '<p class="error">' . $error . '</p>';
		}
	}
}

?>