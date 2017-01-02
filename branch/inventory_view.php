<?php
	$id = $_GET['id'];
	$path = "../resource/images/inv_image/".sprintf('%d', $id).".png";
	/*
	require_once("../resource/database/hive.php");
	$result = mysqli_query($mysqli, "SELECT `ID` FROM `inventory` WHERE `ID` = $id");
	$picture = mysqli_fetch_row($result);	
	*/
				
	echo '<img src="'.$path.'"/>';
?>