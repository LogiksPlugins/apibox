<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(_db()) {
	if(in_array("apibox_tbl", _db()->get_tableList())) {

		$slug = _slug("a/mode/refid");

		include_once __DIR__."/pages/home.php";
	} else {
		echo "<h2 align=center><br><br>Plugin is not properly installed, try installing the plugin again.</h2>";
	}
} else {
	echo "<h2 align=center><br><br>Database Connection Not Found</h2>";
}
?>