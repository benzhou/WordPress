(function (factory) {
    'use strict';
    if (typeof define === 'function' && define.amd) {
        // Register as an anonymous AMD module:
        define([
            'jquery',
            'jquery.ui.widget'
        ], factory);
    } else {
        // Browser globals:
        factory(window.jQuery);
    }
}(function($){
	$.widget("triton.digitalIvy", {
		version : "0.0.1",
		options:{
			verbose : true,//false,
			api: {
	            url: "localhost:50812",
	            forceHttps: false,
	            methods: {
	                getContestList: "/Home/GetContestList"
	            }
        	}
		},

		_create : function(){
			var options = this.options;
			this._log("widget created!");
		},

		_distroy : function(){
			this._log("Widget distroyed!");
		},

		_api : function(options){

		},

		/*
			logger utility: only logs when verbose mode is on and console object exists
		*/
		_log : function(str){
			console.log(this.options.verbose && console !== undefined && console.log !== undefined && typeof console.log.constructor === Function);
			if(this.options.verbose && console !== undefined && console.log !== undefined && typeof console.log.constructor === Function){
				console.log(str);
			}
		}
	});
}));