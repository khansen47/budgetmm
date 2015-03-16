<?php
include_once("../classes/database.php");
include_once("../classes/functions.php");
include_once("../classes/layout.php");

$database  = new Database();
$functions = new Functions();
$layout    = new Layout();

$layout->session_inc();

$user_info = $functions->get_userInfo($_SESSION['login']);

$totals = $database->mysqli->query("SELECT SUM(i.amount) as expenses, (SELECT SUM(ii.amount) as expenses FROM item ii WHERE ii.user_id = '".$user_info['id']."' AND MONTHNAME(ii.date) = '".$_SESSION['month']."' AND ii.cat_id = (SELECT cat_id FROM category WHERE id = ii.cat_id AND type_id = '1')) as incomes FROM item i WHERE i.user_id = '".$user_info['id']."' AND MONTHNAME(i.date) = '".$_SESSION['month']."' AND i.cat_id = (SELECT cat_id FROM category WHERE id = i.cat_id AND type_id = '2')");
$sum_totals = $totals->fetch_assoc();

$items = $database->mysqli->query("SELECT i.*, c.name FROM item i, category c WHERE MONTHNAME(i.date) = '".$_SESSION['month']."' AND i.user_id = '".$user_info['id']."' AND c.user_id = '".$user_info['id']."' AND c.id = i.cat_id ORDER BY c.type_id, c.name, i.date");

$items = $database->mysqli->query("SELECT i.* FROM item i WHERE MONTHNAME(i.date) = '".$_SESSION['month']."' AND i.user_id = '".$user_info['id']."' ORDER BY i.date");
?>

<div style="clear:both;" ></div>
<div id="details">
	<table>
	<?php 
		while ($i_row = $items->fetch_assoc()) { 
			echo "<tr><td>".date("d", strtotime( $i_row['date'] ) ).":<td>".$i_row['amount']."</td><td>".$i_row['comment']."</td></tr>";
		}
	?>
	</table>
</div>