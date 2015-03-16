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

	/*
	*
	* Helper functions for table item
	*
	*/
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

	/*
	*
	* Helper functions for table category
	*
	*/
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

	public static function Category_MonthYear_Total_Load_ID( &$db, $cat_id, $user_id, $month, $year, &$total )
	{
		return $db->single( "SELECT
								SUM( amount ) as total
							 FROM
								item
							 WHERE
								cat_id 				= ?	AND
								user_id 			= ?	AND
								MONTHNAME( date ) 	= ?	AND
								YEAR( date )		= ?",
							$total, $cat_id, $user_id, $month, $year );
	}

	//Helper Functions
	public function get_months( $month )
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

	public function get_years($year) {
		$year_select = "<select id='year-select'>";
		for ($i = 2012; $i <= date("Y", strtotime("+1 years")); $i++) {
			$year_select .= "<option";
			if ($i == $year)
				$year_select .= " selected='selected'";

			$year_select .= ">".$i."</option>";
		}
		$year_select .= "</select>";
		return $year_select;
	}

	public function set_dateInput($month, $year) {
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

	public function month_string( $month ) {
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

	public function month_string_short( $month )
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

	public function month_options( $month )
	{
		$month_option = '<option value="00">*</option>';

		for ( $i = 1; $i < 13; $i++ )
		{
			$month_option .= '<option';

			if ( $i == $month )
			{
				$month_option .= ' selected="selected"';
			}

			$month_option .= ' value="$i">'.$this->month_string_short( $i ).'</option>';
		}

		return $month_option;
	}

	public function year_options( $year )
	{
		$year_options = '<option value="0000">*</option>';

		for ( $i = date("Y", strtotime("-2 years")); $i <= date("Y", strtotime("+2 years")); $i++ )
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

	public function year_review_options( $year )
	{
		$year_options = '<option value="all">All Years</option>';

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