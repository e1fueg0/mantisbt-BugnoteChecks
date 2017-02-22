function response(data) {
	return $('<div />').html(data);
}

function update_progress(_response) {
	var _pr = _response.find('.bugnotechecks_progress_bar_mark');
	if (_pr)
		$('#bugnotechecks_progress_bar').html(_pr[0]);
}

function get_turn(_response) {
	return _response.find('.bugnotechecks_internal_turn');
}

function get_check(_response) {
	return _response.find('.bugnotechecks_internal_check');
}

$(document).ready(function() {
	$(".bugnotechecks_div_check").each(function() {
		$(this).click(function() {
			var _this = $(this);
			var id1 = $(this).find("input[name='id']")[0].value;
			var bugid1 = $(this).find("input[name='bug_id']")[0].value;
			var state1 = $(this).find("input[name='state']")[0].value;
			var url1 = $("#bugnotechecks_url_check")[0].value;
			$.post(url1, { id: id1, bug_id: bugid1, state: state1 }, function (data) {
				var r1 = response(data);
				update_progress(r1);
				_this.html(get_check(r1));
			});
		});
	});
	$(".bugnotechecks_div_turn").each(function() {
		$(this).click(function() {
			var _this = $(this);
			var id1 = $(this).find("input[name='id']")[0].value;
			var bugid1 = $(this).find("input[name='bug_id']")[0].value;
			var state1 = $(this).find("input[name='state']")[0].value;
			var url1 = $("#bugnotechecks_url_turn")[0].value;
			$.post(url1, { id: id1, bug_id: bugid1, state: state1 }, function (data) {
				var r1 = response(data);
				update_progress(r1);
				_this.html(get_turn(r1));
				$("#bugnotechecks_div_check_" + id1).html(get_check(r1));
			});
		});
	});
});
