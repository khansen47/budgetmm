<?php
include_once( "classes/database.php" );
include_once( "classes/functions.php" );
include_once( "classes/layout.php" );

$database2 	= new Database2();
$function 	= new Functions();
$layout 	= new Layout();

$layout->header();

$cat_id		= Functions::Get_Int( 'Cat_ID' );
$year  		= Functions::Get_Int( 'Year' );
$month 		= Functions::Get( 'Month' );

Functions::Validate_User( $database2, $user_info );
Functions::Validate_MonthYear( $month, $year );

Functions::ItemList_Load_CategoryAll( $database2, $cat_id, $month, $year, $user_info[ 'id' ], $items );
Functions::Item_CategoryDate_Total( $database2, $cat_id, $month, $year, $user_info[ 'id' ], $cat_total );
Functions::Item_CategoryYear_Total( $database2, $cat_id, $year, $user_info[ 'id' ], $cat_year_total );
Functions::CategoryList_Load_All( $database2, $user_info[ 'id' ], $categories );

foreach ( $categories as $key => $category )
{
	if ( $category[ 'type_id' ] == 1 )		$income_list 	= $income_list."<a href='year_review_cat.php?Cat_ID=" . $category[ 'id' ]."&Year=" . $year . "'>".$category[ 'name' ]."</a><br />";
	elseif ( $category[ 'type_id' ] == 2 )	$expense_list 	= $expense_list."<a href='year_review_cat.php?Cat_ID=" . $category[ 'id' ]."&Year=" . $year . "'>".$category[ 'name' ]."</a><br />";
}

Functions::Category_Load_ID( $database2, $cat_id, $category );

$avg_cnt 	= date( 'Y' ) > $year ? 12 : date( 'm' );
$diff 		= $category[ 'budget' ] - number_format( ( $cat_year_total[ 'total' ] / $avg_cnt ), 2 );
?>
<div id="container">
	<?php
		Functions::Output_TopHeader( $user_info[ 'name' ] );
		Functions::Output_LeftNavigation( $month, $year );
	?>
	<div id="content">
		<div id="content_details">
			<h1>Review of <?php echo $year; ?></h1>
			<div style="float: right; margin-top: -50px;"><select id="year_review_select"><?php echo $function->year_review_options( $year ); ?></select></div>
			<div id="man-cat">
				<p>Income</p>	<?php echo $income_list; ?>
				<p>Expenses</p>	<?php echo $expense_list; ?>
			</div>
			<input type="hidden" id="cat_id" value="<?php echo $cat_id; ?>" />
			<div>
				<div id="exp-man-cat">
					<p><?php echo $category[ 'name' ]; ?></p>
					<span style="font-size: 14pt;">Yearly Total: <strong>$ <?php echo number_format( $cat_year_total[ 'total' ], 2 ); ?></strong></span><br /><br />
					<span>Average: <strong style="font-size:12pt;">$ <?php echo number_format( ( $cat_year_total[ 'total' ] / $avg_cnt ), 2 ); ?></strong></span>
					<?php if ( $category[ 'budget' ] > 0 ) { ?>
						<br /><br /><span>Budget: <strong style="font-size:12pt;">$ <?php echo number_format( $category[ 'budget' ], 2); ?></strong></span>
						<br /><br /><span>Difference: <strong style="font-size:12pt;<?php if ($diff >= 0) echo 'color:green'; else echo 'color:red';?>">$ <?php echo number_format( $diff, 2 ); ?></strong></span>
					<?php } ?>
					<div style="border-bottom:1px solid black;width:240px;">&nbsp;</div><br />
					<div id="man-cat-month">
					<?php
					for ( $i = 1; $i <= 12; $i++ )
					{
						$month_name 			= Functions::month_string( $i );

						Functions::Item_CategoryDate_Total( $database2, $cat_id, $month_name, $year, $user_info[ 'id' ], $month_total );

						$month_total 			= $month_total[ 'total' ] ? $month_total[ 'total' ] : 0;
						$year_review_cat_month = 'year_review_cat.php?Cat_ID=' . $cat_id . '&Month=' . $month_name . '&Year=' . $year;

						if ( Functions::month_int( $_SESSION[ 'month' ] ) == $i )	echo "<a href='" . $year_review_cat_month . "''><b>".$month_name."</b>";
						else														echo "<a href='" . $year_review_cat_month . "''>".$month_name;

						echo ": <strong style='font-size:12pt;'>$ ".number_format( $month_total, 2 )."</strong></a></p>";
					}
					?>
					</div>
				</div>
			</div>
			<div class="item-graph">
				<?php $year_review_cat_month_url = "graphs/year_review_graph.php?Year=" . $year . "&Cat_ID=" . $cat_id . "&Budget=" . $category[ 'budget' ] . "&Average=" . ( $cat_year_total[ 'total' ] / $avg_cnt ); ?>
				<img src="<?php echo $year_review_cat_month_url; ?>">
			</div>
			<?php if ( count( $items ) ) { ?>
			<table class="detailed-table" cat-id="<?php echo $cat_id; ?>">
				<tr><td colspan="5"><b><?php echo $month; ?></b></td></tr>
				<tr align="left">
					<th width="20px">Day</th>
					<th width="20px">Amount</th>
					<th width="250px">Comment</th>
				</tr>
			<?php foreach ( $items as $key => $item ) { ?>
				<tr align="left">
					<td><?php echo date("jS", strtotime( $item[ 'date' ] ) ); ?></td>
					<td><?php echo number_format( $item[ 'amount' ], 2 ); ?></td>
					<td><?php echo $item[ 'comment' ]; ?></td>
				</tr>
			<?php } ?>
				<tr align="left">
					<td>Total:</td>
					<td colspan="2"><strong>$<?php echo number_format( $cat_total[ 'total' ], 2 ); ?></strong></td>
				</tr>
			</table>
			<?php } ?>
		</div>
		<div class="details-expand"></div>
	</div>
</div>

<?php $layout->footer(); ?>