<?php
	$mysql = array(
		'user' => 'root',
		'password' => '',
		'host' => 'localhost',
		'database' => 'btc'
	);

	mysql_connect($mysql['host'], $mysql['user'], $mysql['password']) or die("Cannot connect to database.");
	mysql_select_db($mysql['database']);
	
	$rpc = array(
		'login' => 'RPC_login',
		'password' => 'RPC_password',
		'ip' => '127.0.0.1',
		'port' => '8332'
	);
	
	$config = array(
		'name' => 'BitPonzi',																	// Name of your ponzi
		'title' => 'get rich!',																// Description
		'full-name' => 'Bitcoin Ponzi',												// Full name of your ponzi
		'val' => 'BTC',																				// Cryptocurrency abbreviation
		'precision' => 4,
		'confirmations' => 1,																	// Minimum number of confirmations to add transaction
		'min' => 0.001,																				// Minimum pay in
		'max' => 0.25,																				// Maximum pay in
		'income' => 0.1,																			// How much money to send - default: 0.1 - 110%
		'fee' => 0.01,																				// Fee taken from pay in amount
		'payout-check' => 180,																// Time between payouts
		'ownaddress' => '1MSkXPRK293dDMD5ds6KqVtyDadDkRyanX', // Your address
		'ponziacc' => 'btc',																	// Name of daemon account
		'address' => '1ponzisApJfHtgrwP7CrpfEgseBmPcRD4',			// Ponzi address
		'privkey' => '',																			// Needed in setup, private key of your address
		'blockchain-addr' => 'https://blockchain.info/en/address/',
		'blockchain-tx' => 'https://blockchain.info/en/tx/'
	);
?>