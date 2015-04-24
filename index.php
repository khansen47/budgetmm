<?php
/*
	File TODO:
		CLEAN UP FILE
		Onchange year month, set post variables?
		Iframes?
		Ajax. change to use the new ajax stuff
		Categeory Review
		content_details should not be limited and should expand the page
*/
include_once( "classes/database.php" );
include_once( "classes/functions.php" );
include_once( "classes/layout.php" );

$database2		= new Database2();
$functions 		= new Functions();
$layout 		= new Layout();

$layout->header();

$month 	= Functions::Get( 'Month' );
$year  	= Functions::Get_Int( 'Year' );
$cat_id = Functions::Get_Int( 'Category_ID' );

Functions::Validate_User( $database2, $user_info );
Functions::Validate_MonthYear( $month, $year );
Functions::Validate_Category( $database2, $cat_id );

Functions::Item_Total_TypeID_MonthYear( $database2, 1, $user_info[ 'id' ], $month, $year, $income_total );
Functions::Item_Total_TypeID_MonthYear( $database2, 2, $user_info[ 'id' ], $month, $year, $expenses_total );

$monthly_total = $income_total[ 'total' ] - $expenses_total[ 'total' ];

if ( $monthly_total < 0 ) 		$head_span_color = "#CF969B";
else							$head_span_color = "#8AB381";

if ( $month == date( "F" ) )	$day = date( "d" );
else							$day = '01';

Functions::ItemList_Load_CategoryAll( $database2, $cat_id, $month, $year, $user_info[ 'id' ], $items );
Functions::Item_CategoryDate_Total( $database2, $cat_id, $month, $year, $user_info[ 'id' ], $cat_total );
Functions::Item_CategoryYear_Total( $database2, $cat_id, $year, $user_info[ 'id' ], $cat_year_total );
?>
<div id="container">
	<?php
		Functions::Output_TopHeader( $user_info[ 'name' ] );
		Functions::Output_LeftNavigation( $month, $year );
		Functions::Output_ChangeDate_Form( 'index.php', $month, $year );
	?>
	<div id="content">
		<div id="content_details">
			<div id="head-div">
				<?php echo "<h2>".$month.", ".$year."<span style='color: ".$head_span_color."'>".number_format( $monthly_total, 2 )."</span></h2>"; ?>
			</div>
			<div style="clear:both"></div>
			<div id="totals">
				<p>Income: 		<span>$<?php echo number_format( $income_total[ 'total' ], 2 ); ?></span></p>
				<p>Expenses: 	<span>$<?php echo number_format( $expenses_total[ 'total' ], 2 ); ?></span></p>
			</div>
			<div id="quick-add">
				<?php echo Functions::Category_Draw_FormSelect( $database2, $user_info[ 'id' ], $cat_id, $month, $year ); ?>
				Amount: 	<input type="text" 		id="quick-amount" 		value="0"/>
				Date: 		<input type="date" 		id="quick-date" 		value="<?php echo $year . '-' . Functions::month_int( $month ). '-' . $day; ?>" />
				Comment: 	<input type="text" 		id="quick-comment" 		value="" />
							<input type="button"	id="add-item-submit" 	value="Add"  />
			</div>
			<div style="clear: both;" ></div>
			<br />
			<div id="details">
				<table width="20%">
					<?php
					Functions::CategoryList_Load_Month( $database2, $user_info[ 'id' ], $month, $year, $categories );

					foreach ( $categories as $key => $category )
					{
						Functions::Item_CategoryDate_Total( $database2, $category[ 'id' ], $month, $year, $user_info[ 'id' ], $cat_total );

						if ( $category[ 'type_id' ] == 1 )		$class = 'income-th';
						else if ( $category[ 'type_id' ] == 2 ) $class = 'expense-th';

						$link = 'index.php?Category_ID=' . $category[ 'id' ]. '&Month=' . $month . '&Year=' . $year;

						echo "<tr>
								<th nowrap align='right' class='".$class."'><a href='". $link . "'>".$category[ 'name' ]."</a></th>
								<td align='left' class='money-td'>".number_format( $cat_total[ 'total' ], 2 )."</td>
							  </tr>";
					} ?>
				</table>
			</div>
		</div>
		<div class="details-expand">
		<?php  Functions::Category_Load_ID( $database2, $cat_id, $category ); ?>
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
		</div>
	</div>
</div>
<?php
	$layout->footer();
?>