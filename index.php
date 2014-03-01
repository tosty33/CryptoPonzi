<?php include('header.php'); ?>
<div class="jumbotron" style="text-align: center;">
  <h1><?php echo $config['full-name'] ?></h1>
  <p>Send <?php echo $config['val'] ?>. Get <strong><?php echo(100 + ($config['income'] * 100)) ?>%</strong> back when the next person sends.</p>
  <div id="address-wrapper" style="overflow: hidden;">
		<a href="<?php echo($config['blockchain-addr'] . $config['address']) ?>">
			<strong><?php echo $config['address'] ?></strong>
		</a>
	</div>
	Send <span class="label label-info"><?php echo $config['min'] ?> < amount < <?php echo $config['max'] ?> <?php echo $config['val'] ?></span> and get back <?php echo(100 + ($config['income'] * 100)) ?>%!<br>
	Note: <strong>do not</strong> send from a web wallet.<br>
	
	<?php if($config['fee'] > 0): ?>
	<h6 style="text-align: center; color: rgb(200,200,200)"><strong>Note:</strong> We are taking <?php echo($config['fee'] * 100) ?>% transaction fee to keep us going :-)</h6>
	<?php endif; ?>
</div>

<div class="jumbotron" style="padding: 30px;">
	<div class="row" style="text-align: center; font-size: 18px;">
		<div class="col-md-3">
			Transactions: <br><span class="label label-info" id="count">0</span>
		</div>
		<div class="col-md-3">
			Paid: <br><span class="label label-success" id="paid">0.00 <?php echo $config['val'] ?></span>
		</div>
		<div class="col-md-3">
			Unpaid: <br><span class="label label-warning" id="unpaid">0.00 <?php echo $config['val'] ?></span>
		</div>
		<div class="col-md-3">
			Received: <br><span class="label label-info" id="received">0.00 <?php echo $config['val'] ?></span>
		</div><br><br><br>
		
		<span class="label label-info" id="collecting"></span>
	</div>
</div>

<div class="jumbotron" style="text-align: center; padding: 20px;">	
	<h2>Last transactions</h2>
	<div class="table-responsive">
		<table class="table table-hover table-striped">
			<thead>
				<tr>
					<td style="width: 6%;"></td>
					<td style="width: 40%">Transaction</td>
					<td style="width: 24%">Amount</td>
					<td style="width: 30%">Date</td>
				</tr>
			</thead>
			<tbody id="trans"></tbody>
		</table>
	</div>
	<div class="form-inline" style="text-align: right; margin-bottom: 20px; margin-top: 20px;" role="form">
		<div class="form-group">
			<label class="sr-only" for="search">Transaction ID</label>
			<input type="email" class="form-control" id="tid" placeholder="Enter transaction ID...">
		</div>
		<button type="submit" class="btn btn-info" onclick="search()">Search</button>
		<button type="submit" class="btn btn-default" onclick="showall()">Show all</button>
	</div>
	<h6 style="text-align: center; color: rgb(200,200,200)"><strong>Note:</strong> Payouts are sent every <?php echo $config['payout-check'] / 60 ?> minutes.</h6>
	<h6 style="text-align: center; color: rgb(200,200,200)"><strong>Note:</strong> Transactions are added after <?php echo $config['confirmations'] ?> confirmation<?php if($config['confirmations'] > 1) echo 's'; ?></h6>
</div>

<script>
	var what = "all";
	
	function search()
	{
		what = $('#tid').val();
		update();
	}
	
	function showall()
	{
		what = "all";
		update();
	}
	
	function update()
	{
		$.get(
				"json.php",
				{
					what: what
				},
				function(data){
					data = JSON.parse(data);
					$('#count').html(data['count']);
					$('#paid').html(parseFloat(data['paid']).toFixed(<?php echo $config['precision'] ?>) + ' <?php echo $config['val'] ?>');
					$('#unpaid').html(parseFloat(data['unpaid']).toFixed(<?php echo $config['precision'] ?>) + ' <?php echo $config['val'] ?>');
					$('#received').html(parseFloat(data['received']).toFixed(<?php echo $config['precision'] ?>) + ' <?php echo $config['val'] ?>');
					
					$('#collecting').html("Collecting " + (parseFloat(data['received']) - parseFloat(data['paid'])).toFixed(<?php echo $config['precision'] ?>) + " / " + parseFloat(data['actual']['topay']).toFixed(<?php echo $config['precision'] ?>) + " for " + data['actual']['tx'].substring(0,32) + "...");
					
					$('#trans').html('');
					for(var i in data['transactions'])
					{
						var state = "";
						if (data['transactions'][i]['state'] == "1")
							state = '<span class="label label-info" id="collecting">Ready</span>';
						else if (data['transactions'][i]['state'] == "2")
							state = '<span class="label label-success" id="collecting">Sent</span>';
						else
							state = '<span class="label label-default" id="collecting">Waiting</span>';
					
						$tr = $('<tr></tr>');
						$('#trans').append($tr);
					
						$td = $('<td>' + state + '</td>');
						$($tr).append($td);
						
						var out = "";
						if (data['transactions'][i]['out'])
							out = '<br>OUT: <a href="<?php echo $config['blockchain-tx'] ?>' + data['transactions'][i]['out'] + '">' + data['transactions'][i]['out'].substring(0,25) + '...</a>';
						
						$td = $('<td style="text-align: left;">IN: <a href="<?php echo $config['blockchain-tx'] ?>' + data['transactions'][i]['tx'] + '">' + data['transactions'][i]['tx'].substring(0,26) + '...</a>' + out + '</td>');
						$($tr).append($td);
						
						$td = $('<td>' + parseFloat(data['transactions'][i]['topay']).toFixed(<?php echo $config['precision'] ?>) + ' <?php echo $config['val'] ?></td>');
						$($tr).append($td);
					
						$td = $('<td>' + data['transactions'][i]['date'] + '</td>');
						$($tr).append($td);
						
						if (data['transactions'].length == 1)
						{
							$tr = $('<tr></tr>');
							$('#trans').append($tr);
							
							$td = $('<td colspan="4">In queue, actual position: <span class="label label-info">' + data['transactions'][i]['queue'] + '</span></td>');
							$($tr).append($td);
						}
					}
					
					if (data['transactions'].length == 0)
					{
						$tr = $('<tr></tr>');
						$('#trans').append($tr);
						
						$td = $('<td colspan="4">No transactions found.</td>');
						$($tr).append($td);
					}
					
					setTimeout(update, 15 * 1000);
				}
		);
	}
	update();
</script>

<?php include('footer.php'); ?>