<?php
	session_start();
	
	if(isset($_SESSION['id']))
	{
		header("location:branch/index.php");
	}
	else
	{
		header("location:index.php");
	}
?>