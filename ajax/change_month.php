<?php
require_once("../classes/layout.php");
require_once("../classes/functions.php");
$layout 	= new Layout();
$function 	= new Functions();
$layout->session_inc();

$month	= $function->post("month");

$month_names = array("January","February","March","April","May","June","July","August","September","October","November","December");
if (in_array($month, $month_names))
	$_SESSION['month'] = $month;
?>