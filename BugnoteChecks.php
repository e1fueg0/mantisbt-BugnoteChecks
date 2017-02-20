<?php
class BugnoteChecksPlugin extends MantisPlugin {
	var $skip = false;

	function register() {
		$this->name = 'BugnoteChecks';
		$this->description = 'BugnoteChecks';
		$this->version = '1.0';
		$this->requires = array(
			'MantisCore' => '2.0.0',
		);
		$this->author = 'Roman Pedchenko; paintings by Dasha Dasher';
		$this->contact = 'bugnotechecks@elfuego.biz';
		$this->url = 'http://elfuego.biz';
	}

	function install() {
		if (version_compare(PHP_VERSION, '5.3.0', '<')) {
			plugin_error(ERROR_PHP_VERSION, ERROR);
			return false;
		}
		return true;
	}

	function events() {
		return array(
			'EVENT_BUGNOTECHECKS_PROGRESS' => EVENT_TYPE_OUTPUT,
			'EVENT_BUGNOTECHECKS_CLICK_TURN' => EVENT_TYPE_OUTPUT,
			'EVENT_BUGNOTECHECKS_CLICK_CHECK' => EVENT_TYPE_OUTPUT,
		);
	}

	function hooks() {
		return array(
			'EVENT_LAYOUT_RESOURCES' => 'resources',
			'EVENT_VIEW_BUGNOTES_START' => 'progress_view',
			'EVENT_VIEW_BUGNOTE' => 'note_view',
			'EVENT_BUGNOTECHECKS_PROGRESS' => 'display_progress',
			'EVENT_BUGNOTECHECKS_CLICK_TURN' => 'click_turn',
			'EVENT_BUGNOTECHECKS_CLICK_CHECK' => 'click_check',
		);
	}

/*
	function schema() {
		return array(
			array( 'CreateTableSQL', array(plugin_table('checks'), "
				id			I		NOTNULL UNSIGNED AUTOINCREMENT PRIMARY,
				bug_id		I		NOTNULL PRIMARY',
				bugnote_id	I		NOTNULL PRIMARY,
				turned		L		DEFAULT \" false \",
				checked		L		DEFAULT \" false \""))
		);
	}
*/

	function resources($p_event) {
		$resources = '<link rel="stylesheet" type="text/css" href="' . plugin_file('main.css') . '" />';
		$resources .= '<script type="text/javascript" src="' . plugin_file('main.js') . '"></script>';
		return  $resources;
	}

	function progress_view($p_event, $bug_id) {
		echo "<input id='bugnotechecks_url_progress' type='hidden' name='url' value='" . plugin_page( 'progress' ) . "'/>";
		echo "<input id='bugnotechecks_url_turn' type='hidden' name='url' value='" . plugin_page( 'turn' ) . "'/>";
		echo "<input id='bugnotechecks_url_check' type='hidden' name='url' value='" . plugin_page( 'check' ) . "'/>";
		echo "<input id='bugnotechecks_bug_id' type='hidden' name='id' value='$bug_id'/>";
		echo "<td><img src='" . plugin_file('icon.png') . "' class='bugnotechecks_icon'/>";
		echo "</td>";
		echo "<td><div class='bugnotechecks_progress_div'><div id='bugnotechecks_progress_bar' class='bugnotechecks_progress_bar'>";
		$this->display_progress($p_event, $bug_id);
		echo "</div></td>";
		echo '<tr class="spacer"><td colspan="2"></td></tr>';
	}

	function display_progress($p_event, $bug_id) {
		$progress = 75;
		echo "<div class='bugnotechecks_inner_bar' style='width: $progress%;'/></div>";
	}

	function note_view($p_event, $bug_id, $bugnote_id, $is_private) {
		echo "<td/><td>";
		echo "<div class='bugnotechecks_div_check'>";
		$this->display_click($bugnote_id, !$is_private);
		echo "</div>";
		echo "</td>";
	}

	function display_click($bugnote_id, $state) {
		$state = !!$state;
		$future_state = !$state;
		echo "<img src='" . plugin_file(($state ? 'un' : "") . 'checked.png') . "'/>";
		echo "<input type='hidden' name='id' value='$bugnote_id'/>";
		echo "<input type='hidden' name='state' value='$future_state'/>";
	}

	function click_turn($p_event, $bugnote_id) {
	}

	function click_check($p_event, $bugnote_id, $state) {
		return $this->display_click($bugnote_id, $state);
	}
}
