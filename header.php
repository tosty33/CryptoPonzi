<?php	include('config.php'); ?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo($config['name'] . ' - ' . $config['title']) ?></title>
	<meta name="keywords" content="<?php echo($config['name'] . ',' . $config['val']) ?>,ponzi,pyramid scheme,pyramid,cryptocoin,bitcoin">
	<meta charset="utf-8">
	<link rel="stylesheet" href="assets/bootstrap.min.css">
	<link rel="stylesheet" href="assets/style.css">
	<script src="assets/jquery-1.11.0.min.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
	<div class="container" style="background: white; margin-bottom: 40px;">
		<div id="header">
			<?php echo($config['name'] . ' - ' . $config['title']) ?>
		</div>
		<ul class="nav nav-tabs" style="margin-left: 4px; width: 99%;">
			<li class="active"><a href="index.php">Play!</a></li>
			<li><a href="<?php echo($config['blockchain-addr'] . $config['address']) ?>">Transactions</a></li>
		</ul>