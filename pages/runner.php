<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$slug = _slug("a/mode/refid");

if(strlen($slug['refid'])<=0) {
	echo "<h2 align=center><br>API Reference Missing</h2>";
	return;
}

loadModuleLib("apibox", "api");

$uriInfo = apibox_info($slug['refid']);
if(!$uriInfo) {
	echo "<h2 align=center><br>API Not Found</h2>";
	return;
}
// $uriInfo['method'] = "POST";

$uriID = $uriInfo['id'];


// printArray($uriInfo);
$tempHeaders = "{}";
$tempParams = "{}";
$tempBody = "{}";

if($uriInfo['headers'] && is_array($uriInfo['headers']) && isset($uriInfo['headers']['data'])) {
	$tempHeaders = json_encode($uriInfo['headers']['data'], true);
}

if($uriInfo['params'] && is_array($uriInfo['params']) && isset($uriInfo['params']['data'])) {
	$tempParams = json_encode($uriInfo['params']['data'], true);
}

if($uriInfo['body'] && is_array($uriInfo['body']) && isset($uriInfo['body']['data'])) {
	$tempBody = json_encode($uriInfo['body']['data'], true);
}

$htmlTab_Nav = "";
$htmlTab_Body = "";

$htmlBody = "";

$htmlBody .= "<div class='form-group col-md-6'>
			    <label>HEADERS *</label>
			    <textarea required name='data[\"headers\"]' class='form-control'>{$tempHeaders}</textarea>
			    <small class='form-text text-muted'>JSON object for parameters passed in HEADERS</small>
			</div>";
$htmlTab_Nav .= "<li role='presentation' class='active'><a href='#headers' aria-controls='headers' role='tab' data-toggle='tab'>Headers</a></li>";
$htmlTab_Body .= "<div role='tabpanel' class='tab-pane active' id='headers'>
				<textarea required name='data[\"headers\"]' class='form-control'>{$tempHeaders}</textarea>
			    <small class='form-text text-muted'>JSON object for parameters passed in HEADERS</small>
			</div>";


$htmlBody .= "<div class='form-group col-md-6'>
			    <label>GET Parameters *</label>
			    <textarea required name='data[\"params\"]' class='form-control'>{$tempParams}</textarea>
			    <small class='form-text text-muted'>JSON object for parameters passed in URL</small>
			</div>";
$htmlTab_Nav .= "<li role='presentation'><a href='#getparams' aria-controls='getparams' role='tab' data-toggle='tab'>Get Params</a></li>";
$htmlTab_Body .= "<div role='tabpanel' class='tab-pane' id='getparams'>
			    <textarea required name='data[\"params\"]' class='form-control'>{$tempParams}</textarea>
			    <small class='form-text text-muted'>JSON object for parameters passed in URL</small>
			</div>";


