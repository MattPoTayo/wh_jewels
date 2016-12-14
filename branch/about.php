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
                				System Version: 1.0.0<br>
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
                			<div class="panel-heading"><i class="fa fa-bell fa-fw"></i>   Contact Tangent</div>                       				 
                			<div class="panel-body">
                				For any questions or clarification, please contact us through the following channel:<br><br>
						Facebook: www.facebook.com/TangentPH (We typically reply within minutes)<br>
						Twitter: @Tangent_PH<br>
						Text Message: +63 918 521 8388<br>
						Email: connect@tangentPH.com<br>
						Online Ticket: http://www.tangentph.com/#contact
                			</div>
                		</div>
                	</div>
                	
                	<div class="col-lg-12">
                		<div class="panel panel-default">
                			<div class="panel-heading"><i class="fa fa-bell fa-fw"></i>   Terms and Condition</div>                       				 
                			<div class="panel-body">
                				This system was developed by <strong>Tangent PH</strong> operating under the legal name, Tangent Software Solutions. No part of the system can be replicated nor copied without proper permission from Tangent PH.<br><br>
                				Please read our terms, policies and condition found <a href="http://www.tangentph.com/terms.html" target="_blank">here</a>.
                			</div>
                		</div>
                	</div>
                </div>
		
	</body>
</html>