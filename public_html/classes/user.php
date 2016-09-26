<?php
include('password.php');
class User extends Password{

    private $_db;

    function __construct($db){
    	parent::__construct();

    	$this->_db = $db;
    }

	private function get_user_hash($username_or_email){

		try {
			
			if (strpos($username_or_email, '@') !== false) {
				
				$stmt = $this->_db->prepare('SELECT password, email, userID FROM users WHERE email = :email AND active="Yes" ');
				$stmt->execute(array('email' => $username_or_email));
			} else {
				$stmt = $this->_db->prepare('SELECT password, username, userID FROM users WHERE username = :username AND active="Yes" ');
				$stmt->execute(array('username' => $username_or_email));
			}
			
			return $stmt->fetch();

		} catch(PDOException $e) {
		    echo '<p class="bg-danger">'.$e->getMessage().'</p>';
		}
	}

	public function login($username,$password){

		$row = $this->get_user_hash($username);

		if($this->password_verify($password,$row['password']) == 1){

		    $_SESSION['loggedin'] = true;
		    $_SESSION['username'] = $row['username'];
		    $_SESSION['userID'] = $row['userID'];
		    return true;
		}
	}

	public function logout(){
		session_destroy();
	}

	public function is_logged_in(){
		if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true){
			return true;
		}
	}

}

?>
