<?php
/**
 * @author daithi coombes <daithi.coombes@futureanalytics.ie>
 */

session_start();
header('Content-Type: application/json');


//security
if(!$_SESSION['loggedIn']){
    print json_encode(array(
        'error' => 'no no no... I\'m sorry dave, but we can\'t do that'
    ));
    die();
}


//bootstrap
require_once('../bootstrap.php');
$db = \FAC\Model::factory()
    ->getDb();

$err = false;
$cols = array('title','description','category','start','end','lat','lng','image','created','updated');
$parts = explode("-", $_POST['pk']);
$id = $parts[0];
$field = $parts[1];
$value = $_POST['value'];


//test field value is correct
if(!in_array($field, $cols))
    $err = 'unkown column';


//build & run query
if(!$err){

    //run query
    $qry = "UPDATE Entry SET {$field}=? WHERE id=?";
    $stmt = $db->prepare($qry);
    $stmt->execute(array($value, $id));

    $res = array(
        'yep' => 'woo hoo!',
        'query' => $qry
    );
}

//else error report
else{
    $res = array(
        'error' => $err
    );
}

//print result and die()!
$json = json_encode($res);
die($json);
