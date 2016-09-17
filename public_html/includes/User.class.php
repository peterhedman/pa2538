<?php

class User{
	
	// variables
	var $id;
	var $email;
	var $rank;
	var $last_login;
	var $token;
	var $token_validity;
	
	public function __construct() {
        $get_arguments       = func_get_args();
        $number_of_arguments = func_num_args();

        if (method_exists($this, $method_name = '__construct'.$number_of_arguments)) {
            call_user_func_array(array($this, $method_name), $get_arguments);
        }
		
		if($number_of_arguments == 0)
		{
			$id = $_SESSION['loginid'];
			$this->loadByID($id);
		}
    }

    public function __construct1($id) {
		$this->loadByID($id);
	}

    public function __construct2($id = null, $email) {
        $this->loadByEmail( $email );
    }

    public function __construct3($id = null, $email = null, $token) {
        $this->loadByToken( $token );
    }
	
	// Load from db functions
	protected function loadByID( $id ) {
    	// do query
		global $db;
    	$row = $db -> select("SELECT * FROM `reg_users` WHERE `id`=" . $id);
		$this->fill($row);
	}
	
	protected function loadByEmail( $email ) {
    	// do query
		global $db;
    	$row = $db -> select("SELECT * FROM `reg_users` WHERE `email`=" . $email);
    	$this->fill($row);
    }

   	protected function loadByToken( $token ) {
    	// do query
		global $db;
    	$row = $db -> select("SELECT * FROM `reg_users` WHERE `token`=" . $token . " AND `token_validity` > NOW()");
    	$this->fill($row);
    }
	
	// sets the parameters in User Object
    protected function fill( $rows ) {
		 
		 if (is_array($rows)) {
			
			foreach($rows as $row){
			
				$this->id = $row["id"];
				$this->email = $row["email"];
				$this->rank = $row["rank"];
				$this->last_login = $row["last_login"];
				$this->token = $row["token"];
				$this->token_validity = $row["token_validity"];	
			
			}
		 }
		
    }
	
	/*
	public function __toString()
	{
		return "getId: ".$this->getId() . "getEmail: ".$this->getEmail(). " getRank: ".$this->getRank()." getLast_login: " . $this->getLast_login() . " getToken: " . $this->getToken() . " getToken_validity: " . $this->getToken_validity();
		
	}*/
	
	// Returns the token
	public function getToken(){
		return $this->token;
	}
	
		
	/**
	 * Login this user
	 */
	
	public function login(){
		global $db;
		// Mark the user as logged in
		$_SESSION['loginid'] = $this->id;
		
		$result = $db -> query("UPDATE `reg_users` SET `last_login`=NOW() WHERE `email`='".$this->email . "'");
	
	}
	
	/**
	 * Destroy the session and logout the user.
	 */
	
	public function logout(){
		$_SESSION = array();
		unset($_SESSION);
	}
	
	/**
	 * Check whether the user is logged in.
	 */
	
	
	public function loggedIn(){
		return isset($this->id) && $_SESSION['loginid'] == $this->id;
	}
	
	/**
	 * Check whether the user is an administrator
	 */
	
	public function isAdmin(){
		return $this->rank == 'administrator';
	}
	
	/**
	 * Find the type of user. It can be either admin or regular.
	 */
	
	public function rank(){
		if($this->rank == 1){
			return 'administrator';
		}
	
		return 'regular';
	}
	
	
	/**
	* Generates a new SHA1 login token, writes it to the database and returns it.
	*/

	public function generateToken(){
		// generate a token for the logged in user. Save it to the database.
		global $db;
		
		$token = sha1($this->email.time().rand(0, 1000000));
		
		// Save the token to the database, 
		// and mark it as valid for the next 10 minutes only
		$result = $db -> query("UPDATE `reg_users` SET `token`='".$token."', `token_validity`=ADDTIME(NOW(),'0:10') WHERE `email`='". $this->email."'");
		
		//error_log("$token: " . $token . " $this->email: " . $this->email);	
		//error_log("resultGenerate: " . json_encode($result));	
		//error_log("mysqlError: " . $db -> error);	
		
		
		if($result)
		{
			return $token;
		}
		
		return "00000";
	}
	
	public function create_logged_in_ip_log($ip){
		global $db;
		// Create a new record in the login attempt table
		$result = $db -> query("INSERT INTO `reg_logged_ip` (`ip`, `email`) VALUES ('" . sprintf("%u", ip2long($ip)) . "', '" . $this->email . "') ON DUPLICATE KEY UPDATE `ts`=NOW()");
	}
	
	
}

?>