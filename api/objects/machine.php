<?php
class Machine{

    // database connection and name of tbale
    private $conn;
    private $table_name = "machines";

    // object properties
    public $id; // foreign key
    public $name;
    public $requester;
    public $groups;
    public $time_requested;
    public $time_completed;
    public $isComplete;
    public $isAccountedFor;

    // constructor
    public function __construct($db){
        $this->$conn = $db;
    }
    // read all
    function read(){
        // define once DB is configured
        $query = "QUERY TO DB";

        $stmt = $this->conn->prepare($query)
        $stmt->execute();
        return $stmt;
    }

    // new entry to db
    function create(){
      // query to insert record
      $query = "QUERY TO JOIN DB"

      $stmt=$this->conn->prepare($query);
      // sanitize potential entry
      $this->id=htmlspecialchars(strip_tags($this->id));
      $this->name=htmlspecialchars(strip_tags($this->name));
      $this->requester=htmlspecialchars(strip_tags($this->requester));
      $this->time_requested=htmlspecialchars(strip_tags($this->time_requested));
      $this->isComplete=htmlspecialchars(strip_tags($this->isComplete));
      $this->isAcountedFor=htmlspecialchars(strip_tags($this->isAccountedFor));
      $this->groups=htmlspecialchars(strip_tags($this->groups));
      // bind entries
      $stmt->bindParam(":id", $this->id);
      $stmt->bindParam(":name", $this->name);
      $stmt->bindParam(":requester", this->requester);
      $stmt->bindParam(":time_requested", this->time_requested);
      $stmt->bindParam(":isComplete", this->isComplete);
      $stmt->bindParam(":isAccountedFor", this->isAccountedFor);
      $stmt->bindParam(":groups", this->groups);
      // check failure
      if($stmt->execute()){
        return true;
      }
      return false;
    }
    // read one entry
    function readOne(){
        // query to read one record
        $query = "QUERY TO READ ONE RECORD";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1,this->id);
        $stmt->execute();

        $row = $stmt0>fetch(PDO::FETCH_ASSOC);

        $this->id = $row['id'];
        $this->name = $row['name'];
        $this->requester = $row['requester'];
        $this->time_requested = $row['time_requested'];
        $this->isComplete = $row['isComplete'];
        $this->isAccountedFor = $row['isAccountedFor'];
        $this->groups = $row['groups'];

    }
    // update entry
    function update(){
        $query = "UPDATE QUERY"

        $stmt = $this->conn->prepare($query);
        // sanitize
        $this->id=htmlspecialchars(strip_tags($this->id));
        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->requester=htmlspecialchars(strip_tags($this->requester));
        $this->time_requested=htmlspecialchars(strip_tags($this->time_requested));
        $this->isComplete=htmlspecialchars(strip_tags($this->isComplete));
        $this->isAcountedFor=htmlspecialchars(strip_tags($this->isAccountedFor));
        $this->groups=htmlspecialchars(strip_tags($this->groups));
        // bind
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":requester", this->requester);
        $stmt->bindParam(":time_requested", this->time_requested);
        $stmt->bindParam(":isComplete", this->isComplete);
        $stmt->bindParam(":isAccountedFor", this->isAccountedFor);
        $stmt->bindParam(":groups", this->groups);

        // check for failure
        if($stmt->execute()){
            return true;
        }
        return false;
    }

}
