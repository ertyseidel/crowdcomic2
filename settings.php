<?php

	$settings = [
		'site_title' => "Lost in Space",
	];

	function setting($s) {
		global $settings;
		echo(isset($settings[$s]) ? $settings[$s] : '');
	}
