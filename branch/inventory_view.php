<?php
	$id = $_GET['id'];
	
	require_once("../resource/database/hive.php");
	$result = mysqli_query($mysqli, "SELECT `Picture` FROM `inventory` WHERE `ID` = $id");
	$picture = mysqli_fetch_row($result);	
				
	echo '<img src="data:image/jpeg;base64,'.base64_encode( $picture[0] ).'"/>';
?>