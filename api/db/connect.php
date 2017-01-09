<?php

class dbConnect {

  private $conn;

  function __construct() {        
  }
  /**
   * Establishing database connection
   * @return database connection handler
   */
  function connect() {
    include_once 'config.php';
    try {
    // Connecting...
    $this->conn = new PDO(DSN, U, P);
    $this->conn->setAttribute(PDO::ATTR_CASE,PDO::CASE_LOWER);
    }
    catch (PDOException $e) {
    // Check for database connection error
      echo "Failed to connect: " .$e->getMessage();
    }
    // returing connection resource
    return $this->conn;
  }
}