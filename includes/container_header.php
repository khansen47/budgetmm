<?php
include_once( "classes/functions.php" );

$functions	= new Functions();
$database2 	= new Database2();

if ( !Functions::User_Load( $database2, $_SESSION[ 'login' ], $user_info ) )
{
	header( 'Location: login.php' );
	die();
}

$month 	= Functions::Get( 'Month' );
$year  	= Functions::Get_Int( 'Year' );

//Validate function for month/year

$home_link		  = 'index.php?Month=' . $month . '&Year=' . $year;
$year_review_link = 'year_review.php?Month=' . $month . '&Year=' . $year;
?>
<div id="container">
	<h1>Budget My Money <span style="float:right;"><a href="logout.php">logout</a>Welcome, <?php echo $user_info[ 'name' ]; ?> -</span></h1>
	<div id="left">
		<h3><a href="<?php echo $home_link; ?>" style="color:#80696B;">Home<a></h3>
		<ul>
			<li><a href="<?php echo $year_review_link; ?>" id="year-review">Year Review</a></li>
			<li><a id="manage-cats">Edit Categories</a></li>
			<li><a id="manage_auto">Edit Reaccuring Bills</a></li>
			<li><a id="all-items">Month Items</a></li>
			<li><a id="month_report">Month Report</a></li>
			<li><a id="all-years">Overall Account</a></li>
		</ul>
	</div>
	<div id="content">
		<div id="content_details">
