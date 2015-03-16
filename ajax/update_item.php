<?php
include_once("../classes/database.php");
include_once("../classes/functions.php");
include_once("../classes/layout.php");

$database 			= new Database();
$function 			= new Functions();
$layout 			= new Layout();

$layout->session_inc();

$return['error'] 	= false;
$return['message'] 	= "test";

$item_id			= $function->post("item_id");
$day				= $function->post("day");
$amount				= $function->post("amount");
$comment			= $function->post("comment");
$cat_id				= $function->post("cat_id");
$user_info 			= $function->get_userInfo($_SESSION['login']);

$check_item 		= $database->mysqli->prepare("SELECT id FROM item WHERE id = ? AND user_id = ? LIMIT 1");
$check_item->bind_param("ii", $item_id, $user_info['id']);

if (!$check_item->execute()) {
	$return['error'] = true;
	$return['message'] = "Error: (" . $check_item->errno . ") ".$check_item->error;
} else {
	if ($check_item->num_rows > 0) {
		$return['error'] = true;
		$return['message'] = "That item does not belong to you, stop hacking";	
		$check_item->close();		
	} else {
		$check_item->close();
		
		//Check Date
		if ($day >= 1 && $day <= 31) {
			$item_day = $_SESSION['year']."-".$function->month_int($_SESSION['month'])."-".$day;
		} else {
			$return['error'] 	= true;
			$return['message'] 	= "Error: Day is not a correct input";
		}
		
		//Check Amount
		if (!is_numeric($amount)) {
			$return['error'] 	= true;
			$return['message'] 	= "Error: Amount is not a number";
		} else if ($amount < 0.00) {
			$return['error'] 	= true;
			$return['message'] 	= "Error: Amount cannot be negative";
		}	
		
		if ($return['error'] == true)
			die(json_encode($return));
		
		$update_item = $database->mysqli->prepare("UPDATE item SET date = ?, amount = ?, comment = ?, cat_id = ? WHERE id = ? AND user_id = ?");
		$update_item->bind_param("sdsiii", $item_day, $amount, $comment, $cat_id, $item_id, $user_info['id']);
		if (!$update_item->execute()) {
			$return['error'] = true;
			$return['message'] = "Error: (" . $update_item->errno . ") ".$update_item->error;
		}
		$update_item->close();
	}	
}

die(json_encode($return));
?>