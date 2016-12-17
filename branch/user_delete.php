<?php
	require_once("verify_access.php");
	require_once("../resource/database/hive.php");

	if(isset($_GET['action']) AND $_GET['action'] == "delete" AND  isset($_GET['employee']))
	{
		$id = $_GET['employee'];
		$query = mysqli_query($mysqli, "UPDATE `entity` SET `Mark` = -1 WHERE `ID` = '$id'");
		
		$_SESSION['success'] = "Successfully deleted user! If this is a mistake, please contact support.";
		header("location:users.php");
	}
	else if(isset($_GET['action']) AND $_GET['action'] == "reset" AND  isset($_GET['employee']))
	{
		$id = $_GET['employee'];
		$query = mysqli_query($mysqli, "UPDATE `entity` SET `Password` = 'whjewels' WHERE `ID` = '$id'");
		
		$_SESSION['success'] = "Successfully changed user password to whjewels! If this is a mistake, please contact support.";
		header("location:users.php");
	}
	else
	{
		header("location:index.php");
	}
?>