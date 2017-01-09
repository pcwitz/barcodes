<?php

class SIS {

  private $conn;

  function __construct() {
    require_once '../db/connect.php';
    // opening db connection
    $db = new dbConnect();
    $this->conn = $db->connect();
  }

  public function get($where, $params) {
    try {
      $select = "SELECT
      CASE
        WHEN
          THEN
        ELSE
      END
      FROM
      JOIN ON "
      .$where;

      $stmt = $this->conn->prepare($select);
      foreach ($params as $param => &$val) { // bindParam needs &$variable
        $stmt->bindParam($param, $val);
      }
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      if ($result) {
        return $result;
      } else {
        return false;
      }
    }
    catch (PDOException $e) {
      die($e->getMessage() .__LINE__);
    }
  }

}