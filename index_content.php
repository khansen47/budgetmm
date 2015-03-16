<?php
include_once("classes/database.php");
include_once("classes/functions.php");
include_once("classes/layout.php");

$database 		= new Database();
$functions 		= new Functions();
$layout 		= new Layout();

$layout->session_inc();

$user_info 		= $functions->get_userInfo($_SESSION['login']);

$totals 		= $database->mysqli->query("SELECT
												SUM(i.amount) as expenses,
												(SELECT
													SUM(ii.amount)
												 FROM
													item ii
												 WHERE
													ii.user_id 			= '".$user_info['id']."' 	AND
													MONTHNAME(ii.date) 	= '".$_SESSION['month']."' 	AND
													YEAR(ii.date)		= '".$_SESSION['year']."'	AND
													ii.cat_id = (SELECT cat_id FROM category WHERE id = ii.cat_id AND type_id = 1 AND deprecated = 0)
												) as incomes
											FROM
												item i
											WHERE
												i.user_id 			= '".$user_info['id']."' 	AND
												MONTHNAME(i.date) 	= '".$_SESSION['month']."' 	AND
												YEAR(i.date)		= '".$_SESSION['year']."'	AND
												i.cat_id 			= (SELECT cat_id FROM category WHERE id = i.cat_id AND type_id = 2 AND deprecated = 0)");
$sum_totals 	= $totals->fetch_assoc();

$format_date 	= $_SESSION['year']."-".$functions->month_int($_SESSION['month'])."-00";

$categories 	= $database->mysqli->query("SELECT
												*
											FROM
												category
											WHERE
												user_id 	= '".$user_info['id']."'	AND
												deprecated 	= 0 						AND
												(
													(
														(start_date <= '".$format_date."' OR start_date = '0000-00-00')	AND
														(end_date 	>= '".$format_date."' OR end_date = '0000-00-00')
													)
													OR
													(
														(start_date <= ".$functions->month_int($_SESSION['month'])." AND start_date <> '0000-00-00' ) 	AND
														(end_date 	>= ".$functions->month_int($_SESSION['month'])." AND end_date <> '0000-00-00' )
													)
												)
											ORDER BY
												type_id, name");

$monthly_total 	= $sum_totals['incomes']-$sum_totals['expenses'];

if ($monthly_total < 0) $head_span_color = "#CF969B";
else					$head_span_color = "#8AB381";

if ($_SESSION["month"] == date("F"))	$day = date("d");
else									$day = '01';
?>

<div id="head-div">
	<h2><?php echo $_SESSION['month'].", ".$_SESSION['year']; ?>:
		<span style="color:<?php echo $head_span_color; ?>">
			$<?php echo number_format($monthly_total, 2); ?>
		</span>
	</h2>
</div>
<div id="month-select-div">
	View Different Month: <?php echo $functions->get_months($_SESSION['month']);?> <?php echo $functions->get_years($_SESSION['year']); ?>
</div>
<div style="clear:both"></div>
<div id="totals">
	<p>Income: 		<span>$<?php echo number_format($sum_totals['incomes'], 2); ?>	</span></p>
	<p>Expenses: 	<span>$<?php echo number_format($sum_totals['expenses'], 2); ?>	</span></p>
</div>
<div id="quick-add">
	<b>Quick Add:</b> 	<select id="quick-type">
							<?php echo $functions->cat_select_list($user_info['id']); ?>
						</select>
	Amount: 			<input type="text" 		id="quick-amount" 		value="0"/>
	Date: 				<input type="date" 		id="quick-date" 		value="<?php echo $_SESSION['year'].'-'.$functions->month_int($_SESSION['month']).'-'.$day; ?>" />
	Comment: 			<input type="text" 		id="quick-comment" />
						<input type="button"	id="quick-add-submit" 	value="Add"  />
						<span id="quick-report"></span>
</div>
<div style="clear:both;" ></div>
<br />
<br />
<div id="details">
	<table width="20%">
		<?php
		while ($cat_row = $categories->fetch_assoc()) {
			?><tr><?php
			if ($cat_row['type_id'] == 1) { ?>
				<th nowrap align=right id="<?php echo $cat_row['id']; ?>" class="income-th"><?php echo $cat_row['name']; ?></th>
		<?php } else { ?>
				<th nowrap align=right id="<?php echo $cat_row['id']; ?>" class="expense-th"><?php echo $cat_row['name']; ?></th>
		<?php }
			$cat_sum = $database->mysqli->query("SELECT
													SUM(amount) as total,
													date
												 FROM
													item
												 WHERE
													cat_id 			= '".$cat_row['id']."' 		AND
													user_id 		= '".$user_info['id']."' 	AND
													MONTHNAME(date) = '".$_SESSION['month']."' 	AND
													YEAR(date)		= '".$_SESSION['year']."'");
			$cat_assoc = $cat_sum->fetch_assoc();
			?>
			<td align=left class="money-td">$<?php echo number_format($cat_assoc['total'], 2); ?></td>
			</tr>
			<?php
		} ?>
	</table>
</div>