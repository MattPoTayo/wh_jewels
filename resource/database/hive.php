<?php
	$enable = true;
	if($enable)
	{
		$DBHost = "localhost";
		$SQLUsername = "root";
		$SQLPassword = "121586";
		$DBName = "wh_jewels";
		
		$mysqli = new mysqli("$DBHost", "$SQLUsername", "$SQLPassword", "$DBName");
	}
?>