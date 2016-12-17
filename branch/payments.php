<?php
	ob_start();
	session_start();
	require_once("verify_access.php");
	$page_name = "Payments";
	$page_type = 7;
?>
<html>
	<?php require_once("../resource/sections/branch_header.php"); ?>
	<body>
		<?php 
			require_once("../resource/sections/branch_banner.php"); 
			require_once("../resource/sections/branch_menu.php"); 
			
			if(isset($_GET['delete']))
			{
				$payment = $_GET['delete'];
				$delete = mysqli_query($mysqli, "UPDATE payment SET Mark = -1 WHERE ID = '$payment'");
				
				if($delete) $_SESSION['success'] = "Successfully deleted payment.";
				else $_SESSION['fail'] = "Error occured. If error persists, please contact support.";
				
				ob_end_clean();
				header("location:payments.php");
			}
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
				<h3>Payments</h3>
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
						
						$result = mysqli_query($mysqli, "SELECT `ID`, `Type`, `Date`, `Amount`, `CBank`, `CDate`, (SELECT Name FROM entity WHERE entity.ID = `Client`), `Mark`, `SID` FROM payment WHERE Mark = 1");
					
						echo '<thead>';
						echo '<tr style="text-align:center;font-weight:bold;">';
						echo '<th style="width:8%;text-align:center;" data-priority="1">PID</th>';
						echo '<th style="width:10%;text-align:center;" data-priority="2">Type</th>';
						echo '<th style="width:10%;text-align:center;" data-priority="3">Date</th>';
						echo '<th style="width:10%;text-align:center;" data-priority="4">Amount</th>';
						echo '<th style="width:10%;text-align:center;" data-priority="4">Check Number</th>';
						echo '<th style="width:10%;text-align:center;" data-priority="5">Check Bank</th>';
						echo '<th style="width:10%;text-align:center;" data-priority="6">Check Date</th>';
						echo '<th style="width:10%;text-align:center;" data-priority="7">Client</th>';
						echo '<th style="width:10%;text-align:center;" data-priority="9">Receipt (SID)</th>';
						echo '<th style="width:10%;text-align:center;" data-priority="8">Action</th>';
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
							
							//CNum
							echo '<td>'.ucwords(strtolower($row[9])).'</td>';
							
							//CBank
							echo '<td>'.ucwords(strtolower($row[4])).'</td>';
							
							//Date
							if($row[5] != "0000-00-00 00:00:00") echo '<td>'.date("F d, Y", strtotime($row[5])).'</td>';
							else echo "<td>&nbsp</td>";
							
							//Client
							echo '<td>'.ucwords(strtolower($row[6])).'</td>';
							
							//Receipt
							echo '<td><a href="receipt.php?id='.$row[8].'" target="_blank">'.sprintf('%05d', $row[8]).'</a></td>';
							
							//Actions
							echo '<td><a href="payments.php?delete='.$row[0].'"><i class="fa fa-times-o" aria-hidden="true"></i> Delete</a></td>';
							
							echo '</tr>';
						}
					?>
					</tbody>
				</table>
			</div>
		</div>			
	</body>
</html>