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
			this._utils.log("widget created!");
		},

		_distroy : function(){

		},

		_api : function(options){},

		_utils : {
			/*
				logger utility: only logs when verbose mode is on and console object exists
			*/
			log : function(str){
				if(this.options.verbose && console !== undefined && typeof console.constructor === Function){
					console.log(str);
				}
			}
		}
	});
}));