<?php
header( 'Content-type: application/json' );

$document_root = $_SERVER[ 'DOCUMENT_ROOT' ];

require_once( 'classes/functions.php' );

$db = new Database2();

$file = Functions::Post( 'file' );

if ( !Functions::Filename( $file ) )
{
	return	JSON_Response_Error( '#Error#', 'Invalid filename parameter.');
}

if ( !file_exists( $document_root . "/budgetmm/ajax/{$file}.php" ) )
{
	return JSON_Response_Error( '#Error#', "File '{$file}' could not be found.");
}

require_once( $document_root . "/budgetmm/ajax/{$file}.php" );

if ( !function_exists( 'Module_JSON' ) )
{
	return JSON_Response_Error( '#Error#', "File '{$file}' does not implement Module_JSON" );
}

call_user_func( 'Module_JSON', $db );

function JSON_Response_Error( $code, $message )
{
	print json_encode( array( 'success' => 0, 'error_code' => $code, 'error_message' => $message ) );

	return false;
}

function JSON_Response_Global_Error()
{
	global $error_code;
	global $error_message;

	print json_encode( array( 'success' => 0, 'error_code' => $error_code, 'error_message' => $error_message ) );

	return false;
}

function JSON_Response_Success( $data = null )
{
	if ( is_null( $data ) )
	{
		print json_encode( array( 'success' => 1 ) );
	} else {
		print json_encode( array( 'success' => 1, 'data' => $data ) );
	}

	return true;
}
?>