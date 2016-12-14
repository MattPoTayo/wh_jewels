<?php
	$print = $_GET['id'];
	$desc = $_GET['desc'];
	$name = $_GET['name'];
	echo '<img style="width:80px;margin-top:-10px;" alt="testing" src="../resource/tools/barcodes.php?text='.sprintf('%05d', $print).'&print=true&size=20" /><br>
		<p style="margin-top:-5px;margin-left:0px;font-size:8px;font-family:calibri">'.$name.'<br>'.$desc.'</p>';
?>