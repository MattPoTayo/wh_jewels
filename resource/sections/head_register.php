<head>
	<title><?php echo $page_name; ?> | WH Jewels &reg 1.0</title>
	
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Thinking Dentist 1.0 WH Jewels Dental Records Management System">
	<meta name="author" content="MattPoTayo">
	
	<!-- BOOTSTRAP -->
	<link href="resource/graphics/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="resource/graphics/font-awesome/css/font-awesome.min.css" rel="stylesheet">

	<!-- SEARCHABLE DROP DOWN -->
  	<link href="resource/tools/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">
	
	<!-- myCSS -->
  	<link href="resource/graphics/css/thinkingdentist_register.css" rel="stylesheet">
  	
  	<!-- TOOLBAR FILES -->
  	
	
	<!-- jQuery -->
	<script src="resource/tools/jquery-1.12.4.min.js"></script>
	<script src="resource/graphics/bootstrap/js/bootstrap.min.js"></script>
	<script src="resource/tools/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
	
	<!-- FONTS AND ICONS -->
	<link href='https://fonts.googleapis.com/css?family=Pacifico' rel='stylesheet' type='text/css'>
	<link href="resource/graphics/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<link rel="shortcut icon" type="image/x-icon" href="resource/images/favicon.png" />
	
	<!--CLOCK JAVASCRIPT-->
	<script type="text/javascript">
		function updateclock() {
			//Get the current time
			var time = new Date();
			var todisplay = '';

			//Add a 0 infront of the hour, minute or second if it is less than 10
			if (time.getHours() < 10) todisplay += '0' + time.getHours();
			else todisplay += time.getHours();

			if (time.getMinutes() < 10) todisplay += ':0' + time.getMinutes();
			else todisplay += ':' + time.getMinutes();

			if (time.getSeconds() < 10) todisplay += ':0' + time.getSeconds();
			else todisplay += ':' + time.getSeconds();

			//Refresh the display
			document.getElementById("clock").innerHTML = todisplay;
		}

		function startclock() {
			//Initial call otherwise the clock would display blank for the first second
			updateclock();
			//Update the clock every second, i.e. every 1000 milliseconds
			setInterval("updateclock()",1000);
		}
	</script>
</head>