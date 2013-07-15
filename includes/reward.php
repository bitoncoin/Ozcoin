<?php
class Reward {
	
	function MoveUnrewardedToBalance() {
		$blocksQ = mysql_query("SELECT blockNumber FROM winning_shares WHERE rewarded = 'N' AND confirms > 119 ORDER BY id ASC") or sqlerr(__FILE__, __LINE__);
		if (mysql_num_rows($blocksQ) > 0)							
		while ($blocksR = mysql_fetch_object($blocksQ)) {	
			$overallreward = 0;		
			$blockNumber = $blocksR->blockNumber;			
			echo "Block: $blockNumber\n";
			$unrewarededQ = mysql_query("SELECT userId, amount FROM unconfirmed_rewards WHERE blockNumber = $blockNumber AND rewarded = 'N'") or sqlerr(__FILE__, __LINE__);
			mysql("BEGIN");
			try {
				while ($unrewardedR = mysql_fetch_object($unrewarededQ)) {
					$amount = $unrewardedR->amount;
					$userid = $unrewardedR->userId;
					$overallreward += $amount;
					echo "UPDATE accountBalance SET balance = balance + $amount WHERE userId = $userid\n";
					mysql_query("UPDATE accountBalance SET balance = balance + $amount WHERE userId = $userid") or sqlerr(__FILE__, __LINE__);
				}
				mysql_query("DELETE FROM unconfirmed_rewards WHERE blockNumber = $blockNumber") or sqlerr(__FILE__, __LINE__);
				mysql_query("UPDATE winning_shares SET rewarded = 'Y' WHERE blockNumber = $blockNumber") or sqlerr(__FILE__, __LINE__);
				mysql_query("UPDATE rounddetails SET rewarded = 'Y' WHERE blockNumber = $blockNumber") or sqlerr(__FILE__, __LINE__);

				mysql_query("COMMIT");
				echo "Total Reward: $overallreward\n";
			} catch (Exception $e) {
				echo("Exception: " . $e->getMessage() . "\n");
				mysql_query("ROLLBACK");
			}														
		}
	}
	
	function LastNShares($difficulty, $bonusCoins) {
		global $settings;
		$overallreward = 0;
		$blocksQ = mysql_query("SELECT share_id, blockNumber from winning_shares WHERE scored = 'N' ORDER BY id ASC") or sqlerr(__FILE__, __LINE__);
		while ($blocksR = mysql_fetch_object($blocksQ)) {
			$blocksQ1 = mysql_query("SELECT share_id, blockNumber from winning_shares WHERE id = $blocksR->share_id") or sqlerr(__FILE__, __LINE__);
			$blocksR1 = mysql_fetch_object($blocksQ1);
			echo "Last N shares scoring\n";
			echo "difficulty is $difficulty \n";
			$blockNumber = $blocksR1->blockNumber;	
			$shareId = $blocksR->share_id;
			$shareLimit = round($difficulty/2);
			
			//Make sure there are at least $shareLimit shares
			$limitQ = mysql_query("SELECT count(id) FROM shares WHERE id <= $shareId AND our_result='Y'") or sqlerr(__FILE__, __LINE__);
			if ($limitR = mysql_fetch_array($limitQ)) {
				if ($limitR[0] < $shareLimit) $shareLimit = round($limitR[0]);
			}
			echo "share limit is $shareLimit\n";

			$sharesQ = mysql_query("SELECT u.id, count(s.id) as shares FROM webUsers u, pool_worker p, (SELECT id, username FROM shares WHERE id <= $shareId AND our_result='Y' ORDER BY id DESC LIMIT $shareLimit) s WHERE u.id = p.associatedUserId AND p.username = s.username  GROUP BY u.id") or sqlerr(__FILE__, __LINE__);	
			mysql_query("BEGIN");
			try {
				while ($sharesR = mysql_fetch_object($sharesQ)) {
					$totalReward = $sharesR->shares/$shareLimit*$bonusCoins;
					$userid = $sharesR->id;
					$totalReward = $totalReward * 100000000;
					$totalReward = floor($totalReward);
					$totalReward = $totalReward/100000000;
					$overallreward += $totalReward;
					echo "$userid - $totalReward - $sharesR->shares\n";
					if ($totalReward > 0.00000001)
						echo "INSERT INTO unconfirmed_rewards (userId, blockNumber, amount, shares) VALUES ($userid, $blockNumber, '$totalReward', $sharesR->shares)\n";
						mysql_query("INSERT INTO unconfirmed_rewards (userId, blockNumber, amount, shares) VALUES ($userid, $blockNumber, '$totalReward', $sharesR->shares)") or sqlerr(__FILE__, __LINE__);
				}
				echo "UPDATE winning_shares SET scored = 'Y' WHERE share_id = $shareId\n";
				mysql_query("UPDATE winning_shares SET scored = 'Y' WHERE share_id = $shareId") or sqlerr(__FILE__, __LINE__);
				mysql_query("COMMIT");
				echo "Total Reward: $overallreward";
			} catch (Exception $e) {
				echo("Exception: " . $e->getMessage() . "\n");
				mysql_query("ROLLBACK");
			}							
			
		}
	}
	
