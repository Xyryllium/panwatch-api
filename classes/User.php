<?php
class User{
    //db connection and table
    private $conn;
	private $table = "users";
	
    //object properties
	public $id;
	public $name;
	public $contactInfo;
	public $address;
    public $email;
    public $avatar;



    //constructor
    public function __construct($db){
        $this->conn = $db;
    }
    
    // public function generateUUID(){
    //         return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
    //             // 32 bits for "time_low"
    //             mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
        
    //             // 16 bits for "time_mid"
    //             mt_rand( 0, 0xffff ),
        
    //             // 16 bits for "time_hi_and_version",
    //             // four most significant bits holds version number 4
    //             mt_rand( 0, 0x0fff ) | 0x4000,
        
    //             // 16 bits, 8 bits for "clk_seq_hi_res",
    //             // 8 bits for "clk_seq_low",
    //             // two most significant bits holds zero and one for variant DCE1.1
    //             mt_rand( 0, 0x3fff ) | 0x8000,
        
    //             // 48 bits for "node"
    //             mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    //         );
    // }

    public function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
	
	public function createUser(){

        $query = "INSERT INTO " . $this->table . "
                    SET
                    name = :name,
                    mobileNumber = :contactInfo,
                    password = :password,
					address = :address,
                    email = :email,
                    hasTemporaryPassword = 1";

        //prepare the query
        $stmt = $this->conn->prepare($query);
        
        //sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
		$this->contactInfo = htmlspecialchars(strip_tags($this->contactInfo));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));

        //bind the value
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':contactInfo', $this->contactInfo);
		$stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
        

        //execute the query
        if($stmt->execute()){
            return true;
        }
        
        return false;
    }

    public function readUser(){
        $query = "SELECT * FROM " . $this->table. " WHERE email = '" . $this->email. "' ";

        //prepare the query
        $stmt = $this->conn->prepare($query);

        //execute the query
        $stmt->execute();

        return $stmt;
    }

    public function readUserInfo(){
        $query = "SELECT * FROM " . $this->table. " WHERE id = " . $this->id. " ";

        //prepare the query
        $stmt = $this->conn->prepare($query);

        //execute the query
        $stmt->execute();

        return $stmt;
    }
    
    public function readUserExist(){
        $query = "SELECT * FROM " . $this->table . "
                WHERE
                email = '" . $this->email. "'";

        //prepare the query
        $stmt = $this->conn->prepare($query);
        
        //execute the query
        $stmt->execute();

        return $stmt;
    }

    public function readUserPassword(){
        $query = "SELECT password FROM " . $this->table . " 
                WHERE id = :id";

        //prepare the query
        $stmt = $this->conn->prepare($query);
        
        //sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));

        //bind the value
        $stmt->bindParam(':id', $this->id);

        //execute the query
        $stmt->execute();

        return $stmt;
    }

    public function updatePassword(){
        $query = "UPDATE " . $this->table . "
                    SET password = :password,
                    hasTemporaryPassword = 0
                WHERE id = :id";

        //prepare the query
        $stmt = $this->conn->prepare($query);
        
        //sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->password = htmlspecialchars(strip_tags($this->password));

        //bind the value
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':password', $this->password);

        //execute the query
        $stmt->execute();

        return $stmt;
    }

    public function updateAvatar(){
        $query = "UPDATE " . $this->table . "
                    SET avatar = :avatar
                    WHERE
                    id = :id";

        //prepare the query
        $stmt = $this->conn->prepare($query);
        
        //sanitize
        $this->avatar = htmlspecialchars(strip_tags($this->avatar));
        $this->id = htmlspecialchars(strip_tags($this->id));

        //bind the value
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':avatar', $this->avatar);

        //execute the query
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    public function updateInfo(){
        $query = "UPDATE " . $this->table . "
                    SET name = :name,
                    mobileNumber = :contactInfo,
                    address = :address,
                    avatar = :avatar
                    WHERE
                    id = :id";

        //prepare the query
        $stmt = $this->conn->prepare($query);
        
        //sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->contactInfo = htmlspecialchars(strip_tags($this->contactInfo));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->avatar = htmlspecialchars(strip_tags($this->avatar));

        //bind the value
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':contactInfo', $this->contactInfo);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':avatar', $this->avatar);

        //execute the query
        if($stmt->execute()){
            return true;
        }

        return false;
    }
	
}