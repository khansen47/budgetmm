<?php
include_once(	"../classes/database.php" );
include_once(	"../classes/functions.php" );
include_once(	"../classes/layout.php" );
$database 	= new Database2();
$function 	= new Functions();
$layout 	= new Layout();

$layout->session_inc();
$user_info = $function->get_userInfo($_SESSION['login']);

 /* -------------- ADD AUTO BILL ---------------*/
$cat_id			= $function->post( "cat_id" );
$amount			= $function->post( "amount" );
$day			= $function->post( "day" );

$errors 		= "";
$success 		= "";
$color 			= "red";

/* ---------- END ADD AUTO BILL ----------------*/

/*-------------- EDIT AUTO BILL ----------------*/
$id		= $function->post( 'id' );
$cat_id	= $function->post( 'cat_id' );
$amount	= $function->post( 'amount' );
$day	= $function->post( 'day' );

if ($delete == 1 && $cat_id != "")
{
	$stmt = $database->mysqli->prepare("UPDATE autobill SET cat_id = ?, amount = ?, day = ? WHERE id = ?");
	$stmt->bind_param("i", $cat_id);
	$stmt->execute();
	$stmt->close();
	
	$success = "Successfully deleted category";
}

/*---------------END DELETE AUTO BILL-----------------*/

$income = $database->mysqli->prepare("SELECT id, name, budget FROM category WHERE type_id = '1' AND user_id = ? AND deprecated = 0");
$income->bind_param("i", $user_info['id']);
$income->bind_result($inc_id, $inc_name, $inc_budget);
$income->execute();

?>
<div class="header">
	<h1>Manage Reacurring Bills</h1>
</div>
<div id="add-cat">	
	
</div>
<div style="clear: both;"></div>