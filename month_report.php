<?php
include_once( 'classes/database.php' );
include_once( 'classes/functions.php' );
include_once( 'classes/layout.php' );

$database 	= new Database();
$database2 	= new Database2();
$functions	= new Functions();
$layout   	= new Layout();

// TODO: Select correct categories, only average needs work if the start date and end date dont spand the entire year

$layout->header();

$month 	= Functions::Get( 'Month' );
$year  	= Functions::Get_Int( 'Year' );

Functions::Validate_User( $database2, $user_info );
Functions::Validate_MonthYear( $month, $year );

$format_date 	= $year."-".$functions->month_int($month)."-00";

Functions::CategoryList_Load_All( $database2, $user_info[ 'id' ], $categories );

$total_budget_diff 	= 0;
$total_incavg_diff 	= 0;
$total_expavg_diff 	= 0;
$total_income		= 0;
$total_expenses		= 0;
?>
<div id="container">
	<?php
		Functions::Output_TopHeader( $user_info[ 'name' ] );
		Functions::Output_LeftNavigation( $month, $year );
	?>
	<div id="content">
		<div id="content_details">
			<h3><?php echo $month.', '.$year; ?></h3>
			<div style="float: right;">
				<a href="ajax/month_report.php">Print</a>
			</div>
			<table cellpadding="1" width="75%" border="1">
				<tr align="left">
					<th nowrap>Category</th>
					<th nowrap>YTD Total</th>
					<th nowrap>Month Total</th>
					<th nowrap>Monthly Budget</th>
					<th nowrap>Monthly Average</th>
					<th nowrap>Difference in Budget</th>
					<!-- <th nowrap>Difference in Average</th> -->
				</tr>
			<?php
				foreach ( $categories as $key => $category )
				{

					$month_int	= Functions::month_int( $month );
					Functions::Item_CategoryDate_Total( $database2, $category[ 'id' ], $month, $year, $user_info[ 'id' ], $month_total );
					Functions::Item_CategoryYearLessMonth_Total( $database2, $category[ 'id' ], $month_int, $year, $user_info[ 'id' ], $year_total );

					echo '<tr>';
					echo '<td nowrap>'.$category[ 'name' ].'</td>';
					echo '<td>$'.number_format( $year_total[ 'total' ], 2 ).'</td>';
					echo '<td>$'.number_format( $month_total[ 'total' ], 2 ).'</td>';

					if ( $category[ 'type_id' ] != 1 && $category[ 'budget'] > 0 )	echo '<td>$'.number_format( $category[ 'budget' ], 2 ).'</td>';
					else															echo '<td>------</td>';

					echo '<td>$'.number_format( ( $year_total[ 'total' ] / $functions->month_int( $month ) ), 2 ).'</td>';

					if ( $category[ 'budget' ] > 0 )	echo '<td>'.number_format( ( $category[ 'budget' ] - $month_total[ 'total' ] ), 2 ).'</td>';
					else								echo '<td>------</td>';

					//echo '<td>'.number_format( ( $month_total[ 'total' ] - ( $year_total[ 'total' ] / $functions->month_int( $month ) ) ), 2 ).'</td>';
					echo '</tr>';

					if ( $category[ 'budget' ] > 0 )
						$total_budget_diff 	+= ( $category[ 'budget' ] - $month_total[ 'total' ] );

					if ( $category[ 'type_id' ] == 1 ) {
						$total_income		+= $month_total[ 'total' ];
						//$total_incavg_diff	+= ( $month_total[ 'total' ] - ( $year_total[ 'total' ] / $functions->month_int( $month ) ) );
					} else {
						$total_expenses		+= $month_total[ 'total' ];
						//$total_expavg_diff	+= ( $month_total[ 'total' ] - ( $year_total[ 'total' ] / $functions->month_int( $month ) ) );
					}

				}
			?>
			</table>

			<table cellpadding="1" width="30%">
				<tr>
					<td><b>Totals</b></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td nowrap>Total Income:</td>
					<td>$<?php echo number_format( $total_income, 2 ); ?></td>
				</tr>
				<tr>
					<td nowrap>Total Expenses:</td>
					<td>$<?php echo number_format( $total_expenses, 2 ); ?></td>
				</tr>
				<tr>
					<td nowrap>Saved</td>
					<td nowrap><?php echo number_format( $total_income - $total_expenses, 2 ); ?> ~ %<?php echo number_format( ( ( ( $total_income - $total_expenses ) / $total_income ) * 100 ), 2 ); ?></td>
				</tr>
				<tr>
					<td nowrap>Total Difference in budget:</td>
					<td><?php echo number_format( $total_budget_diff, 2 ); ?></td>
				</tr>
				<!--
				<tr>
					<td nowrap>Total Income Difference in Average:</td>
					<td><?php echo number_format( $total_incavg_diff, 2 ); ?></td>
				</tr>
				<tr>
					<td nowrap>Total Expenses Difference in Average:</td>
					<td><?php echo number_format( $total_expavg_diff, 2 ); ?></td>
				</tr>
				-->
			</table>
		</div>
	</div>
</div>

<?php $layout->footer(); ?>