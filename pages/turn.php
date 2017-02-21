<?php
echo event_signal('EVENT_BUGNOTECHECKS_CLICK_TURN', array($_POST["id"], $_POST["bug_id"], $_POST["state"]));
