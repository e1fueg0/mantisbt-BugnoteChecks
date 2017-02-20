<?php
echo event_signal('EVENT_BUGNOTECHECKS_CLICK_CHECK', $_POST["id"], $_POST["state"]);
