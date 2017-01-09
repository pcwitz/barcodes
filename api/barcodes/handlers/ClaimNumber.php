<?php
require_once 'handlers/OfficeNumber.php';

class ClaimNumber {

  private $conn;

  function __construct() {
    require_once '../db/connect.php';
    // opening db connection
    $db = new dbConnect();
    $this->conn = $db->connect();
  }

  private function concat($arr) {
    $claimNumber  = $arr['fnd'];
    $claimNumber .= $arr['fyr'];
    $claimNumber .= $arr['case'];
    return $claimNumber;
  }

  public function get($eob) {
    try {
      $select = "SELECT
      AS
      AS
      AS case
      FROM
      WHERE ";

      $stmt = $this->conn->prepare($select);
      $stmt->bindParam(':eob', $eob);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($result) {
        return $this->concat($result);
      } else {
        return '0';
      }
    }
    catch (PDOException $e) {
      die($e->getMessage() .__LINE__);
    }
  }

}