<?php
function Module_JSON( $db )
{
	$item_id = Functions::Post_Int( 'item_id' );

	if ( !Functions::Item_Delete( $db, $item_id ) )
	{
		return JSON_Response_Global_Error();
	}

	return JSON_Response_Success();
}
?>