function LoginUsernameFocus(){
	if($("#input_username").val() == "Username"){
		$("#input_username").val("");
	}
}

function LoginPasswordFocus(){
	if($("#input_password").val() == "password"){
		$("#input_password").val("");
	}
}

function CheckSubmit(e){
  if (!e) var e = window.event;
  if (e.keyCode) code = e.keyCode;
  else if (e.which) code = e.which;

  if (code==13) {
    $('#loginForm').submit();
  }
}

$(function() {
	$(".datatable").each(function() {
		$(this).dataTable({
			bJQueryUI: true,
			bScrollInfinite: true,
			bScrollCollapse: true,
			sScrollY: 200,
			iDisplayLength: 15,
			aaSorting: [[ 0, 'desc' ]]
		});
	});
});