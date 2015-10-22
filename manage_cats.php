<?php
include_once( 'classes/database.php' );
include_once( 'classes/functions.php' );
include_once( 'classes/layout.php' );

$database 	= new Database();
$database2 	= new Database2();
$function 	= new Functions();
$layout 	= new Layout();

$layout->header();

$month 		= Functions::Get( 'Month' );
$year  		= Functions::Get_Int( 'Year' );
$cat_id		= Functions::Get_Int( 'Category_ID' );

Functions::Validate_User( $database2, $user_info );
Functions::Validate_MonthYear( $month, $year );

Functions::CategoryList_Load_All( $database2, $user_info[ 'id' ], $categories );
Functions::Category_Load_ID( $database2, $cat_id, $category );

?>
<div id="container">
	<?php
		Functions::Output_TopHeader( $user_info[ 'name' ] );
		Functions::Output_LeftNavigation( $month, $year );
		Functions::Output_ChangeDate_Form( 'manage_cats.php', $month, $year );
	?>
	<div id="content">
		<div id="content_details">
			<div class="header">
				<h1>Manage Categories</h1>
			</div>
			<div id="cat-content">
				<div id="income-cats">
					<h2>Income</h2>
					<?php
						$expenses 	= 0;
						$class 		= 'income-th';

						foreach ( $categories as $key => $cat )
						{
							if ( $cat[ 'type_id' ] ==  2 && $expenses == 0 )
							{
								$expenses 	= 1;
								$class 		= 'expense-th';
								echo '</div><div id="expenses-cats"><h2>Expenses</h2>';
							}

							$url = 'manage_cats.php?Category_ID=' . $cat[ 'id' ] .'&Month=' . $month . '&Year=' . $year;

							echo '<span class="' . $class . '"><a href="' . $url . '">' . $cat[ 'name' ] . '</a></span><br />';
						}
					?>
				</div>
			</div>
			<div id="add-cat">
				<h3>Add Category:</h3>
				<table cellspacing="2" cellpadding="1">
					<tr>
						<td><b>Type:</b></td>
						<td>
							<select id="add_type_id">
								<option value='2'>Expense</option>
								<option value="1">Income</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><b>Name:</b></td>
						<td><input type="text" id="add_cat_name" value="" /></td>
					</tr>
					<tr>
						<td><b>Budget:</b></td>
						<td><input type="text" id="add_cat_budget" size="10px" value="0" /></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><input type="checkbox" id="add_cat_cntrl_bdgt" value="1" />Controlled Budget</td>
					</tr>
					<tr>
						<td><b>Start Date:</b></td>
						<td>
							<select id="add_start_month"><?php echo Functions::month_options( '00' ); ?></select>
							<select id="add_start_year"><?php echo Functions::year_options( '0000' ); ?></select>
						</td>
					</tr>
					<tr>
						<td><b>End Date:</b></td>
						<td>
							<select id="add_end_month"><?php echo Functions::month_options( '00' ); ?></select>
							<select id="add_end_year"><?php echo Functions::year_options( '0000' ); ?></select>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><input type="button" id="add_cat_submit" value="Add" /></td>
					</tr>
				</table>
				<input type="hidden" value="<?php echo $user_info[ 'id' ]; ?>" id="add_user_id" />
			</div>
			<div id="category-edit">
				<?php 
					if ( $cat_id != 0 )
					{

						$s_date = explode( "-", $category[ 'start_date' ] );
						$e_date = explode( "-", $category[ 'end_date' ] );

						if ( sizeof( $s_date ) == 1 )	$s_date = array( '0000', $category[ 'start_date' ], '00' );
						if ( sizeof( $e_date ) == 1 )	$e_date = array( '0000', $category[ 'end_date' ], '00' );
				?>

				<h3>Edit Category:</h3>
				<table cellspacing="2" cellpadding="1">
					<tr>
						<td>Name:</td>
						<td><input type="text" id="edit_name" value="<?php echo $category[ 'name' ]; ?>" /></td>
					</tr>
					<tr>
						<td>Type:</td>
						<td>
							<?php 
								if ( $category[ 'type_id' ] == 2 ) 		echo "<b>Expense</b>";
								else if ( $category[ 'type_id' ] == 1 ) echo "<b>Income</b>";
							?>
						</td>
					</tr>
					<tr>
						<td>Budget:</td>
						<td><input type="text" id="edit_budget" size="10px" value="<?php echo number_format( $category[ 'budget' ], 2 ); ?>" /></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>
							<?php if ( $category[ 'cntrl_bdgt' ] ) { ?>
									<input type="checkbox" id="edit_cat_cntrl_bdgt" value="1" checked />Controlled Budget
								<?php } else { ?>
									<input type="checkbox" id="edit_cat_cntrl_bdgt" value="1" />Controlled Budget
								<?php } ?>
						</td>
					</tr>
					<tr>
						<td>Start Date:</td>
						<td>
							<select id="edit_start_month"><?php echo Functions::month_options( $s_date[ 1 ] ); ?></select>
							<select id="edit_start_year"><?php echo Functions::year_options( $s_date[ 0 ] ); ?></select>
						</td>
					</tr>
					<tr>
						<td>End Date:</td>
						<td>
							<select id="edit_end_month"><?php echo Functions::month_options( $e_date[ 1 ] ); ?></select>
							<select id="edit_end_year"><?php echo Functions::year_options( $e_date[ 0 ] ); ?></select>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><input id="edit_cat_save" type="button" value="Save" /></td>
						<td><input id="delete_cat" type="button" value="Delete" /></td>
					</tr>
				</table>
				<input type="hidden" id="edit_id" 	value="<?php echo $cat_id; ?>" />
				<input type="hidden" id="month" 	value="<?php echo $month; ?>" />
				<input type="hidden" id="year" 		value="<?php echo $year; ?>" />
				<?php } ?>
			</div>
			<div style="clear: both;"></div>
		</div>
	</div>
</div>

<?php $layout->footer(); ?>