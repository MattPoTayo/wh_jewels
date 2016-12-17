<?php
	ob_start();
	require_once("verify_access.php");
	require_once("../resource/database/hive.php");
	$page_name = "myProfile";
	$page_type = 8;
?>
<html>
	<?php require_once("../resource/sections/branch_header.php"); ?>
	<link href="../resource/graphics/css/sb-admin-2.css" rel="stylesheet">
	<body>
		<?php require_once("../resource/sections/branch_banner.php"); ?>
		<?php require_once("../resource/sections/branch_menu.php"); ?>
		<div class="form_class_full">
			<div class="form_title"><h3 class="page_title">WH Jewels Profile</h3></div>
				<div>
					<?php
						require_once("../resource/database/hive.php");
						
						$profile = mysqli_query($mysqli, "SELECT * FROM `entity` WHERE `ID` = '$userID'");
						$myprofile = $profile->fetch_assoc();
							
						$name = $myprofile['Name'];
						$birthdate = date("Y-m-d", strtotime($myprofile['Birthdate']));
						$phone = $myprofile['Phone'];
						$address = $myprofile['Address'];
						$username = $myprofile['Username'];
						$password1 = $myprofile['Password'];
						$password2 = $myprofile['Password'];
						
						if($_POST)
						{
							$name = $mysqli->real_escape_string($_POST['name']);
							$birthdate = $mysqli->real_escape_string($_POST['birthdate']);
							$phone = $mysqli->real_escape_string($_POST['phone']);
							$address = $mysqli->real_escape_string($_POST['address']);
							$username = $mysqli->real_escape_string($_POST['username']);
							$password1 = $mysqli->real_escape_string($_POST['password1']);
							$password2 = $mysqli->real_escape_string($_POST['password2']);
							
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
							else if($name == "" or $birthdate == "" or $phone == "" or $address == "" or $username == "" or $password1 == "" or $password2 == "")
							{
								echo "<p class='ffail'>Missing required field!</p>";	
							}
							else if(strlen($password1) < 8)
							{
								echo "<p class='ffail'>Password must be at least 8 characters long.</p>";	
							}			
							else if($password1 != $password2)
							{
								echo "<p class='ffail'>Passwords do not match!</p>";	
							}
							else
							{
								$new = mysqli_query($mysqli, "UPDATE `entity` SET `Name` = '$name', `Birthdate` = '$birthdate', `Phone` = '$phone', `Address` = '$address', `Username` = '$username', `Password` = '$password1' WHERE `ID` = '$userID'");
								if($new) echo "<p class='fsuccess'> Successfully updated profile.</p>";
								else echo "<p class='ffail'> Oops, something went wrong. If error persists, please contact support.</p>";
							}
						}
					?>
				</div>
				<div class="form_content">
					<form class="form-horizontal" method=post action="<?php echo $_SERVER['PHP_SELF'];?>">
						<div class="form-group">
							<div class="col-sm-8">
								<label for="name">Name*</label>
								<input name="name" type="text" class="form-control" placeholder="Name" value=<?php echo "'".$name."'"; ?> required>
							</div>
							<div class="col-sm-4">
								<label for="birthdate">Birth Date*</label>
								<input name="birthdate" type="date" class="form-control" value=<?php echo "'".$birthdate."'"; ?> required>
							</div>
						</div>
						
						<div class="form-group">
							<div class="col-sm-4">
								<label for="phone">Phone*</label>
								<input name="phone" type="text" class="form-control" placeholder="Phone" value=<?php echo "'".$phone."'"; ?> required>
							</div>
							<div class="col-sm-8">
								<label for="address">Address*</label>
								<input name="address" type="text" class="form-control" placeholder="Address" value=<?php echo "'".$address."'"; ?> required>
							</div>
						</div>
						
						<div class="form-group">
							<div class="col-sm-4">
								<label for="username">E-mail*</label>
								<input name="username" type="text" class="form-control" placeholder="E-mail" value=<?php echo "'".$username."'"; ?> required>
							</div>
							<div class="col-sm-4">
								<label for="password1">Password*</label>
								<input name="password1" type="password" class="form-control" placeholder="Password" value=<?php echo "'".$password1."'";?> required>
							</div>
							<div class="col-sm-4">
								<label for="mn">Retype Password*</label>
								<input name="password2" type="password" class="form-control" placeholder="Retype Password" value=<?php echo "'".$password2."'";?> required>
							</div>
						</div>
						
						
						<div class="form-group">
							<div class="col-sm-offset-0 col-sm-6">
								<button type="submit" class="form_button">Update</button>
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