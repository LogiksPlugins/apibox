$(function() {

});
function cloneAPI(row) {
	var refid = $(row).data("refid");
	
	lgksConfirm("Do you want to clone the selected API", "Clone API", function(ans) {
    	if(ans) {
    		processAJAXPostQuery(_service("apibox", "cloneAPI"), "API_ID="+refid, function(responseData) {
				if(responseData.Data) {
					lgksToast("Cloning Completed");
				} else {
					lgksToast("Cloning Failed");
				}

				LGKSReports.getInstance(Object.keys(LGKSReportsInstances)[0]).reloadDataGrid();
			}, "json");
	    }
	});
}

function codeSample(row) {
	var refid = $(row).data("refid");
	
	// processAJAXPostQuery(_service("apibox", "codeSample"), "API_ID="+refid, function(responseData) {
		
	// });

	lgksAlert(`<pre style=''>loadModule("apibox");\n\n$response = apibox_run(${refid}, $getParameters, $payload);</pre>`, "Code Preview");
}