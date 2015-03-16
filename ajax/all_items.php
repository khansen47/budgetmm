<?php
include_once("../classes/database.php");
include_once("../classes/functions.php");
include_once("../classes/layout.php");

$database  		= new Database();
$functions 		= new Functions();
$layout    		= new Layout();

$layout->session_inc();

$user_info 		= $functions->get_userInfo($_SESSION['login']);

$items_query	= "SELECT
						i.*,
						c.name,
						c.type_id
					FROM
						item i,
						category c
					WHERE
						MONTHNAME( i.date ) = '".$_SESSION[ 'month' ]."' 	AND
						YEAR( i.date ) 		= '".$_SESSION[ 'year' ]."'		AND
						i.user_id 			= '".$user_info[ 'id' ]."'		AND
						c.id 				= i.cat_id 
					ORDER BY 
						i.date";
					
$items			= $database->mysqli->query( $items_query );
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
		while ( $i_row = $items->fetch_assoc() )
		{
			echo "<tr>
					<td>".date( "jS", strtotime( $i_row[ 'date' ] ) )	."</td>
					<td>".number_format( $i_row[ 'amount' ], 2 )		."</td>
					<td>".$i_row[ 'comment' ]							."</td>
					<td>".$i_row[ 'name' ]								."</td>";
			
			if ( $i_row[ 'type_id' ] == 1 ) 	echo "<td>Income</td>";
			elseif ( $i_row[ 'type_id' ] == 2 ) echo "<td>Expenses</td>";
			
			echo "<td><input type=checkbox /></tr>";
		}
	?>
	</table>
</div>