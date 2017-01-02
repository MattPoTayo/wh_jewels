<?php
	ob_start();
	session_start();
	require_once("verify_access.php");
	require_once("../resource/database/hive.php");
	$page_name = "Home";
	$page_type = 1;
	
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
	
?>
<html>
	<?php require_once("../resource/sections/branch_header.php"); ?>
	<link href="../resource/graphics/css/sb-admin-2.css" rel="stylesheet">
	<body>
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
		
		<?php require_once("../resource/sections/branch_banner.php"); ?>
		<?php require_once("../resource/sections/branch_menu.php"); ?>
		<?php require_once("../resource/sections/branch_dashboard_head.php"); ?>
		
		<div class="messages">
			<?php
				if(isset($_SESSION['success'])) { echo "<p class='fsuccess'>".$_SESSION['success']."</p>"; unset($_SESSION['success']); }
				else if(isset($_SESSION['fail'])) { echo "<p class='ffail'>".$_SESSION['fail']."</p>"; unset($_SESSION['fail']); }
			?>
		</div>
		
		<div class="row" style="margin-top:10px;">
			<?php
				if(isset($_SESSION['success'])) { echo "<p class='fsuccess'><img src='../graphics/images/active.png' style='width:10px;'/> ".$_SESSION['success']."</p>"; unset($_SESSION['success']); }
				else if(isset($_SESSION['error'])) { echo "<p class='ffail'><img src='../graphics/images/delete.png' style='width:10px;'/> ".$_SESSION['error']."</p>"; unset($_SESSION['error']); }
			?>
        		<div class="col-lg-12">
                		<div class="panel panel-default">
                			<div class="panel-heading"><i class="fa fa-bullhorn fa-fw"></i>   Message from Developer</div>                       				 
                			<div class="panel-body">
                				<div>
                					<?php
                						$message = mysqli_query($mysqli, "SELECT Message FROM `messages` WHERE Type = 3 ORDER BY ID");
                						
                						for($i=0, $total=0; $i < mysqli_num_rows($message); $i++)
                						{	$message->data_seek($i);
	                						$row = $message->fetch_row();
	                						if($i > 0)
	                							echo"<br>";
	                						echo "<strong>".$row[0]."</strong>";
                						}
                					?>
                				</div>
                			</div>
                		</div>
                		
	                	<div class="panel panel-default">
	                			<div class="panel-heading"><i class="fa fa-history fa-fw"></i>   Latest Transactions</div>                       				 
	                			<div class="panel-body">
			                		<div class="table-wrapper">
					        		<table id="patients" class="display responsive nowrap selecttable" cellspacing="0" width="100%">
									<?php
										require_once("../resource/database/hive.php");
										$result = mysqli_query($mysqli, "SELECT `ID`, `Reference`, (SELECT Name FROM entity WHERE ID = `Source`),  (SELECT Name FROM entity WHERE ID =  `Destination`), `Date`, (SELECT SUM(Amount) FROM particular WHERE particular.Mark = 1 AND Transaction = transaction.ID), (SELECT Type FROM particular WHERE particular.Mark = 1 AND Transaction = transaction.ID LIMIT 1), Comment FROM `transaction` WHERE `Mark` = 1 LIMIT 50");
					
										echo '<thead>';
										echo '<tr style="text-align:center;font-weight:bold;">';
										echo '<th style="width:8%;text-align:center;" data-priority="1">SID</th>';
										echo '<th style="width:10%;text-align:center;" data-priority="2">Reference</th>';
										echo '<th style="width:10%;text-align:center;" data-priority="3">Date and Time</th>';
										echo '<th style="width:10%;text-align:center;" data-priority="4">Type</th>';
										echo '<th style="width:10%;text-align:center;" data-priority="5">Amount</th>';
										echo '<th style="width:10%;text-align:center;" data-priority="6">Action</th>';
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
											echo '<td><a href="receipt.php?id='.$row[0].'&action=view" target="_blank"><i class="fa fa-file-o" aria-hidden="true"></i> View</a></td>';
											
											echo '</tr>';
										}
									?>
									</tbody>
								</table>
							</div>
			                	</div>
			                </div>
	                	</div>
	                </div>
                </div>
		
	</body>
</html>