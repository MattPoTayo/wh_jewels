<div class="banner">
	<div class="col-md-6 banner-left"><h4><img src="/resource/images/wh_jewels_icon.png" alt="Banner Image"/>WH Jewels</h4></div>
	<div class="col-md-6 banner-right">
		<?php 
			require_once("../resource/database/hive.php");
			date_default_timezone_set ('Asia/Taipei');
			$date_today = date("Y-m-d");
			$time_now = date("Y-m-d H:i:s");
			
			$userID = $_SESSION['id'];
			
			$myname = mysqli_query($mysqli, "SELECT `Name` FROM `entity` WHERE `ID` = '$userID'");
			$myname = mysqli_fetch_row($myname);
			$userName = $myname[0];
			
			echo $date_today.' (<span id="clock"><script type="text/javascript">startclock();</script></span>) | Logged In as <a href=" profile.php?id='.$userID.'">'.$userName.'</a><br>'; 
		?>
		<a href="logout.php">Log Out</a>
	</div>
	<div style="clear:both"></div>
</div>