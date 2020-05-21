<?php 

	function has_post() {
		if (count($_POST) > 0) {
			return true;
		}

		return false;
	}

	function verify_null($value) {
		if (is_null($value)) {
			return 'class="error"';
		}

		return '';
	}

	function love($value) {
		if ($value == 1) {
			return 'checked="checked"';
		}

		return '';
	}

	function hate($value) {
		if ($value == 2) {
			return 'checked="checked"';
		}

		return '';
	}
