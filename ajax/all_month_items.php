<?php
include_once( '../classes/database.php' );
include_once( '../classes/functions.php' );
include_once( '../classes/layout.php' );

$database2  = new Database2();
$functions 	= new Functions();
$layout    	= new Layout();

$layout->session_inc();

if ( !Functions::User_Load( $database2, $_SESSION[ 'login' ], $user_info ) )
{
	header( 'Location: login.php' );
	die();
}

Functions::ItemList_Load_Date( $database2, $_SESSION[ 'month' ], $_SESSION[ 'year' ], $user_info[ 'id' ], $items );
?>

<h2>All items for <?php echo $_SESSION[ 'month' ]." ".$_SESSION[ 'year' ]; ?></h2>
<div style="clear:both;" ></div>
<div id="details">
	<table id="all_month_items">
		<tr>
			<th>Date</th>
			<th>Amount</th>
			<th>Comment</th>
			<th>Category</th>
			<th>Category Type</th>
			<th>Check</th>
		</tr>
	<?php
		foreach ( $items as $key => $item )
		{
			Functions::Category_Load_ID( $database2, $item[ 'cat_id' ], $category );

			echo "<tr>
					<td>". date( "jS", strtotime( $item[ 'date' ] ) ) ."</td>
					<td>". number_format( $item[ 'amount' ], 2 ) ."</td>
					<td>". $item[ 'comment' ] ."</td>
					<td>". $category[ 'name' ] ."</td>";

			if ( $category[ 'type_id' ] == 1 ) 		echo "<td>Income</td>";
			elseif ( $category[ 'type_id' ] == 2 ) 	echo "<td>Expenses</td>";

			echo "<td><input type=checkbox /></tr>";
		}
	?>
	</table>
</div>