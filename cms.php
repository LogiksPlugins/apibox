<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(_db()) {
	if(in_array("apibox_tbl", _db()->get_tableList())) {

		$slug = _slug("a/mode/refid");

		switch($slug['mode']) {
			case "runAPI":
				include_once __DIR__."/pages/run_api.php";
			break;
			// case "codeSample":
			// 	include_once __DIR__."/pages/code_sample.php";
			// break;
			default:
				header("Location:"._link("modules/reports/apibox.main"));
		}
	} else {
		echo "<h2 align=center><br><br>Plugin is not properly installed, try installing the plugin again.</h2>";
	}
} else {
	echo "<h2 align=center><br><br>Database Connection Not Found</h2>";
}
?>