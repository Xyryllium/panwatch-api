<?php
class Records{
    //db connection and table
    private $conn;
	private $table = "contact_records";
	private $tableContactType = "contact_type";
	
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



    //constructor
    public function __construct($db){
        $this->conn = $db;
	}
	
	public function createRecord(){
		$addQuery = "";
		if(($this->type == 'person')) {
			$addQuery = "address = :address";
		}
        if(($this->type == 'establishment')){
            $addQuery = "hasSocialDistancing = :hasSocialDistancing , hasTemperatureCheck = :hasTemperatureCheck";
		}
		if(($this->type == 'event')){
			$addQuery = "hasSocialDistancing = :hasSocialDistancing , hasTemperatureCheck = :hasTemperatureCheck,
							attendees = :attendees";
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

		if(($this->type == 'person')) {
			$type = 1;
			$stmt->bindParam(':type', $type);
			$this->address = htmlspecialchars(strip_tags($this->address));
			$stmt->bindParam(':address', $this->address);
		}
		if(($this->type == "establishment")) {
			$type = 2;
			$stmt->bindParam(':type', $type);
			$this->hasSocialDistancing = htmlspecialchars(strip_tags($this->hasSocialDistancing));
			$this->hasTemperatureCheck = htmlspecialchars(strip_tags($this->hasTemperatureCheck));
			$stmt->bindParam(':hasSocialDistancing', $this->hasSocialDistancing);
			$stmt->bindParam(':hasTemperatureCheck', $this->hasTemperatureCheck);
		}
		if(($this->type == "event")) {
			$type = 3;
			$stmt->bindParam(':type', $type);
			$this->hasSocialDistancing = htmlspecialchars(strip_tags($this->hasSocialDistancing));
			$this->hasTemperatureCheck = htmlspecialchars(strip_tags($this->hasTemperatureCheck));
			$this->attendees = htmlspecialchars(strip_tags($this->attendees));
			$stmt->bindParam(':hasSocialDistancing', $this->hasSocialDistancing);
			$stmt->bindParam(':hasTemperatureCheck', $this->hasTemperatureCheck);
			$stmt->bindParam(':attendees', $this->attendees);
		}

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
					WHERE a.userId = ". $this->id." ";

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
	
	public function readStatistics(){
        $query = "SELECT COUNT(id) as stats FROM " . $this->table. " WHERE userId = "  . $this->id. " AND typeId = " . $this->type ." ";

        //prepare the query
        $stmt = $this->conn->prepare($query);

        //execute the query
        $stmt->execute();

        return $stmt;
    }

}