<?php 
	require_once("verify_access.php");
	require_once("../resource/database/hive.php");
	$conversion = 49;
	
	if(isset($_GET['id']))
	{
		$userID	= $_SESSION['id'];
		$userBranch = 100;
		$creation = $_GET['id'];
		
		//Check Receipt Integrity
		$check = mysqli_query($mysqli, "SELECT * FROM `transaction` WHERE `ID` = '$creation' AND `Mark` >= 0");
		
		if($check AND mysqli_num_rows($check) == 1)
		{
			$transaction = $check->fetch_assoc();
			$creation = $transaction['ID'];
			$creatorID = $transaction['Creator'];
			
			//Creator
			$creator = mysqli_query($mysqli, "SELECT Name FROM entity WHERE ID = '$creatorID'");
			$creator = mysqli_fetch_row($creator);
			
			//Destination Details
			$destination_ID = $transaction['Destination'];
			$destination_query = mysqli_query($mysqli, "SELECT * FROM `entity` WHERE `ID` = '$destination_ID'");
			$destination = $destination_query->fetch_assoc();
			
			//Source Details
			$source_ID = $transaction['Source'];
			$source_query = mysqli_query($mysqli, "SELECT * FROM `entity` WHERE `ID` = '$source_ID'");
			$source = $source_query->fetch_assoc();
			
			//Branch Details
			$branch_query = mysqli_query($mysqli, "SELECT * FROM `entity` WHERE `ID` = '$userBranch'");
			$branch = $branch_query->fetch_assoc();
		}
		else
		{
			header("location:index.php");
		}
	}
	else
	{
		header("location:index.php");
	}
	ob_start();
