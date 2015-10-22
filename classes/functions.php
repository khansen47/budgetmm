<?php
include_once( "database.php" );

class Functions
{
	private $database;
	private $db2;

	public function __construct()
	{
		$this->database = new Database(); // create a new database instance
		$this->db2		= new Database2();
	}

	public static function Get( $value )
	{
		return isset( $_GET[ $value ] ) ? trim( $_GET[ $value ] ) : '';
	}

	public static function Get_Int( $value )
	{
		return isset( $_GET[ $value ] ) ? (int)$_GET[ $value ] : 0;
	}

	public static function Post( $value )
	{
		return isset( $_POST[ $value ] ) ? trim( $_POST[ $value ] ) : '';
	}

	public static function Post_Int( $value )
	{
		return isset( $_POST[ $value ] ) ? (int)$_POST[ $value ] : 0;
	}

	public static function Post_Float( $value )
	{
		return isset( $_POST[ $value ] ) ? (float)$_POST[ $value ] : 0.00;
	}

	public static function Post_Active( $value )
	{
		if ( isset( $_POST[ $value ] ) && ( $_POST[ $value ] == 'true' || $_POST[ $value ] == 1 ) )
		{
			return 1;
		}

		return 0;
	}

	public static function Filename( $filename )
	{
		if ( !preg_match( "/^[a-zA-Z_]+$/", $filename ) )
		{
			return false;
		}

		return true;
	}

	public static function Error( $code, $message )
	{
		global $error_code;
		global $error_message;

		$error_code 	= $code;
		$error_message 	= $message;

		return false;
	}

	public static function clean_input( $v )
	{
		return trim( $v );
	}

	public static function Validate_User( &$db, &$user )
	{
		if ( !Functions::User_Load( $db, $_SESSION[ 'login' ], $user ) )
		{
			header( 'Location: login.php' );
			die();
		}
	}

	public static function Validate_MonthYear( &$month, &$year )
	{
		$month 	= $month ? $month : date( "F" );
		$year	= $year ? $year : date( "Y" );

		if ( Functions::month_int( $month ) == '00' )	$month = date( "F" );

		for ( $i = 2012; $i <= date( "Y" ) + 1; $i++ )
		{
			if ( $year == $i )
				return;
		}

		$year = date( "Y" );
	}

	public static function Validate_Category( &$db, &$cat_id )
	{
		if ( !Functions::Category_Load_ID( $db, $cat_id, $category ) )
		{
			if ( !Functions::Category_Load_Default( $db, $category ) )
			{
				die( 'fail' );
			}

			$cat_id = $category[ 'id' ];
		}
	}

	//New Functions

	/*
	*
	* Helper functions for table users
	*
	*/
	public static function User_Load( &$db, $username, &$user )
	{
		return $db->single( 'SELECT * FROM users WHERE username = ?', $user, $username );
	}

	public static function User_Load_ID( &$db, $id, &$user )
	{
		return $db->single( 'SELECT * FROM users WHERE id = ?', $user, $id );
	}

	/*
	*
	* Helper functions for table item
	*
	*/
	public static function Item_Insert( &$db, $item )
	{
		return $db->query( 'INSERT INTO item
							( cat_id, user_id, amount, date, comment )
							VALUES
							( ?, ?, ?, ?, ? )',
							$item[ 'cat_id' ], $item[ 'user_id' ], $item[ 'amount' ], $item[ 'date' ], $item[ 'comment' ] );
	}

	public static function Item_Update( &$db, $item )
	{
		return $db->query( 'UPDATE item
							SET
								cat_id 	= ?,
								amount 	= ?,
								date 	= ?,
								comment = ?
							WHERE
								id 		= ?',
							$item[ 'cat_id' ], $item[ 'amount' ], $item[ 'date' ], $item[ 'comment' ],
							$item[ 'id' ] );
	}

	public static function Item_Delete( &$db, $id )
	{
		return $db->query( 'DELETE FROM item WHERE id = ?', $id );
	}

	public static function Item_Load_ID( &$db, $id, &$item )
	{
		return $db->single( 'SELECT * FROM item WHERE id = ?', $item, $id );
	}

	public static function ItemList_Load_Date( &$db, $month, $year, $user_id, &$items )
	{
		return $db->select( "SELECT
								*
							 FROM
								item
							 WHERE
								MONTHNAME( date )	= ? AND
								YEAR( date )	 	= ? AND
								user_id 			= ?
							 ORDER BY
								date",
							 $items, $month, $year, $user_id );
	}

