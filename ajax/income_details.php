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
<?php
	$query		= "SELECT
					name, type_id, budget, start_date, end_date
				   FROM
					category
				   WHERE
					id 		= ? AND
					user_id = ?";
	$cat_info 	= $database->mysqli->prepare($query);
	$cat_info->bind_param("ii", $cat_id, $user_info['id']);
	$cat_info->bind_result($name, $type_id, $budget, $start_date, $end_date);
	$cat_info->execute();
	$cat_info->fetch();
	$cat_info->close();

	$query		= "SELECT
					SUM(i.amount) as cat_total
				   FROM
					item i
				   WHERE
					i.user_id 		= ?	AND
					i.cat_id 		= ?	AND
					YEAR(i.date) 	= '".$_SESSION['year']."'";
	$totals 	= $database->mysqli->prepare($query);
	$totals->bind_param("ii", $user_info['id'], $cat_id);
	$totals->bind_result($category_total);
	$totals->execute();
	$totals->fetch();
	$totals->close();

	$query		= "SELECT
					MONTHNAME(i.date) as month,
					SUM(i.amount) as m_total
				   FROM
					item i
				   WHERE
					i.user_id 		= ?	AND
					i.cat_id 		= ?	AND
					YEAR(i.date)	= '".$_SESSION['year']."'
				   GROUP BY
					MONTH(date)";

	$month_tot	= $database->mysqli->prepare($query);
	$month_tot->bind_param("ii", $user_info['id'], $cat_id);
	$month_tot->execute();
	$month_tot->store_result();
	$months_used = $month_tot->num_rows;
	if ( $months_used == NULL || $months_used == 0 )
		$months_used = 1;

	$month_tot->close();
?>
	<h2><?php echo $name; ?> Details</h2>
		<table id="cat-details" cellspacing="2" cellpadding="2">
	<?php if ($type_id == 2) {?>
		<tr>
			<td>Budget:</td>
			<td><b>$ <?php echo number_format( $budget, 2 ); ?></b></td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td>Yearly Total:</td>
			<td><b>$ <?php echo number_format( $category_total, 2); ?></b></td>
		</tr>
		<tr>
			<td>Monthly Average Spent:</td>
			<td><b>$ <?php echo number_format( ($category_total/$months_used), 2); ?></b></td>
		</tr>
		<tr>
			<td>Spent This Month:</td>
			<td><b>$ <?php echo number_format( $cat_total[ 'total' ], 2 ); ?></b></td>
		</tr>
		<?php 	$diff_budget = number_format( ( $budget - $cat_total[ 'total' ] ), 2 );
				$diff_avg	 = number_format( ( ( $category_total / $months_used ) - $cat_total[ 'total' ] ), 2 );
				if ($diff_budget < 0)	$d_b_style = "style='color: red;'";
				else					$d_b_style = "style='color:green;'";
				if ($diff_avg < 0) 		$d_a_style = "style='color: red;'";
				else					$d_a_style = "style='color: green;'";
		?>
		<?php if ($budget > 0) {?>
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
			<td><b>$ <?php echo number_format( $category_total, 2 ); ?></b></td>
		</tr>
		<tr>
			<td>Monthly Average:</td>
			<td><b>$ <?php echo number_format( $category_total / $months_used, 2 ); ?></b></td>
		</tr>
		<tr>
			<td>Earned this Month:</td>
			<td><b>$ <?php echo number_format( $cat_total[ 'total' ], 2 ); ?></b></td>
		</tr>
		<tr>
			<td>Difference:</td>
			<td><b>$ <?php echo number_format( $cat_total[ 'total' ] - ( $category_total / $months_used ), 2 ); ?></b></td>
		</tr>
	<?php  } ?>
	</table>
</div>
<div class="item-graph">
	<img src="graphs/cat_month_graph.php?cat_id=<?php echo $cat_id; ?>">
</div>