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

    function read(){

        // define once DB is configured
        $query = "QUERY TO DB";

        $stmt = $this->conn->prepare($query)
        $stmt->execute();
        return $stmt;
    }

}