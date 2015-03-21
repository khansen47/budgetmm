<?php
/*
	File TODO:
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

Functions::Validate_User( $database2, $user_info );
Functions::Validate_MonthYear( $month, $year );

Functions::Item_Total_TypeID_MonthYear( $database2, 1, $user_info[ 'id' ], $month, $year, $income_total );
Functions::Item_Total_TypeID_MonthYear( $database2, 2, $user_info[ 'id' ], $month, $year, $expenses_total );

$monthly_total = $income_total[ 'total' ] - $expenses_total[ 'total' ];

if ( $monthly_total < 0 ) 		$head_span_color = "#CF969B";
else							$head_span_color = "#8AB381";

if ( $month == date( "F" ) )	$day = date( "d" );
else							$day = '01';
?>
<div id="container">
	<?php
		Functions::Output_TopHeader( $user_info[ 'name' ] );
		Functions::Output_LeftNavigation( $month, $year );
	?>
	<div id="content">
		<div id="content_details">
			<div id="head-div">
				<?php echo "<h2>".$month.", ".$year."<span style='color: ".$head_span_color."'>".number_format( $monthly_total, 2 )."</span></h2>"; ?>
			</div>
			<div id="month-select-div">
				View Different Month: <?php echo $functions->get_months( $month )." ".$functions->get_years( $year ); ?>
			</div>
			<div style="clear:both"></div>
			<div id="totals">
				<p>Income: 		<span>$<?php echo number_format( $income_total[ 'total' ], 2 ); ?>	</span></p>
				<p>Expenses: 	<span>$<?php echo number_format( $expenses_total[ 'total' ], 2 ); ?>	</span></p>
			</div>
			<div id="quick-add">
				<b>Quick Add:</b> 	<select id="quick-type">
										<?php echo $functions->cat_select_list($user_info[ 'id' ]); ?>
									</select>
				Amount: 			<input type="text" 		id="quick-amount" 		value="0"/>
				Date: 				<input type="date" 		id="quick-date" 		value="<?php echo $year . '-' . $functions->month_int( $month ). '-' . $day; ?>" />
				Comment: 			<input type="text" 		id="quick-comment" />
									<input type="button"	id="quick-add-submit" 	value="Add"  />
									<span id="quick-report"></span>
			</div>
			<div style="clear: both;" ></div>
			<br />
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

						echo "<tr>
								<th nowrap align='right' id='".$category[ 'id' ]."' class='".$class."'>".$category[ 'name' ]."</th>
								<td align='left' class='money-td'>".number_format( $cat_total[ 'total' ], 2 )."</td>
							  </tr>";
					} ?>
				</table>
			</div>
		</div>
		<div class="details-expand"></div>
	</div>
</div>
<?php
	$layout->footer();
?>