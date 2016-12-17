<?php
	ob_start();
	session_start();
	require_once("verify_access.php");
	$page_name = "Receiving";
	$page_type = 5;
?>
<html>
	<?php require_once("../resource/sections/branch_header.php"); ?>
	<body>
		<?php 
			require_once("../resource/sections/branch_banner.php"); 
			
			//New Receiving
			if(!isset($_SESSION['receive']))
			{
				$creator = $_SESSION['id'];
				$new = mysqli_query($mysqli, "INSERT INTO `transaction`(`ID`, `Reference`, `Source`, `Destination`, `Comment`, `Date`, `Mark`, `Creator`, `Type`) VALUES ('', '', '200', '100', '', '$time_now', '2', '$creator', 1)");
				$_SESSION['receive'] = $mysqli->insert_id;
				$sid = $_SESSION['receive'];
				
				$reference = "";
				$comment = "";
			}
			else
			{
				$sid = $_SESSION['receive'];
				
				$reference = mysqli_query($mysqli, "SELECT `Reference` FROM transaction WHERE ID = '$sid'");
				$reference = mysqli_fetch_row($reference); $reference = $reference[0];
				
				$comment = mysqli_query($mysqli, "SELECT `Comment` FROM transaction WHERE ID = '$sid'");
				$comment = mysqli_fetch_row($comment); $comment = $comment[0];
			}
			
			//Actions
			if(isset($_GET['delete']))
			{
				$inventory = $_GET['delete'];
				$delete_connection = mysqli_query($mysqli, "UPDATE particular SET Mark = -1 WHERE Transaction = '$sid' AND Inventory = '$inventory'");
				$delete_inventory = mysqli_query($mysqli, "UPDATE inventory SET Mark = -1 WHERE ID = '$inventory'");
				if($delete_connection AND $delete_inventory)
					$_SESSION['success'] = "iID No. ".$inventory." successfully deleted.";
				else
					$_SESSION['fail'] = "iID No. ".$inventory." delete failed. Please contact support if error persists.";
				ob_end_clean();
				header("location:t_receiving.php");
			}
			
			if(isset($_GET['cancel']))
			{	
				$inventory = $_GET['cancel'];
				$delete_connection = mysqli_query($mysqli, "UPDATE particular SET Mark = -1 WHERE Transaction = '$sid' AND Inventory = '$inventory'");
				$reset_inventory = mysqli_query($mysqli, "UPDATE inventory SET Mark = -1 WHERE ID = '$inventory'");	
				$delete_transaction = mysqli_query($mysqli, "UPDATE transaction SET Mark = -1 WHERE ID = '$sid'");	
				unset($_SESSION['receive']);
				ob_end_clean();
				header("location:index.php");
			}
			
			if(isset($_GET['finalize']))
			{			
				$finalize = mysqli_query($mysqli, "UPDATE particular, inventory, transaction SET transaction.Mark = 1, particular.Mark = 1, inventory.Mark = 1 WHERE transaction.ID = $sid AND particular.Transaction = $sid AND particular.Inventory = inventory.ID AND inventory.Mark > 0 AND particular.Mark > 0");
				if($finalize)
				{
					unset($_SESSION['receive']);
					ob_end_clean();
					header("location:receipt.php?id=".$sid);
				}
				else
				{
					$_SESSION['fail'] = "Failed to finalize. Please contact support if error persists.";
				}
			}
			
			if(isset($_GET['save']))
			{			
				unset($_SESSION['receive']);
				ob_end_clean();
				header("location:index.php");
			}
			
			//Update Receipt Data
			if(isset($_POST['reference']))
			{
				$reference = $mysqli->real_escape_string($_POST['reference']);
				$comment = $mysqli->real_escape_string($_POST['comment']);
				$update = mysqli_query($mysqli, "UPDATE `transaction` SET `Reference` = '$reference', `Comment` = '$comment' WHERE `ID` = '$sid'");
				$_SESSION['success'] = "Successfully updated receipt details.";
			}
			
			//Add Item
			if(isset($_POST['name']))
			{
				$name = $mysqli->real_escape_string($_POST['name']);
				$category = $mysqli->real_escape_string($_POST['category']);
				$description = $mysqli->real_escape_string($_POST['description']);
				$weight = $mysqli->real_escape_string($_POST['weight']);
				$price= $mysqli->real_escape_string($_POST['price']);
				$imagetmp = addslashes (file_get_contents($_FILES['img']['tmp_name']));
				
				$newInventory = mysqli_query($mysqli, "INSERT INTO `inventory`(`ID`, `Name`, `Category`, `Subcategory`, `Description`, `Weight`, `Buy`, `Sell`, `Picture`, `Mark`) VALUES ('', '$name', '$category', '', '$description', '$weight', '$price', '', '$imagetmp', '2')");
				$new_inventory = $mysqli->insert_id;
				$newConnection = mysqli_query($mysqli, "INSERT INTO `particular`(`ID`, `Transaction`, `Inventory`, `Type`, `Amount`, `Mark`) VALUES ('', '$sid', '$new_inventory', '1', '$price', 2)");
			
				$_SESSION['success'] = "Successfully added new item.";
			}
		?>	
		
		<div style="width:100%">
			<div class="row">
				<div class="col-sm-4">
					<h3>Receive from Supplier</h3>
					<h5>SID No. <?php echo $sid; ?></h5>
					<div class="messages">
						<?php
							if(isset($_SESSION['success'])) { echo "<p class='fsuccess'>".$_SESSION['success']."</p>"; unset($_SESSION['success']); }
							else if(isset($_SESSION['fail'])) { echo "<p class='ffail'>".$_SESSION['fail']."</p>"; unset($_SESSION['fail']); }
						?>
					</div>
				</div>
				<div class="col-sm-8">
					<div style="margin-right:-10px">
						<div class="form_class_view">
							<div class="form_title_view">Receipt Details</div>
							<div class="form_content_view">
								<form class="form-horizontal" method=post action="<?php echo $_SERVER['PHP_SELF'];?>">
									<div class="form-group">
										<div class="col-sm-3">
											<label for="reference" class="control-label">Client Receipt No.</label>
											<input type="text" name="reference" class="form-control" required <?php if(isset($reference)) echo  "value='".$reference."'"; ?> >
										</div>
										
										<div class="col-sm-6">
											<label for="comment" class="control-label">Supplier</label>
											<input type="text" name="comment" class="form-control" <?php if(isset($comment)) echo "value='".$comment."'"; ?> >
										</div>
									
										<div class="col-sm-3">
											<label for="update" class="control-label">Update</label>
											<button name="update" type="submit" class="view_button">Update</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div style="clear:both"></div>
				
				<?php if($comment != "") { ?>
				<div style="margin-top:10px;width:100%">
					<div class="form_class_view">
						<div class="form_title_view">Add Items</div>
						<div class="form_content_view">
							<form class="form-horizontal" enctype="multipart/form-data" method=post action="<?php echo $_SERVER['PHP_SELF'];?>" >
								<div class="form-group">
									<div class="col-sm-3">
										<label for="name" class="control-label">Code</label>
										<input type="text" name="name" class="form-control" <?php if(isset($name)) echo  "value='".$name."'"; ?> >
									</div>
									
									<div class="col-sm-3">
										<label for="category" class="control-label">Category</label>
										<input type="text" name="category" class="form-control" <?php if(isset($category)) echo "value='".$category."'"; ?> >
									</div>
								
									<div class="col-sm-3">
										<label for="weight" class="control-label">Weight</label>
										<input type="text" name="weight" class="form-control" <?php if(isset($weight)) echo  "value='".$weight."'"; ?> >
									</div>
									
									<div class="col-sm-3">
										<label for="price" class="control-label">Price</label>
										<input type="text" name="price" class="form-control" <?php if(isset($price)) echo "value='".$price."'"; ?> >
									</div>
									
									
								</div>
								<div class="form-group">
								
									<div class="col-sm-3">
										<label for="img" class="control-label">Image</label>
										<input type="file" name="img" class="form-control" >
									</div>
									
									<div class="col-sm-7">
										<label for="description" class="control-label">Description</label>
										<input type="text" name="description" class="form-control" <?php if(isset($description)) echo "value='".$description."'"; ?> >
									</div>
									
								
									<div class="col-sm-2">
										<label for="add" class="control-label">Add</label>
										<button name="add" type="submit" class="view_button">Add</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				
				<div class="selecttable" style="width:98%;margin-left:1%">
			        	<table class="table table-bordered table-stripped" style="font-size:12px;width:100%">
					<?php
						$result = mysqli_query($mysqli, "SELECT inventory.ID, Name, Category, Description, Weight, Picture, Amount FROM particular, inventory WHERE particular.Transaction = '$sid' AND inventory.ID = particular.Inventory AND particular.Mark > 0");
						
						echo '<thead>';
						echo '<tr style="text-align:center;font-weight:bold;background:black;color:white">';
						echo '<th style="width:8%;text-align:center">Picture</th>';
						echo '<th style="width:8%;text-align:center">iID</th>';
						echo '<th style="width:10%;text-align:center">Code</th>';
						echo '<th style="width:10%;text-align:center">Category</th>';
						echo '<th style="width:35%;text-align:center">Description</th>';
						echo '<th style="width:8%;text-align:center">Weight</th>';
						echo '<th style="width:8%;text-a0ign:center">Amount</th>';
						echo '<th style="width:13%;text-align:center">Action</th>';
						echo '</tr></thead><tbody>';
						
						for($i=0, $total=0; $i < mysqli_num_rows($result); $i++)
						{	
							$result->data_seek($i);
			    				$row = $result->fetch_row();
			    				
			    				echo "<tr style='text-align:center'>";
			    				
			    				//Picture
							echo '<td><img style="width:20px;" src="data:image/jpeg;base64,'.base64_encode( $row[5] ).'"/></td>';
			    				
			    				//ID
							echo '<td>'.sprintf('%05d', $row[0]).'</td>';
							
							//Name
							echo '<td>'.$row[1].'</td>';
							
							//Category
							echo '<td>'.$row[2].'</td>';
							
							//Description
							echo '<td>'.$row[3].'</td>';
							
							//Weight
							echo '<td>'.$row[4].' g</td>';
							
							//Amount
							echo '<td>$ '.number_format($row[6],2).'</td>';
							$total += $row[6];
							
							//Delete
							echo '<td><a href="inventory_edit.php?inventory='.$row[0].'&sid='.$sid.'"><i class="fa fa-edit" aria-hidden="true"></i> Edit</a>&nbsp|&nbsp<a href="t_receiving.php?delete='.$row[0].'"><i class="fa fa-times" aria-hidden="true"></i> Delete</a></td>';
			    				
			    				echo "</tr>";
						}
						
						if(mysqli_num_rows($result) == 0)
						{
							echo "<tr><td colspan=7 style='text-align:center'>Cart Empty.</td></tr>";
						}
						else
						{
							echo "<tr><td colspan=6 style='text-align:right;margin-right:5px;'>Total Amount Payable</td><td style='text-align:center;font-weight:bold'>$ ".number_format($total,2)."</td><td>&nbsp</td></tr>";
						}
					?>
					</tbody>
					</table>
				</div>
				
				<?php } ?>
			
				<div class="selecttable" style="width:98%;margin-left:1%">
			        	<table class="table" style="font-size:12px;width:100%">
			        		<tr style="text-align:center;">
			        			<td style="width:76%;text-align:right;margin-right:5px;">&nbsp</td>
			        			<td style="width:8%"><a class='btn view_button' href='t_receiving.php?save=true' role='button'>Save</a></td>
			        			<td style="width:8%"><a class='btn view_button' href='t_receiving.php?cancel=true' role='button'>Cancel</a></td>
			        			<?php if($comment != "") { ?><td style="width:8%"><a class='btn view_button' href='t_receiving.php?finalize=true' role='button'>Finalize</a></td><?php } ?>
			        		</tr>
			        	</table>
			        </div>
			</div>
		</div>			
	</body>
</html>