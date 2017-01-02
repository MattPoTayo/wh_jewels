<?php 
	ob_start(); 
	$page_name = "Login";
	$page_section = 0; //Login Page
	require_once("resource/sections/head_login.php");
	session_start();
?>
<html>
	<body>
		<div>
			<div>
				<div class="form-centered-login">
					<div class="form_title"><h1 class="logo_font"><img src="/resource/images/wh_jewels_cover.png" height="70" alt="Banner Image"/>WH Jewels</h1></div>
					<div>
						<?php
							require_once("resource/database/hive.php");
							
							if(isset($_SESSION['success'])) { echo "<p class='fsuccess'>".$_SESSION['success']."</p>"; unset($_SESSION['success']); }
							else if(isset($_SESSION['fail'])) { echo "<p class='ffail'>".$_SESSION['fail']."</p>"; unset($_SESSION['fail']); }
							
							$username = "";
							$password = "";
							
							if($_POST)
							{
								$username = $mysqli->real_escape_string($_POST['username']);
								$password = $mysqli->real_escape_string(md5 ($_POST['password']));
								#echo(md5($_POST['password']));
								$user = mysqli_query($mysqli, "SELECT * FROM entity WHERE `Username` = '$username' AND `Password` = '$password' AND `Mark` = 1");
								
								if(!$user or mysqli_num_rows($user) != 1) 
								{
									echo "<p class='ffail'>Invalid user details!</p>";	
								}				
								else
								{
									$session_data = $user->fetch_assoc();
									$_SESSION['id'] =  $session_data['ID'];
									$_SESSION['name'] = $session_data['Name'];
									
									ob_end_clean();
									header("location:homepage.php");
								}
							}
						?>
					</div>
					<div class="form_content">
						<form class="form-horizontal" method=post action="<?php echo $_SERVER['PHP_SELF'];?>">
							<div class="form-group">
								<div class="col-sm-12">
									<input name="username" type="text" class="form-control" placeholder="Username" value=<?php echo "'".$username."'"; ?>>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<input name="password" type="password" class="form-control" placeholder="Password" value=<?php echo "'".$password."'"; ?>>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-offset-0 col-sm-12">
									<button type="submit" class="form_button">Log-In</button>
								</div>
							</div>				
						</form>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>