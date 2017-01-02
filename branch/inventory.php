<?php
	ob_start();
	session_start();
	require_once("verify_access.php");
	$page_name = "Inventory";
	$page_type = 4;
	
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
				<?php
					if(isset($_GET['type']) AND $_GET['type'] == 3)
						echo "<h3>Sold Inventory</h3>";
					else if (isset($_GET['type']) AND $_GET['type'] == 4)
						echo "<h3>Borrowed Inventory</h3>";
					else if (isset($_GET['type']) AND $_GET['type'] == 5)
						echo "<h3>Repair Requests</h3>";
					else if (isset($_GET['type']) AND $_GET['type'] == 6)
						echo "<h3>Repair Released</h3>";
					else
						echo "<h3>Available Inventory</h3>";
				?>
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
						$path = "value";
						if(isset($_GET['type']))
						{
							$type = $_GET['type'];
							$result = mysqli_query($mysqli, "SELECT `ID`, `Name`, `Category`, `Subcategory`, `Description`, `Weight`, `Buy`, `Sell`, `Mark` FROM `inventory` WHERE `Mark` = '$type'");
						}
						else
							$result = mysqli_query($mysqli, "SELECT `ID`, `Name`, `Category`, `Subcategory`, `Description`, `Weight`, `Buy`, `Sell`, `Mark` FROM `inventory` WHERE `Mark` = 1");
					
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
							echo '<tr style="text-align:center;">';
							$result->data_seek($i);
			    				$row = $result->fetch_row();
			    				
			    				//ID
							echo '<td>'.sprintf('%05d', $row[0]).'</td>';
							
							//Picture
							$path =  "../resource/images/inv_image/".sprintf('%d', $row[0]).".png";
							echo '<td><img style="width:30px;" src="'.$path.'"/></td>';
							
							//Name
							echo '<td>'.ucwords(strtolower($row[1])).'</td>';
							
							//Category
							echo '<td>'.ucwords(strtolower($row[2])).'</td>';
							
							//Description
							echo '<td>'.$row[4].'</td>';
							
							//Weight
							echo '<td>'.$row[5].' g</td>';
							
							//Actions
							echo '<td><a href="inventory_edit.php?inventory='.$row[0].'"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</a>&nbsp|&nbsp<a href="inventory_history.php?id='.$row[0].'" target="_blank"><i class="fa fa-history-o" aria-hidden="true"></i> History</a>&nbsp|&nbsp<a href="inventory_barcode.php?id='.$row[0].'&action=view&desc='.$row[4].'&name='.$row[1].'"><i class="fa fa-barcode" aria-hidden="true"></i> Print Barcode</a></td>';
							
							echo '</tr>';
						}
					?>
					</tbody>
				</table>
			</div>
		</div>			
	</body>
</html>