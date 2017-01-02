<?php
	ob_start();
	session_start();
	require_once("verify_access.php");
	require_once("../resource/database/hive.php");
	$page_name = "Contact Tangent";
	$page_type = 8;
?>
<html>
	<?php require_once("../resource/sections/branch_header.php"); ?>
	<link href="../resource/graphics/css/sb-admin-2.css" rel="stylesheet">
	<body>
		<?php require_once("../resource/sections/branch_banner.php"); ?>
		<?php require_once("../resource/sections/branch_menu.php"); ?>
		
		
		<div class="row" style="margin-top:10px;">
			<?php
				if(isset($_SESSION['success'])) { echo "<p class='fsuccess'><img src='../graphics/images/active.png' style='width:10px;'/> ".$_SESSION['success']."</p>"; unset($_SESSION['success']); }
				else if(isset($_SESSION['error'])) { echo "<p class='ffail'><img src='../graphics/images/delete.png' style='width:10px;'/> ".$_SESSION['error']."</p>"; unset($_SESSION['error']); }
			?>
			<div class="col-lg-4">
                		<div class="panel panel-default">
                			<div class="panel-heading"><i class="fa fa-bell fa-fw"></i>   About WH Jewels Inventory System</div>                       				 
                			<div class="panel-body">
                				System Name: WH Jewels 1.0<br>
                				System Version: 1.1.1<br>
                				Date of Launch: N.A.<br>
                				Last Day of Coding: November 4, 2016<br>
                				Hosting Plan: Basic X<br>
                				Business Analyst/Team Leader: Engr. Johnelson Tan<br> 
                				Team: Team Beta<br>
                				Barcode System: Code128<br>
                				Reset Database: <a href="reset_database.php">Reset</a><br><br>
                			</div>
                		</div>
                	</div>
        		<div class="col-lg-8">
                		<div class="panel panel-default">
                			<div class="panel-heading"><i class="fa fa-bell fa-fw"></i>   Contact Support</div>                       				 
                			<div class="panel-body">
                				For any questions or clarification, please contact us through the following channel:<br><br>
						Text/Viber Message: +63 905 880 0741<br>
						Email: matthew.tizon@gmail.com<br>
                                                Skype: matt.nelsoft@gmail.com
                			</div>
                		</div>
                	</div>
                	
                	<div class="col-lg-12">
                		<div class="panel panel-default">
                			<div class="panel-heading"><i class="fa fa-bell fa-fw"></i>   Terms and Condition</div>                       				 
                			<div class="panel-body">
                				This system was first implemented by Tangent PH, later on tranfer all controls and privilages to client company <strong>WH JEWELS</strong>. All upcoming developments will be automatically credited to this company with proper turnover protocols from first provider.<br><br>
                				Development can be monitored via git repository hosted <a href="https://github.com/MattPoTayo/wh_jewels" target="_blank">here</a>.
                			</div>
                		</div>
                	</div>
                </div>
		
	</body>
</html>