<?php

/**
 * The user class used for logging in and storing user information
 * @author Justin Pruskowski <racecorp5@gmail.com>
 * @version 0.9.1
 */
class User {

    /** 
     *  Unique user Id
     *  @var int
     */
    public $id;

    /** 
     *  Unique user Name
     *  @var string
     */
    public $userName;

    /** 
     *  User's password
     *  @var string
     */
    public $password;

    /**
     *  User's email address
     *  @var string
     */
    public $email;

    /**
     *  User's first name
     *  @var string
     */
    public $firstName;

    /**
     *  User's last name 
     *  @var string
     */
    public $lastName;

    /**
     *  Date the user was created 
     *  In the format of YYYY-MM-DD HH:MM:SS
     *  @var string
     */
    private $created;

    /**
     *  Date the user last logged in 
     *  In the format of YYYY-MM-DD HH:MM:SS
     *  @var string
     */
    private $lastLogin;

    /**
     *  Database Connection
     *  @var PDO connection
     */
    private $db;

    /**
     *  Is the user logged in?
     *  @var bool
     */
    private $is_logged = false;

    /**
     *  Message to display to user
     *  @var array
     */
    private $msg = array();

    /**
     *  Error to log
     *  @var array
     */
    private $error = array();


    /** 
     *  Initial object values
     *  Will setup object with values of user id
     *  @param PDO connection
     *  @param user id
     */
    function __construct($db, $id = NULL) {
        if(!$db) return NULL;
        $this->db = $db;
        if($id) $this->get_user_info($id);
        $this->update_messages();
        return $this;
    }

    /**
     *  Check if the user is logged
     *  @returns array
     */
    public function is_logged() {
        return $this->is_logged;
    }

    /**
     *  Get info messages
     *  @returns array
     */
    public function get_message() {
        return $this->msg;
    }

    /**
     *  Get errors
     *  @returns array
     */
    public function get_error() {
        return $this->error;
    }

    /**
     * Copy error & info messages from $_SESSION to the user object
     * @todo move TO JS for a js error effect 
     */
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

    /**
     *  Try to log the user in
     *  Logs in a user or sets errors if there are problems
     */
    public function login() {
        if (!empty($_POST['userName']) && !empty($_POST['password'])) {
            $this->userName = $_POST['userName'];
            $this->password = md5($_POST['password']);
            $user = $this->verify_password();
            if ($user) {
                $this->get_user_info($user->id);

                session_regenerate_id(true);
                $_SESSION['id'] = session_id();
                $_SESSION['user_id'] = $this->id;
                $_SESSION['email'] = $this->email;
                $_SESSION['userName'] = $this->userName;
                $_SESSION['is_logged'] = true;
                $this->is_logged = true;

                //Update the lastLogin field
                $this->addLoginLog();

                // Set a cookie that expires in one week
                if (isset($_POST['remember'])) {
                    setcookie('userName', $this->userName, time() + 604800);
                }

                // To avoid resending the form on refreshing
                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit();

            } else {
                $this->error[] = 'Wrong username/password combination.';
            }
        } elseif (empty($_POST['userName'])) {
            $this->error[] = 'Username field was empty.';
        } elseif (empty($_POST['password'])) {
            $this->error[] = 'Password field was empty.';
        }
    }

    /**
     *  Register a new user
     *  Inserts a new user or sets errors if there are problems
     */
	public function register() {
		if (!empty($_POST['userName']) && !empty($_POST['password']) && !empty($_POST['passwordConfirm']
            && !empty($_POST['email']) && !empty($_POST['lastName']) && !empty($_POST['firstName']) )) {
			if ($_POST['password'] == $_POST['passwordConfirm']) {
                $query = $this->db->prepare('INSERT INTO users 
                    (userName, password, email, firstName, lastName, created)
                    VALUES ( :userName, :password, :email, :firstName, :lastName, :created)');
                $query->bindParam(':userName', $userName);
                $query->bindParam(':password', $password);
                $query->bindParam(':email', $email);
                $query->bindParam(':firstName', $firstName);
                $query->bindParam(':lastName', $lastName);
                $query->bindParam(':created', $created);

                $userName = $_POST['userName'];
				$password = md5($_POST['password']);
				$email = $_POST['email'];
				$firstName = $_POST['firstName'];
				$lastName = $_POST['lastName'];
				$created = date("Y-m-d H:i:s");

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
		} elseif (empty($_POST['userName'])) {
			$this->error[] = 'Username field was empty.';
		} elseif (empty($_POST['password'])) {
			$this->error[] = 'Password field was empty.';
		} elseif (empty($_POST['passwordConfirm'])) {
			$this->error[] = 'You need to confirm the password.';
		} elseif (empty($_POST['email'])) {
			$this->error[] = 'Email field was empty.';
		} elseif (empty($_POST['lastName'])) {
			$this->error[] = 'Last Name field was empty.';
		} elseif (empty($_POST['firstName'])) {
			$this->error[] = 'First Name field was empty.';
		}
	}

    /**
     *  Check if userName and password match
     *  @returns object with userid on success, null on fail
     */
    private function verify_password() {
        $query = $this->db->prepare('SELECT id FROM users
            WHERE userName = :userName AND password = :password');
        $query->bindParam(':userName', $this->userName);
        $query->bindParam(':password', $this->password);

        $query->execute();
        return $query->fetch(PDO::FETCH_OBJ);
    }

    /** 
     *  Log a user out and destroy their session
     */
    public function logout() {
        session_unset();
        session_destroy();
        $this->is_logged = false;
        setcookie('userName', '', time()-3600);
    }

    /**
     *  Get info about a user
     *
     *  Sets this properties to the results of the values in the db  
     */
    public function get_user_info($id) {
        $query = $this->db->prepare('SELECT * FROM users WHERE id = :id');
        $query->bindParam(':id', $id);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);

        //Set the fields in $this to the resulting db result
        foreach($result as $k => $v) $this->$k = $v;
    }

    /**
     *  set the lastLogindate as right now
     *
     *  Sets this properties to the results of the values in the db  
     */
    public function addLoginLog() {
        $query = $this->db->prepare('UPDATE users SET lastLogin = :lastLogin
            WHERE id = :id');
        $query->bindParam(':lastLogin', $lastLogin);
        $query->bindParam(':id', $id);
        $lastLogin = date("Y-m-d H:i:s");
        $id = $this->id;
        if (!$query->execute()) {
			$this->error[] = 'There was a problem setting the last login.';
        }
    }

    /** 
     *  Print info messages on screen
     */
    public function display_info() {
        foreach ($this->msg as $msg) {
            echo '<p class="msg">' . $msg . '</p>';
        }
    }

    /** 
     *  Print errors messages on screen
     */
    public function display_errors() {
        foreach ($this->error as $error) {
            echo '<p class="error">' . $error . '</p>';
        }
    }
}
?>
