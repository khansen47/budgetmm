<?php
include_once("../classes/database.php");
include_once("../classes/functions.php");
include_once("../classes/layout.php");

$database  = new Database();
$functions = new Functions();
$layout    = new Layout();

$layout->session_inc();

?>
