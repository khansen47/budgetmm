<?php
include_once("../classes/database.php");
include_once("../classes/functions.php");
include_once("../classes/layout.php");
$database	= new Database();
$function	= new Functions();
$layout		= new Layout();

$layout->session_inc();

$user_info 			= $function->get_userInfo($_SESSION['login']);
?>