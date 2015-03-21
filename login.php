<?php
require_once( "classes/layout.php" );
include_once( "classes/database.php" );
$layout 	= new Layout();
$database 	= new Database();

$layout->title( "Login" );
$layout->header();

$login 		= '';

if( isset( $_POST[ 'login' ] ) && isset( $_POST[ 'pass' ] ) )
{
	$_POST[ 'login' ]	= $database->mysqli->real_escape_string( strip_tags( trim( $_POST[ 'login' ] ) ) );
	$_POST[ 'pass' ]	= $database->mysqli->real_escape_string( strip_tags( trim( $_POST[ 'pass' ] ) ) );

	$q 					= $database->mysqli->query( "SELECT * FROM users WHERE username='{$_POST[ 'login' ]}' AND user_pw='{$_POST[ 'pass' ]}'", $db ) or die( $database->mysqli->error() );

	if( $q->num_rows > 0 )
	{
		$_SESSION[ 'login' ] 	= $_POST[ 'login' ];
		$_SESSION[ 'month' ] 	= date( "F" );
		$_SESSION[ 'year' ] 	= date( "Y" );
		header( "Location: index.php?Month=" . date( "F" ) . "&Year=" .date( "Y" ) );
	}
	else
	{
		$login = 'Invalid credentials, please try again';
	}
}
?>
<div class="login-form">
	<form method="post" action="login.php">
		<div id="login-div">
			<span>Login: </span><input type="text" name="login" autocomplete="off" />
		</div>
		<div id="pass-div">
			<span>Password: </span><input type="password" name="pass" autocomplete="off" />
		</div>
		<div id="submit-div"><input type="submit" value="Login" /></div>
	</form>
	<p><?php echo $login; ?></p>
</div>

<?php $layout->footer(); ?>