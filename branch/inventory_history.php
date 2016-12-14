<?php
	ob_start();
	session_start();
	require_once("verify_access.php");
	$page_name = "History";
	$page_type = 4;
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
	            })
	        </script>		
		
		<div>
			<div class="row" style="margin-left:1px;">
				<h3>Inventory History</h3>
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
						
						$result3 = mysqli_query($mysqli, "SELECT * FROM `inventory` WHERE `ID` = '$id'");
						$result3->data_seek(0);
					    	$row3 = $result3->fetch_row();
					?>
					<tr><td style="width:20%">Name</td><td style="width:30%"><?php echo $row3[1]; ?></td><td rowspan=6 style="text-align:center"><?php echo '<img style="width:30%;" src="data:image/jpeg;base64,'.base64_encode( $row3[8] ).'"/>'; ?></td></tr>
					<tr><td style="width:20%">Category</td><td style="width:30%"><?php echo $row3[2]; ?></td></tr>
					<tr><td style="width:20%">Description</td><td style="width:30%"><?php echo $row3[4]; ?></td></tr>
					<tr><td style="width:20%">Weight</td><td style="width:30%"><?php echo $row3[5]; ?> g</td></tr>
					<tr><td style="width:20%">Supplier Price</td><td style="width:30%">$ <?php echo number_format($row3[6],2); ?></td></tr>
					<tr>
						<td style="width:20%">Status</td><td style="width:30%">
						<?php
							if($row3[9] == 1) echo "Available";
							else if($row3[9] == 2) echo "On Transaction";
							else if($row3[9] == 3) echo "Sold";
							else if($row3[9] == 4) echo "Borrowed";
							else if($row3[9] == 5) echo "Repairing";
							else if($row3[9] == 6) echo "Released";
						?>
						</td>
					</tr>
				
			</table>
			
			<div class="table-wrapper">
		        	<table id="patients" class="display responsive nowrap selecttable" cellspacing="0" width="100%">
					<?php
						$result = mysqli_query($mysqli, "SELECT Transaction FROM particular WHERE Mark = 1 AND Inventory = '$id'");
						
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
							
							$result2 = mysqli_query($mysqli, "SELECT `ID`, `Reference`, (SELECT Name FROM entity WHERE ID = `Source`),  (SELECT Name FROM entity WHERE ID =  `Destination`), `Date`, (SELECT SUM(Amount) FROM particular WHERE particular.Mark = 1 AND Transaction = transaction.ID), Type, Comment, transaction.Mark FROM `transaction` WHERE `ID` = '$row[0]'");
							$result2->data_seek(0);
			    				$row2 = $result2->fetch_row();
							
							//Reference
							echo '<td>'.$row2[1].'</td>';
							
							//Date
							echo '<td>'.date("M d, Y h:i A", strtotime($row2[4])).'</td>';
							
							//Source
							if($row2[6] == 1) echo "<td>".$row2[7]."</td>";
							else echo '<td>'.ucwords(strtolower($row2[2])).'</td>';
							
							//Destination
							echo '<td>'.ucwords(strtolower($row2[3])).'</td>';
							
							//Type
							if($row2[6] == 1) echo "<td>Supplier Receive [+]</td>";
							else if($row2[6] == 2) echo "<td>Borrow [-]</td>";
							else if($row2[6] == 3) echo "<td>Sell [-]</td>";
							else if($row2[6] == 4) echo "<td>Borrowed Return [+]</td>";
							else if($row2[6] == 5) echo "<td>Repair Request [+]</td>";
							else if($row2[6] == 6) echo "<td>Repair Release [-]</td>";
							
							//Amount
							if($row2[6] == 1) echo '<td>$ '.number_format($row2[5],2).'</td>';
							else echo '<td>&#8369 '.number_format($row2[5],2).'</td>';
							
							//Actions
							echo '<td><a href="receipt.php?id='.$row[0].'&action=view" target="_blank"><i class="fa fa-file-o" aria-hidden="true"></i> View</a></td>';
							
							echo '</tr>';
						}
					?>
					</tbody>
				</table>
			</div>
		</div>			
	</body>
</html>