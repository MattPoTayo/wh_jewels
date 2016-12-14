<?php
	ob_start();
	session_start();
	require_once("verify_access.php");
	$page_name = "Transactions";
	$page_type = 5;
?>
<html>
	<?php require_once("../resource/sections/branch_header.php"); ?>
	<body>
		<?php 
			require_once("../resource/sections/branch_banner.php"); 
			require_once("../resource/sections/branch_menu.php"); 
			
			if(isset($_GET['continue']))
			{
				$sid = $_GET['continue'];
				$ttype = $_GET['type'];
				
				if($ttype == 1)
				{
					$_SESSION['receive'] = $sid;
					ob_end_clean();
					header("location:t_receiving.php");
				}
				else if($ttype == 2)
				{
					$_SESSION['borrow'] = $sid;
					ob_end_clean();
					header("location:t_borrow.php");
				}
				else if($ttype == 3)
				{
					$_SESSION['sales'] = $sid;
					ob_end_clean();
					header("location:t_sales.php");
				}
				else if($ttype == 4)
				{
					$_SESSION['return'] = $sid;
					ob_end_clean();
					header("location:t_return.php");
				}
				else if($ttype == 5)
				{
					$_SESSION['repair'] = $sid;
					ob_end_clean();
					header("location:t_repair.php");
				}
				else if($ttype == 6)
				{
					$_SESSION['release'] = $sid;
					ob_end_clean();
					header("location:t_release.php");
				}
			}
			
			if(isset($_GET['reverse']))
			{
				$sid = $_GET['reverse'];
				$items = mysqli_query($mysqli, "SELECT `Inventory` FROM `particular` WHERE Mark = 1 AND particular.Transaction = '$sid'");
				
			    	for($ok=true, $i=0; $i < mysqli_num_rows($items); $i++)
				{
					$items->data_seek($i);
			    		$row = $items->fetch_row();
			    		
			    		$check = mysqli_query($mysqli, "SELECT `Transaction` FROM `particular`, `transaction` WHERE `Transaction` = transaction.ID AND `Inventory` = '$row[0]' AND transaction.`Mark` = 1 ORDER BY Transaction DESC LIMIT 1");
			    		$check = mysqli_fetch_row($check);
			    		
			    		if($check[0] != $sid) $ok = false;
				}
				
				if($ok)
				{
					$reverse_connection = mysqli_query($mysqli, "UPDATE particular, inventory SET particular.Mark = 2, transaction.Mark = 2 WHERE Transaction = '$sid' AND inventory.Inventory = inventory.ID");
					$reverse_transaction = mysqli_query($mysqli, "UPDATE transaction SET Mark = 2 WHERE ID = '$sid'");
					
					if($reverse_connection and $reverse_transaction)
					$_SESSION['success'] = "Successfully reversed transaction.";
				}
				else
				{
					$_SESSION['fail'] = "One of the items in the receipt is already included in a newer receipt. Please reverse that receipt first before reversing this one.";
				}
				
			}
		?>	
		
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.11/css/jquery.dataTables.min.css"/>
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.0.2/css/responsive.dataTables.min.css"/>
		<script type="text/javascript" src="https://cdn.datatables.net/1.10.11/js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.0.2/js/dataTables.responsive.min.js"></script>
				
		<script type="text/javascript" charset="utf-8">
	            $(document).ready(function(){
	                $('#patients').dataTable({
			        "order": [[ 0, "desc" ]]
			    });
	            })
	        </script>		
		
		<div>
			<div class="row" style="margin-left:1px;">
				<h3>Transactions</h3>
				<div class="messages">
					<?php
						if(isset($_SESSION['success'])) { echo "<p class='fsuccess'>".$_SESSION['success']."</p>"; unset($_SESSION['success']); }
						else if(isset($_SESSION['fail'])) { echo "<p class='ffail'>".$_SESSION['fail']."</p>"; unset($_SESSION['fail']); }
					?>
				</div>
			</div>
			<div class="table-wrapper">
		        	<table id="patients" class="display responsive nowrap selecttable" cellspacing="0" width="100%">
					<?php
						require_once("../resource/database/hive.php");
						
						$result = mysqli_query($mysqli, "SELECT `ID`, `Reference`, (SELECT Name FROM entity WHERE ID = `Source`),  (SELECT Name FROM entity WHERE ID =  `Destination`), `Date`, (SELECT SUM(Amount) FROM particular WHERE particular.Mark = 1 AND Transaction = transaction.ID), Type, Comment, transaction.Mark FROM `transaction` WHERE `Mark` >= 1");
					
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
							if($row[6] == 1) echo '<td>$ '.number_format($row[5],2).'</td>';
							else echo '<td>&#8369 '.number_format($row[5],2).'</td>';
							
							//Actions
							if($row[8] == 1) echo '<td><a href="receipt.php?id='.$row[0].'&action=view" target="_blank"><i class="fa fa-file-o" aria-hidden="true"></i> View</a>&nbsp|&nbsp<a href="transactions.php?reverse='.$row[0].'&action=reverse"><i class="fa fa-undo" aria-hidden="true"></i> Reverse</a></td>';
							else if($row[8] == 2) echo '<td><a href="transactions.php?continue='.$row[0].'&type='.$row[6].'"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Continue</a></td>';
							
							echo '</tr>';
						}
					?>
					</tbody>
				</table>
			</div>
		</div>			
	</body>
</html>