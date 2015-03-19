<?php
include_once( "../classes/database.php" );
include_once( "../classes/functions.php" );
include_once( "../classes/layout.php" );

$database2 	= new Database2();
$function 	= new Functions();
$layout 	= new Layout();

$layout->session_inc();

if ( !Functions::User_Load( $database2, $_SESSION[ 'login' ], $user_info ) )
{
	header( 'Location: login.php' );
	die();
}

$database2->single( "SELECT
						sum(amount) as total
					 FROM
						item
					 WHERE
						user_id = ?	AND
						EXISTS( SELECT id FROM category WHERE id = cat_id AND type_id = 1 AND deprecated = 0 )",
					$total_income, $user_info[ 'id' ] );

$database2->single( "SELECT
						sum(amount) as total
					 FROM
						item
					 WHERE
						user_id = ?	AND
						EXISTS( SELECT id FROM category WHERE id = cat_id AND type_id = 2 AND deprecated = 0 )",
					$total_expenses, $user_info[ 'id' ] );
?>

<h1>Your overall Budget</h1>
<h2>Total Income: <span class="income-th">$<?php echo number_format( $total_income[ 'total' ], 2 ); ?></span></h2>
<h2>Total Expenses: <span class="expense-th">$<?php echo number_format( $total_expenses[ 'total' ], 2 ); ?></span></h2>
<h2>Difference: <span class="<?php if ( ( $total_income[ 'total' ] - $total_expenses[ 'total' ] ) >= 0 ) echo "income-th"; else echo"expense-th"; ?>">$<?php echo number_format( ( $total_income[ 'total' ] - $total_expenses[ 'total' ] ), 2 ); ?></span>