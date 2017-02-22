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
			'EVENT_BUGNOTECHECKS_CLICK_TURN' => EVENT_TYPE_EXECUTE,
			'EVENT_BUGNOTECHECKS_CLICK_CHECK' => EVENT_TYPE_EXECUTE,
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

	function schema() {
		return array(
			array( 'CreateTableSQL', array(plugin_table('checks'), "
				id			I		NOTNULL UNSIGNED AUTOINCREMENT PRIMARY,
				bug_id		I		NOTNULL,
				bugnote_id	I		NOTNULL,
				checked		L		DEFAULT \" false \",
				by_user_id	I
				",
			array()))
		);
	}

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
		echo "<td><div id='bugnotechecks_progress_bar' class='bugnotechecks_progress_div'>";
		$this->display_progress($p_event, $bug_id);
		echo "</div></td>";
		echo '<tr class="spacer"><td colspan="2"></td></tr>';
	}

	function get_checks_for_bug($bug_id) {
		$t_result = db_query("SELECT count(*) FROM " . plugin_table('checks') . " WHERE bug_id = $bug_id");
		return db_result($t_result, 0);
	}

	function get_checked_checks_for_bug($bug_id) {
		$t_result = db_query("SELECT count(*) FROM " . plugin_table('checks') . " WHERE bug_id = $bug_id AND checked = true");
		return db_result($t_result, 0);
	}

	function display_progress($p_event, $bug_id) {
		$total = $this->get_checks_for_bug($bug_id);
		if (!$total) {
			echo "<div class='bugnotechecks_progress_bar_mark'/>";
			return;
		}
		$progress = ceil($this->get_checked_checks_for_bug($bug_id) / $total);
		echo "<div class='bugnotechecks_progress_bar bugnotechecks_progress_bar_mark'>";
		echo "<div class='bugnotechecks_inner_bar' style='width: $progress%;'/></div>";
		echo "</div>";
	}

	function get_check_row($bugnote_id) {
		$t_result = db_query("SELECT * FROM " . plugin_table('checks') . " WHERE bugnote_id = $bugnote_id");
		return db_fetch_array($t_result);
	}

	function create_check($bugnote_id) {
		$bug_id = bugnote_get_field($bugnote_id, 'bug_id');
		if (empty($bug_id))
			return;
		db_query("INSERT INTO " . plugin_table('checks') . " (bug_id, bugnote_id) VALUES ($bug_id, $bugnote_id)");
	}

	function drop_check($bugnote_id) {
		db_query("DELETE FROM " . plugin_table('checks') . " WHERE bugnote_id = $bugnote_id");
	}

	function note_view($p_event, $bug_id, $bugnote_id, $is_private) {
		$turn_state = false;
		$row = $this->get_check_row($bugnote_id);
		if ($row) {
			$turn_state = true;
		}

		echo "<td/>";
		echo "<div class='bugnotechecks_div_turn'>";
		$this->display_turn($bugnote_id, $bug_id, $turn_state);
		echo "</div>";
		echo "<td>";
		echo "<div class='bugnotechecks_div_check' id='bugnotechecks_div_check_$bugnote_id'>";
		if ($turn_state) {
			$this->display_check($bugnote_id, $bug_id, $row['checked'], $row['by_user_id']);
		}
		echo "</div>";
		echo "</td>";
	}

	function display_turn($bugnote_id, $bug_id, $state) {
		echo "<div class='bugnotechecks_internal_turn'>";
		$state = !!$state;
		$future_state = !$state;
		echo "<img src='" . plugin_file(($state ? 'on' : 'off') . '.png') . "' class='bugnotechecks_turn_icon'/>";
		echo "<input type='hidden' name='id' value='$bugnote_id'/>";
		echo "<input type='hidden' name='bug_id' value='$bug_id'/>";
		echo "<input type='hidden' name='state' value='$future_state'/>";
		echo "</div>";
	}

	function display_check($bugnote_id, $bug_id, $state, $by_user_id) {
		$future_state = !$state;
		echo "<div class='bugnotechecks_internal_check'>";
		echo "<img src='" . plugin_file((!$state ? 'un' : '') . 'checked.png') . "'/>";
		if ($state)
			echo " by " . prepare_user_name($by_user_id);
		echo "<input type='hidden' name='id' value='$bugnote_id'/>";
		echo "<input type='hidden' name='bug_id' value='$bug_id'/>";
		echo "<input type='hidden' name='state' value='$future_state'/>";
		echo "</div>";
	}

	function click_turn($p_event, $bugnote_id, $bug_id, $state) {
		$row = $this->get_check_row($bugnote_id);
		if (!$row) {
			$this->create_check($bugnote_id);
		} else {
			$this->drop_check($bugnote_id);
		}
		$row = $this->get_check_row($bugnote_id);
		$turn_state = !!$row;
		$this->display_turn($bugnote_id, $bug_id, $turn_state);
		if ($turn_state)
			$this->display_check($bugnote_id, $bug_id, false, null);
		$this->display_progress($p_event, $bug_id);
	}

	function click_check($p_event, $bugnote_id, $bug_id) {
		$row = $this->get_check_row($bugnote_id);
		if (!$row) {
			return;
		}
		$state = $row['checked'];
		$state = !!$state;
		if (!$state) {
			db_query("UPDATE " . plugin_table('checks') . " SET checked = true, by_user_id = " . auth_get_current_user_id()
				. " WHERE bugnote_id = $bugnote_id");
		} else {
			db_query("UPDATE " . plugin_table('checks') . " SET checked = false, by_user_id = null WHERE bugnote_id = $bugnote_id");
		}
		$this->display_check($bugnote_id, $bug_id, !$state, auth_get_current_user_id());
		$this->display_progress($p_event, $bug_id);
	}
}
