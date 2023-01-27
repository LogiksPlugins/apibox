<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$slug = _slug("a/page/b/c");

loadModule("pages");

function pageSidebar() {
	return "XXX";
}

printPageComponent(false,[
	"BASE_DIR"=>dirname(__DIR__),
    "toolbar"=>[
		"reloadLists"=>["title"=>"","align"=>"left","icon"=>"<i class='fa fa-refresh'></i>"],
		"newAPI"=>["title"=>"New API","align"=>"left","icon"=>"<i class='fa fa-plus'></i>", "class"=>(in_array($slug['page'], ["main", ""])?"":"hidden")],
		"exportAPI"=>["title"=>"Export","align"=>"left","icon"=>"<i class='gg gg-export'></i>", "class"=>(in_array($slug['page'], ["main", ""])?"":"hidden")],
		"importAPI"=>["title"=>"Import","align"=>"left","icon"=>"<i class='gg gg-import'></i>", "class"=>(in_array($slug['page'], ["main", ""])?"":"hidden")],

		"goHome"=>["title"=>"","align"=>"left","icon"=>"<i class='fa fa-chevron-left'></i>", "class"=>(in_array($slug['page'], ["runner"])?"":"hidden")],
		"editActiveAPI"=>["title"=>"Edit","align"=>"left","icon"=>"<i class='fa fa-pencil fa-pencil-alt'></i>", "class"=>(in_array($slug['page'], ["runner"])?"":"hidden")],

		"loadManagerComponent"=>["title"=>"API List","align"=>"right", "class"=>(($slug['page']=="main" || $slug['page']=="")?"active":"")],
		"loadEnvironmentComponent"=>["title"=>"Environments","align"=>"right", "class"=>($slug['page']=="environment"?"active":"")],

		//["title"=>"Search Group","type"=>"search","align"=>"right"]
		//"listContent"=>["icon"=>"<i class='fa fa-refresh'></i>"],
		//"createContent"=>["icon"=>"<i class='fa fa-plus'></i>","tips"=>"Create New"],
		//"preview"=>["icon"=>"<i class='fa fa-eye'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Preview Content"],
		//['type'=>"bar"],
		//"rename"=>["icon"=>"<i class='fa fa-terminal'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Rename Content"],
		//"deleteContent"=>["icon"=>"<i class='fa fa-trash'></i>","class"=>"onsidebarSelect"],
    ],
    // "sidebar"=>"pageSidebar",
    // "contentArea"=>"pageContentArea",
    "contentArea"=>"pageContentDefault",
  ]);
?>
<script>
function reloadLists() {
	window.location.reload();
}
function goHome() {
	window.location = _link("modules/apibox");
}
function newAPI() {
	window.location = _link("modules/apibox/creator");
}
function loadManagerComponent() {
	window.location = _link("modules/apibox");
}
function loadEnvironmentComponent() {
	window.location = _link("modules/apibox/environment");
}
function exportAPI() {
	lgksConfirm("Do you want to export all API current system table to .install folder, this will overwrite existing file?", "Export API", function(ans) {
		if(ans) {
			waitingDialog.show();
			processAJAXQuery(_service("apibox", "exportAPI"), function(data) {
				waitingDialog.hide();
				lgksAlert(data.Data);
			}, "json");
		}
	});
	
}
function importAPI() {
	lgksMsg("What type of import would you want to do?<br><br><citie class='text-muted'>Importing api will search and import <b>apibox.json</b> files from across app and plugins.</citie>", "Import API", {
		    buttons: {
		        confirm1: {
		            label: 'Truncate Existing',
		            className: 'btn-success',
		            callback: function (result) {
		                waitingDialog.show();
						processAJAXQuery(_service("apibox", "importAPITruncate"), function(data) {
							waitingDialog.hide();
							LGKSReportsInstances[Object.keys(LGKSReportsInstances)[0]].reloadDataGrid();
							lgksToas(data.Data);
						}, "json");
		            }
		        },
		        confirm2: {
		            label: 'Import Directly',
		            className: 'btn-success',
		            callback: function (result) {
		                waitingDialog.show();
						processAJAXQuery(_service("apibox", "importAPI"), function(data) {
							waitingDialog.hide();
							LGKSReportsInstances[Object.keys(LGKSReportsInstances)[0]].reloadDataGrid();
							lgksToas(data.Data);
						}, "json");
		            }
		        },
		        cancel: {
		            label: 'Cancel',
		            className: 'btn-danger'
		        }
			}
		})
}
</script>