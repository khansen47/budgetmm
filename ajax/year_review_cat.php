<?php
include_once( "../classes/database.php" );
include_once( "../classes/functions.php" );
include_once( "../classes/layout.php" );

$database 	= new Database();
$database2 	= new Database2();
$function 	= new Functions();
$layout 	= new Layout();

$layout->session_inc();

$cat_id		= Functions::Post_Int( 'cat_id' );
$year		= Functions::Post_Int( 'year' ) ? Functions::Post_Int( 'year' ) : $_SESSION[ 'year' ];
$month		= Functions::Post( 'month' ) 	? Functions::Post( 'month' ) 	: $_SESSION[ 'month' ];

if ( !Functions::User_Load( $database2, $_SESSION[ 'login' ], $user_info ) )
{
	header( 'Location: login.php' );
	die();
}

Functions::ItemList_Load_CategoryAll( $database2, $cat_id, $month, $year, $user_info[ 'id' ], $items );
Functions::Item_CategoryDate_Total( $database2, $cat_id, $month, $year, $user_info[ 'id' ], $cat_total );
Functions::Item_CategoryYear_Total( $database2, $cat_id, $year, $user_info[ 'id' ], $cat_year_total );
Functions::Category_Load_ID( $database2, $cat_id, $category );

$avg_cnt 	= date( 'Y' ) > $year ? 12 : date( 'm' );
$diff 		= $category[ 'budget' ] - number_format( ( $cat_year_total[ 'total' ] / $avg_cnt ), 2 );
?>
<input type="hidden" id="cat_id" value="<?php echo $cat_id; ?>" />
<div>
	<div id="exp-man-cat">
		<p><?php echo $category[ 'name' ]; ?></p>
		<span style="font-size: 14pt;">Yearly Total: <strong>$ <?php echo number_format( $cat_year_total[ 'total' ], 2 ); ?></strong></span><br /><br />
		<span>Average: <strong style="font-size:12pt;">$ <?php echo number_format( ( $cat_year_total[ 'total' ] / $avg_cnt ), 2 ); ?></strong></span>
		<?php if ($category[ 'budget' ] > 0) { ?>
			<br /><br /><span>Budget: <strong style="font-size:12pt;">$ <?php echo number_format( $category[ 'budget' ], 2); ?></strong></span>
			<br /><br /><span>Difference: <strong style="font-size:12pt;<?php if ($diff >= 0) echo 'color:green'; else echo 'color:red';?>">$ <?php echo number_format( $diff, 2 ); ?></strong></span>
		<?php } ?>
		<div style="border-bottom:1px solid black;width:240px;">&nbsp;</div><br />
		<div id="man-cat-month">
		<?php
		for ( $i = 1; $i <= 12; $i++ )
		{
			$month_name = Functions::month_string( $i );

			Functions::Item_CategoryDate_Total( $database2, $cat_id, $month_name, $year, $user_info[ 'id' ], $month_total );
			
			$month_total = $month_total[ 'total' ] ? $month_total[ 'total' ] : 0;

			if ( Functions::month_int( $_SESSION[ 'month' ] ) == $i )	echo "<span style='cursor: pointer; text-decoration: underline;' month='".$month_name."'><b>".$month_name."</b>";
			else														echo "<span style='cursor: pointer; text-decoration: underline;' month='".$month_name."'>".$month_name;

			echo ": <strong style='font-size:12pt;'>$ ".number_format( $month_total, 2 )."</strong></span></p>";
		}
		?>
		</div>
	</div>
</div>
<div class="item-graph">
	<img src="graphs/year_review_graph.php?cat_id=<?php echo $cat_id; ?>&budget=<?php echo $category[ 'budget' ]; ?>&avg=<?php echo $cat_year_total[ 'total' ] / $avg_cnt; ?>">
</div>
<?php if ( count( $items ) ) { ?>
<table class="detailed-table" cat-id="<?php echo $cat_id; ?>">
	<tr><td colspan="5"><b><?php echo $month; ?></b></td></tr>
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
<?php } ?>