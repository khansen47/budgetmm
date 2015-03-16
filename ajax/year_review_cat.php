<?php
include_once("../classes/database.php");
include_once("../classes/functions.php");
include_once("../classes/layout.php");

$database 	= new Database();
$function 	= new Functions();
$layout 	= new Layout();

$layout->session_inc();

$user_info 			= $function->get_userInfo($_SESSION['login']);
$cat_id				= $function->post("cat_id");
$year				= $function->post("year");

if ( $year == '' )
{
	$year 			= $_SESSION['year'];
}

$year_clause		= ' AND YEAR( date ) = "'.$year.'"';

if ( $year == 'all' )
{
	$year_clause	= '';
}

$totals 			= $database->mysqli->query("SELECT 
													SUM(i.amount) as cat_total, 
													(SELECT name FROM category WHERE id = '".$cat_id."') as cat_name 
												FROM 
													item i 
												WHERE 
													i.user_id 		= '".$user_info['id']."' 	AND 
													i.cat_id 		= '".$cat_id."' ".
													$year_clause );
$total_assoc 		= $totals->fetch_assoc();
												
$month_count = 1;
$month_totals = "";

while ( $month_count <= 12 ) {
	$month_totals_cat 		= $database->mysqli->query("SELECT 
															COALESCE( sum(i.amount), 0 ) as i_amount 
														FROM 
															item i 
														WHERE 
															i.user_id 		= '".$user_info['id']."' 	AND 
															i.cat_id 		= '".$cat_id."' 			AND 
															MONTH(i.date)	= '".$month_count."' ".
															$year_clause ."
														GROUP BY 
															MONTH(date)");
	$temp 						= $month_totals_cat->fetch_assoc();
	$month_totals				= $month_totals."<span>".$function->month_string($month_count).": <strong style='font-size:12pt;'>$ ".number_format($temp['i_amount'], 2)."</strong><span></p>";
	$month_count++;
}
													
$budget 			= $database->mysqli->query("SELECT 
													budget 
												FROM 
													category 
												WHERE 
													id = '".$cat_id."'");
$budget_tot 		= $budget->fetch_assoc();

if (date("Y") > $year)
	$avg_cnt = 12;
else
	$avg_cnt = date("m");
?>
<div id="man-cat-inside">
	<div id="exp-man-cat">
		<p><?php echo $total_assoc['cat_name']; ?></p>
		<span style="font-size: 14pt;">Yearly Total: <strong>$ <?php echo number_format($total_assoc['cat_total'], 2); ?></strong></span><br /><br />
		<span>Average: <strong style="font-size:12pt;">$ <?php echo number_format(($total_assoc['cat_total']/$avg_cnt), 2); ?></strong></span>
		<?php if ($budget_tot['budget'] > 0) { ?>
			<br /><br /><span>Budget: <strong style="font-size:12pt;">$ <?php echo number_format( $budget_tot['budget'], 2); ?></strong></span>
		<?php $diff = $budget_tot['budget']-number_format(($total_assoc['cat_total']/$avg_cnt), 2); ?>
			<br /><br /><span>Difference: <strong style="font-size:12pt;<?php if ($diff >= 0) echo 'color:green'; else echo 'color:red';?>">$ <?php echo number_format( $diff, 2); ?></strong></span>
		<?php } ?>
		<div style="border-bottom:1px solid black;width:240px;">&nbsp;</div><br />
		<?php echo $month_totals;?>
	</div>
</div>
<div class="item-graph">
	<img src="graphs/year_review_graph.php?cat_id=<?php echo $cat_id; ?>&budget=<?php echo $budget_tot['budget']; ?>&avg=<?php echo $total_assoc['cat_total']/$avg_cnt; ?>">
</div>
