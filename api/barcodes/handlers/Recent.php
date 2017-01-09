<?php

class Recent {

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
      FROM
      JOIN
      ON
      AND
      AND "
      .$where
      ."ORDER BY col DESC";

      $stmt = $this->conn->prepare($select);
      foreach ($params as $param => &$val) { // bindParam needs &$variable
        $stmt->bindParam($param, $val);
      }
      $stmt->execute();
      $recents = $stmt->fetchAll(PDO::FETCH_ASSOC);
      if ($recents) {
        // beautify results:
        // don't want zero for extension...want empty string
        // don't want office number in array...want office abbreviation

        require_once 'handlers/OfficeNumber.php';
        $officeNumber = new OfficeNumber;

        foreach($recents as $key => $value) {
          if ($value['u'] ==='0') {
            $recents[$key]['u'] = 'N/A';
          } else {
            $recents[$key]['u'] = '5-'.$value['upext#'];
          }
          if ($value['v'] ===null) {
            $recents[$key]['v'] = 'N/A';
          }
          $recents[$key]['w'] = $officeNumber->getOfficeAbbr($value['w']);
        }
        return $recents;
      } else {
        return false;
      }
    }
    catch (PDOException $e) {
      die($e->getMessage() .__LINE__);
    }
  }

}