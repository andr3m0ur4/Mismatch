<?php 

	function has_post() {
		if (count($_POST) > 0) {
			return true;
		}

		return false;
	}
