<?php
	ob_start();
	require_once("verify_access.php");
	require_once("../resource/database/hive.php");
	$page_name = "New Payment";
	$page_type = 7;
?>
<html>
	<?php require_once("../resource/sections/branch_header.php"); ?>
	<link href="../resource/graphics/css/sb-admin-2.css" rel="stylesheet">
	<body>
		<?php require_once("../resource/sections/branch_banner.php"); ?>
		<?php require_once("../resource/sections/branch_menu.php"); ?>
		<div class="form_class">
			<div class="form_title"><h3 class="page_title">New Payment</h3></div>
				<div>
					<?php
						require_once("../resource/database/hive.php");
						
						if($_POST)
						{
							$type = $mysqli->real_escape_string($_POST['type']);
							$amount = $mysqli->real_escape_string($_POST['amount']);
							$date = $time_now;
							$cnum = $mysqli->real_escape_string($_POST['cnum']);
							$cbank = $mysqli->real_escape_string($_POST['cbank']);
							$cdate = $mysqli->real_escape_string($_POST['cdate']);
							$client = $mysqli->real_escape_string($_POST['client']);
							$sid = $mysqli->real_escape_string($_POST['sid']);
							
							date_default_timezone_set ('Asia/Taipei');
							$date_today = date("Y-m-d H:i:s");
							
							if(!is_numeric($amount))
							{
								echo "<p class='ffail'>Amount should be a number!</p>";	
							}	
							else if($amount == "" or $client == 0)
							{
								echo "<p class='ffail'>Missing required field!</p>";	
							}
							else
							{
								$new = mysqli_query($mysqli, "INSERT INTO `payment`(`ID`, `Type`, `Date`, `Amount`, `CBank`, `CDate`, `Client`, `SID`, `Mark`, `CNum`) 
											     VALUES ('', '$type', '$date', '$amount', '$cbank', '$cdate', '$client', '$sid', 1, '$cnum')");
											      
								if($new) $_SESSION['success'] = "Successfully added new payment. Click <a href='payment_new.php'>here</a> to add another one.";
								else $_SESSION['fail'] = "Oops, something went wrong. If error persists, please contact Tangent.";
								
								ob_end_clean();
								header("location:payments.php");
							}
						}
					?>
				</div>
				<div class="form_content">
					<form class="form-horizontal" method=post action="<?php echo $_SERVER['PHP_SELF'];?>">
						<div class="form-group">
							<div class="col-sm-12">
								<label for="type">Type*</label>
								<select name="type" class="form-control">
									<option value=1>Cash</option> 
									<option value=2>Check</option>
								</select> 
							</div>
						</div>	
						
						<div class="form-group">
							<div class="col-sm-12">
								<label for="amount">Amount*</label>
								<input name="amount" type="text" class="form-control" placeholder="Amount" value=<?php echo "'".$amount."'"; ?>>
							</div>
						</div>
						
						<div class="form-group">
							<div class="col-sm-12">
								<label for="cnum">Check Number*</label>
								<input name="amount" type="text" class="form-control" placeholder="Check Number" value=<?php echo "'".$cnum."'"; ?>>
							</div>
						</div>
						
						<div class="form-group">
							<div class="col-sm-6">
								<label for="cbank">Check Bank</label>
								<input name="cbank" type="text" class="form-control" placeholder="Bank" value=<?php echo "'".$cbank."'"; ?>>
							</div>
							<div class="col-sm-6">
								<label for="cdate">Check Date</label>
								<input name="cdate" type="date" class="form-control" value=<?php echo "'".$cdate."'"; ?>>
							</div>
						</div>
							
						<div class="form-group">
							<div class="col-sm-12">
								<label for="cdate">Client*</label>
								<select name="client" class="form-control" >
								<?php
									$clients = mysqli_query($mysqli, "SELECT `ID`, `Name` FROM entity WHERE `Mark` = 1 AND `Type` = 4");
									for($i=0; $i<mysqli_num_rows($clients) and mysqli_num_rows($clients)>0; $i++)
									{
										$clients->data_seek($i);
										$row = $clients->fetch_row();
									
										echo "<option value='".$row[0]."'>".sprintf('%05d', $row[0])." ".ucwords(strtolower($row[1]))."</option>";
									}
								?>
								</select>
							</div>
						</div>
							
						<div class="form-group">
							<div class="col-sm-12">
								<label for="sid">SID</label>
								<input name="sid" type="text" class="form-control" placeholder="SID" value=<?php echo "'".$sid."'"; ?>>
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