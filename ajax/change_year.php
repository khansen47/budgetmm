<?php
require_once("../classes/layout.php");
require_once("../classes/functions.php");
$layout 	= new Layout();
$function 	= new Functions();
$layout->session_inc();

$year	= $function->post("year");

$_SESSION['year'] = $year;
?>