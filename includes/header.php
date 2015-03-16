<?php
/*Use of Sessions*/
if( !session_id() ) session_start();
?>
<!DOCTYPE html PUBLIC 
	"-//W3C//DTD XHTML 1.0 Strict//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?php echo $this->title; ?></title>
	<base href="<?php echo $this->base_url; ?>" />	
	<link rel="stylesheet" type="text/css" href="css/style.css" ></link>
	<link rel="stylesheet" type="text/css" href="css/calendar.full.css" ></link>
	<?php $this->css(); ?>
	<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
	<script type="text/javascript" src="js/jquery.tools.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.8.17.custom.min.js"></script>
	<script type="text/javascript" src="js/money_time.js"></script>
	<?php $this->js(); ?>
</head>
<body>
