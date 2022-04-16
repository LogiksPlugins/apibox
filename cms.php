<?php
if(!defined('ROOT')) exit('No direct script access allowed');

loadModule("pages");

function pageContentArea() {//Change Log Since : <span id='dated'>".date("d/m/Y H:i:s")."</span>
    return "<div class='container table-responsive'>
    <h2 id='changeLogTitle'></h2>
    <ul id='changeLogBody' class='list-group'>
      
    </ul>
</div>";
}

//echo _css(["bootstrap.datetimepicker","apibox"]);
//echo _js(["moment","bootstrap.datetimepicker","apibox"]);

printPageComponent(false,[
		"toolbar"=>[
			"reloadPage"=>["icon"=>"<i class='fa fa-refresh'></i>"],
// 			"getChangeLog"=>["icon"=>"<i class='fa fa-eye'></i>","align"=>"right","title"=>"Fetch"],
			
// 			"getPatches"=>["icon"=>"<i class='fa fa-list'></i>","align"=>"left","title"=>"Patches"],
			
// 			"downloadChangeLog"=>["icon"=>"<i class='fa fa-download'></i>","class"=>"hidden","title"=>"Download"],
		],
		"sidebar"=>false,
		"contentArea"=>"pageContentArea"
	]);

?>
<script>
$(function() {
    
});
function reloadPage() {
    window.location.reload();
}
</script>