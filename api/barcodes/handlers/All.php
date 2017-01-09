<?php

class All {

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
	    LEFT OUTER JOIN
      ON
      AND
      AND"
      .$where ."";

      $stmt = $this->conn->prepare($select);
      foreach ($params as $param => &$val) { // bindParam needs &$variable
        $stmt->bindParam($param, $val);
      }
      $stmt->execute();
      $alls = $stmt->fetchAll(PDO::FETCH_ASSOC);
      if ($alls) {
        // beautify results:
        // don't want zero for extension...want empty string
        // don't want office number in array...want office abbreviation

        require_once 'handlers/OfficeNumber.php';
        $officeNumber = new OfficeNumber;

        foreach($alls as $key => $value) {
          if ($value['u'] ==='0') {
            $alls[$key]['u'] = 'N/A';
          } else {
            $alls[$key]['u'] = '5-'.$value['u'];
          }
          if ($value['v'] ===null) {
            $alls[$key]['v'] = 'N/A';
          }
          $alls[$key]['w'] = $officeNumber->getOfficeAbbr($value['w']);
        }
        return $alls;
      } else {
        return false;
      }
    }
    catch (PDOException $e) {
      die($e->getMessage() .__LINE__);
    }
  }

}