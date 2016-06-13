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

    /**
     *  Check if username and password match
     *  @returns object with userid on success, null on fail
     */
    private function verify_password() {
        $query = $this->db->prepare('SELECT id FROM users
            WHERE username = :username AND password = :password');
        $query->bindParam(':username', $this->username);
        $query->bindParam(':password', md5($this->password));

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
        setcookie('username', '', time()-3600);
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