switch(strtoupper($uriInfo['method'])) {
	case 'GET':
		
		break;
	case 'POST':
		$htmlBody .= "<div class='form-group col-md-6'>
			    <label>POST Body *</label>
			    <textarea required name='data[\"body\"]' class='form-control'>{$tempBody}</textarea>
			    <small class='form-text text-muted'>JSON object for parameters passed in BODY</small>
			</div>";
		$htmlTab_Nav .= "<li role='presentation'><a href='#postparams' aria-controls='postparams' role='tab' data-toggle='tab'>Post Params</a></li>";
		$htmlTab_Body .= "<div role='tabpanel' class='tab-pane' id='postparams'>
				<textarea required name='data[\"body\"]' class='form-control'>{$tempBody}</textarea>
			    <small class='form-text text-muted'>JSON object for parameters passed in BODY</small>
			</div>";
		break;
	case 'PUT':
		$htmlBody .= "<div class='form-group col-md-6'>
			    <label>PUT Body *</label>
			    <textarea required name='data[\"body\"]' class='form-control'>{$tempBody}</textarea>
			    <small class='form-text text-muted'>JSON object for parameters passed in BODY</small>
			</div>";
		$htmlTab_Nav .= "<li role='presentation'><a href='#putparams' aria-controls='putparams' role='tab' data-toggle='tab'>Put Params</a></li>";
		$htmlTab_Body .= "<div role='tabpanel' class='tab-pane' id='putparams'>
				<textarea required name='data[\"body\"]' class='form-control'>{$tempBody}</textarea>
			    <small class='form-text text-muted'>JSON object for parameters passed in BODY</small>
			</div>";
		break;
	case 'PATCH':
		$htmlBody .= "<div class='form-group col-md-6'>
			    <label>PATCH Body *</label>
			    <textarea required name='data[\"body\"]' class='form-control'>{$tempBody}</textarea>
			    <small class='form-text text-muted'>JSON object for parameters passed in BODY</small>
			</div>";
		$htmlTab_Nav .= "<li role='presentation'><a href='#patchparams' aria-controls='patchparams' role='tab' data-toggle='tab'>Patch Params</a></li>";
		$htmlTab_Body .= "<div role='tabpanel' class='tab-pane' id='patchparams'>
				<textarea required name='data[\"body\"]' class='form-control'>{$tempBody}</textarea>
			    <small class='form-text text-muted'>JSON object for parameters passed in BODY</small>
			</div>";
		break;
	case 'DELETE':
		$htmlBody .= "<div class='form-group col-md-6'>
			    <label>DELETE Body *</label>
			    <textarea required name='data[\"body\"]' class='form-control'>{$tempBody}</textarea>
			    <small class='form-text text-muted'>JSON object for parameters passed in BODY</small>
			</div>";
		$htmlTab_Nav .= "<li role='presentation'><a href='#deleteparams' aria-controls='deleteparams' role='tab' data-toggle='tab'>Delete Params</a></li>";
		$htmlTab_Body .= "<div role='tabpanel' class='tab-pane' id='deleteparams'>
				<textarea required name='data[\"body\"]' class='form-control'>{$tempBody}</textarea>
			    <small class='form-text text-muted'>JSON object for parameters passed in BODY</small>
			</div>";
		break;
	default:
		echo "<h2 align=center><br>API Method Not Supported</h2>";
		return;
}
?>
<style type="text/css">
.cursor_pointer {
	cursor: pointer;
}
label.label_checkbox {
	margin-right: 10px;
}
pre {
    white-space: pre-wrap;
}
pre pre {
	border: 0px;
	padding: 0px;
}
</style>
<div class='container'>
	<div class='run_api_form'>
		<form>
			<input type='hidden' name='API_ID' value='<?=$uriID?>' />
			<h3 class='text-center'>API Runner</h3>
			<br>
			<div class="form-group">
			    <div class='row1'>
			    	<div class='col-md-9' style="padding: 0px;">
			    		<static class="form-control"><?=$uriInfo['title']?></static>
			    	</div>
			    	<div class='col-md-3' style="padding: 0px;">
			    		<select id='enviromentList' name='enviroment' class='form-control select'>
							<option value=''>Loading ...</option>
						</select>
			    	</div>
			    </div>
			    <small class="form-text text-muted"><?="<b>{$uriInfo['method']}</b> {$uriInfo['end_point']}{$uriInfo['subpath']}"?></small>
			</div>
			<div class="form-group">
				<label class='label_checkbox'><input type="checkbox" name='debug_api' value='true' /> Debug API</label>
				<label class='label_checkbox'><input type="checkbox" name='show_mock' value='true' /> Show Mock</label>
			</div>
			<div class='row'>
				<div class='col-md-12'>
					<ul class="nav nav-tabs" role="tablist">
						<?=$htmlTab_Nav?>
						
					</ul>
					<div class="tab-content">
						<?=$htmlTab_Body?>
					</div>
				 </div>
			</div>
			<hr>
			<div class='text-center'>
				<button type="reset" class="btn btn-danger">Reset</button>
				<button type="button" class="btn btn-success btn-run">RUN</button>
			</div>
		</form>
		<pre id='responseBody' class='hidden d-none'></pre>
	</div>
</div>
<script>
$(function() {
	processAJAXQuery(_service("apibox", "envList"), function(data) {
		$("#enviromentList").html("");
    	$.each(data.Data, function(a,b) {
    		$("#enviromentList").append(`<option value="${b}">${b}</option>`);
    	})
	}, "json")
	
	$(".btn-run").click(runAPI);
});
function runAPI() {
	$("#responseBody").removeClass("hidden").removeClass("d-none");
	$("#responseBody").html("");

	var allOK = true;
	$("textarea").each(function() {
	    try {
	        $(this).val(JSON.stringify(JSON.parse($(this).val()), null, "\t"));
	    } catch(e) {
	    	allOK = false;
	        $("#responseBody").append("Error parsing data for - "+$(this).closest(".form-group").find("label").text()+"\n");
	    }
	});
	if(!allOK) {
		return;
	}

	$("#responseBody").html("<div class='ajaxloading ajaxloading5'>Processing ...</div>");
	processAJAXPostQuery(_service("apibox", "runAPI"), $("form").serialize(), function(responseData) {
		$("#responseBody").html(responseData);
	}, function() {
		$("#responseBody").html("<h3 align=center>Error while running API</h3>");
	});
}
function editActiveAPI() {
	window.location = _link("modules/forms/apibox.editor/edit/<?=$uriID?>");
}
</script>