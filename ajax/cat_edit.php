<?php
include_once("../classes/database.php");
include_once("../classes/functions.php");
include_once("../classes/layout.php");

$database	= new Database();
$function	= new Functions();
$layout		= new Layout();

$layout->session_inc();

$user_info	= $function->get_userInfo($_SESSION['login']);
$cat_id		= $function->post("cat_id");

/*--------------Edit Cat -------------------*/
$edit_errors	= "";
$success		= "";

$edit_type		= $function->post("edit_type");
$edit_name		= $function->post("edit_name");
$edit_budget	= $function->post("edit_budget");
$edit_s_month	= $function->post("edit_s_month");
$edit_s_year	= $function->post("edit_s_year");
$edit_e_month	= $function->post("edit_e_month");
$edit_e_year	= $function->post("edit_e_year");

if ( $edit_s_month == '*' ) 							$edit_s_month 	= '00';
if ( $edit_s_year == '*' )								$edit_s_year 	= '0000';
if ( $edit_e_month == '*' ) 							$edit_e_month 	= '00';
if ( $edit_e_year == '*' )								$edit_e_year 	= '0000';

if ( $edit_s_year == '0000' && $edit_s_month != '00' )	$edit_s_date 	= $edit_s_month;
else 													$edit_s_date 	= $edit_s_year."-".$edit_s_month."-00";

if ( $edit_e_year == '0000' && $edit_e_month != '00' )	$edit_e_date 	= $edit_e_month;
else 													$edit_e_date 	= $edit_e_year."-".$edit_e_month."-00";

if ($edit_type != "" && $edit_name != "" && $edit_budget != "") {
	if ($edit_type == "") {
		$edit_errors .= "Please do not try to hack<br />";
	}
	
	if ($edit_name == "") {
		$edit_errors .= "Please enter a Name<br />";
	}
	
	if ($edit_budget != "" && !is_numeric($edit_budget)) {
		$edit_errors .= "Budget must be a proper number";
	}
	
	if ($edit_errors == "") {
		$query = "UPDATE 
					category 
				  SET 
					name = ?, type_id = ?, budget = ?, start_date = ?, end_date = ?, last_updated = NOW() 
				  WHERE 
					id = ?";
		$stmt = $database->mysqli->prepare($query);
		$stmt->bind_param("sidssi", $edit_name, $edit_type, $edit_budget, $edit_s_date, $edit_e_date, $cat_id);
		$stmt->execute();
		$stmt->close();
		
		$success = "Updated successfully!";
	}
}

/* ---------- End Edit Cat -----------------*/

$cat = $database->mysqli->prepare("SELECT name, type_id, budget, start_date, end_date FROM category WHERE id = ? AND user_id = ?");
$cat->bind_param("ii", $cat_id, $user_info['id']);
$cat->bind_result($name, $type_id, $budget, $start_date, $end_date);
$cat->execute();
$cat->fetch();

$s_date = explode( "-", $start_date );
$e_date = explode( "-", $end_date );

if ( sizeof( $s_date ) == 1 )
	$s_date = array('0000', $start_date, '00');

if ( sizeof( $e_date ) == 1 )
	$e_date = array('0000', $end_date, '00');
?>
<span style="color:red;"><?php echo $edit_errors; ?></span>
<span style="color:green;"><?php echo $success; ?></span>

<table cellspacing="2" cellpadding="1">
	<tr>
		<td>Name:</td>
		<td><input type="text" id="edit-name" value="<?php echo $name; ?>" /></td>
	</tr>
	<tr>
		<td>Category:</td>
		<td>
			<select id="edit-type">
				<option id="2" <?php if ($type_id == 2) echo "selected='Selected'"; ?>>Expense</option>
				<option id="1" <?php if ($type_id == 1) echo "selected='Selected'"; ?>>Income</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>Budget:</td>
		<td><input type="text" id="edit-budget" size="10px" value="<?php echo $budget; ?>" /></td>
	</tr>
	<tr>
		<td>Start Date:</td>
		<td>
			<select id="edit-start-month"><?php echo $function->month_options( $s_date[1] ); ?></select>
			<select id="edit-start-year"><?php echo $function->year_options( $s_date[0] ); ?></select>
		</td>
	</tr>
	<tr>
		<td>End Date:</td>
		<td>
			<select id="edit-end-month"><?php echo $function->month_options( $e_date[1] ); ?></select>
			<select id="edit-end-year"><?php echo $function->year_options( $e_date[0] ); ?></select>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input id="edit-cat-save" type="button" value="Save" /></td>
		<td><input id="delete-cat" type="button" value="Delete" /></td>
	</tr>
</table>
<input type="hidden" id="edit-id" value="<?php echo $cat_id; ?>" />