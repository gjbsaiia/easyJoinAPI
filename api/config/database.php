<?php
class Database{

    // there has to be a more secure way to do this
    private $host = "localhost";
    private $db_name = "machine_base";
    private $user = "user";
    private $pwd = "!R3usabl3_Int3rn_Work!"; 
    public $conn;

    // establish db connection
    public function getConnection(){

        $this->conn = null;

        try{
            $this->conn = new PDO("mysql:host=".$this->host.";dbname=".$this->db_name,$this.user,$this->pwd);
            $this->conn->exec("set names utf8");
        }
        catch(PDOException $exception){
            echo "Connection error: ".$exception->getMessege();
        }

        return $this->conn;
    }
}
?>