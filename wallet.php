<?php

$cookieValid = false;
$activeMiners = false;

include ("includes/requiredFunctions.php");

ScriptIsRunLocally();

ini_set("memory_limit","512M");

class BAccount {
    public $name;
    public $balance;
    public $txns = array();

    public function __construct($name, $balance) {
        $this->name = $name;
        $this->balance = $balance;
    }
}

class BTxn {
    public $address;
    public $category;
    public $amount;
    public $fee;
    public $confirmations;
    public $txid;
    public $time;

    public function __construct($address, $category, $amount, $fee, $confirmations, $txid, $time) {
    	$this->address = $address;
    	$this->category = $category;
    	$this->amount = $amount;
    	$this->fee = $fee;
    	$this->confirmations = $confirmations;
    	$this->txid = $txid;
    	$this->time = $time;
    }
}

$accounts = array();

$bitcoinController = new BitcoinClient($rpcType, $rpcUsername, $rpcPassword, $rpcHost);

$bitAccounts = $bitcoinController->query("listaccounts");
foreach ($bitAccounts as $name => $balance) {
    $account = new BAccount($name, $balance);
    $accounts[] = $account;

    $bitTransactions = $bitcoinController->query("listtransactions", $name, 10000);
    foreach ($bitTransactions as $bitT) {
        $account->txns[] = new BTxn(@$bitT['address'], $bitT['category'], $bitT['amount'], @$bitT['fee'], $bitT['confirmations'], $bitT['txid'], $bitT['time']);
    }

    usort($account->txns, function($a, $b) { return $b->time - $a->time; });
}

?>
<html>
    <head>
        <title>Wallet</title>
        <style>
            .receive {
            	background-color: #E5FFE5;
            }

            .send {
            	background-color: #FFE5E5;
            }
			
			.transactions {
				width: 100%;
			}
			
			.transactions td {
				font-family: monospace;
			}
			
			.transactions td a {
				color: #021A50;
				text-decoration: none;
			}
			
			.transactions td a:hover {
				color: #021A50;
				text-decoration: underline;
			}
			
			body {
				width: 95%;
				padding: 2%;
				margin: 0;
			}
        </style>
		
		<link rel="stylesheet" href="/css/sunny/jquery-ui-1.8.15.custom.css" type="text/css" />
		<link rel="stylesheet" href="/css/datatables.css" type="text/css" />
		
		<script type="text/javascript" src="/js/excanvas.js"></script>
		<script type="text/javascript" src="/js/jquery-1.6.1.min.js"></script>
		<script type="text/javascript" src="/js/jquery.ba-bbq.min.js"></script>
		<script type="text/javascript" src="/js/jquery-ui-1.8.15.custom.min.js"></script>
		<script type="text/javascript" src="/js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" src="/js/wallet.js?v=5"></script>
    </head>
    <body>
        <h1>Wallet</h1>
		
		<p>
			<a id="remove_links" href="#">Remove links (for easier copying)</a>
		</p>
        
        <? foreach ($accounts as $account): ?>
            <h2><?= $account->name ?></h2>
            <p>Balance: <?= $account->balance ?></p>

            <table class="transactions display">
                <thead>
                    <tr>
                        <th>Address</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Fee</th>
                        <th>Confirmations</th>
                        <th>Txid</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>

                <? foreach ($account->txns as $txn): ?>
                    <tr class="<?= $txn->amount < 0 ? "send" : "receive" ?>">
                        <td><a href="http://blockexplorer.com/address/<?= $txn->address ?>" target="_blank"><?= $txn->address ?></a></td>
                        <td><?= $txn->category ?></td>
                        <td><?= $txn->amount ?></td>
                        <td><?= $txn->fee ?></td>
                        <td><?= $txn->confirmations ?></td>
                        <td><a href="http://blockexplorer.com/tx/<?= $txn->txid ?>" target="_blank"><?= $txn->txid ?></a></td>
                        <td><?= strftime("%F %H:%M", $txn->time) ?></td>
                    </tr>
                <? endforeach; ?>

                </tbody>
            </table>
        <? endforeach; ?>

    </body>
</html>
