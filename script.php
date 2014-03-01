<?php
	include('config.php');
	require_once 'jsonRPCClient.php';
	$client = new jsonRPCClient('http://' . $rpc['login'] . ':' . $rpc['password'] . '@' . $rpc['ip'] . ':' . $rpc['port'] . '/') or die('Error: could not connect to RPC server.');

	$lastPayout = time();
	$adresses = array();

	function getAddress($trans)
	{
		global $client;
		$address = "";

		$details = $client->getrawtransaction($trans["txid"], 1);

		$vintxid = $details['vin'][0]['txid'];
		$vinvout = $details['vin'][0]['vout'];

		try {
			$transactionin = $client->getrawtransaction($vintxid, 1);
		}
		catch (Exception $e) {
			die("Error with getting transaction details.\nYou should add 'txindex=1' to your .conf file and then run the daemon with the -reindex parameter.");
		}
		
		if ($vinvout == 1)
			$vinvout = 0;
		else
			$vinvout = 1;
		
		$address = $transactionin['vout'][!$vinvout]['scriptPubKey']['addresses'][0];
		return $address;
	}
	
	while(true)
	{
		// Parsing and adding new transactions to database
		print("Parsing transactions...\n");
		$transactions = $client->listtransactions($config['ponziacc'], 100);
		$i = 0;
		foreach ($transactions as $trans)
		{
			echo("Parsing " . ++$i . "\n");
			
			if ($trans['category'] != "receive" || $trans["confirmations"] < $config['confirmations'])
				continue;
			
			if ($trans['amount'] > $config['max'] || $trans['amount'] < $config['min'])
			{
				$query = mysql_query('SELECT * FROM `transactions` WHERE `tx` = "'.$trans['txid'].'";');
				if (!mysql_fetch_assoc($query))
				{
					if ($trans['amount'] < 0)
						continue;

					if ($config['sendback'])
						$client->sendtoaddress(getAddress($trans), $trans['amount'] - ($trans['amount'] * $config['fee']));
					else
						$client->sendtoaddress($config['ownaddress'], $trans['amount'] - ($trans['amount'] * $config['fee']));
						
					mysql_query("INSERT INTO `transactions` (`id`, `amount`, `topay`, `address`, `state`, `tx`, `date`) VALUES (NULL, '" . $trans['amount'] . "', '0', '0', '3', '" . $trans['txid'] . "', " . (time()) . ");");
					print($trans['amount'] + " - Payment has been sent to you!\n");
					continue;
				}
			}
		
			$query = mysql_query('SELECT * FROM `transactions` WHERE `tx` = "'.$trans['txid'].'";');
			if (!mysql_fetch_assoc($query)) // Transaction not found in DB
			{
				$amount = $trans['amount'];
				$topay = $amount * (1.0 + $config['income']);
				print("Transaction added! [" . $amount . "]\n");
				$address = getAddress($trans);

				mysql_query("INSERT INTO `transactions` (`id`, `amount`, `topay`, `address`, `state`, `tx`, `date`) VALUES (NULL, '" . $amount . "', '" . $topay . "', '" . $address . "', '0', '" . $trans['txid'] . "', " . (time()) . ");");
			}
		}
		
		$query = mysql_query("SELECT SUM(amount) FROM `transactions`;");
		$query = mysql_fetch_row($query);
		$money = $query[0];
		
		$query = mysql_query("SELECT SUM(topay) FROM `transactions` WHERE `state` > 0;");
		$query = mysql_fetch_row($query);
		$money -= $query[0];
		
		$query = mysql_query("SELECT * FROM `transactions` WHERE `state` = 0 AND `topay` > 0 ORDER BY `id` ASC;");
		while($row = mysql_fetch_assoc($query))
		{
			print("Money: " . $money . "\n");
			if ($money < $row['topay'])
				break;
				
			mysql_query("UPDATE `transactions` SET `state` = 1 WHERE `id` = " . $row['id'] . ";");
			$money -= $row['topay'];
		}
		
		// Paying out
		if (time() - $lastPayout > $config['payout-check'])
		{
			$lastPayout = time();
			$query = mysql_query('SELECT * FROM `transactions` WHERE `state` = 1 ORDER BY `date` ASC;');
			while($row = mysql_fetch_assoc($query))
			{
				$txout = $client->sendfrom($config['ponziacc'], $row['address'], round((float)$row['topay'], 4) - ($row['amount'] * $config['fee']));
				mysql_query("UPDATE `transactions` SET `state` = 2, `out` = '" . $txout . "' WHERE `id` = " . $row['id'] . ";");
				print($row['topay'] . " " . $config['val'] ." sent to " . $row['address'] . ".\n");
			}
		}

		echo ("Waiting...\n");
		sleep(20);
	}
?>