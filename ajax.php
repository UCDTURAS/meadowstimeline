<?php

//bootstrap
require_once('bootstrap.php');

//default get csv
if(!isset($_GET['format']) || $_GET['format']=='csv'){

  $csv = $db->downloadCSV(array('id','title','description','category','start','end','lat','lon','image','file','created','updated'));
  echo $csv;
}

//get lovely dovely json
if($_GET['format']=='json'){

  $json = $db->downloadJSON($_GET['query']);
  echo $json;
}

die();
