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

// 	  BTC Donations: 163Pv9cUDJTNUbadV4HMRQSSj3ipwLURRc
   
error_reporting(E_ALL);
ini_set('display_errors', '1'); 

//Set page starter variables//
$includeDirectory = "/var/www/includes/";

// echo "include 1\n";
//Include site functions
include($includeDirectory."requiredFunctions.php");

// echo "include 2\n";

//Include Reward class
include($includeDirectory.'reward.php');
$reward = new Reward();

//Check that script is run locally
//ScriptIsRunLocally();

lock("shares");

// echo "include 3\n";

	//Include Block class
	include($includeDirectory."block.php");
	$block = new Block();

// echo "getLatestDbBlockNumber()\n";
	
	//Get latest block in database
	$latestDbBlock = $block->getLatestDbBlockNumber();

// echo "UpdateConfirms()\n";

	$block->UpdateConfirms($bitcoinController);	

	//Do block work if new block 
//	if ($latestDbBlock < $lastBlockNumber) {		
		//Insert last block number into networkBlocks
// echo "include 4\n";

		include($includeDirectory."stats.php");
		$stats = new Stats();
		$lastwinningid = $stats->lastWinningShareId();

// echo "FindNewGenerations()\n";
		
		//Find new generations
		$block->FindNewGenerations($bitcoinController);		
//	}

unlock("shares");
	
	//Check for unscored blocks() 
	if ($block->CheckUnscoredBlocks()) {
		lock("money");
		try {
			//Get Difficulty
			$difficulty = $bitcoinDifficulty;
			if(!$difficulty)
			{
			   echo "no difficulty! exiting\n";
			   exit;
			}
			
			//Reward by selected type;
			if ($settings->getsetting("siterewardtype") == 0) {
				//LastNShares
				$reward->LastNShares($difficulty, $bonusCoins);
			//} else if ($settings->getsetting("siterewardtype") == 2) {
				////MaxPPS
				//MaxPPS();
			} else {
				//Proportional Scoring
				$reward->ProportionalScoring($bonusCoins);
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		unlock("money");
	}
		
	
	//Check for unrewarded blocks
	if ($block->CheckUnrewardedBlocks()) {			
		lock("money");	
		try {
			$reward->MoveUnrewardedToBalance();
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		unlock("money");
	}

//Check for orphans and delete rewards if found.
$winningAccountQ = mysql_query("SELECT id, blockNumber FROM winning_shares WHERE type = 'orphan'") or sqlerr(__FILE__, __LINE__);
while ($winningAccountR = mysql_fetch_object($winningAccountQ)) {
	mysql_query("DELETE FROM unconfirmed_rewards WHERE blockNumber = $winningAccountR->blockNumber") or sqlerr(__FILE__, __LINE__);
}
?>
