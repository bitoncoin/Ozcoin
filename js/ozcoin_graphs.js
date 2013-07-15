// Run the script on DOM ready:
$(function(){
	$('#blocks_over_week').visualize({type: 'area', width: '318px', height: '100px'});
	$('#user_hashrate_lasthour').visualize({type: 'area', width: '910px', height: '250px'});
	$('#user_hashrate_last24').visualize({type: 'area', width: '910px', height: '250px'});
	$('#user_hashrate_lastmonth').visualize({type: 'area', width: '910px', height: '250px'});
});