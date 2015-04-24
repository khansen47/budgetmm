<?php
//TODO REMOVE FILE
/*
include_once("../classes/database.php");
include_once("../classes/functions.php");
include_once("../classes/layout.php");

$database2	= new Database2();
$function	= new Functions();
$layout		= new Layout();

$layout->session_inc();

if ( !Functions::User_Load( $database2, $_SESSION[ 'login' ], $user_info ) )
{
	header( 'Location: login.php' );
	die();
}

$cat_id			= Functions::Post_Int( "cat_id" );

Functions::Category_Load_ID( $database2, $cat_id, $category );

$edit_errors	= '';
$success		= '';

$edit_type		= Functions::Post( 'edit_type' );
$edit_name		= Functions::Post( 'edit_name' );
$edit_budget	= Functions::Post( 'edit_budget' );
$edit_s_month	= Functions::Post( 'edit_s_month' );
$edit_s_year	= Functions::Post( 'edit_s_year' );
$edit_e_month	= Functions::Post( 'edit_e_month' );
$edit_e_year	= Functions::Post( 'edit_e_year' );

if ( $edit_s_month == '*' ) 							$edit_s_month 	= '00';
if ( $edit_s_year == '*' )								$edit_s_year 	= '0000';
if ( $edit_e_month == '*' ) 							$edit_e_month 	= '00';
if ( $edit_e_year == '*' )								$edit_e_year 	= '0000';

if ( $edit_s_year == '0000' && $edit_s_month != '00' )	$edit_s_date 	= $edit_s_month;
else 													$edit_s_date 	= $edit_s_year."-".$edit_s_month."-00";

if ( $edit_e_year == '0000' && $edit_e_month != '00' )	$edit_e_date 	= $edit_e_month;
else 													$edit_e_date 	= $edit_e_year."-".$edit_e_month."-00";

if ( $edit_type != "" && $edit_name != "" && $edit_budget != "" )
{
	if ( $edit_type == "" )
	{
		$edit_errors .= "Please do not try to hack<br />";
	}

	if ( $edit_name == "" )
	{
		$edit_errors .= "Please enter a Name<br />";
	}

	if ( $edit_budget != "" && !is_numeric( $edit_budget ) )
	{
		$edit_errors .= "Budget must be a proper number";
	}

	if ( $edit_errors == "" )
	{
		$category[ 'name' ] 		= $edit_name;
		$category[ 'type_id' ] 		= $edit_type;
		$category[ 'budget' ] 		= round( $edit_budget, 2 );
		$category[ 'start_date' ] 	= $edit_s_date;
		$category[ 'end_date' ] 	= $edit_e_date;

		if ( !Functions::Category_Update_ID( $database2, $category ) )
			$edit_errors = $error_message;

		$success = "Updated successfully!";
	}
}


$s_date = explode( "-", $category[ 'start_date' ] );
$e_date = explode( "-", $category[ 'end_date' ] );

if ( sizeof( $s_date ) == 1 )	$s_date = array('0000', $category[ 'start_date' ], '00');
if ( sizeof( $e_date ) == 1 )	$e_date = array('0000', $category[ 'end_date' ], '00');
?>
<span style="color:red;"><?php echo $edit_errors; ?></span>
<span style="color:green;"><?php echo $success; ?></span>

<table cellspacing="2" cellpadding="1">
	<tr>
		<td>Name:</td>
		<td><input type="text" id="edit-name" value="<?php echo $category[ 'name' ]; ?>" /></td>
	</tr>
	<tr>
		<td>Category:</td>
		<td>
			<select id="edit-type">
				<option id="2" <?php if ($category[ 'type_id' ] == 2) echo "selected='Selected'"; ?>>Expense</option>
				<option id="1" <?php if ($category[ 'type_id' ] == 1) echo "selected='Selected'"; ?>>Income</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>Budget:</td>
		<td><input type="text" id="edit-budget" size="10px" value="<?php echo number_format( $category[ 'budget' ], 2 ); ?>" /></td>
	</tr>
	<tr>
		<td>Start Date:</td>
		<td>
			<select id="edit-start-month"><?php echo $function->month_options( $s_date[1] ); ?></select>
			<select id="edit-start-year"><?php echo $function->year_options( $s_date[0] ); ?></select>
		</td>
	</tr>
	<tr>
		<td>End Date:</td>
		<td>
			<select id="edit-end-month"><?php echo $function->month_options( $e_date[1] ); ?></select>
			<select id="edit-end-year"><?php echo $function->year_options( $e_date[0] ); ?></select>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input id="edit-cat-save" type="button" value="Save" /></td>
		<td><input id="delete-cat" type="button" value="Delete" /></td>
	</tr>
</table>
<input type="hidden" id="edit-id" value="<?php echo $cat_id; ?>" />
<?php
*/
?>