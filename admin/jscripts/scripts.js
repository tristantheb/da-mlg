$(document).ready(function() {
	$("link[disabled]").removeAttr("disabled");
	redList();
	window.setInterval(function() {
		redList();
	}, 10000);
});

var name = "";

function redList() {
	$.getJSON('./includes/function_redlist.php', function(data) {
		if(data['error'] == '0') {
			if(data['alert'] == '1') {
				if (name != data['name']) {
					name = data['name'];
					Swal.fire({
						position: 'top-end',
						title: 'Alerte !',
						text: 'Un jeune en liste rouge ('+data['name']+') est présent et veut s\'actualiser !',
						icon: 'warning',
						timer: 300000,
						allowOutsideClick: false,
						allowEscapeKey: false,
						allowEnterKey: false
					});
				}
			}
		}
	});
}