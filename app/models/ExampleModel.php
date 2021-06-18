<?php

namespace App\Model;

use App\Config\Dbh;
use PDO;

class ExampleModel {
    // * Declare your parameters here: 
    // private $id;
    // private $param;

    private $db_conn;

    // * Define setters and getters for your params:
    // function setId($id) { $this->id = $id; }
    // function getId() { return $this->id; }
    // function setParam($param) { $this->param = $param; }
    // function getParam() { return $this->param; }

    // Connect to database on object creation
    public function __construct() {
        $db = new Dbh();
        $this->db_conn = $db->connect();
    }

    // * Define your methods below

    // public function create($table) {
    // 	$sql = "INSERT INTO $table (column, ...) VALUES (:param, ...)";
    // 	$stmt = $this->db_conn->prepare($sql);
    // 	$stmt->bindParam(':param', $this->param);	
    // 	if($stmt->execute()) {
    // 		return true;
    // 	} else {
    // 		return false;
    // 	}
    // }

    // public function read($table) {
    //     $sql = "SELECT * FROM $table";
    // 	$stmt = $this->db_conn->prepare($sql);
    //     if($stmt->execute()) {
    //         $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //         header ("Content-Type: application/json");
    //         echo json_encode($users,JSON_PRETTY_PRINT|JSON_NUMERIC_CHECK);    
    // 		return true;
    // 	} else {
    // 		return false;
    // 	}
    // }

    // public function update($table) {
    //     $sql = "UPDATE $table SET column = :param
    //     WHERE id = :id";
    // 	$stmt = $this->db_conn->prepare($sql);
    //     $stmt->bindParam(':id', $this->id);
    // 	$stmt->bindParam(':param', $this->param);	
    // 	if($stmt->execute()) {
    // 		return true;
    // 	} else {
    // 		return false;
    // 	}
    // }

    // public function delete($table) {
    //     $sql = "DELETE FROM $table WHERE id = :id";
    // 	$stmt = $this->db_conn->prepare($sql);
    // 	$stmt->bindParam(':id', $this->id);
    // 	if($stmt->execute()) {
    // 		return true;
    // 	} else {
    // 		return false;
    // 	}
    // }
}