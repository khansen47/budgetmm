<?php
require_once("../jpgraph/src/jpgraph.php");
require_once("../jpgraph/src/jpgraph_bar.php");
include_once("../classes/database.php");
include_once("../classes/functions.php");
include_once("../classes/layout.php");
$database 	= new Database();
$function 	= new Functions();
$layout 	= new Layout();

$layout->session_inc();
$user_info = $function->get_userInfo($_SESSION['login']);

$month_count = 1;

while ($month_count <= 12) {
	$month_total_income = $database->mysqli->query("SELECT 
														COALESCE( sum(amount), 0 ) as i_amount 
													FROM 
														item 
													WHERE 
														user_id 	= '".$user_info['id']."' 	AND 
														MONTH(date) = ".$month_count." 			AND 
														YEAR(date) 	= '".$_SESSION['year']."'	AND
														EXISTS (SELECT id FROM category WHERE type_id = 1 AND id = cat_id)");
	$temp 								= $month_total_income->fetch_assoc();
	$income_arr[ $month_count - 1 ] 	= ( float )$temp['i_amount'];
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
														MONTH(date) = ".$month_count." 			AND 
														YEAR(date) 	= '".$_SESSION['year']."'	AND
														EXISTS (SELECT id FROM category WHERE type_id = 2 AND id = cat_id)");
	$temp 								= $month_total_expense->fetch_assoc();
	$expense_arr[ $month_count - 1 ] 	= ( float )$temp['i_amount'];
	$month_count++;
}

//Graph
$graph 		= new Graph( 900, 600 );

$graph->SetScale( 'textlin' );
$graph->xaxis->SetTickLabels( array( 'Jan', 'Feb', 'Mar', 'Ap', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ) );

$bar1		= new BarPlot( $income_arr );
$bar2		= new BarPlot( $expense_arr );
$group_bar	= new GroupBarPlot( array( $bar1, $bar2 ) );

$graph->Add( $group_bar );

$bar1->SetColor( 'white' );
$bar1->SetFillColor( '#7CA67E' );

$bar2->SetColor( 'white' );
$bar2->SetFillColor( '#886789' );

$graph->Stroke();
?>