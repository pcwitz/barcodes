<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

  if (isset($_GET['userId']) && isset($_GET['callback'])) { // this should always be the case
    
    $cb = $_GET['callback'];
    
    // build an object with clean search values
    $params = new class{};
    $res = []; // final response

    function utf8_converter($array) {
      array_walk_recursive($array, function(&$item, $key) {
        if(!mb_detect_encoding($item, 'utf-8', true)) {
          // $item = utf8_encode($item);
          $item = mb_convert_encoding($item, 'UTF-8');
        }
      });
      return $array;
    }

    function jsonp($res, $cb) {
      $json = json_encode($res);
      $jsonp = $cb . '(' . $json . ')';
      echo $jsonp;
    }

    // userId only and always used in recent and all queries to get office number criteria
    require_once 'handlers/OfficeNumber.php';
    $officeNumber = new OfficeNumber;
    $office = $officeNumber->get($_GET['userId']);
    $where = ' WHERE col5 ' . $office->crit;

    if (isset($_GET['lname']) || isset($_GET['fname'])) { // only time we run the sis query is when we have a name

      $sisWhere = ' WHERE ';

      if (isset($_GET['lname'])) {

        // name also used in recent and all queries
        $where .= 'AND UPPER(col1) LIKE :lname ';
        $sisWhere .= 'UPPER(col2) LIKE :lname ';

        $params->lname = strtoupper($_GET['lname']) .'%';
        
      }

      if (isset($_GET['fname'])) {

        $where .= 'AND UPPER(col3) LIKE :fname ';

        if (property_exists($params,'lname')) {
          $sisWhere .= 'AND ';
        }

        $sisWhere .= 'UPPER(col4) LIKE :fname ';

        $params->fname = strtoupper($_GET['fname']) .'%';

      }
      // run the sis query
      require_once 'handlers/SIS.php';
      $sis = new SIS;
      $sises = $sis->get($sisWhere, $params);

      $res['sis'] = $sises;

    }
    // sis query conditions over, begin recent- and all-only query

    if (isset($_GET['eob'])) {

      // use eob to get claim number
      require_once 'handlers/ClaimNumber.php';
      $claimNumber = new ClaimNumber;

      $where .= 'AND DIGITS(col5)||DIGITS(col6)||DIGITS(col7)=:eob ';
      $params->eob = $claimNumber->get($_GET['eob']);

    } // end eob

    if (isset($_GET['ssn'])) {

      $where .= 'AND col8=:ssn ';
      $params->ssn = $_GET['ssn'];

    } // end ssn

    if (isset($_GET['group'])) {

      $where .= 'AND col5=:group AND col6=:year AND col7=:case ';
      $params->group = $_GET['group'];
      $params->year = $_GET['year'];
      $params->case = $_GET['case'];

    } // end claim number

    if (isset($_GET['dob'])) {

      $where .= 'AND col9=:dob ';
      $params->dob = $_GET['dob'];

    } // end dob

    if (isset($_GET['doi'])) {

      $where .= 'AND col10=:doi ';
      $params->doi = $_GET['doi'];

    } // end doi

    // end recent and all-only query

    // run recent and all queries
    require_once 'handlers/Recent.php';
    $recent = new Recent;
    $recents = $recent->get($where, $params);
    $res['recent'] = $recents;

    require_once 'handlers/All.php';
    $all = new All;
    $alls = $all->get($where, $params);
    $res['all'] = $alls;

    //php's json_encode will not tolerate other than utf-8
    $res = utf8_converter($res);
    jsonp($res, $cb); // echo out json

  } else {
    http_response_code(204); // no content
  }
} else {
  http_response_code(400); // bad request
}