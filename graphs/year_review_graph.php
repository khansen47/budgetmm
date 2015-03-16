<?php
require_once("../jpgraph/src/jpgraph.php");
require_once("../jpgraph/src/jpgraph_line.php");
require_once("../jpgraph/src/jpgraph_bar.php");
include_once("../classes/database.php");
include_once("../classes/functions.php");
include_once("../classes/layout.php");
$database 	= new Database();
$function 	= new Functions();
$layout 	= new Layout();

$layout->session_inc();
$user_info = $function->get_userInfo($_SESSION['login']);

$cat_id	= $_GET["cat_id"];
$budget	= $_GET["budget"];
$avg	= $_GET["avg"];

$ydata 	= array(0,0,0,0,0,0,0,0,0,0,0,0);
$bdata	= array($budget, $budget, $budget, $budget, $budget, $budget, $budget, $budget, $budget, $budget, $budget, $budget);
$adata	= array($avg, $avg, $avg, $avg, $avg, $avg, $avg, $avg, $avg, $avg, $avg, $avg);

$query 	= "SELECT
			SUM(amount) as month_amount,
			MONTH(date) as month
		   FROM 
			item
		   WHERE
			cat_id 			= ? AND
			YEAR(date)		= ?
		   GROUP BY
			MONTH(date)";
$item_list = $database->mysqli->prepare($query);
$item_list->bind_param("is", $cat_id, $_SESSION['year']);
$item_list->bind_result($month_amount, $month);
$item_list->execute();

while($item_list->fetch()) {
	$ydata[$month-1] = $month_amount;
}

$item_list->close();

// Create the graph. These two calls are always required
$graph = new Graph(600,500);
$graph->SetScale('textlin');
$graph->xaxis->SetTickLabels(array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'));

// Create the linear plot
$bar = new BarPlot($ydata);
$graph->Add($bar);
$bar->SetColor('white');
$bar->SetFillColor("#7CA67E");
$bar->SetLegend('Amount');

if ($budget > 0) {
	$budgetline= new LinePlot($bdata);
	$budgetline->SetColor('yellow');
	$budgetline->SetLegend('Budget');
}

$avgline= new LinePlot($adata);
$avgline->SetColor('orange');
$avgline->SetLegend('Average');


// Add the plot to the graph
$graph->Add($avgline);
if ($budget > 0) $graph->Add($budgetline);

// Display the graph
$graph->Stroke();
?>