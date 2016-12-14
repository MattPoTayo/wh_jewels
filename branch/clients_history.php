<?php
	ob_start();
	session_start();
	require_once("verify_access.php");
	$page_name = "Client History";
	$page_type = 3;
?>
<html>
	<?php require_once("../resource/sections/branch_header.php"); ?>
	<body>
		<?php 
			require_once("../resource/sections/branch_banner.php"); 
			require_once("../resource/sections/branch_menu.php"); 
		?>	
		
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.11/css/jquery.dataTables.min.css"/>
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.0.2/css/responsive.dataTables.min.css"/>
		<script type="text/javascript" src="https://cdn.datatables.net/1.10.11/js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.0.2/js/dataTables.responsive.min.js"></script>
				
		<script type="text/javascript" charset="utf-8">
	            $(document).ready(function(){
	                $('#patients').dataTable({
			        "iDisplayLength": [100]
			    });
			    
			$('#patients2').dataTable({
			        "iDisplayLength": [100]
			    });
			    
			$('#patients3').dataTable({
			        "iDisplayLength": [100]
			    });
	            })
	        </script>		
		
		<div>
			<div class="row" style="margin-left:1px;">
				<h3>Client History</h3>
				<div class="messages">
					<?php
						if(isset($_SESSION['success'])) { echo "<p class='fsuccess'>".$_SESSION['success']."</p>"; unset($_SESSION['success']); }
						else if(isset($_SESSION['fail'])) { echo "<p class='ffail'>".$_SESSION['fail']."</p>"; unset($_SESSION['fail']); }
					?>
				</div>
			</div>
			
			<table class="table" style="width:100%;font-size:12px">
				<?php
					require_once("../resource/database/hive.php");
					$id = $_GET['id'];
					
					$result3 = mysqli_query($mysqli, "SELECT * FROM `entity` WHERE `ID` = '$id'");
					$result3->data_seek(0);
				    	$row3 = $result3->fetch_row();
				    	
				    	$bill = mysqli_query($mysqli, "SELECT SUM(Amount) FROM particular, transaction WHERE particular.Type > 1 AND particular.Mark = 1 AND transaction.Mark = 1 AND Transaction = transaction.ID AND (Source = $id OR Destination = $id)");
					$bill = mysqli_fetch_row($bill);
					
					$payment = mysqli_query($mysqli, "SELECT SUM(Amount) FROM payment WHERE Client = $id AND Mark = 1");
					$payment = mysqli_fetch_row($payment);
					
					$last = mysqli_query($mysqli, "SELECT Date FROM transaction WHERE Mark = 1 AND (Source = $id OR Destination = $id) ORDER BY ID DESC LIMIT 1 ");
					$last = mysqli_fetch_row($last);

				?>
				<tr><td style="width:20%">Client ID</td><td style="width:30%"><?php echo $row3[0]; ?></td><td style="width:20%">Total Bill</td><td style="width:30%"><?php echo number_format($bill[0],2); ?></td></tr>
				<tr><td style="width:20%">Name</td><td style="width:30%"><?php echo $row3[1]; ?></td><td style="width:20%">Total Payments</td><td style="width:30%"><?php echo number_format($payment[0],2); ?></td></tr>
				<tr><td style="width:20%">Address</td><td style="width:30%"><?php echo $row3[2]; ?></td><td style="width:20%">Total Balance</td><td style="width:30%"><?php echo number_format(($bill[0]-$payment[0]),2); ?></td></tr>
				<tr><td style="width:20%">Phone</td><td style="width:30%"><?php echo $row3[3]; ?></td><td style="width:20%">Last Transaction</td><td style="width:30%"><?php echo date("F d, Y H:i A", strtotime($last[0])); ?></td></tr>

			</table>
			
			<hr style="border-top: 2px solid black;">
			
			<h4>Receipts</h4>
			<div class="table-wrapper">
		        	<table id="patients" class="display responsive nowrap selecttable" cellspacing="0" width="100%">
					<?php
						$result = mysqli_query($mysqli, "SELECT `ID`, `Reference`, (SELECT Name FROM entity WHERE ID = `Source`),  (SELECT Name FROM entity WHERE ID =  `Destination`), `Date`, (SELECT SUM(Amount) FROM particular WHERE particular.Mark = 1 AND Transaction = transaction.ID), Type, Comment, transaction.Mark FROM `transaction` WHERE `Mark` = 1 AND (Source = $id or Destination = $id)");
					
						echo '<thead>';
						echo '<tr style="text-align:center;font-weight:bold;">';
						echo '<th style="width:8%;text-align:center;" data-priority="1">SID</th>';
						echo '<th style="width:10%;text-align:center;" data-priority="2">Reference</th>';
						echo '<th style="width:10%;text-align:center;" data-priority="3">Date</th>';
						echo '<th style="width:15%;text-align:center;" data-priority="4">Source</th>';
						echo '<th style="width:15%;text-align:center;" data-priority="5">Destination</th>';
						echo '<th style="width:10%;text-align:center;" data-priority="6">Type</th>';
						echo '<th style="width:10%;text-align:center;" data-priority="7">Amount</th>';
						echo '<th style="width:10%;text-align:center;" data-priority="8">Action</th>';
						echo '</tr></thead><tbody>';
					
						for($i=0; $i < mysqli_num_rows($result); $i++)
						{	
							echo '<tr style="text-align:center;">';
							$result->data_seek($i);
			    				$row = $result->fetch_row();
			    				
			    				//ID
							echo '<td>'.sprintf('%05d', $row[0]).'</td>';
							
							//Reference
							echo '<td>'.$row[1].'</td>';
							
							//Date
							echo '<td>'.date("M d, Y h:i A", strtotime($row[4])).'</td>';
							
							//Source
							if($row[6] == 1) echo "<td>".$row[7]."</td>";
							else echo '<td>'.ucwords(strtolower($row[2])).'</td>';
							
							//Destination
							echo '<td>'.ucwords(strtolower($row[3])).'</td>';
							
							//Type
							if($row[6] == 1) echo "<td>Supplier Receive [+]</td>";
							else if($row[6] == 2) echo "<td>Borrow [-]</td>";
							else if($row[6] == 3) echo "<td>Sell [-]</td>";
							else if($row[6] == 4) echo "<td>Borrowed Return [+]</td>";
							else if($row[6] == 5) echo "<td>Repair Request [+]</td>";
							else if($row[6] == 6) echo "<td>Repair Release [-]</td>";
							
							//Amount
							echo '<td>'.number_format($row[5],2).'</td>';
							
							//Actions
							echo '<td><a href="receipt.php?id='.$row[0].'&action=view"><i class="fa fa-file-o" aria-hidden="true"></i> View</a></td>';
							
							echo '</tr>';
						}
					?>
					</tbody>
				</table>
			</div>
			
			<hr style="border-top: 2px solid black;">
			
			<h4>Payments</h4>
			<div class="table-wrapper">
		        	<table id="patients2" class="display responsive nowrap selecttable" cellspacing="0" width="100%">
					<?php
						require_once("../resource/database/hive.php");
						
						$result = mysqli_query($mysqli, "SELECT `ID`, `Type`, `Date`, `Amount`, `CBank`, `CDate`, (SELECT Name FROM entity WHERE entity.ID = `Client`), `Mark` FROM payment WHERE Mark = 1 AND Client = $id");
					
						echo '<thead>';
						echo '<tr style="text-align:center;font-weight:bold;">';
						echo '<th style="width:8%;text-align:center;" data-priority="1">PID</th>';
						echo '<th style="width:10%;text-align:center;" data-priority="2">Type</th>';
						echo '<th style="width:10%;text-align:center;" data-priority="3">Date</th>';
						echo '<th style="width:10%;text-align:center;" data-priority="4">Amount</th>';
						echo '<th style="width:22%;text-align:center;" data-priority="5">Check Bank</th>';
						echo '<th style="width:10%;text-align:center;" data-priority="6">Check Date</th>';
						echo '</tr></thead><tbody>';
					
						for($i=0; $i < mysqli_num_rows($result); $i++)
						{	
							echo '<tr style="text-align:center;">';
							$result->data_seek($i);
			    				$row = $result->fetch_row();
			    				
			    				//ID
							echo '<td>'.sprintf('%05d', $row[0]).'</td>';
							
							//Type
							if($row[1] == 1) echo "<td>Cash</td>";
							else if($row[1] == 2) echo "<td>Check</td>";
							
							//Date
							echo '<td>'.date("M d, Y h:i A", strtotime($row[2])).'</td>';
							
							//Amount
							echo '<td>'.number_format($row[3],2).'</td>';
							
							//CBank
							echo '<td>'.ucwords(strtolower($row[4])).'</td>';
							
							//Date
							if($row[5] != "0000-00-00 00:00:00") echo '<td>'.date("F d, Y", strtotime($row[5])).'</td>';
							else echo "<td>&nbsp</td>";
														
							echo '</tr>';
						}
					?>
					</tbody>
				</table>
			</div>
			
			<hr style="border-top: 2px solid black;">
			
			<h4>Borrowed Items</h4>
			<div class="table-wrapper">
		        	<table id="patients3" class="display responsive nowrap selecttable" cellspacing="0" width="100%">
					<?php
						$result = mysqli_query($mysqli, "SELECT `ID`, `Name`, `Category`, `Subcategory`, `Description`, `Weight`, `Buy`, `Sell`, `Picture`, `Mark` FROM `inventory` WHERE `Mark` = '4'");
						
						echo '<thead>';
						echo '<tr style="text-align:center;font-weight:bold;">';
						echo '<th style="width:8%;text-align:center;" data-priority="1">i-ID</th>';
						echo '<th style="width:10%;text-align:center;" data-priority="2">Picture</th>';
						echo '<th style="width:10%;text-align:center;" data-priority="3">Name</th>';
						echo '<th style="width:10%;text-align:center;" data-priority="4">Category</th>';
						echo '<th style="width:22%;text-align:center;" data-priority="5">Description</th>';
						echo '<th style="width:10%;text-align:center;" data-priority="6">Weight</th>';
						echo '<th style="width:10%;text-align:center;" data-priority="7">View</th>';
						echo '</tr></thead><tbody>';
					
						for($i=0; $i < mysqli_num_rows($result); $i++)
						{	
							$result->data_seek($i);
			    				$row = $result->fetch_row();
			    				
			    				$mine = mysqli_query($mysqli, "SELECT `Destination` FROM transaction, particular WHERE Inventory = $row[0] AND transaction.Mark = 1 AND particular.Mark = 1 AND Transaction = transaction.ID ORDER BY Transaction DESC LIMIT 1");
			    				$mine = mysqli_fetch_row($mine);
			    				
			    				if($mine[0] == $id)
			    				{
			    					echo '<tr style="text-align:center;">';
			    					
				    				//ID
								echo '<td>'.sprintf('%05d', $row[0]).'</td>';
								
								//Picture
								echo '<td><img style="width:30px;" src="data:image/jpeg;base64,'.base64_encode( $row[8] ).'"/></td>';
								
								//Name
								echo '<td>'.ucwords(strtolower($row[1])).'</td>';
								
								//Category
								echo '<td>'.ucwords(strtolower($row[2])).'</td>';
								
								//Description
								echo '<td>'.$row[4].'</td>';
								
								//Weight
								echo '<td>'.$row[5].'</td>';
								
								//Actions
							echo '<td><a href="inventory_history.php?id='.$row[0].'&action=view" target="_blank"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> History</a>';
								
								echo '</tr>';
							}
						}
					?>
					</tbody>
				</table>
			</div>

		</div>			
	</body>
</html>