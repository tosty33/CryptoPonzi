<?php 
	include('config.php');
	$json = array();
	
	if ($_GET['what'] == "all" || $_GET['what'] == "")
		$query = mysql_query('SELECT * FROM `transactions` WHERE `amount` > 0 AND `state` < 3 ORDER BY id DESC, state DESC LIMIT 20;');
	else
		$query = mysql_query('SELECT * FROM `transactions` WHERE `tx` = "' . mysql_real_escape_string($_GET['what']) . '" AND `state` < 3 LIMIT 1;');

	$transactions = array();
	while($row = mysql_fetch_assoc($query)) 
	{
		$row['timestamp'] = $row['date'];
		$row['date'] = date("d/m/Y H:i:s", $row['date']);
		$transactions[] = $row;
	}
	
	if (sizeof($transactions) == 1)
	{
		$query = mysql_query("SELECT COUNT(*) FROM `transactions` WHERE `amount` > 0 AND `state` = 0 AND `date` < " . $transactions[0]['timestamp'] . ";");
		$transactions[0]['queue'] = mysql_fetch_row($query)[0];
	}
	
	$json['transactions'] = $transactions;
	
	// Actual transaction
	$query = mysql_query('SELECT * FROM `transactions` WHERE `amount` > 0 AND `state` = 0 ORDER BY date ASC LIMIT 1;');
	$json['actual'] = mysql_fetch_assoc($query);
	
	//$query = mysql_query("SELECT COUNT(col) FROM table");
	$query = mysql_query("SELECT COUNT(*) FROM `transactions` WHERE `amount` > 0 AND `state` < 3;");
	$query = mysql_fetch_row($query);
	
	$json['count'] = $query[0];
	
	// Received
	$query = mysql_query("SELECT SUM(amount) FROM `transactions` WHERE `state` < 3;");
	$query = mysql_fetch_row($query);
	
	$json['received'] = $query[0];
	
	// Unpaid
	$query = mysql_query("SELECT SUM(topay) FROM `transactions` WHERE `state` = 0;");
	$query = mysql_fetch_row($query);

	if ($query[0] == null)
		$json['unpaid'] = 0;
	else
		$json['unpaid'] = $query[0];
	
	// Paid
	$json['paid'] = ($json['received'] * (1 + $config['income'])) - $json['unpaid'];
	
	echo json_encode($json);
?>