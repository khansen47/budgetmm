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

$cat_id	= $_GET["cat_id"];
$ydata = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
$query = "SELECT
			SUM(amount) as day_amount,
			DAY(date) as day
		  FROM 
			item
		  WHERE
			cat_id 			= ? AND
			YEAR(date)		= ?	AND
			MONTHNAME(date)	= ?
		  GROUP BY
			DAY(date)";
$item_list = $database->mysqli->prepare($query);
$item_list->bind_param("iss", $cat_id, $_SESSION['year'], $_SESSION['month']);
$item_list->bind_result($day_amount, $day);
$item_list->execute();

while($item_list->fetch()) {
	$ydata[$day] = $day_amount;
}

$item_list->close();

$graph 	= new Graph(450,300);

$graph->SetScale('intlin');
$graph->xaxis->title->Set('Day of Month');

$bar1	= new BarPlot( $ydata );

$graph->Add( $bar1 );

$bar1->SetColor('white');
$bar1->SetFillColor( '#7CA67E' );

$graph->Stroke();
?>