<?php

class OfficeNumber {

  private $conn;

  function __construct() {
    require_once '../db/connect.php';
    // opening db connection
    $db = new dbConnect();
    $this->conn = $db->connect();
  }

  private function getOfficeCode($userId) {
    try {
      $userId = strtoupper($userId) .'%';
      $select = "SELECT
      FROM
      WHERE LIKE :userId";

      $stmt = $this->conn->prepare($select);
      $stmt->bindParam(':userId', $userId);
      $stmt->execute();
      $result = $stmt->fetch();
      return $result[0];
    }
    catch (PDOException $e) {
      die($e->getMessage() .__LINE__);
    }
  }

  public function getOfficeAbbr($code) {
    $abbr;
    switch ($code) {
      case '1':
        $abbr = 'PHI';
        break;
      case '2':
        $abbr = 'CMP';
        break;
      case '3':
        $abbr = 'PIT';
        break;
      case '4':
        $abbr = 'MTL';
        break;
      case '00005':
        $abbr = 'JAC';
        break;
      case '00006':
        $abbr = 'FTL';
        break;
      case '00007':
        $abbr = 'LKM';
        break;
      case '00008':
        $abbr = 'BRD';
        break;
      default:
        $abbr = false;
    }
    return $abbr;
  }

  public function get($userId) {
    $office = new class{};
    $code = $this->getOfficeCode($userId);
    $office->abbr = 


    $check = substr($code,1,1);

    if ($check !==' ' ||'0') {
      return false;
    }
    else {
      if ($check ===' ') {
        if ($code !=='    ') {
          $office->crit = '<  ';
        } else {
          $office->crit = '>  ';
        }
      }
      if ($check ==='0') {
        $office->crit = '> AND col1 NOT IN () ';
      }
      return $office;
    } 
  }
}