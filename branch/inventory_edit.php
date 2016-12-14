<?php
	ob_start();
	require_once("verify_access.php");
	require_once("../resource/database/hive.php");
	$page_name = "Edit Inventory";
	$page_type = 4;
?>
<html>
	<?php require_once("../resource/sections/branch_header.php"); ?>
	<link href="../resource/graphics/css/sb-admin-2.css" rel="stylesheet">
	<body>
		<?php require_once("../resource/sections/branch_banner.php"); ?>
		<?php require_once("../resource/sections/branch_menu.php"); ?>
		<div class="form_class">
			<div class="form_title"><h3 class="page_title">Update Inventory</h3></div>
				<div>
					<?php
						require_once("../resource/database/hive.php");
						
						$inventoryID = $_GET['inventory'];
						$inventory = mysqli_query($mysqli, "SELECT * FROM `inventory` WHERE `ID` = '$inventoryID'");
						$inventory = $inventory ->fetch_assoc();
							
						$name = $inventory['Name'];
						$category = $inventory['Category'];
						$description = $inventory['Description'];
						$weight = $inventory['Weight'];
						
						if(isset($_GET['sid']))
						{
							$sid = $_GET['sid'];
							
							//Price
							$price = mysqli_query($mysqli, "SELECT `Amount` FROM particular WHERE `Mark` = 2 AND `Transaction` = '$sid' AND `Inventory` = '$inventoryID'");
							$price = mysqli_fetch_row($price); $price = $price[0];
						}
						
						if($_POST)
						{
							$name = $mysqli->real_escape_string($_POST['name']);
							$category = $mysqli->real_escape_string($_POST['category']);
							$description = $mysqli->real_escape_string($_POST['description']);
							$weight = $mysqli->real_escape_string($_POST['weight']);
							$imagetmp = addslashes (file_get_contents($_FILES['img']['tmp_name']));
							
							if(isset($_GET['sid']))
							{	
								//Update price
								$price = $mysqli->real_escape_string($_POST['price']);
								$update_price = mysqli_query($mysqli, "UPDATE particular SET Amount = '$price' WHERE Mark = 2 AND `Transaction` = '$sid' AND `Inventory` = '$inventoryID'");
								
								if(isset($_SESSION['receive']))
									$update_buy = mysqli_query($mysqli, "UPDATE `inventory` SET `Buy` = '$price' WHERE `ID` = '$inventoryID'");
								else if(isset($_SESSION['sales']))
									$update_buy = mysqli_query($mysqli, "UPDATE `inventory` SET `Sell` = '$price' WHERE `ID` = '$inventoryID'");
								
							}
							
							if($imagetmp != "")
								$edit = mysqli_query($mysqli, "UPDATE `inventory` SET `Name` = '$name', `Category` = '$category', `Description` = '$description', `Weight` = '$weight', `Picture` = '$imagetmp' WHERE `ID` = '$inventoryID'");
							else
								$edit = mysqli_query($mysqli, "UPDATE `inventory` SET `Name` = '$name', `Category` = '$category', `Description` = '$description', `Weight` = '$weight' WHERE `ID` = '$inventoryID'");								
							
							if($edit) 
							{
								ob_end_clean();
							
								if(isset($_SESSION['sales']))
									header("location:t_sales.php");
								else if(isset($_SESSION['receive']))
									header("location:t_receiving.php");
								else if(isset($_SESSION['borrow']))
									header("location:t_borrow.php");
								else if(isset($_SESSION['return']))
									header("location:t_return.php");
								else if(isset($_SESSION['repair']))
									header("location:t_repair.php");
								else if(isset($_SESSION['release']))
									header("location:t_release.php");	
								else
									header("location:inventory.php");
							}
							else echo "<p class='ffail'> Oops, something went wrong. If error persists, please contact Tangent.</p>";
						}
					?>
				</div>
				<div class="form_content">
					<form class="form-horizontal" enctype="multipart/form-data" method=post action="<?php if(isset($_GET['sid'])) echo $_SERVER['PHP_SELF']."?inventory=".$inventoryID."&sid=".$sid; else echo $_SERVER['PHP_SELF']."?inventory=".$inventoryID; ?>">
						<div class="form-group">
							<div class="col-sm-12">
								<label for="name" class="control-label">Code</label>
								<input type="text" name="name" class="form-control" <?php if(isset($name)) echo  "value='".$name."'"; ?> >
							</div>
							
							<div class="col-sm-12">
								<label for="category" class="control-label">Category</label>
										<input type="text" name="category" class="form-control" <?php if(isset($category)) echo "value='".$category."'"; ?> >
							</div>
							
							<div class="col-sm-12">
								<label for="weight" class="control-label">Weight</label>
										<input type="text" name="weight" class="form-control" <?php if(isset($weight)) echo  "value='".$weight."'"; ?> >
							</div>
							
							<div class="col-sm-12">
								<label for="img" class="control-label">Image</label>
								<input type="file" name="img" class="form-control" >
							</div>
							
							<div class="col-sm-12">
								<label for="description" class="control-label">Description</label>
								<input type="text" name="description" class="form-control" <?php if(isset($description)) echo "value='".$description."'"; ?> >
							</div>
							
							<?php if(isset($_GET['sid']))  { ?>
							<div class="col-sm-12">
								<label for="price" class="control-label">Amount</label>
								<input type="text" name="price" class="form-control" <?php if(isset($price)) echo "value='".$price."'"; ?> >
							</div>
							<?php } ?>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-0 col-sm-6">
								<button type="submit" class="form_button">Update</button>
							</div>
							<div class="col-sm-offset-0 col-sm-6">
							<a class="btn form_button" href="inventory.php" role="button">Back</a>
						</div>			
					</form>
				</div>
			</div>
		</div>
	</div>
	</body>
</html>