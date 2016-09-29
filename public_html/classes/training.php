<?php
class Training{

    private $id;
	private $user_id;
	private $date;
	private $time;
	private $start_location;
	private $end_location;
	private $waypoints;
	private $start_address;
	private $distance;
	private $type;
	private $default_speed;
	private $parent_session;
	private $join_location;
	private $stop_location;
	

    function __construct(){
    	//echo " - A NEW TRAING CREATED";
    }
	
	public function __toString()
    {
        return "id: " . $this->id . "</br>nuser_id: " . $this->user_id . "</br>date: " . $this->date . "</br>time: " . $this->time . "</br>startPos: " . $this->start_location . "</br>endPos: " . $this->end_location . "</br>waypoint: " . $this->waypoints . "</br>StartAdress: " . $this->start_address . "</br>Dist: " . $this->distance . "</br>type: " . $this->type . "</br>ts: " . "</br>Default speed: " . $this->default_speed . "</br>parent_session: " . $this->parent_session . "</br>join_location: " . $this->join_location . "</br>stop_location: " . $this->stop_location ."</br>ts: " . $this->ts . "</br></br>";
    }
	
	public function getID(){
		return $this->id;	
	}
	
	public function getDate(){
	
		return $this->date . " " . $this->time;	
	}
	
	public function getStartAdress(){
		
		$stringArray = explode(',', $this->start_address);
		$cityString = substr($stringArray[1], 8);
		$returnArray = array($stringArray[0], $cityString);
		
		return $returnArray;
		
	}
	
	public function getAllPoints(){
		return array($this->start_location, $this->waypoints, $this->end_location);
	}
	
	public function getDateOnly(){
		return $this->date;	
	}
	
	public function getTime(){
		return substr($this->time, 0, -3);	
	}
	
	public function getType(){
		return $this->type;	
	}
	
	public function getUserID(){
		return $this->user_id;
		
	}
	
	public function getDistance(){
		return $this->distance;	
	}
	public function getDefaultSpeed(){
		return $this->default_speed;	
	}
	public function getJoinLocation(){
		return $this->join_location;
	}
	public function getStopLocation(){
		return $this->stop_location;	
	}
	
	public function getParent(){
		return $this->parent_session;	
	}

}

?>
