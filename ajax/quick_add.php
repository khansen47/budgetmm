<?php
//TODO REMOVE FILE
include_once( "../classes/database.php" );
include_once( "../classes/functions.php" );
include_once( "../classes/layout.php" );

$database 	= new Database();
$function 	= new Functions();
$layout 	= new Layout();

$layout->session_inc();

$category	= $function->post( "category" );
$amount		= $function->post( "amount" );
$date		= $function->post( "date" );
$comment	= $function->post( "comment" );

$user_info = $function->get_userInfo( $_SESSION['login'] );

$date = date( "Y-m-d", strtotime( $date ) );

$insert = $database->mysqli->prepare("INSERT INTO item SET date = ?, amount = ?, cat_id = ?, comment = ?, user_id = ?");
$insert->bind_param("sdisi", $date, $amount, $category, $comment, $user_info['id']);
$insert->execute();
$insert->close();

?>