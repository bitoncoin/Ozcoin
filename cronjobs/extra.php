<?PHP

error_reporting(E_ALL);
ini_set('display_errors', '1'); 

// Update MtGox last price via curl, 3 second timeout on connection
$mtgox_ticker = exec("/usr/bin/curl -q -s --connect-timeout 3 'https://mtgox.com/code/data/ticker.php'");
if (!is_null($mtgox_ticker)) {
    if ($ticker_obj = json_decode($mtgox_ticker)) {
        if (intval($ticker_obj->ticker->last) > 0) {
            $settings->setsetting('mtgoxlast', $ticker_obj->ticker->last);
        }
    }
}

// Update Tradehill last price via curl, 3 second timeout on connection
$th_ticker = exec("/usr/bin/curl -q -s --connect-timeout 5 'https://api.tradehill.com/APIv1/USD/Ticker'");
if (!is_null($th_ticker)) {
    if ($ticker_obj = json_decode($th_ticker)) {
        if (intval($ticker_obj->ticker->last) > 0) {
$str=$ticker_obj->ticker->last;
$len=strlen($str);
$test=substr($str,0,($len-6));
            $settings->setsetting('tradehill', $test);
        }
    }
}

include ("../includes/JSON.php");

function exchangeRate($amount, $currency, $exchangeIn) {
    $url = @ 'http://www.google.com/ig/calculator?hl=en&q=' . urlEncode($amount . $currency . '=?' . $exchangeIn);
    $data = @ file_get_contents($url);
 
    if(!$data) {
        throw new Exception('Could not connect');
    }
 
    $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
 
    $array = $json->decode($data);
 
    if(!$array) {
        throw new Exception('Could not parse the JSON');
    }
 
    if($array['error']) {
        throw new Exception('Google reported an error: ' . $array['error']);
    }
 
    return (float) $array['rhs'];
}

try {
	$rate = exchangeRate(1, 'usd', 'aud');
$settings->setsetting('aud', $rate);
}
catch(Exception $exception) {
	// log $exception->getMessage()
	echo 'Due to technical difficulties, we couldn\'t get the exchange rate';
}

$lastPaidBlockQ = mysql_query("select blockNumber from rounds where paid = 1 order by blockNumber desc limit 1");
if ($lastPaidBlock = mysql_fetch_object($lastPaidBlockQ)) {
    $block = $lastPaidBlock->blockNumber;
    mysql_query("delete from shares_history where blockNumber < $block and (upstream_result != 'Y' or upstream_result is null) limit 500000") or die("Error clearing shares: " . mysql_error());
}

$difficulty = $bitcoinController->query("getdifficulty");
$hashrate = $settings->getsetting('currenthashrate');


$time_to_find = CalculateTimePerBlock($difficulty, $hashrate);
$intpart = floor( $time_to_find );
$fraction = $time_to_find - $intpart; // results in 0.75
$minutes = number_format(($fraction * 60 ),0);
$timetofind = "" . number_format($time_to_find,0) . " Hours " . $minutes . " Minutes";

$result = mysql_query("SELECT blockNumber, confirms, timestamp FROM networkBlocks WHERE confirms > 0 ORDER BY blockNumber DESC LIMIT 1");
$resultrow = mysql_fetch_object($result);
$time_last_found = $resultrow->timestamp;
$now = new DateTime( "now" );
$hours_diff = ($now->getTimestamp() - $time_last_found) / 3600;
$time_last_found_out = floor( $hours_diff ). " Hours " . $hours_diff*60%60 . " Minutes";

$settings->setsetting('timetofind', $timetofind);
$settings->setsetting('time_last_found_out', $time_last_found_out);
?>