<?php
	ob_start();
	session_start();
	require_once("verify_access.php");
	$page_name = "Release";
	$page_type = 5;
?>
<html>
	<?php require_once("../resource/sections/branch_header.php"); ?>
	<body>
		<?php 
			require_once("../resource/sections/branch_banner.php"); 
			
			//New Receiving
			if(!isset($_SESSION['release']))
			{
				$creator = $_SESSION['id'];
				$new = mysqli_query($mysqli, "INSERT INTO `transaction`(`ID`, `Reference`, `Source`, `Destination`, `Comment`, `Date`, `Mark`, `Creator`, `Type`) VALUES ('', '', '100', '2', '', '$time_now', '2', '$creator', 6)");
				$_SESSION['release'] = $mysqli->insert_id;
				$sid = $_SESSION['release'];
			}
			else
			{
				$sid = $_SESSION['release'];		
				
				$destination = mysqli_query($mysqli, "SELECT `Destination` FROM transaction WHERE ID = '$sid'");
				$destination = mysqli_fetch_row($destination); $destination = $destination[0];
				
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
				$reset_inventory = mysqli_query($mysqli, "UPDATE inventory SET Mark = 5 WHERE ID = '$inventory'");
				if($delete_connection AND $reset_inventory)
					$_SESSION['success'] = "iID No. ".$inventory." successfully deleted.";
				else
					$_SESSION['fail'] = "iID No. ".$inventory." delete failed. Please contact support if error persists.";
				ob_end_clean();
				header("location:t_release.php");
			}
			
			if(isset($_GET['pdelete']))
			{
				$payment = $_GET['delete'];
				$delete_payment = mysqli_query($mysqli, "UPDATE payment SET Mark = -1 WHERE ID = '$payment'");
				if($delete_payment)
					$_SESSION['success'] = "PID No. ".$payment." successfully deleted.";
				else
					$_SESSION['fail'] = "PID No. ".$payment." delete failed. Please contact support if error persists.";
				ob_end_clean();
				header("location:t_release.php");
			}
			
			if(isset($_GET['finalize']))
			{			
				$finalize = mysqli_query($mysqli, "UPDATE particular, inventory, transaction SET transaction.Mark = 1, particular.Mark = 1, inventory.Mark = 6 WHERE transaction.ID = $sid AND particular.Transaction = $sid AND particular.Inventory = inventory.ID AND inventory.Mark > 0 AND particular.Mark > 0");
				$finalize_payments = mysqli_query($mysqli, "UPDATE payment SET Mark = 1 WHERE SID = '$sid'");
				if($finalize and $finalize_payments)
				{
					unset($_SESSION['release']);
					ob_end_clean();
					header("location:receipt.php?id=".$sid);
				}
				else
				{
					$_SESSION['fail'] = "Failed to finalize. Please contact support if error persists.";
				}
			}
			
			if(isset($_GET['cancel']))
			{	
				$inventory = $_GET['cancel'];
				$delete_connection = mysqli_query($mysqli, "UPDATE particular SET Mark = -1 WHERE Transaction = '$sid' AND Inventory = '$inventory'");
				$delete_inventory = mysqli_query($mysqli, "UPDATE inventory SET Mark = 5 WHERE ID = '$inventory'");	
				$delete_transaction = mysqli_query($mysqli, "UPDATE transaction SET Mark = -1 WHERE ID = '$sid'");	
				$delete_payments = mysqli_query($mysqli, "UPDATE payment SET Mark = -1 WHERE SID = '$sid'");
				unset($_SESSION['release']);
				ob_end_clean();
				header("location:index.php");
			}
			
			if(isset($_GET['save']))
			{			
				unset($_SESSION['release']);
				ob_end_clean();
				header("location:index.php");
			}
			
			//Update Receipt Data
			if(isset($_POST['destination']))
			{
				$destination = $mysqli->real_escape_string($_POST['destination']);
				$reference = $mysqli->real_escape_string($_POST['reference']);
				$comment = $mysqli->real_escape_string($_POST['comment']);
				$update = mysqli_query($mysqli, "UPDATE `transaction` SET `Destination` = '$destination', `Reference` = '$reference', `Comment` = '$comment' WHERE `ID` = '$sid'");
				$update_payments = mysqli_query($mysqli, "UPDATE payment SET Client = '$destination' WHERE SID = '$sid'"); 
				$_SESSION['success'] = "Successfully updated receipt details.";
			}
			
			//Add Item
			if(isset($_POST['inventory']))
			{
				$inventory = $mysqli->real_escape_string($_POST['inventory']);
				$price = $mysqli->real_escape_string($_POST['price']);
				
				//Transaction Type: 1-buy, 2-borrow, 3-sell, 4-release
				//Mark: 1- available, 2- on transaction, 3-sold, 4-borrowed
				$add_cart = mysqli_query($mysqli, "INSERT INTO `particular`(`ID`, `Transaction`, `Inventory`, `Type`, `Amount`, `Mark`) VALUES ('', '$sid', '$inventory', '6', '$price', 2)");
				$update_inventory = mysqli_query($mysqli, "UPDATE inventory SET Mark = 2 WHERE ID = '$inventory'");
				
				$_SESSION['success'] = "Successfully added new item.";
			}
			
			//Payments
			if(isset($_POST['ptype']))
			{
				$ptype = $mysqli->real_escape_string($_POST['ptype']);
				$amount = $mysqli->real_escape_string($_POST['amount']);
				$cbank = $mysqli->real_escape_string($_POST['cbank']);
				$cdate = $mysqli->real_escape_string($_POST['cdate']);
				
				$add_pay  = mysqli_query($mysqli, "INSERT INTO `payment`(`ID`, `Type`, `Date`, `Amount`, `CBank`, `CDate`, `Client`, `SID`, `Mark`) 
								   VALUES ('', '$ptype', '$time_now', '$amount', '$cbank', '$cdate', '$destination', '$sid', '2')");
				
				if($add_pay) $_SESSION['success'] = "Successfully added payment.";
			}
		?>	
		
		<div style="width:100%">
			<div class="row">
				<div class="col-sm-4">
					<h3>Release Repaired Items</h3>
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
										<div class="col-sm-4">
											<label for="reference" class="control-label">Reference</label>
											<input type="text" name="reference" class="form-control" <?php if(isset($reference)) echo  "value='".$reference."'"; ?> >
										</div>
										<div class="col-sm-8">
											<label for="destination" class="control-label">Client</label>
											<select name="destination" class="form-control" >
											<?php
												$clients = mysqli_query($mysqli, "SELECT `ID`, `Name` FROM entity WHERE `Mark` = 1 AND `Type` = 4");
												for($i=0; $i<mysqli_num_rows($clients) and mysqli_num_rows($clients)>0; $i++)
												{
													$clients->data_seek($i);
													$row = $clients->fetch_row();
												
													if($row[0] == $destination) echo "<option value='".$row[0]."' SELECTED>".sprintf('%05d', $row[0])." ".ucwords(strtolower($row[1]))."</option>";
													else echo "<option value='".$row[0]."'>".sprintf('%05d', $row[0])." ".ucwords(strtolower($row[1]))."</option>";
												}
											?>
											</select>
										</div>
									</div>
									<div class="form-group">	
										<div class="col-sm-9">
											<label for="comment" class="control-label">Comments</label>
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
				
				<?php if($reference != "") { ?>
				
				<h3 style="margin-left:1%">Client Cart</h3>
				<div class="selecttable" style="width:98%;margin-left:1%">
			        	<table class="table table-bordered table-stripped" style="font-size:12px;width:100%">
					<?php
						$result = mysqli_query($mysqli, "SELECT inventory.ID, Name, Category, Description, Weight, Amount FROM particular, inventory WHERE particular.Transaction = '$sid' AND inventory.ID = particular.Inventory AND particular.Mark > 0");
						
						echo '<thead>';
						echo '<tr style="text-align:center;font-weight:bold;background:black;color:white">';
						echo '<th style="width:8%;text-align:center">Picture</th>';
						echo '<th style="width:8%;text-align:center">iID</th>';
						echo '<th style="width:20%;text-align:center">Name</th>';
						echo '<th style="width:15%;text-align:center">Category</th>';
						echo '<th style="width:25%;text-align:center">Description</th>';
						echo '<th style="width:8%;text-align:center">Weight</th>';
						echo '<th style="width:8%;text-align:center">Amount</th>';
						echo '<th style="width:8%;text-align:center">Delete</th>';
						echo '</tr></thead><tbody>';
						
					?>
					
					<form method=post action="<?php echo $_SERVER['PHP_SELF'];?>" >
						<td style="vertical-align:middle;text-align:center;font-weight:bold">Add Item</td>
						<td colspan=5>
							<select name="inventory" class="form-control" >
							<?php
								$available = mysqli_query($mysqli, "SELECT `ID`, `Name`, `Category`, `Weight` FROM inventory WHERE `Mark` = 5");
								for($i=0; $i<mysqli_num_rows($available) and mysqli_num_rows($available)>0; $i++)
								{
									$available->data_seek($i);
									$row = $available->fetch_row();
								
									$repairs = mysqli_query($mysqli, "SELECT Source FROM transaction, particular WHERE Transaction = transaction.ID AND Inventory = '$row[0]' AND particular.Mark = 1 ORDER BY transaction.ID DESC LIMIT 1");
									$repairs = mysqli_fetch_row($repairs);
									
									if($repairs[0] == $destination)
									echo "<option value='".$row[0]."'>".sprintf('%05d', $row[0])." ".$row[1]." [".$row[2].", ".$row[3]."]</option>";
								}
							?>
							</select>
						</td>
						<td><input type="text" name="price" class="form-control" required ></td>
						<td><button name="add" type="submit" class="view_button">Add</button></td>
					</form>
					
					<?php
						
						for($i=0, $total=0; $i < mysqli_num_rows($result); $i++)
						{	
							$result->data_seek($i);
			    				$row = $result->fetch_row();
			    				
			    				echo "<tr style='text-align:center'>";
			    				
			    				//Picture
			    			$path = "../resource/images/inv_image/".sprintf('%d', $row[0]).".png";
							echo '<td><img style="width:20px;" src="'.$path.'"/></td>';
			    				
			    				//ID
							echo '<td>'.sprintf('%05d', $row[0]).'</td>';
							
							//Name
							echo '<td>'.ucwords(strtolower($row[1])).'</td>';
							
							//Category
							echo '<td>'.ucwords(strtolower($row[2])).'</td>';
							
							//Description
							echo '<td>'.$row[3].'</td>';
							
							//Weight
							echo '<td>'.$row[4].'</td>';
							
							//Amount
							echo '<td>'.number_format($row[5],2).'</td>';
							$total += $row[5];
							
							//Delete
							echo '<td><a href="t_receiving.php?delete='.$row[0].'">Delete</a></td>';
			    				
			    				echo "</tr>";
						}
						
						if(mysqli_num_rows($result) == 0)
						{
							echo "<tr><td colspan=7 style='text-align:center'>Cart Empty.</td></tr>";
						}
						else
						{
							echo "<tr><td colspan=6 style='text-align:right;margin-right:5px;'>Total Amount Payable</td><td style='text-align:center;font-weight:bold'>".number_format($total,2)."</td><td>&nbsp</td></tr>";
						}
					?>
					</tbody>
					</table>
				</div>
				
				<hr style="border-top: 2px solid black;">
				
				<h3 style="margin-left:1%">Payments</h3>
				<div class="selecttable" style="width:100%;margin-left:1%">
			        	<table class="table table-bordered table-stripped" style="font-size:12px;width:100%">
					<?php
						$result = mysqli_query($mysqli, "SELECT * FROM payment WHERE `SID` = '$sid' AND Mark >= 1");
						
						echo '<thead>';
						echo '<tr style="text-align:center;font-weight:bold;background:black;color:white">';
						echo '<th style="width:20%;text-align:center">PID</th>';
						echo '<th style="width:20%;text-align:center">Type</th>';
						echo '<th style="width:20%;text-align:center">Bank</th>';
						echo '<th style="width:20%;text-align:center">Check Date</th>';
						echo '<th style="width:10%;text-align:center">Amount</th>';
						echo '<th style="width:10%;text-align:center">Delete</th>';
						echo '</tr></thead><tbody>';
					?>
					
					<form method=post action="<?php echo $_SERVER['PHP_SELF'];?>" >
						<td style="vertical-align:middle;text-align:center;font-weight:bold">Add Payment</td>
						<td>
							<select name="ptype" class="form-control" >
								<option value=1>Cash</option>
								<option value=2>Check</option>
							</select>
						</td>
						<td><input type="text" name="cbank" placeholder="Check Bank" class="form-control"></td>
						<td><input type="date" name="cdate" class="form-control"></td>
						<td><input type="text" name="amount" placeholder="Amount" class="form-control" required ></td>
						<td><button name="add" type="submit" class="view_button">Add</button></td>
					</form>
					
					<?php
						for($i=0, $ptotal=0; $i < mysqli_num_rows($result); $i++)
						{	
							$result->data_seek($i);
			    				$row = $result->fetch_row();
			    				
			    				echo "<tr style='text-align:center'>";
						    	//ID
							echo '<td>'.$row[0].'</td>';
							
							//Type
							if($row[1] == 1) 
							{
								echo '<td>Cash</td>';
								echo '<td>&nbsp</td>';
								echo '<td>&nbsp</td>';
							}
							else 
							{
								echo '<td>Check</td>';
							
								//Bank
								echo '<td>'.$row[4].'</td>';
							
								//Date
								echo '<td>'.date("M d, Y H:i A", strtotime($row[5])).'</td>';
							}
							
							//Amount
							echo '<td>'.number_format($row[3],2).'</td>'; $ptotal += $row[3];
							
							//Delete
							echo '<td><a href="t_release.php?pdelete='.$row[0].'">Delete</a></td>';
			    				
			    				echo "</tr>";
						}
						
						if(mysqli_num_rows($result) == 0)
						{
							echo "<tr><td colspan=6 style='text-align:center'>No payments made.</td></tr>";
						}
						
						echo "<tr><td colspan=5 style='text-align:right;margin-right:5px;'>Total Amount Payable</td><td style='text-align:center;font-weight:bold'>".number_format($total,2)."</td><td>&nbsp</td></tr>";
						echo "<tr><td colspan=5 style='text-align:right;margin-right:5px;'>Total Amount Paid</td><td style='text-align:center;font-weight:bold'>".number_format($ptotal,2)."</td><td>&nbsp</td></tr>";
						echo "<tr><td colspan=5 style='text-align:right;margin-right:5px;'>Total Balance</td><td style='text-align:center;font-weight:bold'>".number_format(($total-$ptotal),2)."</td><td>&nbsp</td></tr>";
						
					?>
					</tbody>
					</table>
				</div>
				
				<?php } ?>
			
				<div class="selecttable" style="width:98%;margin-left:1%">
			        	<table class="table" style="font-size:12px;width:100%">
			        		<tr style="text-align:center;">
			        			<td style="width:76%;text-align:right;margin-right:5px;">&nbsp</td>
			        			<td style="width:8%"><a class='btn view_button' href='t_release.php?save=true' role='button'>Save</a></td>
			        			<td style="width:8%"><a class='btn view_button' href='t_release.php?cancel=true' role='button'>Cancel</a></td>
			        			<?php if($reference != "") { ?><td style="width:8%"><a class='btn view_button' href='t_release.php?finalize=true' role='button'>Finalize</a></td><?php } ?>
			        		</tr>
			        	</table>
			        </div>
			</div>
		</div>			
	</body>
</html>