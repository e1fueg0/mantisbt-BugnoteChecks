function response(data) {
	return $('<div />').html(data);
}

function update_progress(_response) {
	var _pr = _response.find('.bugnotechecks_inner_bar');
	if (_pr)
		$('#bugnotechecks_progress_bar').html(_pr);
}

function update_turn(_response, parent) {
	var _pr = _response.find('.bugnotechecks_internal_turn');
	if (_pr)
		parent.html(_pr);
}

function update_check(_response, parent) {
	var _pr = _response.find('.bugnotechecks_internal_check');
	if (_pr)
		parent.html(_pr);
}

$(document).ready(function() {
	$(".bugnotechecks_div_check").each(function() {
		$(this).click(function() {
			var id1 = $(this).find("input[name='id']")[0].value;
			var state1 = $(this).find("input[name='state']")[0].value;
			var url1 = $("#bugnotechecks_url_check")[0].value;
			$.get(url1, { id: id1, state: state1 }, function (data) {
				var $response = response(data);
				update_progress(_response);
				update_check(_response, $(this))
			});
		});
	});
	$(".bugnotechecks_div_turn").each(function() {
		$(this).click(function() {
			var id1 = $(this).find("input[name='id']")[0].value;
			var state1 = $(this).find("input[name='state']")[0].value;
			var url1 = $("#bugnotechecks_url_turn")[0].value;
			$.get(url1, { id: id1, state: state1 }, function (data) {
				var r1 = response(data);
				update_progress(_response);
				update_turn(_response, $(this))
				update_check(_response, $(this))
			});
		});
	});
});
