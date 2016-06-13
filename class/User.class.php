<?php

/**
 * The user class used for logging in and storing user information
 * @author Justin Pruskowski <racecorp5@gmail.com>
 * @version 0.9.1
 */
class User {

    /** 
     *  Unique userId
     *  @var int
     */
    public $userId;

    /** 
     *  Unique user Name
     *  @var string
     */
    public $userName;

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
     *  In the format of YYYY-MM-DD HH:MM
     *  @var string
     */
	private $created;

    /**
     *  Date the user last logged in 
     *  In the format of YYYY-MM-DD HH:MM
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
     *  @param PDO connection
     */
	function __construct($db) {
		if(!$db) return NULL;
		$this->db = $db;
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
     *
     *  Returns true on success or an error msg ifnil there are 0 or
     *  more than 1 student in the result set.
     */
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





    /** Creates a RtStudent from the given result set and alters the
     *  session to login that student.
     *
     *  Returns true on success or an error msg ifnil there are 0 or
     *  more than 1 student in the result set.
     *  
     * @param string $sql to select a single user 
     * @param string $prodUuid '', or uuid of product tree for session
     * @param string $dbo null for global $DBO, or any open DboMy 
     * @returns mixed true on success, error string on failure
     */
    static function sessionLogin($sql, $prodUuid='', $dbo=null) {
    }

    /** Creates a RtStudent from the given result set and alters the
     *  session to login that student.
     *
     *  Returns true on success or an error msg ifnil there are 0 or
     *  more than 1 student in the result set.
     *  
     * @param string $sql to select a single user 
     * @param string $prodUuid '', or uuid of product tree for session
     * @param string $dbo null for global $DBO, or any open DboMy 
     * @returns mixed true on success, error string on failure
     */
    static function sessionLogin($sql, $prodUuid='', $dbo=null) {
    }

    /** Creates a RtStudent from the given result set and alters the
     *  session to login that student.
     *
     *  Returns true on success or an error msg ifnil there are 0 or
     *  more than 1 student in the result set.
     *  
     * @param string $sql to select a single user 
     * @param string $prodUuid '', or uuid of product tree for session
     * @param string $dbo null for global $DBO, or any open DboMy 
     * @returns mixed true on success, error string on failure
     */
    static function sessionLogin($sql, $prodUuid='', $dbo=null) {
    }