	public static function ItemList_Load_CategoryAll( &$db, $cat_id, $month, $year, $user_id, &$items )
	{
		return $db->select( "SELECT
								*
							 FROM
								item
							 WHERE
								cat_id 				= ? AND
								MONTHNAME( date )	= ? AND
								YEAR( date )	 	= ? AND
								user_id 			= ?
							 ORDER BY
								date",
							 $items, $cat_id, $month, $year, $user_id );
	}

	public static function ItemList_Load_CatIDAndMonth( &$db, $cat_id, $month, &$items )
	{
		return $db->select( "SELECT
								SUM( amount ) as day_amount,
								DAY( date ) as day
							 FROM
								item
							 WHERE
								cat_id 				= ? AND
								MONTHNAME( date )	= ? AND
								YEAR( date )		= YEAR( NOW() )
							 GROUP BY
								DAY( date )",
		  					$items, $cat_id, $month );
	}

	public static function Item_Total_TypeID_MonthYear( &$db, $type_id, $user_id, $month, $year, &$total )
	{
		return $db->single( "SELECT
								SUM( i.amount ) as total
							FROM
								item i
								LEFT OUTER JOIN category c ON i.cat_id = c.id
							WHERE
								i.user_id 			= ? AND
								MONTHNAME( i.date ) = ? AND
								YEAR( i.date )		= ?	AND
								c.type_id 			= ? AND
								c.deprecated 		= 0",
							$total, $user_id, $month, $year, $type_id );
	}

	public static function Item_CategoryDate_Total( &$db, $cat_id, $month, $year, $user_id, &$total )
	{
		return $db->single( "SELECT
								SUM( amount ) as total
							 FROM
								item
							 WHERE
								cat_id 				= ? AND
								MONTHNAME( date )	= ? AND
								YEAR( date )	 	= ? AND
								user_id 			= ?",
							 $total, $cat_id, $month, $year, $user_id );
	}

	public static function Item_CategoryYear_Total( &$db, $cat_id, $year, $user_id, &$total )
	{
		return $db->single( "SELECT
								SUM( amount ) as total
							 FROM
								item
							 WHERE
								cat_id 			= ? AND
								YEAR( date )	= ? AND
								user_id 		= ?",
							 $total, $cat_id, $year, $user_id );
	}

	public static function Item_CategoryYearLessMonth_Total( &$db, $cat_id, $month, $year, $user_id, &$total )
	{
		return $db->single( "SELECT
								SUM( amount ) as total
							 FROM
								item
							 WHERE
								cat_id 			= ? 	AND
								MONTH( date )	<= ? 	AND
								YEAR( date )	= ? 	AND
								user_id 		= ?",
							 $total, $cat_id, $month, $year, $user_id );
	}

	public static function Item_Date_Total( &$db, $month, $year, $user_id, &$total )
	{
		return $db->single( "SELECT
								SUM( amount ) as total
							 FROM
								item
							 WHERE
								MONTHNAME( date )	= ? AND
								YEAR( date )	 	= ? AND
								user_id 			= ? AND
								EXISTS ( SELECT id FROM category WHERE type_id = 1 AND id = cat_id )",
							 $total, $month, $year, $user_id );
	}

	public static function ItemCategoryMonth_Average( &$db, $years, $cat_id, $month, $year, &$average )
	{
		return $db->single( "SELECT
								( SUM( i.amount ) / ? ) as total
							 FROM
								category c,
								item i
							 WHERE
							 	c.id 			= ?		AND
								i.cat_id 		= c.id 	AND 
								c.cntrl_bdgt	= 0		AND
								MONTH( i.date ) = ?		AND
								YEAR( i.date ) != ?",
							$average, $years, $cat_id, $month, $year );
	}

	/*
	*
	* Helper functions for table category
	*
	*/
	public static function Category_Insert( &$db, $category )
	{
		return $db->query( "INSERT INTO category
							( name, user_id, type_id, budget, start_date, end_date, deprecated, last_updated )
							VALUES
							( ?, ?, ?, ?, ?, ?, ?, ? )",
							$category[ 'name' ], $category[ 'user_id' ], $category[ 'type_id' ], $category[ 'budget' ], $category[ 'start_date' ],
							$category[ 'end_date' ], $category[ 'deprecated' ], 0 );
	}

