<?php
include_once( "../classes/database.php" );
include_once( "../classes/functions.php" );
include_once( "../classes/layout.php" );

$database 		= new Database();
$database2		= new Database2();
$functions 		= new Functions();
$layout 		= new Layout();

$layout->session_inc();

$cat_id 		= Functions::Post_Int( 'cat_id' );
$month 			= $_SESSION[ 'month' ] ? $_SESSION[ 'month' ] : date( "F" );
$year			= $_SESSION[ 'year' ] ? $_SESSION[ 'year' ] : date( "Y" );

if ( !Functions::User_Load( $database2, $_SESSION[ 'login' ], $user_info ) )
{
	header( 'Location: login.php' );
	die();
}

Functions::ItemList_Load_CategoryAll( $database2, $cat_id, $month, $year, $user_info[ 'id' ], $items );
Functions::Item_CategoryDate_Total( $database2, $cat_id, $month, $year, $user_info[ 'id' ], $cat_total );
Functions::Item_CategoryYear_Total( $database2, $cat_id, $year, $user_info[ 'id' ], $cat_year_total );
Functions::Category_Load_ID( $database2, $cat_id, $category );
?>
<table class="detailed-table" cat-id="<?php echo $cat_id; ?>">
	<tr align="left">
		<th width="20px">Day</th>
		<th width="20px">Amount</th>
		<th width="250px">Comment</th>
		<th width="5px">Edit</th>
		<th width="5px">Delete</th>
	</tr>
<?php foreach ( $items as $key => $item ) { ?>
	<tr align="left">
		<td><?php echo date("jS", strtotime( $item[ 'date' ] ) ); ?></td>
		<td><?php echo number_format( $item[ 'amount' ], 2 ); ?></td>
		<td><?php echo $item[ 'comment' ]; ?></td>
		<td id="edit-item" item-id="<?php echo $item[ 'id' ]; ?>"><a>edit</a></td>
		<td><a id="delete-item" item-id="<?php echo $item[ 'id' ]; ?>">Delete</a></td>
	</tr>
<?php } ?>
	<tr align="left">
		<td>Total:</td>
		<td><strong>$<?php echo number_format( $cat_total[ 'total' ], 2 ); ?></strong></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
</table>

<div class="detailed-div">
	<h2><?php echo $category[ 'name' ]; ?> Details</h2>
		<table id="cat-details" cellspacing="2" cellpadding="2">
	<?php if ( $category[ 'type_id' ] == 2 ) {?>
		<tr>
			<td>Budget:</td>
			<td><b>$ <?php echo number_format( $category[ 'budget' ], 2 ); ?></b></td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td>Yearly Total:</td>
			<td><b>$ <?php echo number_format( $cat_year_total[ 'total' ], 2); ?></b></td>
		</tr>
		<tr>
			<td>Monthly Average:</td>
			<td><b>$ <?php echo number_format( ($cat_year_total[ 'total' ] / Functions::month_int( $month ) ), 2); ?></b></td>
		</tr>
		<tr>
			<td>Spent This Month:</td>
			<td><b>$ <?php echo number_format( $cat_total[ 'total' ], 2 ); ?></b></td>
		</tr>
		<?php
			$diff_budget = number_format( ( $category[ 'budget' ] - $cat_total[ 'total' ] ), 2 );
			$diff_avg	 = number_format( ( ( $cat_year_total[ 'total' ] / Functions::month_int( $month ) ) - $cat_total[ 'total' ] ), 2 );

			if ($diff_budget < 0)	$d_b_style = "style='color: red;'";
			else					$d_b_style = "style='color:green;'";

			if ($diff_avg < 0) 		$d_a_style = "style='color: red;'";
			else					$d_a_style = "style='color: green;'";
		?>
		<?php if ($category[ 'budget' ] > 0) {?>
		<tr>
			<td>Difference in Budget:</td>
			<td <?php echo $d_b_style; ?>><b>$ <?php echo $diff_budget; ?></b></td>
		</tr>
		<?php } ?>
		<tr>
			<td>Difference in Average:</td>
			<td <?php echo $d_a_style; ?>><b>$ <?php echo $diff_avg; ?></b></td>
		</tr>
	<?php } else {?>
		<tr>
			<td>Yearly Total:</td>
			<td><b>$ <?php echo number_format( $cat_year_total[ 'total' ], 2 ); ?></b></td>
		</tr>
		<tr>
			<td>Monthly Average:</td>
			<td><b>$ <?php echo number_format( $cat_year_total[ 'total' ] / Functions::month_int( $month ), 2 ); ?></b></td>
		</tr>
		<tr>
			<td>Earned this Month:</td>
			<td><b>$ <?php echo number_format( $cat_total[ 'total' ], 2 ); ?></b></td>
		</tr>
		<tr>
			<td>Difference:</td>
			<td><b>$ <?php echo number_format( $cat_total[ 'total' ] - ( $cat_year_total[ 'total' ] / Functions::month_int( $month ) ), 2 ); ?></b></td>
		</tr>
	<?php  } ?>
	</table>
</div>
<div class="item-graph">
	<img src="graphs/cat_month_graph.php?cat_id=<?php echo $cat_id; ?>">
</div>