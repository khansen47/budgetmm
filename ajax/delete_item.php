<?php
include_once("../classes/database.php");
include_once("../classes/functions.php");
include_once("../classes/layout.php");
$database = new Database();
$function = new Functions();
$layout = new Layout();

$layout->session_inc();

$return['error'] = false;
$return['message'] = "";

$item_id	= $function->post("item_id");

$user_info = $function->get_userInfo($_SESSION['login']);

$check_item = $database->mysqli->prepare("SELECT id FROM item WHERE id = ? AND user_id = ? LIMIT 1");
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
		$delete_item = $database->mysqli->prepare("DELETE FROM item WHERE id = ? AND user_id = ?");
		$delete_item->bind_param("ii", $item_id, $user_info['id']);
		if (!$delete_item->execute()) {
			$return['error'] = true;
			$return['message'] = "Error: (" . $delete_item->errno . ") ".$delete_item->error;
		}
		$delete_item->close();
	}	
}

die(json_encode($return));
?>