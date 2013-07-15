<?PHP

error_reporting(E_ALL);
ini_set('display_errors', '1'); 

//Set page starter variables//
$includeDirectory = "/var/www/ltc/includes/";

//Include site functions
include($includeDirectory."requiredFunctions.php");

$bitcoinController = new BitcoinClient($rpcType, $rpcUsername, $rpcPassword, $rpcHost);

$result = mysql_query("SELECT blockNumber,confirms FROM winning_shares ORDER BY blockNumber DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);

while ($shit = mysql_fetch_object($result)) {		
if ($shit->confirms == '0') {
$acadq = mysql_query("SELECT accountAddress FROM networkBlocks WHERE blockNumber = $shit->blockNumber");
$acada = mysql_fetch_object($acadq);
$transactions1 = $bitcoinController->query("gettransaction" ,"$shit->accountAddress");
$txid = $transactions1['txid'];
mysql_query("UPDATE winning_shares SET txid = '".$txid."' WHERE blockNumber = $shit->blockNumber) or sqlerr(__FILE__, __LINE__);
}
}
?>