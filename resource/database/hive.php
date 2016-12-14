<?php
	$enable = true;
	if($enable)
	{
		$DBHost = "localhost";
		$SQLUsername = "root";
		$SQLPassword = "121586";
		$DBName = "whjewels";
		
		$mysqli = new mysqli("$DBHost", "$SQLUsername", "$SQLPassword", "$DBName");
	}
?>