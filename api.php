<?php 
//    Copyright (C) 2011  Mike Allison <dj.mikeallison@gmail.com>
//
//    This program is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 3 of the License, or
//    (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program.  If not, see <http://www.gnu.org/licenses/>.

//    BTC Donations: 163Pv9cUDJTNUbadV4HMRQSSj3ipwLURRc
$includeDirectory = "/var/www/includes/";

include($includeDirectory."requiredFunctions.php");

if (!isset($_GET["api_key"])){

    $result = apc_fetch("api:public");

    if ($result !== FALSE) {    
        echo $result;
    } else {
        connectToDb();

        class Hashrate {
            var $hashrate = null;           
        }

        $hashrate= new Hashrate();
        $resultU = mysql_query("SELECT value FROM settings WHERE setting='currenthashrate'");
        $resultU1 = mysql_query("SELECT value FROM settings WHERE setting='currentroundshares'");
        $resultU2 = mysql_query("SELECT value FROM settings WHERE setting='currentworkers'");
        $resultU3 = mysql_query("SELECT count(webUsers.id) as count FROM webUsers WHERE hashrate > 0");
        $resultU4 = mysql_query("SELECT blockNumber, confirms, timestamp FROM networkBlocks WHERE confirms > 0 ORDER BY blockNumber DESC LIMIT 1");

        $hashobj = mysql_fetch_object($resultU);
        $sharesobj = mysql_fetch_object($resultU1);
        $workersobj = mysql_fetch_object($resultU2);
        $usersobj = mysql_fetch_object($resultU3);
        $roundobj = mysql_fetch_object($resultU4);

        $hashrate->hashrate = $hashobj->value;
        $hashrate->shares = $sharesobj->value;
        $hashrate->activeworkers = $workersobj->value;
        $hashrate->activeusers = $usersobj->count;
        $lastupdatedtime = time() - $roundobj->timestamp;
        $hashrate->roundduration = $lastupdatedtime;
        $bitcoinController = new BitcoinClient($rpcType, $rpcUsername, $rpcPassword, $rpcHost);
        $current_block_no = $bitcoinController->query("getblocknumber");
        $hashrate->currentblock = $current_block_no;

        $output = json_encode($hashrate);
        echo $output;
        apc_store("api:public", $output, 60);
    }
    exit;

}
    


class User {
    var $current_balance = null;
    var $hashrate = null;   
    var $payout_history = null;
    var $workers = array();     
}

class Worker {  
    var $alive = null;
    var $hashrate = null;   
}
    
connectToDb();
$apikey = mysql_real_escape_string($_GET["api_key"]);

$useridq = mysql_query("SELECT id FROM webUsers WHERE api_key = '".$apikey."'") or sqlerr(__FILE__, __LINE__);
$userida = mysql_fetch_object($useridq);
$id = $userida->id;

$user = new User();
/*
$estq = mysql_query("SELECT estimate FROM roundDetails WHERE userId = '$id' ORDER BY estimate DESC");
$esta = mysql_fetch_object($estq);
if($esta->estimate == NULL) {
$user->last_paid = '0'; } ELSE {
$user->last_paid = $esta->estimate;}
*/
$blockq = mysql_query("SELECT blockNumber FROM networkBlocks WHERE confirms > '0' ORDER BY blockNumber DESC");
$blocka = mysql_fetch_object($blockq);
$user->last_block = $blocka->blockNumber;

$bitcoinController = new BitcoinClient($rpcType, $rpcUsername, $rpcPassword, $rpcHost);
$user->current_block = $bitcoinController->query("getblocknumber");


$resultU = mysql_query("SELECT u.id, u.hashrate, u.round_estimate, shares_this_round, b.balance, b.paid from webUsers u, accountBalance b WHERE u.id = b.userId AND u.api_key='".$apikey."'");
if ($userobj = mysql_fetch_object($resultU)){
    $userid = $userobj->id;
    $user->current_balance = $userobj->balance;
    $user->hashrate = $userobj->hashrate;
    $user->payout_history = $userobj->paid;
    $user->round_estimate = $userobj->round_estimate;
    $user->shares_this_round = $userobj->shares_this_round;
}
if (isset($_GET["api_key"])){
$userQ = mysql_query("SELECT id FROM webUsers WHERE api_key = '".$apikey."'");
$userA = mysql_fetch_object($userQ);
$userId1 = $userA->id;
$resultW = mysql_query("SELECT username, hashrate, active, hashes FROM pool_worker WHERE associatedUserId=$userId1");
while ($workerobj = mysql_fetch_object($resultW)) {
    $worker = new Worker();
    $worker->alive = $workerobj->active;
    $worker->hashrate = $workerobj->hashrate;
    $worker->shares_done = $workerobj->hashes;
    $user->workers[$workerobj->username] = $worker;
}} 

echo json_encode($user);
//echo json_encode($workers);


?>
