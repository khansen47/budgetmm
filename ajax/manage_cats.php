<?php
include_once( "../classes/database.php" );
include_once( "../classes/functions.php" );
include_once( "../classes/layout.php" );

$database 	= new Database();
$function 	= new Functions();
$layout 	= new Layout();

$layout->session_inc();
$user_info = $function->get_userInfo( $_SESSION['login'] );

 /* -------------- Add category ---------------*/
$cat_type		= $function->post("cat_type");
$cat_name		= $function->post("cat_name");
$cat_budget		= $function->post("cat_budget");
$cat_s_month	= $function->post("cat_s_month");
$cat_s_year		= $function->post("cat_s_year");
$cat_e_month	= $function->post("cat_e_month");
$cat_e_year		= $function->post("cat_e_year");

$errors 		= "";
$success 		= "";
$color 			= "red";

$start_date		= $cat_s_year."-".$cat_s_month."-00";
$end_date		= $cat_e_year."-".$cat_e_month."-00";

if ($cat_type != "" && $cat_name != "" && $cat_budget != "") {
	if ($cat_type == "") {
		$errors = "Please do not try to hack<br />";
	}
	
	if ($cat_name == "") {
		$errors .= "Please enter a Name<br />";
	}
	
	if ($cat_budget != "" && !is_numeric($cat_budget)) {
		$errors .= "Budget must be a proper number";
	}
	
	if ($errors == "") {
		$stmt = $database->mysqli->prepare("INSERT IGNORE INTO category SET name = ?, user_id = ?, type_id = ?, budget = ?, start_date = ?, end_date = ?, last_updated = NOW()");
		$stmt->bind_param("siidss", $cat_name, $user_info['id'], $cat_type, $cat_budget, $start_date, $end_date);
		$stmt->execute();
		$stmt->close();
		
		$success = "Successfully added category!";
		$color = "green";
		$cat_type = "";
		$cat_name = "";
		$cat_budget = "";
	}
}

/* ---------- End Add Cat ------------------*/

/*---------------DELETE CAT-----------------*/
$cat_id	= $function->post("cat_id");
$delete	= $function->post("delete");

if ($delete == 1 && $cat_id != ""){
	$stmt = $database->mysqli->prepare("UPDATE category SET deprecated = 1, last_updated = NOW() WHERE id = ?");
	$stmt->bind_param("i", $cat_id);
	$stmt->execute();
	$stmt->close();
	
	$success = "Successfully deleted category";
}

/*---------------END DELETE CAT-----------------*/

$income = $database->mysqli->prepare("SELECT id, name, budget FROM category WHERE type_id = '1' AND user_id = ? AND deprecated = 0");
$income->bind_param("i", $user_info['id']);
$income->bind_result($inc_id, $inc_name, $inc_budget);
$income->execute();

?>
<div class="header">
	<h1>Manage Categories</h1>
</div>
<div id="add-cat">	
	Add Category: <select id="cat-type"><option id='2'>Expense</option><option id="1">Income</option></select>
	<b>Name:</b> <input type="text" id="add-cat-name" value="<?php echo $cat_name; ?>" />
	Budget:		<input type="text" id="add-cat-budget" size="10px" value="0" />

	Start Date: <select id="add-start-month"><?php echo $function->month_options( '0' ); ?></select>
				<select id="add-start-year"><?php echo $function->year_options( '0000' ); ?></select>

	End Date: 	<select id="add-end-month"><?php echo $function->month_options( '0' ); ?></select>
				<select id="add-end-year"><?php echo $function->year_options( '0000' ); ?></select>
	<br />
	<br />
	<input type="button" id="add-cat-submit" value="Add Category" />
	<?php echo "<span style='color:".$color."'>".$errors.$success."</span>"; ?>
</div>
<div id="cat-content">
	<div id="income-cats">
		<h2>Income</h2>
		<?php while ($income->fetch()) { ?>
				<span cat-id="<?php echo $inc_id; ?>"><?php echo $inc_name;?></span><br />
		<?php }
			$income->close();
			
			$expenses = $database->mysqli->prepare("SELECT id, name, budget FROM category WHERE type_id = '2' AND user_id = ? AND deprecated = 0 ORDER BY name");
			$expenses->bind_param("i", $user_info['id']);
			$expenses->bind_result($exp_id, $exp_name, $exp_budget);
			$expenses->execute();
		?>
	</div>
	<div id="expenses-cats">
		<h2>Expenses</h2>
		<?php while ($expenses->fetch()) { ?>
				<span cat-id="<?php echo $exp_id; ?>"><?php echo $exp_name;?></span><br />
		<?php } 
			$expenses->close(); ?>
	</div>
	<div id="category-edit">
	</div>
</div>
<div style="clear: both;"></div>