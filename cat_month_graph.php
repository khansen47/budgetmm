<?php
require_once( "jpgraph/src/jpgraph.php" );
require_once( "jpgraph/src/jpgraph_line.php" );
require_once( "classes/database.php" );
require_once( "classes/functions.php" );
require_once( "classes/layout.php" );

$database 	= new Database();
$database2 	= new Database2();
$function 	= new Functions();
$layout 	= new Layout();

$layout->session_inc();
//$user_info = $function->get_userInfo($_SESSION['login']);

if ( !$functions->User_Load( $database2, $_SESSION[ 'login' ], $user_info ) )
{
	header( 'Location: login.php' );
	die();
}

$cat_id	= $_GET[ "cat_id" ];
$ydata = array( 0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0 );

$function->ItemList_Load_CatIDAndMonth( $database2, $cat_id, $_SESSION[ 'month' ], $items );

foreach ($items as $key => $item)
{
	$ydata[ $item[ 'day' ] ] = $item[ 'day_amount' ];
}

// Create the graph. These two calls are always required
$graph = new Graph( 450, 300 );
$graph->SetScale( 'intlin' );
$graph->xaxis->title->Set( 'Day of Month' );

// Create the linear plot
$lineplot=new LinePlot( $ydata );
$lineplot->SetColor( '#000000' );

// Add the plot to the graph
$graph->Add( $lineplot );

// Display the graph
$graph->Stroke();
?>