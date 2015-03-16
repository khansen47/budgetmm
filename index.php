<?php
require_once( "classes/layout.php" );
include_once( "classes/functions.php" );

$layout 	= new Layout();
$functions	= new Functions();
$database2 	= new Database2();

$layout->header();
$layout->session_inc();

if ( !isset( $_SESSION[ 'login' ] ) )
{
	header( "Location: login.php" );
	die();
}

if ( !$functions->User_Load( $database2, $_SESSION[ 'login' ], $user_info ) )
{
	header( 'Location: login.php' );
	die();
}
?>
<div id="container">
	<h1>Budget My Money <span style="float:right;"><a href="logout.php">logout</a>Welcome, <?php echo $user_info[ 'name' ]; ?> -</span></h1>
	<div id="content">
		<div id="content_details">
			<?php include_once("index_content.php"); ?>
		</div>
		<div class="details-expand"></div>
	</div>
	
	<div id="left">
		<h3><a href="index.php" style="color:#80696B;">Home<a></h3>
		<ul>
			<li><a id="year-review">Year Review</a></li>
			<li><a id="manage-cats">Edit Categories</a></li>
			<li><a id="manage_auto">Edit Reaccuring Bills</a></li>
			<li><a id="all-items">Month Items</a></li>
			<li><a id="month_report">Month Report</a></li>
			<li><a id="all-years">Overall Account</a></li>
		</ul>
	</div>
</div>
