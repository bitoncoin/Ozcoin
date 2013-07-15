
$(function() {
	var dt = $('.transactions').dataTable({
		aaSorting: [[ 6, 'desc' ]],
		bJQueryUI: true,
		//sPaginationType: 'full_numbers',
		bScrollInfinite: true,
		bScrollCollapse: true,
		sScrollY: 500,
		iDisplayLength: 50
	});
	
	var p = $.deparam.querystring();
	if (p.filter !== undefined) {
		dt.fnFilter('(' + p.filter.split(' ').join('|') + ')', null, true, false);
	}
	
	$('#remove_links').click(function() {
		$('.transactions > tbody > tr > td > a').each(function() {
			var a = $(this);
			a.parent().html(a.html());
		});
		
		$(this).remove();
	});
});