$(document).ready(function() {
	$(".bugnotechecks_div_check").each(function() {
		$(this).click(function() {
			alert("!");
			var id1 = $(this).find("input[name='id']")[0].value;
			var state1 = $(this).find("input[name='state']")[0].value;
			var url1 = $("#bugnotechecks_url_check")[0].value;
			$(this).load(url1, { id: id1, state: state1 });
			id1 = $("#bugnotechecks_bug_id")[0].value;
			url1 = $("#bugnotechecks_url_progress")[0].value;
			$("#bugnotechecks_progress_bar").load(url1, { id: id1 });
		});
	})
});
