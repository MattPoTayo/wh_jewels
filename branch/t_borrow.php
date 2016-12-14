<?php
	ob_start();
	session_start();
	require_once("verify_access.php");
	$page_name = "Borrow";
	$page_type = 5;
?>
<html>
	<?php require_once("../resource/sections/branch_header.php"); ?>
	<body>
		<?php 
			require_once("../resource/sections/branch_banner.php"); 
			
			//New Receiving
			if(!isset($_SESSION['borrow']))
			{
				$creator = $_SESSION['id'];
				$new = mysqli_query($mysqli, "INSERT INTO `transaction`(`ID`, `Reference`, `Source`, `Destination`, `Comment`, `Date`, `Mark`, `Creator`, `Type`) VALUES ('', '', '100', '2', '', '$time_now', '2', '$creator', 2)");
				$_SESSION['borrow'] = $mysqli->insert_id;
				$sid = $_SESSION['borrow'];
			}
			else
			{
				$sid = $_SESSION['borrow'];		
				
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
				$reset_inventory = mysqli_query($mysqli, "UPDATE inventory SET Mark = 1 WHERE ID = '$inventory'");
				if($delete_connection AND $reset_inventory)
					$_SESSION['success'] = "iID No. ".$inventory." successfully deleted.";
				else
					$_SESSION['fail'] = "iID No. ".$inventory." delete failed. Please contact Tangent if error persists.";
				ob_end_clean();
				header("location:t_borrow.php");
			}
			
			if(isset($_GET['pdelete']))
			{
				$payment = $_GET['pdelete'];
				$delete_payment = mysqli_query($mysqli, "UPDATE payment SET Mark = -1 WHERE ID = '$payment'");
				if($delete_payment)
					$_SESSION['success'] = "PID No. ".$payment." successfully deleted.";
				else
					$_SESSION['fail'] = "PID No. ".$payment." delete failed. Please contact Tangent if error persists.";
				ob_end_clean();
				header("location:t_borrow.php");
			}
			
			if(isset($_GET['cancel']))
			{	
				$inventory = $_GET['cancel'];
				$delete_connection = mysqli_query($mysqli, "UPDATE particular SET Mark = -1 WHERE Transaction = '$sid' AND Inventory = '$inventory'");
				$reset_inventory = mysqli_query($mysqli, "UPDATE inventory SET Mark = 1 WHERE ID = '$inventory'");	
				$delete_transaction = mysqli_query($mysqli, "UPDATE transaction SET Mark = -1 WHERE ID = '$sid'");	
				$delete_payments = mysqli_query($mysqli, "UPDATE payment SET Mark = -1 WHERE SID = '$sid'");
				unset($_SESSION['borrow']);
				ob_end_clean();
				header("location:index.php");
			}
			
			if(isset($_GET['finalize']))
			{			
				$finalize = mysqli_query($mysqli, "UPDATE particular, inventory, transaction SET transaction.Mark = 1, particular.Mark = 1, inventory.Mark = 4 WHERE transaction.ID = $sid AND particular.Transaction = $sid AND particular.Inventory = inventory.ID AND inventory.Mark > 0 AND particular.Mark > 0");
				$finalize_payments = mysqli_query($mysqli, "UPDATE payment SET Mark = 1 WHERE Mark = 2 AND SID = '$sid'");
				if($finalize and $finalize_payments)
				{
					unset($_SESSION['borrow']);
					ob_end_clean();
					header("location:receipt.php?id=".$sid);
				}
				else
				{
					$_SESSION['fail'] = "Failed to finalize. Please contact Tangent if error persists.";
				}
			}
			
			if(isset($_GET['save']))
			{			
				unset($_SESSION['borrow']);
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
				
				//Transaction Type: 1-buy, 2-borrow, 3-sell
				$add_cart = mysqli_query($mysqli, "INSERT INTO `particular`(`ID`, `Transaction`, `Inventory`, `Type`, `Amount`, `Mark`) VALUES ('', '$sid', '$inventory', '2', '$price', 2)");
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
								   VALUES ('', '$ptype', '$time_now', '$amount', '$cbank', '$cdate', '$client', '$sid', '2')");
				
				if($add_pay) $_SESSION['success'] = "Successfully added payment.";
			}
		?>	
		
		<div style="width:100%">
			<div class="row">
				<div class="col-sm-4">
					<h3>Borrow Transaction</h3>
					<h5>SID No. <?php echo sprintf('%05d', $sid); ?></h5>
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
											<label for="reference" class="control-label">Trust Receipt Agreement No.</label>
											<input type="text" name="reference" class="form-control" required <?php if(isset($reference)) echo  "value='".$reference."'"; ?> >
										</div>
										
										<div class="col-sm-8">
											<label for="destination" class="control-label">Client</label>
											<select name="destination" class="selectpicker form-control" data-live-search="true">
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
											<label for="comment" class="control-label">Remarks</label>
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
					<form method=post action="<?php echo $_SERVER['PHP_SELF'];?>" >
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
						echo '<th style="width:9%;text-align:center">Weight</th>';
						echo '<th style="width:10%;text-align:center">Amount</th>';
						echo '<th style="width:10%;text-align:center">Actions</th>';
						echo '</tr></thead><tbody>';
						
					?>
					
					<tr>
						<td style="vertical-align:middle;text-align:center;font-weight:bold">Add Item</td>
						<td colspan=5>
							<select name="inventory" class="selectpicker form-control" data-live-search="true">
							<?php
								$available = mysqli_query($mysqli, "SELECT `ID`, `Name`, `Category`, `Weight` FROM inventory WHERE `Mark` = 1");
								for($i=0; $i<mysqli_num_rows($available) and mysqli_num_rows($available)>0; $i++)
								{
									$available->data_seek($i);
									$row = $available->fetch_row();
								
									echo "<option value='".$row[0]."'>".sprintf('%05d', $row[0])." ".$row[1]." [".$row[2].", ".$row[3]."]</option>";
								}
							?>
							</select>
						</td>
						<td><input type="text" name="price" class="form-control" required ></td>
						<td><button name="add" type="submit" class="view_button">Add</button></td>
					</tr>
					
					<?php
						
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
							echo '<td>'.$row[4].'g</td>';
							
							//Amount
							echo '<td>&#8369 '.number_format($row[6],2).'</td>';
							$total += $row[6];
							
							//Delete
							echo '<td><a href="inventory_edit.php?inventory='.$row[0].'&sid='.$sid.'"><i class="fa fa-edit" aria-hidden="true"></i> Edit</a>&nbsp|&nbsp<a href="t_borrow.php?delete='.$row[0].'"><i class="fa fa-times" aria-hidden="true"></i> Delete</a></td>';
			    				
			    				echo "</tr>";
						}
						
						if(mysqli_num_rows($result) == 0)
						{
							echo "<tr><td colspan=8 style='text-align:center'>Cart Empty.</td></tr>";
						}
						else
						{
							echo "<tr><td colspan=6 style='text-align:right;margin-right:5px;'>Total Amount Payable</td><td style='text-align:center;font-weight:bold'>&#8369 ".number_format($total,2)."</td><td>&nbsp</td></tr>";
						}
					?>
					</tbody>
					</table>
					</form>
				</div>
				
				<hr style="border-top: 2px solid black;">
				
				<h3 style="margin-left:1%">Payments</h3>
				<div class="selecttable" style="width:100%;margin-left:1%">
					<form method=post action="<?php echo $_SERVER['PHP_SELF'];?>" >
			        	<table class="table table-bordered table-stripped" style="font-size:12px;width:100%">
					<?php
						$result = mysqli_query($mysqli, "SELECT * FROM payment WHERE `SID` = '$sid' AND Mark >= 1");
						
						echo '<thead>';
						echo '<tr style="text-align:center;font-weight:bold;background:black;color:white">';
						echo '<th style="width:20%;text-align:center">PID</th>';
						echo '<th style="width:10%;text-align:center">Type</th>';
						echo '<th style="width:10%;text-align:center">Check No.</th>';
						echo '<th style="width:20%;text-align:center">Bank</th>';
						echo '<th style="width:20%;text-align:center">Check Date</th>';
						echo '<th style="width:10%;text-align:center">Amount</th>';
						echo '<th style="width:10%;text-align:center">Delete</th>';
						echo '</tr></thead><tbody>';
					?>
					
					<tr>
						<td style="vertical-align:middle;text-align:center;font-weight:bold">Add Payment</td>
						<td>
							<select name="ptype" class="form-control" >
								<option value=1>Cash</option>
								<option value=2>Check</option>
							</select>
						</td>
						<td><input type="text" name="cnum" placeholder="Check Number" class="form-control"></td>
						<td><input type="text" name="cbank" placeholder="Check Bank" class="form-control"></td>
						<td><input type="date" name="cdate" class="form-control"></td>
						<td><input type="text" name="amount" placeholder="Amount" class="form-control" required ></td>
						<td><button name="add" type="submit" class="view_button">Add</button></td>
					</tr>
					
					<?php
						for($i=0, $ptotal=0; $i < mysqli_num_rows($result); $i++)
						{	
							$result->data_seek($i);
			    				$row = $result->fetch_row();
			    				
			    				echo "<tr style='text-align:center'>";
						    	//ID
							echo '<td>'.sprintf('%05d', $row[0]).'</td>';
							
							//Type
							if($row[1] == 1) 
							{
								echo '<td>Cash</td>';
								echo '<td>&nbsp</td>';
								echo '<td>&nbsp</td>';
								echo '<td>&nbsp</td>';
							}
							else 
							{
								echo '<td>Check</td>';
							
								//Check Number
								echo '<td>'.$row[9].'</td>';
							
								//Bank
								echo '<td>'.$row[4].'</td>';
							
								//Date
								echo '<td>'.date("F d, Y", strtotime($row[5])).'</td>';
							}
							
							//Amount
							echo '<td>&#8369 '.number_format($row[3],2).'</td>'; $ptotal += $row[3];
							
							//Delete
							echo '<td><a href="t_borrow.php?pdelete='.$row[0].'"><i class="fa fa-times" aria-hidden="true"></i> Delete</a></td>';
			    				
			    				echo "</tr>";
						}
						
						if(mysqli_num_rows($result) == 0)
						{
							echo "<tr><td colspan=7 style='text-align:center'>No payments made.</td></tr>";
						}
						
						echo "<tr><td colspan=5 style='text-align:right;margin-right:5px;'>Total Amount Payable</td><td style='text-align:center;font-weight:bold'>&#8369 ".number_format($total,2)."</td><td>&nbsp</td></tr>";
						echo "<tr><td colspan=5 style='text-align:right;margin-right:5px;'>Total Amount Paid</td><td style='text-align:center;font-weight:bold'>&#8369 ".number_format($ptotal,2)."</td><td>&nbsp</td></tr>";
						echo "<tr><td colspan=5 style='text-align:right;margin-right:5px;'>Total Balance</td><td style='text-align:center;font-weight:bold'>&#8369 ".number_format(($total-$ptotal),2)."</td><td>&nbsp</td></tr>";
						
					?>
					</tbody>
					</table>
					</form>
				</div>
				<?php } ?>
				
				<div class="selecttable" style="width:98%;margin-left:1%">
			        	<table class="table" style="font-size:12px;width:100%">
			        		<tr style="text-align:center;">
			        			<td style="width:76%;text-align:right;margin-right:5px;">&nbsp</td>
			        			<td style="width:8%"><a class='btn view_button' href='t_borrow.php?save=true' role='button'>Save</a></td>
			        			<td style="width:8%"><a class='btn view_button' href='t_borrow.php?cancel=true' role='button'>Cancel</a></td>
			        			<?php if($reference != "") { ?><td style="width:8%"><a class='btn view_button' href='t_borrow.php?finalize=true' role='button'>Finalize</a></td><?php } ?>
			        		</tr>
			        	</table>
			        </div>
			</div>
		</div>			
	</body>
</html>