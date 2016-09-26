<?php

class User{
	
	// variables
	var $id;
	var $email;
	var $rank;
	var $last_login;
	var $token;
	var $token_validity;
	var $prefered_ip;
	
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
				$this->prefered_ip = $row["prefered_ip"];
			
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
		return $this->rank == 9;
	}
	
	/**
	 * Find the type of user. It can be either admin or regular.
	 */
	
	public function rank(){
		if($this->rank == 9){
			return 'administrator';
		} else if($this->rank == 2){
			return 'advanced runner';
		}else if($this->rank == 1){
			return 'medium runner';
		}
		
		return 'easy runner';
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
		/*
		INSERT INTO daily_events (created_on, last_event_id, last_event_created_at)
  VALUES ('2010-01-19', 23, '2010-01-19 10:23:11')
ON DUPLICATE KEY UPDATE
  last_event_id = IF(last_event_created_at < VALUES(last_event_created_at), VALUES(last_event_id), last_event_id);
		*/
		
		$result = $db -> query("INSERT INTO `reg_logged_ip` (`ip`, `email`, `uniquekey`) VALUES ('" . sprintf("%u", ip2long($ip)) . "', '" . $this->email . "', '" . sprintf("%u", ip2long($ip)) . "-" . $this->email . "') ON DUPLICATE KEY UPDATE `ts`= NOW()");
		
	}
	
	public function get_logged_ipadresses(){
		global $db;
		
		$result =  $db -> select("SELECT * FROM `reg_logged_ip` WHERE `email`='" . $this->email . "' ORDER BY `ts` DESC");	
		
		return $result;
	}
	
	public function remove_user_ip($unique_key){
		global $db;
		$result =  $db -> query("DELETE FROM `reg_logged_ip` WHERE `uniquekey` ='".$unique_key."'");	
		return $result;
	}
	
	public function remove_user(){
		global $db;
		$result =  $db -> query("DELETE `reg_users`, `reg_logged_ip` FROM `reg_users` INNER JOIN `reg_logged_ip` WHERE `reg_users`.`email` = `reg_logged_ip`.`email` AND `reg_users`.`email` ='".$this->email."'");	
		return $result;
	}
	
	public function set_prefered_ip($ip_choise){
		global $db;
		
		$result = $db -> query("UPDATE `reg_users` SET `prefered_ip`='".$ip_choise."' WHERE `email`='". $this->email."'");
		
		return $result;
	}
	
	
}

?>