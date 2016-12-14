<?php
	require_once("verify_access.php");
	require_once("../resource/database/hive.php");
	
	$reset = mysqli_query($mysqli, "DELETE FROM `inventory`"); echo "Inventory cleared.<br>";
	$reset = mysqli_query($mysqli, "DELETE FROM `particular`"); echo "Particulars cleared.<br>";
	$reset = mysqli_query($mysqli, "DELETE FROM `transaction`"); echo "Transactions cleared.<br>";
	$reset = mysqli_query($mysqli, "DELETE FROM `payment`"); echo "Payments cleared.<br>";
	
	$reset = mysqli_query($mysqli, "ALTER TABLE `inventory` AUTO_INCREMENT = 1");
	$reset = mysqli_query($mysqli, "ALTER TABLE `particular` AUTO_INCREMENT = 1");
	$reset = mysqli_query($mysqli, "ALTER TABLE `transaction` AUTO_INCREMENT = 1");
	$reset = mysqli_query($mysqli, "ALTER TABLE `payment` AUTO_INCREMENT = 1");
	
	echo "<a href='index.php'>Go back to homepage.</a>"
?>