?>
<style>
	@media print{ #cmdPrint{ display: none;} }
	@media screen {}	
	@media print,screen 
	{
		body 
		{ 
			margin:0; 
			padding: 0.25in;
			font-family: Helvetica,Arial,Tahoma, Courier and Andale Mono,Serif; /*Monospace fonts = Arial,Tahoma, Courier and Andale Mono.*/
			font-size: 5pt;	
		}
		@page 
		{
			margin: 0;
		}
		.a4 
		{
			text-align: left;
			padding: 0.15in;/**/
			margin-left:1in;
			height: 11in;
			width : 8.5in; /* 8.5in; */
		}	
	}
	
	table.bordered
	{
		border-collapse:collapse;
		border: 1px solid black;
	}
	
	table.bordered td, th
	{
	       border: 1px solid gray;
	}
	
	.footer
	{
		font-size:14px;
		text-align:left;
		color:gray;
	}
</style>

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title>Receipt | WH Jewels</title>
	<link rel="shortcut icon" type="image/x-icon" href="../resource/images/favicon.png" />
</head>
<body>
	<main>
		<div style="width:100%;">
			<div style="width:90%;margin-left:auto;margin-right:auto;">
				<table style="width:100%;margin-left:auto;margin-right:auto;">
					<tr>
						<td>
							<p style="display:inline;font-size:110%;font-weight:bold;"><img src="/resource/images/wh_jewels_icon.png" alt="Banner Image"/>&#9830&nbsp<?php echo $branch['Name']; ?></p><br>
							<p style="display:inline;font-size:70%;">Phone: <?php echo $branch['Phone']; ?></p><br>
							<p style="display:inline;font-size:70%;">Instagram: <?php echo $branch['Username']; ?></p><br>
						</td>
						<td style='text-align:right;'>
							<p style='display:inline;font-size:20px;font-weight:bold;'>
								<?php
									$type = mysqli_query($mysqli, "SELECT Type FROM particular WHERE particular.Mark = 1 AND Transaction = '$creation' LIMIT 1");
									$type = mysqli_fetch_row($type);
									
									if($type[0] == 1) echo "Receiving Voucher";
									else if($type[0] == 2) echo "Borrowing Slip";
									else if($type[0] == 3) echo "Trust Agreement Receipt";
									else if($type[0] == 4) echo "Borrowed Items Return";
									else if($type[0] == 5) echo "Repair Request";
									else if($type[0] == 6) echo "Release Form";
									else "Transaction Record";
								?>
							</p><br>
							<p style='font-size:22px;display:inline;font-family:courier;'>Transaction No. <font style='color:red'><?php echo sprintf('%05d', $transaction['ID']); ?></font></p>
						</td>
					</tr>
				</table><br>
			
				<?php if($type[0] == 1) { ?>
				
				<table style="font-size:12px;margin-bottom:10px;width:100%" border=0 cellspacing=0>
					<tr><td style="width:25%">Date:</td><td style="border-bottom:solid 1px;"><?php echo date("F d, Y h:i A", strtotime($transaction['Date'])); ?></td><td style="width:40%">&nbsp</td></tr>
					<tr><td>Receipt No.:</td><td style="border-bottom:solid 1px;"><?php echo $transaction['Reference']; ?><td>&nbsp</td></tr>
					<tr><td>Supplier:</td><td style="border-bottom:solid 1px;"><?php echo $transaction['Comment']; ?></td><td>&nbsp</td></tr>
				</table>
				
				<?php } else if($type[0] == 2 or $type[0] == 3) { ?>
				
				<table style="font-size:12px;margin-bottom:10px;width:100%" border=0 cellspacing=0>
					<tr><td style="width:25%">Date:</td><td style="border-bottom:solid 1px;"><?php echo date("F d, Y h:i A", strtotime($transaction['Date'])); ?></td><td style="width:40%">&nbsp</td></tr>
					<tr><td>Trust Receipt Agreement No.:</td><td style="border-bottom:solid 1px;"><?php echo $transaction['Reference']; ?><td>&nbsp</td></tr>
					<tr><td>Client ID:</td><td style="border-bottom:solid 1px;"><?php echo sprintf('%05d', $destination['ID']); ?></td><td>&nbsp</td></tr>
					<tr><td>Client:</td><td style="border-bottom:solid 1px;"><?php echo $destination['Name']; ?></td><td>&nbsp</td></tr>
					<tr><td>Phone:</td><td style="border-bottom:solid 1px;"><?php echo $destination['Phone']; ?></td><td>&nbsp</td></tr>
					<tr><td>Address:</td><td style="border-bottom:solid 1px;"><?php echo $destination['Address']; ?></td><td>&nbsp</td></tr>
				</table>
				
				<?php } else if($type[0] == 4) { ?>
				
				<table style="font-size:12px;margin-bottom:10px;width:100%" border=0 cellspacing=0>
					<tr><td style="width:25%">Date:</td><td style="border-bottom:solid 1px;"><?php echo date("F d, Y h:i A", strtotime($transaction['Date'])); ?></td><td style="width:40%">&nbsp</td></tr>
					<tr><td>Trust Receipt Agreement No.:</td><td style="border-bottom:solid 1px;"><?php echo $transaction['Reference']; ?><td>&nbsp</td></tr>
					<tr><td>Client ID:</td><td style="border-bottom:solid 1px;"><?php echo sprintf('%05d', $source['ID']); ?></td><td>&nbsp</td></tr>
					<tr><td>Client:</td><td style="border-bottom:solid 1px;"><?php echo $source['Name']; ?></td><td>&nbsp</td></tr>
					<tr><td>Phone:</td><td style="border-bottom:solid 1px;"><?php echo $source['Phone']; ?></td><td>&nbsp</td></tr>
					<tr><td>Address:</td><td style="border-bottom:solid 1px;"><?php echo $source['Address']; ?></td><td>&nbsp</td></tr>
				</table>
				
				<?php } else { ?>
			
				<table style="font-size:12px;margin-bottom:10px;width:100%" border=0 cellspacing=0>
					<tr>
						<td style="width:25%">Date:</td><td style="width:25%;border-bottom:solid 1px;"><?php echo date("F d, Y h:i A", strtotime($transaction['Date'])); ?></td>
						<td style="width:2%;">&nbsp</td>
						<td style="width:23%;">Reference/SI Number:</td><td style="width:25%;border-bottom:solid 1px;"><?php echo $transaction['Reference']; ?></td>
					</tr>
					<tr>
						<td style="width:25%">Source:</td><td style="width:25%;border-bottom:solid 1px;"><?php if($source['ID'] != 200) { echo "[".sprintf('%05d', $source['ID'])."] ".ucwords(strtolower($source['Name'])); } else echo "Supplier: ".$transaction['Comment']; ?></td>
						<td style="width:2%;">&nbsp</td>
						<td style="width:23%">Destination:</td><td style="width:25%;border-bottom:solid 1px;"><?php echo "[".sprintf('%05d', $destination['ID'])."] ".ucwords(strtolower($destination['Name'])); ?></td>
					</tr>
					<tr>
						<td style="width:25%">Source Contact No.</td><td style="width:25%;border-bottom:solid 1px;"><?php echo $source['Phone']; ?></td>
						<td style="width:2%;">&nbsp</td>
						<td style="width:23%">Destination Contact No.</td><td style="width:25%;border-bottom:solid 1px;"><?php echo $destination['Phone']; ?></td>
					</tr>
					<tr>
						<td style="width:25%">Source Address</td><td style="width:25%;border-bottom:solid 1px;"><?php echo $source['Address']; ?></td>
						<td style="width:2%;">&nbsp</td>
						<td style="width:23%">Destination Address</td><td style="width:25%;border-bottom:solid 1px;"><?php echo $destination['Address']; ?></td>
					</tr>				
				</table>
				
				<?php } ?>
				
				<table style="margin-top:10px;font-size:12px;margin-bottom:10px;width:100%" class="bordered">
					<tr style="text-align:center;font-weight:bold;background:#212121;color:white">
						<td style="width:10%">Picture</td>
						<td style="width:15%">iID</td>
						<td style="width:15%">Name</td>
						<td style="width:15%">Category</td>
						<td style="width:25%">Description</td>
						<td style="width:10%">Weight</td>
						<td style="width:10%"><?php if($type[0] == 1) echo "Amount in $"; else echo "Amount in PHP"; ?></td>
					</tr>
					<?php		 
						$result = mysqli_query($mysqli, "SELECT inventory.ID, Name, Category, Description, Weight, Picture, Amount FROM particular, inventory WHERE particular.Transaction = '$creation' AND inventory.ID = particular.Inventory AND particular.Mark > 0");
							 
						for($i=0, $total=0; $i < mysqli_num_rows($result); $i++)
						{	
							echo '<tr style="text-align:center;">';
							$result->data_seek($i);
			    				$row = $result->fetch_row();
			    				
			    				echo "<tr style='text-align:center'>";
			    				
							//Picture
							echo '<td><img style="width:70px;" src="data:image/jpeg;base64,'.base64_encode( $row[5] ).'"/></td>';
			    				
			    				//ID
							echo '<td><img style="margin-top:10px" alt="testing" src="../resource/tools/barcodes.php?text='.sprintf('%05d', $row[0]).'&print=true&size=40" /></td>';
							
							//Name
							echo '<td>'.$row[1].'</td>';
							
							//Category
							echo '<td>'.ucwords(strtolower($row[2])).'</td>';
							
							//Description
							echo '<td>'.$row[3].'</td>';
							
							//Weight
							echo '<td>'.$row[4].' g</td>';
							
							//Amount
							if($type[0] == 1) { echo '<td>$ '.number_format($row[6],2).'<br>(&#8369 '.number_format(($row[6]*$conversion),2).')</td>'; }
							else echo '<td>'.number_format($row[6],2).'</td>';
							$total += $row[6];
							
							echo '</tr>';
						}
						if(mysqli_num_rows($result) == 0)
						{
							echo "<tr><td colspan=7 style='text-align:center;'>Cart Empty!</td></tr>";
						}
						else
						{
							echo "<tr><td colspan=6 style='text-align:right'>No. of Items &nbsp</td><td style='text-align:center;font-weight:bold'>".$i."</td></tr>";
							if($type[0] == 1) echo "<tr><td colspan=6 style='text-align:right'>Total Bill&nbsp</td><td style='text-align:center;font-weight:bold'>$ ".number_format($total,2)."<br>(&#8369 ".number_format(($total*$conversion),2).")</td></tr>";
							else echo "<tr><td colspan=6 style='text-align:right'>Total Bill&nbsp</td><td style='text-align:center;font-weight:bold'>".number_format($total,2)."</td></tr>";
						}
					?>
				</table>
				
				<?php if($type[0] > 1) { ?>
				<table style="margin-top:10px;font-size:12px;margin-bottom:10px;width:100%" class="bordered">
					<tr style="text-align:center;font-weight:bold;background:#212121;color:white">
						<td style="width:10%">PID</td>
						<td style="width:10%">Type</td>
						<td style="width:25%">Date</td>
						<td style="width:20%">Check Bank</td>
						<td style="width:25%">Check Date</td>
						<td style="width:10%">Amount in PHP</td>
					</tr>
					<?php
						$result = mysqli_query($mysqli, "SELECT `ID`, `Type`, `Date`, `Amount`, `CBank`, `CDate`, (SELECT Name FROM entity WHERE entity.ID = `Client`), `Mark` FROM payment WHERE Mark = 1 AND SID = '$creation'");
					
						for($i=0, $totalp=0; $i < mysqli_num_rows($result); $i++)
						{	
							$result->data_seek($i);
		    					$row = $result->fetch_row();
		    					
		    					echo '<tr style="text-align:center;">';
		    					echo '<td>'.sprintf('%05d', $row[0]).'</td>';
		    					
							//Type
							if($row[1] == 1) echo "<td>Cash</td>";
							else if($row[1] == 2) echo "<td>Check</td>";
							
							//Date
							echo '<td>'.date("M d, Y h:i A", strtotime($row[2])).'</td>';
							
							//CBank
							echo '<td>'.$row[4].'</td>'; 
							
							//Date
							if($row[5] != "0000-00-00 00:00:00") echo '<td>'.date("F d, Y", strtotime($row[5])).'</td>';
							else echo "<td>&nbsp</td>";
							
							//Amount
							echo '<td>'.number_format($row[3],2).'</td>'; $totalp += $row[3];
							
							echo '</tr>';
						}
						if(mysqli_num_rows($result) == 0)
						{
							echo "<tr><td colspan=6 style='text-align:center;'>No Payments.</td></tr>";
						}
						
						echo "<tr><td colspan=5 style='text-align:right;'>Total Payments</td><td style='text-align:center;'> ".number_format($totalp,2)."</td></tr>";
						echo "<tr><td colspan=5 style='text-align:right;'>Total Bill</td><td style='text-align:center;'> ".number_format($total,2)."</td></tr>";
						echo "<tr><td colspan=5 style='text-align:right;'>Total Balance</td><td style='text-align:center;'>-".number_format(($total-$totalp),2)."</td></tr>";
					?>
				</table>
				<?php } ?>
				
				<table style="margin-top:10px;font-size:12px;margin-bottom:10px;width:100%">
					<tr>
						<td style="width:15%;vertical-align:bottom;text-align:center;margin-top:10px">
							<hr>
							<strong>Prepared By:</strong><br>
							<?php echo $creator[0]; ?>
						</td>
						<td style="width:2%;">&nbsp</td>
						<td style="width:15%;vertical-align:bottom;text-align:center;margin-top:10px">
							<hr>
							<?php 
								if($type[0] == 1) echo "<strong>Confirmed By:</strong><br>".$transaction['Comment']; 
								else if($type[0] == 2 or $type[0] == 3 or $type[0] == 4) echo "<strong>Received By:</strong><br>".$destination['Name'];
								else if($type[0] == 5) echo "<strong>Confirmed By:</strong><br>".$source['Name']; 
								else if($type[0] == 6) echo "<strong>Received By:</strong><br>".$destination['Name']; 
							?>
						</td>
						<?php echo '<td style="width"15%"><img style="margin-top:10px" alt="testing" src="../resource/tools/barcodes.php?text='.sprintf('%05d', $creation).'&print=true&size=45" /></td>'; ?>
						<td style="width:58%;vertical-align:top;margin-top:10px;border: 1px solid gray;padding:5px;">
							<strong>Remarks:</strong><br>
							<?php if($type[0] != 1) echo $transaction['Comment']; else echo "None."; ?>
						</td>
					</tr>
				</table>
				
				<p style="font-size:10px"><a href="transactions.php">WH Jewels &reg WH Jewels Inventory Management System</a></p>
			</div>
		</div>
	</main>
</body>
			
</html>