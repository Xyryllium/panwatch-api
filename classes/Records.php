<?php
class Records{
    //db connection and table
    private $conn;
	private $table = "contact_records";
	private $tableContactType = "contact_type";
	private $tableAttendees= "event_attendees";
	
    //object properties
	public $id;
	public $type;
	public $location;
	public $dateContacted;
	public $timeContacted;
	public $hasFacemask;
	public $hasFaceshield;
	public $duration;
	public $contactInfo;
	public $address;
	public $hasTemperatureCheck;
	public $hasSocialDistancing;
	public $attendees;
	public $limit;
	public $event_id;



    //constructor
    public function __construct($db){
        $this->conn = $db;
	}
	
	public function createRecord(){
		$addQuery = "";
		if(($this->type == 'person')) {
			$addQuery = "address = :address";
		}
        if(($this->type == 'establishment' || $this->type == 'event')){
            $addQuery = "hasTemperatureCheck = :hasTemperatureCheck";
		}
        $query = "INSERT INTO " . $this->table . "
                    SET
                    name = :name,
					location = :location,
					userId = :id,
					dateContacted = :dateContacted,
					timeContacted = :timeContacted,
					hasFacemask = :hasFacemask,
					hasFaceshield = :hasFaceshield,
					duration = :duration,
					contactInfo = :contactInfo,
					typeId = :type,
					hasSocialDistancing = :hasSocialDistancing,
					{$addQuery}";

        //prepare the query
        $stmt = $this->conn->prepare($query);
        
        //sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
		$this->location = htmlspecialchars(strip_tags($this->location));
		$this->id = htmlspecialchars(strip_tags($this->id));
		$this->dateContacted = htmlspecialchars(strip_tags($this->dateContacted));
		$this->timeContacted = htmlspecialchars(strip_tags($this->timeContacted));
		$this->hasFacemask = htmlspecialchars(strip_tags($this->hasFacemask));
		$this->hasFaceshield = htmlspecialchars(strip_tags($this->hasFaceshield));
		$this->duration = htmlspecialchars(strip_tags($this->duration));
		$this->contactInfo = htmlspecialchars(strip_tags($this->contactInfo));
		$this->type = htmlspecialchars(strip_tags($this->type));
		$this->hasSocialDistancing = htmlspecialchars(strip_tags($this->hasSocialDistancing));

		//bind the value
        $stmt->bindParam(':name', $this->name);
		$stmt->bindParam(':location', $this->location);
		$stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':dateContacted', $this->dateContacted);
        $stmt->bindParam(':timeContacted', $this->timeContacted);
        $stmt->bindParam(':hasFacemask', $this->hasFacemask);
        $stmt->bindParam(':hasFaceshield', $this->hasFaceshield);
        $stmt->bindParam(':duration', $this->duration);
		$stmt->bindParam(':contactInfo', $this->contactInfo);
		$stmt->bindParam(':hasSocialDistancing', $this->hasSocialDistancing);

		if(($this->type == 'person')) {
			$type = 1;
			$stmt->bindParam(':type', $type);
			$this->address = htmlspecialchars(strip_tags($this->address));
			$stmt->bindParam(':address', $this->address);
			
		}
		if(($this->type == "establishment")) {
			$type = 2;
			$stmt->bindParam(':type', $type);
			$this->hasTemperatureCheck = htmlspecialchars(strip_tags($this->hasTemperatureCheck));
			$stmt->bindParam(':hasTemperatureCheck', $this->hasTemperatureCheck);
		}
		if(($this->type == "event")) {
			$type = 3;
			$stmt->bindParam(':type', $type);
			$this->hasTemperatureCheck = htmlspecialchars(strip_tags($this->hasTemperatureCheck));
			$stmt->bindParam(':hasTemperatureCheck', $this->hasTemperatureCheck);
		}

        //execute the query
        if($stmt->execute()){
            return true;
		}
        return false;
	}
	
	public function createAttendees(){

        $query = "INSERT INTO " . $this->tableAttendees . "
                    SET
                    name = :name,
                    user_id = :id,
                    event_id = :event_id";

        //prepare the query
        $stmt = $this->conn->prepare($query);
        
        //sanitize
        $this->attendees = htmlspecialchars(strip_tags($this->attendees));
		$this->id = htmlspecialchars(strip_tags($this->id));
        $this->event_id = htmlspecialchars(strip_tags($this->event_id));

        //bind the value
        $stmt->bindParam(':name', $this->attendees);
        $stmt->bindParam(':id', $this->id);
		$stmt->bindParam(':event_id', $this->event_id);
        

        //execute the query
        if($stmt->execute()){
            return true;
        }
        
        return false;
    }

    public function readRecords(){
		$query = "SELECT a.id as _id, 
					a.name, 
					a.location, 
					a.dateContacted, 
					a.timeContacted,
					a.duration as timeContactedEnded,
					b.type 
					FROM " . $this->table. " as a LEFT JOIN " . $this->tableContactType. " as b
					ON a.typeId = b.id
					WHERE a.userId = ". $this->id." ORDER BY DATE(dateContacted) DESC, timeContacted DESC ";

        //prepare the query
        $stmt = $this->conn->prepare($query);

        //execute the query
        $stmt->execute();

        return $stmt;
	}
	
	public function readSpecificRecords(){
        $query = "SELECT a.id as _id, 
					a.name, 
					a.location, 
					a.dateContacted, 
					a.timeContacted,
					a.duration as timeContactedEnded,
					b.type FROM " . $this->table. " as a LEFT JOIN " . $this->tableContactType. " as b
					ON a.typeId = b.id WHERE a.userId = "  . $this->id. " AND b.type = " . $this->type ." ";

        //prepare the query
        $stmt = $this->conn->prepare($query);

        //execute the query
        $stmt->execute();

        return $stmt;
	}

	public function readLastRecord(){
		$query = "SELECT a.id as _id, 
					a.name, 
					a.location, 
					a.dateContacted, 
					a.timeContacted,
					b.type 
					FROM " . $this->table. " as a LEFT JOIN " . $this->tableContactType. " as b
					ON a.typeId = b.id
					WHERE a.userId = ". $this->id." ORDER BY a.id DESC LIMIT " . $this->limit. " ";

        //prepare the query
        $stmt = $this->conn->prepare($query);

        //execute the query
        $stmt->execute();

        return $stmt;
	}

	public function readNameOfAttendees(){
		$query = "SELECT c.name
					FROM " . $this->table. " as a 
					LEFT JOIN " . $this->tableAttendees ." as c ON a.id = c.event_id
					WHERE a.userId = ". $this->id ." and c.event_id = ". $this->event_id ." ";

        //prepare the query
        $stmt = $this->conn->prepare($query);

        //execute the query
        $stmt->execute();

        return $stmt;
	}
	
	public function readStatistics(){
        $query = "SELECT COUNT(id) as stats FROM " . $this->table. " WHERE userId = "  . $this->id. " AND typeId = " . $this->type ." ";

        //prepare the query
        $stmt = $this->conn->prepare($query);

        //execute the query
        $stmt->execute();

        return $stmt;
	}
	
	public function readStatisticsInAttendees(){
        $query = "SELECT COUNT(id) as attendees FROM " . $this->tableAttendees. " WHERE user_id = "  . $this->id. "";

        //prepare the query
        $stmt = $this->conn->prepare($query);

        //execute the query
        $stmt->execute();

        return $stmt;
    }

}