	public static function Category_Update( &$db, $category )
	{
		return $db->query( "UPDATE
								category
							SET
								name = ?, budget = ?, cntrl_bdgt = ?, start_date = ?, end_date = ?, last_updated = NOW()
							WHERE
								id = ?",
							$category[ 'name' ], $category[ 'budget' ], $category[ 'cntrl_bdgt' ], $category[ 'start_date' ], $category[ 'end_date' ],
							$category[ 'id' ] );
	}

	public static function Category_Delete( &$db, $id )
	{
		return $db->query( 'DELETE FROM category WHERE id = ?', $id );
	}

	public static function Category_Deprecate( &$db, $id )
	{
		return $db->query( 'UPDATE category SET deprecated = 1, last_updated = NOW() WHERE id = ?', $id );
	}

	public static function Category_Load_ID( &$db, $cat_id, &$category )
	{
		return $db->single( "SELECT * FROM category WHERE id = ?", $category, $cat_id );
	}

	public static function Category_Load_Name( &$db, $name, &$category )
	{
		return $db->single( "SELECT * FROM category WHERE name = ?", $category, $name );
	}

	public static function Category_Load_Default( &$db, &$cat_id )
	{
		return $db->single( "SELECT id FROM category WHERE type_id = 1 ORDER BY name", $cat_id );
	}

