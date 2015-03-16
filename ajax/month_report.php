<?php
include_once( '../classes/database.php' );
include_once( '../classes/functions.php' );
include_once( '../classes/layout.php' );

$database 	= new Database();
$functions	= new Functions();
$layout   	= new Layout();

// TODO: Select correct categories, only average needs work if the start date and end date dont spand the entire year

$layout->session_inc();

$user_info		= $functions->get_userInfo( $_SESSION[ 'login' ] );

$format_date 	= $_SESSION[ 'year' ]."-".$functions->month_int($_SESSION['month'])."-00";

$categories 	= $database->mysqli->query("SELECT 
												* 
											FROM 
												category 
											WHERE 
												user_id 	= '".$user_info['id']."' AND
												deprecated 	= 0
											ORDER BY 
												type_id, name");

$total_budget_diff 	= 0;
$total_incavg_diff 	= 0;
$total_expavg_diff 	= 0;
$total_income		= 0;
$total_expenses		= 0;
?>


<h3><?php echo $_SESSION['month'].', '.$_SESSION['year']; ?></h3> 
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
	while ( $cat_row = $categories->fetch_assoc() ) 
	{
		$query		= "SELECT 
						SUM(amount) as total
					   FROM 
						item 
					   WHERE 
						cat_id 			= ? AND 
						user_id 		= ?	AND 
						MONTHNAME(date) = ? AND
						YEAR(date)		= ?";
		
		$month_tot	= $database->mysqli->prepare( $query );
		$month_tot->bind_param( "issi", $cat_row[ 'id' ], $user_info[ 'id' ], $_SESSION[ 'month' ], $_SESSION[ 'year' ] );
		$month_tot->bind_result( $month_total );
		$month_tot->execute();
		$month_tot->fetch();
		$month_tot->close();
												
		$query		= "SELECT 
						SUM(i.amount) as cat_total
					   FROM 
						item i 
					   WHERE 
						i.cat_id 		= 	? AND 
						i.user_id 		= 	? AND 
						MONTH( i.date ) <=	? AND
						YEAR( i.date ) 	= 	?";
		$totals 	= $database->mysqli->prepare( $query );
		$month_int	= $functions->month_int( $_SESSION[ 'month' ] );
		$totals->bind_param( "issi", $cat_row[ 'id' ], $user_info[ 'id' ], $month_int, $_SESSION[ 'year' ] );
		$totals->bind_result( $year_total );
		$totals->execute();
		$totals->fetch();
		$totals->close();

		echo '<tr>';
		echo '<td nowrap>'.$cat_row[ 'name' ].'</td>';
		echo '<td>$'.number_format( $year_total, 2 ).'</td>';
		echo '<td>$'.number_format( $month_total, 2 ).'</td>';

		if ( $cat_row[ 'type_id' ] != 1 && $cat_row[ 'budget'] > 0 )	
											echo '<td>$'.number_format( $cat_row[ 'budget' ], 2 ).'</td>';
		else								echo '<td>------</td>';
			
		echo '<td>$'.number_format( ( $year_total / $functions->month_int( $_SESSION[ 'month' ] ) ), 2 ).'</td>';
		
		if ( $cat_row[ 'budget' ] > 0 )		echo '<td>'.number_format( ( $cat_row[ 'budget' ] - $month_total ), 2 ).'</td>';
		else								echo '<td>------</td>';

		//echo '<td>'.number_format( ( $month_total - ( $year_total / $functions->month_int( $_SESSION[ 'month' ] ) ) ), 2 ).'</td>';
		echo '</tr>';
		
		if ( $cat_row[ 'budget' ] > 0 )
			$total_budget_diff 	+= ( $cat_row[ 'budget' ] - $month_total );
		
		if ( $cat_row[ 'type_id' ] == 1 ) {
			$total_income		+= $month_total;
			//$total_incavg_diff	+= ( $month_total - ( $year_total / $functions->month_int( $_SESSION[ 'month' ] ) ) );
		} else {
			$total_expenses		+= $month_total;
			//$total_expavg_diff	+= ( $month_total - ( $year_total / $functions->month_int( $_SESSION[ 'month' ] ) ) );
		}
	} ?>
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