	function ProportionalScoring($bonusCoins) {	
		//Go through all of shares that are uncounted shares; Check if there are enough confirmed blocks to award user their BTC
		$overallReward = 0;
		$lastrewarded = 0;

		//Get last rewarded share id
		$rewardedblocksQ = mysql_query("SELECT share_id from winning_shares WHERE rewarded = 'Y' ORDER BY blockNumber DESC LIMIT 0,1") or sqlerr(__FILE__, __LINE__);
		if ($rewardedblocksR = mysql_fetch_row($rewardedblocksQ)) {
			$lastrewarded = $rewardedblocksR[0];
		}

		//Get unrewarded blocks
		$blocksQ = mysql_query("SELECT id,share_id, blockNumber from winning_shares WHERE scored = 'N' ORDER BY id ASC") or sqlerr(__FILE__, __LINE__);		
		while ($blocksR = mysql_fetch_object($blocksQ)) {
			$blocksQ1 = mysql_query("SELECT share_id, blockNumber from winning_shares WHERE id = $blocksR->id") or sqlerr(__FILE__, __LINE__);
			$blocksR1 = mysql_fetch_object($blocksQ1);
			//echo "Proportional Scoring \n";
			$shareid = $blocksR->share_id;
			$blockNumber = $blocksR1->blockNumber;
			//Get unrewarded shares
			$totalRoundSharesQ = mysql_query("SELECT count(id) as id FROM shares WHERE id <= $shareid AND id > $lastrewarded AND our_result='Y' ") or sqlerr(__FILE__, __LINE__);
			if ($totalRoundSharesR = mysql_fetch_object($totalRoundSharesQ)) {
				$totalRoundShares = $totalRoundSharesR->id;
				$userListCountQ = mysql_query("SELECT DISTINCT username, count(id) as id FROM shares WHERE id <= $shareid  AND id > $lastrewarded AND our_result='Y' GROUP BY username") or sqlerr(__FILE__, __LINE__);
				while ($userListCountR = mysql_fetch_object($userListCountQ)) {
					mysql_query("BEGIN");
					try {
						$username = $userListCountR->username;
						$uncountedShares = $userListCountR->id;
						$shareRatio = $uncountedShares/$totalRoundShares;
						$predonateAmount = $bonusCoins*$shareRatio;				
									
						//get owner userId and donation percent
						$ownerIdQ = mysql_query("SELECT p.associatedUserId, u.donate_percent FROM pool_worker p, webUsers u WHERE u.id = p.associatedUserId AND p.username = '$username' LIMIT 0,1") or sqlerr(__FILE__, __LINE__);
						$ownerIdObj = mysql_fetch_object($ownerIdQ);
						$userid = $ownerIdObj->associatedUserId;						
						
						//Force decimal value (remove e values)
						$totalReward = rtrim(sprintf("%f",$predonateAmount ),"0");							
						
						if ($totalReward > 0.00000001)	{											
							//Round Down to 8 digits
							$totalReward = $totalReward * 100000000;
							$totalReward = floor($totalReward);
							$totalReward = $totalReward/100000000;
																							
							//Update balance
							mysql_query("INSERT INTO unconfirmed_rewards (userId, blockNumber, amount, shares) VALUES ($userid, $blockNumber, '$totalReward', $uncountedShares)") or sqlerr(__FILE__, __LINE__);
							mysql_query("INSERT INTO rounddetails (userId, blockNumber, amount, shares) VALUES ($userid, $blockNumber, '$totalReward', $uncountedShares)") or sqlerr(__FILE__, __LINE__);
						}
						mysql_query("UPDATE winning_shares SET scored = 'Y' WHERE share_id = $shareid") or sqlerr(__FILE__, __LINE__);
						mysql_query("COMMIT");
					} catch (Exception $e) {
						echo("Exception: " . $e->getMessage() . "\n");
						mysql_query("ROLLBACK");
					}								
				}
			}
		}
	}
}
?>
