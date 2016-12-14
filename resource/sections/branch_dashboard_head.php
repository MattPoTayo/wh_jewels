<?php
	$label1 = "Available Inventory";
	$label2 = "Borrowed Inventory";
	$label3 = "Total Value of Available Inventory";
	$label4 = "Total Income This Month";
	
	$value1 = mysqli_query($mysqli, "SELECT COUNT(*) FROM `inventory` WHERE `Mark` = 1");
	$value1 = mysqli_fetch_row($value1); 
	
	$value2 = mysqli_query($mysqli, "SELECT COUNT(*) FROM `inventory` WHERE `Mark` = 4");
	$value2 = mysqli_fetch_row($value2);
	
	$value3 = mysqli_query($mysqli, "SELECT SUM(`Buy`) FROM `inventory` WHERE `Mark` = 1");
	$value3 = mysqli_fetch_row($value3); 
	
	$value4 = mysqli_query($mysqli, "SELECT SUM(Amount) FROM particular, transaction WHERE particular.Type > 1 AND particular.Mark = 1 AND transaction.Mark = 1 AND Transaction = transaction.ID AND MONTH(transaction.Date) = MONTH(CURRENT_DATE())");
	$value4 = mysqli_fetch_row($value4);
	
	$link1 = "inventory.php";
	$link2 = "inventory.php?type=4";
	$link3 = "transactions.php";
	$link4 = "transactions.php";
?>
<div class="row" style="margin-top:20px">
	<div class="col-lg-3 col-md-6">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<div class="row">
					<div class="col-xs-3">
						<i class="fa fa-tags fa-4x"></i>
					</div>
					<div class="col-xs-9 text-right">
						<div class="huge"><? echo $value1[0]; ?></div>
						<div><?php echo $label1; ?></div>
					</div>
				</div>
			</div>
			  <a href=<?php echo '"'.$link1.'"'; ?>>
				<div class="panel-footer">
					<span class="pull-left">New Transaction</span>
					<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
					<div class="clearfix"></div>
				</div>
			</a>
		</div>
	</div>
	<div class="col-lg-3 col-md-6">
		<div class="panel panel-green">
			<div class="panel-heading">
				<div class="row">
					<div class="col-xs-3">
						<i class="fa fa-credit-card fa-4x"></i>
					</div>
					<div class="col-xs-9 text-right">
						<div class="huge"><? echo $value2[0]; ?></div>
						<div><?php echo $label2; ?></div>
					</div>
				</div>
			</div>
			 <a href=<?php echo '"'.$link2.'"'; ?>>
				<div class="panel-footer">
					<span class="pull-left">View Details</span>
					<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
					<div class="clearfix"></div>
				</div>
			</a>
		</div>
	</div>
	<div class="col-lg-3 col-md-6">
		<div class="panel panel-red">
			<div class="panel-heading">
				<div class="row">
					<div class="col-xs-3">
						<i class="fa fa-check-square-o fa-4x" aria-hidden="true"></i>
					</div>
					<div class="col-xs-9 text-right">
						<div class="huge">$ <? echo number_format($value3[0],2); ?></div>
						<div><?php echo $label3; ?></div>
					</div>
				</div>
			</div>
			 <a href=<?php echo '"'.$link3.'"'; ?>>
				<div class="panel-footer">
					<span class="pull-left">View Details</span>
					<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
					<div class="clearfix"></div>
				</div>
			</a>
		</div>
	</div>
	<div class="col-lg-3 col-md-6">
		<div class="panel panel-yellow">
			<div class="panel-heading">
				<div class="row">
					<div class="col-xs-3">
						<i class="fa fa-money fa-4x"></i>
					</div>
					<div class="col-xs-9 text-right">
						<div class="huge">&#8369 <? echo number_format($value4[0],2); ?></div>
						<div><?php echo $label4; ?></div>
					</div>
				</div>
			</div>
			 <a href=<?php echo '"'.$link4.'"'; ?>>
				<div class="panel-footer">
					<span class="pull-left">View Details</span>
					<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
					<div class="clearfix"></div>
				</div>
			</a>
		</div>
	</div>
</div>