<?php
include_once("../classes/database.php");
include_once("../classes/functions.php");
include_once("../classes/layout.php");
$database 	= new Database();
$function 	= new Functions();
$layout 	= new Layout();

$layout->session_inc();

$user_info 	= $function->get_userInfo($_SESSION['login']);

$inc_query 	=  "SELECT 
					sum(amount) 
				FROM 
					item 
				WHERE 
					user_id = ?	AND
					EXISTS( SELECT id FROM category WHERE id = cat_id AND type_id = 1 AND deprecated = 0 )";
$total_inc	= $database->mysqli->prepare($inc_query);
$total_inc->bind_param("i", $user_info['id']);
$total_inc->bind_result($total_income);
$total_inc->execute();
$total_inc->fetch();
$total_inc->close();

$exp_query 	=  "SELECT 
					sum(amount) 
				FROM 
					item 
				WHERE 
					user_id = ?	AND
					EXISTS( SELECT id FROM category WHERE id = cat_id AND type_id = 2 AND deprecated = 0 )";
$total_exp	= $database->mysqli->prepare($exp_query);
$total_exp->bind_param("i", $user_info['id']);
$total_exp->bind_result($total_expenses);
$total_exp->execute();
$total_exp->fetch();
$total_exp->close();

?>

<h1>Your overall Budget</h1>
<h2>Total Income: <span class="income-th">$<?php echo number_format($total_income, 2); ?></span></h2>
<h2>Total Expenses: <span class="expense-th">$<?php echo number_format($total_expenses, 2); ?></span></h2>
<h2>Difference: <span class="<?php if (($total_income-$total_expenses) >= 0) echo "income-th"; else echo"expense-th"; ?>">$<?php echo number_format( ( $total_income - $total_expenses ), 2 ); ?></span>