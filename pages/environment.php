<?php
if(!defined('ROOT')) exit('No direct script access allowed');

echo _js("vue");
?>
<style>
.pageComp {
	overflow:hidden;
}
.pageComp.withFixedBar .pageCompContainer {
    height: 95%;
    height: calc(100% - 42px);
}
.totalBox {
	height: 100%;
}
.col-sidebar {
	width: 20%;
    height: 100%;
    display: inline-block;
    float: left;
}
.col-textarea {
	display: inline-block;
    width: 80%;
}
.list-group-item {
	cursor: pointer;
    padding: 10px 15px;
}
.create-item {
	text-align: center;
    font-weight: bold;
    border-style: dashed;
}
</style>
<div id='environment_container' class='totalBox'>
	<div class='col-sidebar'>
		<ul id='environment_list' class='list-group'>
			<template v-if="items.length > 0">
				<template v-for="item in items">
					<li class='list-group-item' :data-refid="item" @click="loadEnvItem"><i class='fa fa-code'></i> {{ item }}</li>
				</template>
				<li class='list-group-item create-item' @click="createEnvItem">Create Environment</li>
			</template>
			<template v-else-if="items === false">
				<div class='ajaxloading ajaxloading3'></div>
			</template>
			<template v-else>
				<h4 class='text-center'>No Environments</h4>
				<li class='list-group-item create-item' @click="createEnvItem">Create Environment</li>
			</template>
		</ul>
	</div>
	<div class='col-textarea'>
		<template v-if="currentItemLoading==false">
			<template v-if="currentItem">
				<div class='toolbar' style='width:100%;height:5%;'>
					<label style="line-height: 34px;padding-left: 5px;">{{currentItem}}</label>
					<button class='btn btn-default pull-right' style="height: 100%;"@click="saveEnvItem"><i class='fa fa-save'></i></button>
				</div>
				<textarea id='environment_data' class='form-control textarea' style='width:100%;height:95%;resize:none' placeholder='{}' v-model="envData"></textarea>
			</template>
			<template v-else>
				<textarea id='environment_data' class='form-control textarea' style='width:100%;height:100%;resize:none' placeholder='Load Environment Data from Left' disabled ></textarea>
			</template>
		</template>
		<template v-else>
			<div class='ajaxloading ajaxloading3'></div>
		</template>
	</div>
</div>
<script>
$(function() {
	var vueApp = Vue.createApp({
	    data() {
	      return {
	      	currentItem: false,
	      	currentItemLoading: false,
	        items: [],
	        envData: ""
	      }
	    },
	    mounted: function() {
	    	this.getEnvList();
	    },
	    methods: {
	    	async getEnvList() {
	    		var _CURRENT_APP = this;
		      	this.items = false;
		      	processAJAXQuery(_service("apibox", "envList"), function(data) {
			    	_CURRENT_APP.items = data.Data;
				}, "json")
		    },
		    async loadEnvItem(btn) {
		    	var _CURRENT_APP = this;
		    	_CURRENT_APP.currentItem = "";
		    	_CURRENT_APP.currentItemLoading = true;
		    	processAJAXPostQuery(_service("apibox", "envData"), "env="+$(btn.target).data("refid"),function(data) {
			    	_CURRENT_APP.currentItem = $(btn.target).data("refid");
			    	_CURRENT_APP.envData = data.Data;
			    	_CURRENT_APP.currentItemLoading = false;
				}, "json")
		    },
		    async saveEnvItem(btn) {
		    	var _CURRENT_APP = this;

		    	try {
		    		JSON.parse(_CURRENT_APP.envData);

		    		processAJAXPostQuery(_service("apibox", "saveData"), "env="+_CURRENT_APP.currentItem+"&data="+_CURRENT_APP.envData,function(data) {
				    	lgksToast(data.Data.msg);
					}, "json")
		    	} catch(e) {
		    		lgksAlert(e.message);
		    	}
		    },
		    async createEnvItem(btn) {
		    	var _CURRENT_APP = this;
		    	lgksPrompt("New Environment Name (No Special characters or space allowed)?", "New Name", function(ans) {
		    		if(ans) {
		    			this.items = false;
		    			processAJAXPostQuery(_service("apibox", "envCreate"), "new_env="+ans,function(data) {
		    				if(data.Data.status=="success")
					    		_CURRENT_APP.items = data.Data.items;
					    	else {
					    		lgksToast(data.Data.msg);
					    	}
						}, "json")
		    		}
		    	});
		    }
		  }
	  }).mount('#environment_container');
});
</script>