	public static function CategoryList_Load_All( &$db, $user_id, &$categories )
	{
		return $db->select( "SELECT
								*
							 FROM
								category
							 WHERE
								user_id		= ?	AND
								deprecated	= 0
							 ORDER BY
								type_id, name", $categories, $user_id );
	}

	public static function CategoryList_Load_Month( &$db, $user_id, $month, $year, &$categories )
	{
		$month_int 		= Functions::month_int( $month );
		$format_date 	= $year."-".$month_int."-00";

		return $db->select( "SELECT
							 	*
							 FROM
							 	category
							 WHERE
							 	user_id 	= ?	AND
							 	deprecated 	= 0 AND
							 	(
							 		(
							 			( start_date 	<= ? OR start_date 	= '0000-00-00' ) AND
							 			( end_date 		>= ? OR end_date 	= '0000-00-00' )
							 		)
							 		OR
							 		(
							 			( start_date 	<= ? AND start_date <> '0000-00-00' ) AND
							 			( end_date 		>= ? AND end_date 	<> '0000-00-00' )
							 		)
							 	)
							 ORDER BY
							 	type_id, name",
							$categories, $user_id, $format_date, $format_date, $month_int, $month_int );
	}

	//Helper Functions
	public static function isCurrency( $number )
	{
	  return preg_match( "/^-?[0-9]+(?:\.[0-9]{1,2})?$/", $number );
	}

	public static function Output_TopHeader( $username )
	{
		echo '<h1>Budget My Money <span style="float:right;"><a href="logout.php">logout</a>Welcome, ' . $username . ' -</span></h1>';
	}

	public static function Output_LeftNavigation( $month, $year )
	{
		$month_year				= '?Month=' . $month . '&Year=' . $year;

		$home_link		  		= 'index.php' 				. $month_year;
		$year_review_link 		= 'year_review.php' 		. $month_year;
		$month_items_link 		= 'all_month_items.php' 	. $month_year;
		$month_report_link		= 'month_report.php' 		. $month_year;
		$edit_categories_link	= 'manage_cats.php' 		. $month_year;
		$month_projection_link	= 'month_projection.php'	. $month_year;
		$edit_bills_link		= '';
		$overal_account_link	= '';

		//<li><a id="manage_auto">Edit Reaccuring Bills</a></li>

		echo '<div id="left">
				<ul class="top_nav">
					<li><a href="' . $home_link . '">Home</a></li>
					<li><a href="' . $year_review_link 		. '">Year Review</a></li>
					<li><a href="' . $edit_categories_link	. '">Edit Categories</a></li>
					<li><a href="' . $month_items_link 		. '">Month Items</a></li>
					<li><a href="' . $month_report_link 	. '">Month Report</a></li>
					<li><a href="' . $month_projection_link . '">Month Projection</a></li>
					<li><a id="all-years">Overall Account</a></li>
				</ul>
			  </div>';
	}

	public static function Output_ChangeDate_Form( $page, $month, $year, $parameters = [] )
	{
		echo '<form action="' . $page . '" method="get" id="change_date_form">
				Date: ' . Functions::Month_Select( $month ) . Functions::Year_Select( $year );

				foreach ( $parameters as $key => $parameter )
				{
					echo '<input type="hidden" name="' . $key .'" value="' . $parameter .'" />';
				}

		echo '</form>';
	}

	public static function Category_Draw_FormSelect( &$db, $user_id, $cat_id, $month, $year )
	{
		Functions::CategoryList_Load_All( $db, $user_id, $categories );

		$first 		= 1;
		$options 	= "<form action='index.php' method='get' id='change_category_form'>
							<b>Quick Add:</b>
							<select name='Category_ID' id='quick-type'>
								<option disabled='disabled'>Income</option>";

		foreach ( $categories as $key => $category )
		{
			if ( $category[ 'type_id' ] == 2 )
			{
				if ( $first == 1 )
				{
					$options 	.= "<option disabled='disabled'>&nbsp;</option>";
					$options 	.= "<option disabled='disabled'>Expenses</option>";
				}

				$first 			= 0;
			}

			$options 			.= "<option id='".$category[ 'id' ]. "' value='".$category[ 'id' ]."'";

			if ( $category[ 'id' ] == $cat_id )
			{
				$options		.= ' selected';
			}

			$options			.= ">&nbsp;".$category[ 'name' ]."</option>";
		}

		$options .= '</select>
					<input type="hidden" name="Month" value="' . $month . '" />
					<input type="hidden" name="Year" value="' . $year . '" />
				</form>';

		return $options;
	}

	public static function get_months( $month )
	{
		$month_names 	= array( "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" );
		$months 		= "<select id='month-select'>";

		foreach ( $month_names as $month_in )
		{
			$months 	.= "<option";

			if ($month_in == $month)
				$months .= " selected='selected'";

			$months 	.= ">".$month_in."</option>";
		}

		$months 		.= "</select>";

		return $months;
	}

	public static function get_years( $year )
	{
		echo "<select id='year-select'>";

		for ( $i = 2012; $i <= date( "Y", strtotime( "+1 years" ) ); $i++ )
		{
			echo "<option";

			if ( $i == $year )
				echo " selected='selected'";

			echo ">" . $i . "</option>";
		}

		echo "</select>";
	}

	public static function Year_Select( $year )
	{
		$years = "<select id='year_select' name='Year'>";

		for ( $i = 2012; $i <= date( "Y", strtotime( "+1 years" ) ); $i++ )
		{
			$years 		.= "<option";

			if ( $i == $year )
				$years 	.= " selected='selected'";

			$years 		.= ">" . $i . "</option>";
		}

		$years 			.= "</select>";

		return $years;
	}

	public static function Month_Select( $month )
	{
		$month_names 	= array( "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" );
		$months 		= "<select id='month_select' name='Month'>";

		foreach ( $month_names as $month_in )
		{
			$months 	.= "<option";

			if ($month_in == $month)
				$months .= " selected='selected'";

			$months 	.= ">".$month_in."</option>";
		}

		$months 		.= "</select>";

		return $months;
	}

	public static function set_dateInput($month, $year) {
		if ($month == date("F") && $year == date("Y"))
			return $month." ".date("d").", ".$year;
		else
			return $month." 1, ".$year;
	}

	public static function month_int( $month ) {
		if ($month == "January") 		return "01";
		elseif ($month == "February") 	return "02";
		elseif ($month == "March") 		return "03";
		elseif ($month == "April") 		return "04";
		elseif ($month == "May") 		return "05";
		elseif ($month == "June") 		return "06";
		elseif ($month == "July") 		return "07";
		elseif ($month == "August") 	return "08";
		elseif ($month == "September") 	return "09";
		elseif ($month == "October") 	return "10";
		elseif ($month == "November") 	return "11";
		elseif ($month == "December") 	return "12";
		else							return "00";
	}

	public static function month_string( $month ) {
		if ($month == 1) 		return "January";
		elseif ($month == 2) 	return "February";
		elseif ($month == 3) 	return "March";
		elseif ($month == 4) 	return "April";
		elseif ($month == 5) 	return "May";
		elseif ($month == 6) 	return "June";
		elseif ($month == 7) 	return "July";
		elseif ($month == 8) 	return "August";
		elseif ($month == 9) 	return "September";
		elseif ($month == 10) 	return "October";
		elseif ($month == 11) 	return "November";
		elseif ($month == 12) 	return "December";
		else					return "";
	}

	public static function month_string_short( $month )
	{
		if ($month == 1) 		return "Jan";
		elseif ($month == 2) 	return "Feb";
		elseif ($month == 3) 	return "Mar";
		elseif ($month == 4) 	return "Apr";
		elseif ($month == 5) 	return "May";
		elseif ($month == 6) 	return "Jun";
		elseif ($month == 7) 	return "Jul";
		elseif ($month == 8) 	return "Aug";
		elseif ($month == 9) 	return "Sep";
		elseif ($month == 10) 	return "Oct";
		elseif ($month == 11) 	return "Nov";
		elseif ($month == 12) 	return "Dec";
		else					return "";
	}

	public static function month_options( $month )
	{
		$month_option = '<option value="00">*</option>';

		for ( $i = 1; $i < 13; $i++ )
		{
			$month_option .= '<option';

			if ( $i == $month )
			{
				$month_option .= ' selected="selected"';
			}

			$month_option .= ' value="' . str_pad( $i, 2, '0', STR_PAD_LEFT ) . '">'.Functions::month_string_short( $i ).'</option>';
		}

		return $month_option;
	}

	public static function year_options( $year )
	{
		$year_options = '<option value="0000">*</option>';

		for ( $i = 2012; $i <= date("Y", strtotime("+2 years")); $i++ )
		{
			$year_options .= '<option';

			if ( $i == $year )
			{
				$year_options .= ' selected="selected"';
			}

			$year_options .= ' value="'.$i.'">'.$i.'</option>';
		}

		return $year_options;
	}

	public static function year_review_options( $year )
	{
		for ($i = 2012; $i <= date("Y", strtotime("+2 years")); $i++)
		{
			$year_options .= '<option';

			if ( $i == $year )
			{
				$year_options .= ' selected="selected"';
			}

			$year_options .= ' value="'.$i.'">'.$i.'</option>';
		}

		return $year_options;
	}

	//Old functions
	public function income_cat_list( $user_id )
	{
		$query 			= "SELECT * FROM category WHERE type_id = '1' AND user_id = ? AND deprecated = 0 ORDER BY name";
		$income_count	= $this->db2->select( $query, $income_list, $user_id );

		return $income_list;
	}

	public function cat_select_list( $user_id )
	{
		/*
		$query			= "SELECT * FROM category c WHERE user_id = '$user_id' ORDER BY type_id, name";
		$category_count = $this->db2->select( $query, $categories_select, $user_id );

		$first 			= 0;
		var_dump( $categories_select );
		foreach ( $categories_select as $cat )
		{
			if ($cat['type_id'] == 1) {
				$options 	.= "<option id=".$cat['id'].">&nbsp;".$cat['name']."</option>";
				$first		 = 1;
			} else {
				if ($first == 1) {
					$options .= "<option disabled='disabled'>&nbsp;</option>";
					$options .= "<option disabled='disabled'>Expenses</option>";
				}

				$first = 0;

				$options .= "<option id=".$cat['id'].">&nbsp;".$cat['name']."</option>";
			}
		}

		return $options;
		*/
		$categories_select = $this->database->mysqli->query("SELECT * FROM category c WHERE user_id = '$user_id' ORDER BY type_id, name");
		$options = "<option disabled='disabled'>Income</option>";
		$first = 0;
		while ($cat = $categories_select->fetch_assoc()) {
			if ($cat['type_id'] == 1) {
				$options .= "<option id=".$cat['id'].">&nbsp;".$cat['name']."</option>";
				$first = 1;
			} else {
				if ($first == 1) {
					$options .= "<option disabled='disabled'>&nbsp;</option>";
					$options .= "<option disabled='disabled'>Expenses</option>";
				}
				$first = 0;

				$options .= "<option id=".$cat['id'].">&nbsp;".$cat['name']."</option>";
			}
		}
		return $options;
	}

	public function get_userInfo( $user_login )
	{
		$uid_q = $this->database->mysqli->query("SELECT * FROM users WHERE username = '".$user_login."'");

		return $uid_q->fetch_assoc();
	}


}
?>