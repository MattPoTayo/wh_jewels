<?php
	ob_start();
	require_once("verify_access.php");
	require_once("../resource/database/hive.php");
	$page_name = "New Client";
	$page_type = 3;
?>
<html>
	<?php require_once("../resource/sections/branch_header.php"); ?>
	<link href="../resource/graphics/css/sb-admin-2.css" rel="stylesheet">
	<body>
		<?php require_once("../resource/sections/branch_banner.php"); ?>
		<?php require_once("../resource/sections/branch_menu.php"); ?>
		<div class="form_class">
			<div class="form_title"><h3 class="page_title">New Client</h3></div>
				<div>
					<?php
						require_once("../resource/database/hive.php");
						
						if($_POST)
						{
							$name = $mysqli->real_escape_string($_POST['name']);
							$phone = $mysqli->real_escape_string($_POST['phone']);
							$address = $mysqli->real_escape_string($_POST['address']);
							$username = $mysqli->real_escape_string($_POST['username']);
							
							date_default_timezone_set ('Asia/Taipei');
							$date_today = date("Y-m-d H:i:s");
							
							if(strlen($name) > 120)
							{
								echo "<p class='ffail'>Name can only be 45 characters long!</p>";	
							}	
							else if(strlen($address) > 225)
							{
								echo "<p class='ffail'>Address can only be 225 characters long!</p>";	
							}
							else if($name == "" or $phone == "")
							{
								echo "<p class='ffail'>Missing required field!</p>";	
							}
							else
							{
								$new = mysqli_query($mysqli, "INSERT INTO `entity`(`ID`, `Name`, `Address`, `Phone`, `Birthdate`, `Username`, `Password`, `Level`, `Type`, `Mark`) 
											      VALUES ('', '$name', '$address', '$phone', '', '$username', '', '1', '4', '1')");
											      
								if($new) $_SESSION['success'] = "Successfully added new client. Click <a href='clients_new.php'>here</a> to add another one.";
								else $_SESSION['fail'] = "Oops, something went wrong. If error persists, please contact support.";
								
								ob_end_clean();
								header("location:clients.php");
							}
						}
						else
						{
							$name = "";
							$phone = "";
							$address = "";
							$username = "";
						}
					
					?>
				</div>
				<div class="form_content">
					<form class="form-horizontal" method=post action="<?php echo $_SERVER['PHP_SELF'];?>">
						<div class="form-group">
							<div class="col-sm-12">
								<label for="name">Name*</label>
								<input name="name" type="text" class="form-control" placeholder="Name" value=<?php echo "'".$name."'"; ?> required>
							</div>
							
							<div class="col-sm-12">
								<label for="phone">Phone*</label>
								<input name="phone" type="text" class="form-control" placeholder="Phone" value=<?php echo "'".$phone."'"; ?> required>
							</div>
							
							<div class="col-sm-12">
								<label for="address">Address</label>
								<input name="address" type="text" class="form-control" placeholder="Address" value=<?php echo "'".$address."'"; ?>>
							</div>
							
							<div class="col-sm-12">
								<label for="username">E-mail</label>
								<input name="username" type="text" class="form-control" placeholder="E-mail" value=<?php echo "'".$username."'"; ?>>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-0 col-sm-6">
								<button type="submit" class="form_button">Save</button>
							</div>
							
							<div class="col-sm-offset-0 col-sm-6">
								<a class="btn form_button" href="index.php" role="button">Back</a>
							</div>
						</div>				
					</form>
				</div>
			</div>
		</div>
	</div>
	</body>
</html>