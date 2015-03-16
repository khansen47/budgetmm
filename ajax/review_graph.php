<?php
include_once("../classes/database.php");
include_once("../classes/functions.php");
include_once("../classes/layout.php");
$database = new Database();
$function = new Functions();
$layout = new Layout();

$layout->session_inc();
$user_info = $function->get_userInfo($_SESSION['login']);
	
?>
<html>
<head>
	<script type="text/javascript" src="../js/jquery-1.7.1.min.js"></script>
	<script type="text/javascript" src="../js/jquery.jqplot.min.js"></script>
	<script type="text/javascript" src="../js/plugins/jqplot.barRenderer.min.js"></script>	
	<script type="text/javascript" src="../js/plugins/jqplot.canvasTextRenderer.min.js"></script>
	<script type="text/javascript" src="../js/plugins/jqplot.categoryAxisRenderer.min.js"></script>
	<script type="text/javascript" src="../js/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
	<link rel="stylesheet" type="text/css" href="../css/jquery.jqplot.css" />
	
	<script type="text/javascript">
	
	</script>
</head>
<body>
	<h1></h1>
	<div id="chart1b" style="width:1650px;height:600px;"></div>
</body>
</html>