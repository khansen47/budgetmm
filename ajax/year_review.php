<?php
include_once( "../classes/database.php" );
include_once( "../classes/functions.php" );
include_once( "../classes/layout.php" );

$database 	= new Database();
$database2 	= new Database2();
$function 	= new Functions();
$layout 	= new Layout();

$layout->session_inc();

$year		= Functions::Post_Int( 'year' );

if ( !Functions::User_Load( $database2, $_SESSION[ 'login' ], $user_info ) )
{
	header( 'Location: login.php' );
	die();
}

if ( $year == '' )
{
	$year = $_SESSION['year'];
}

$year_clause = ' AND YEAR( date ) = "'.$year.'"';

if ( $year == 'all' )
{
	$year_clause	= '';
}

$cat_select			= $database->mysqli->query("SELECT
													*
												FROM
													category
												WHERE
													user_id		= '".$user_info['id']."'	AND
													deprecated	= 0
												ORDER BY
													type_id, name");

$totals 			= $database->mysqli->query("SELECT 
													SUM(i.amount) as income_tot, 
													(
														SELECT 
															SUM(i.amount) as exp_tot 
														FROM 
															item i 
														WHERE 
															i.user_id 		= '".$user_info['id']."' 								AND 
															EXISTS (SELECT id FROM category WHERE type_id = '2' AND id = i.cat_id)".
															$year_clause.
												   ") as exp_tot 
												FROM 
													item i 
												WHERE 
													i.user_id 		= '".$user_info['id']."' 								AND 
													EXISTS (SELECT id FROM category WHERE type_id = '1' AND id = i.cat_id)".
													$year_clause);

$income_arr 		= "";
$expense_arr 		= "";
$income_list		= "";
$expense_list		= "";
$income_totals		= "";
$expense_totals		= "";
$total_assoc 		= $totals->fetch_assoc();

while ($cat = $cat_select->fetch_assoc()) {
	if ($cat['type_id'] == 1)		$income_list 	= $income_list."<span cat-id='".$cat['id']."'>".$cat['name']."</span><br />";
	elseif ($cat['type_id'] == 2)	$expense_list 	= $expense_list."<span cat-id='".$cat['id']."'>".$cat['name']."</span><br />";
}

$month_count = 1;

while ($month_count <= 12) {
	$month_total_income = $database->mysqli->query("SELECT 
														COALESCE( sum( amount ), 0 ) as i_amount 
													FROM 
														item 
													WHERE 
														user_id 	= '".$user_info['id']."' 	AND 
														MONTH(date) = ".$month_count."
														".$year_clause. "AND
														EXISTS (SELECT id FROM category WHERE type_id = 1 AND id = cat_id)");
	$temp 						= $month_total_income->fetch_assoc();
	$income_arr[$month_count] 	= $temp['i_amount'];
	$income_totals 				= $income_totals."<span>".$function->month_string($month_count).": <strong style='font-size:12pt;'>$ ".number_format($temp['i_amount'], 2)."</strong><span></p>";
	$month_count++;
}

$month_count = 1;

while ($month_count <= 12) {
	$month_total_expense = $database->mysqli->query("SELECT 
														COALESCE( sum(amount), 0 ) as i_amount 
													FROM 
														item 
													WHERE 
														user_id 	= '".$user_info['id']."' 	AND 
														MONTH(date) = ".$month_count."
														".$year_clause. "AND
														EXISTS (SELECT id FROM category WHERE type_id = 2 AND id = cat_id)");
	$temp 						= $month_total_expense->fetch_assoc();
	$expense_arr[$month_count] 	= $temp['i_amount'];
	$expense_totals 			= $expense_totals."<span>".$function->month_string($month_count).": <strong style='font-size:12pt;'>$ ".number_format($temp['i_amount'], 2)."</strong><span></p>";
	$month_count++;
}

if ($year != date("Y"))
	$month_num = 12;
else
	$month_num = date("m");
	
?>
<h1>Review of <?php echo $year; ?></h1>
<div style="float: right; margin-top: -50px;"><select id="year_review_select"><?php echo $function->year_review_options( $year ); ?></select></div>
<div id="man-cat">
	<p>Income</p>	<?php echo $income_list; ?>
	<p>Expenses</p>	<?php echo $expense_list; ?>
</div>
<div id="man-cat-inside">
	<div id="inc-man-cat">
		<p>Income</p>
		<span style="font-size: 14pt;">Yearly Total: <strong>$ <?php echo number_format($total_assoc['income_tot'], 2); ?></strong></span><br /><br />
		<span>Average: 
			<strong style="font-size:12pt;">
				$ <?php echo number_format(($total_assoc['income_tot']/$month_num), 2); ?>
			</strong>
		</span>
		<div style="border-bottom:1px solid black;width:240px;">&nbsp;</div><br />
		<?php echo $income_totals; ?>
	</div>
	<div id="exp-man-cat">
		<p>Expenses</p>
		<span style="font-size: 14pt;">Yearly Total: <strong>$ <?php echo number_format($total_assoc['exp_tot'], 2); ?></strong></span><br /><br />
		<span>Average: 
			<strong style="font-size:12pt;">
				$ <?php echo number_format(($total_assoc['exp_tot']/$month_num), 2); ?>
			</strong>
		</span>
		<div style="border-bottom:1px solid black;width:240px;">&nbsp;</div><br />
		<?php echo $expense_totals; ?>
	</div>
	<div id="overall-man-cat">
		<p>Overall</p>
		<?php 
			$tot_col = (($total_assoc['income_tot']-$total_assoc['exp_tot']) > 0) ? "#7CA67E" : "#886789";
			$avg_col = ((($total_assoc['income_tot']-$total_assoc['exp_tot'])/$month_num) > 0) ? "#7CA67E" : "#886789";
		?>
		<span style="font-size: 14pt;">Yearly Total: 
			<strong style="color:<?php echo $tot_col; ?>;">
				$ <?php echo number_format(($total_assoc['income_tot']-$total_assoc['exp_tot']), 2); ?>
			</strong>
		</span><br /><br />
			<span>Average: 
				<strong style="font-size:12pt;color:<?php echo $avg_col; ?>;">
					$ <?php echo number_format((($total_assoc['income_tot']-$total_assoc['exp_tot'])/$month_num), 2); ?>
				</strong>
			</span>
			<div style="border-bottom:1px solid black;width:240px;">&nbsp;</div><br />
		<?php
		
		for ($i=1; $i <= 12; $i++) {
			$tot = number_format(($income_arr[$i] - $expense_arr[$i]), 2);
			$color = ($tot > 0) ? "#7CA67E" : "#886789";
			echo "<span>".$function->month_string($i).": <strong style='font-size:12pt;color:".$color."'>$ ".$tot."</strong><span></p>";
		}
		?>
	</div>	
	<div align='center'>
		<img src="graphs/year_bar_graph.php" />
	</div>
</div>

