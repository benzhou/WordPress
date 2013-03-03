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
}(function($, param2){
	$.widget("triton.digitalIvy", {
		version : "0.0.1",
		options:{
			verbose : true,//false,
			api: {
	            url: "http://dev4sanban.test.listenernetwork.net",
	            forceHttps: false,
	            methods: {
	                getContestList: "/Home/GetContestList"
	            }
        	}
		},

		_create : function(){
			var opt = this.options;
			this._log("_create: widget created!");
			this._log(param2);

			this._log("_create: this.element:");
			this._log(this.element);

			this._log("_create: Start to request data, call _getDIContestList method...");
			this._getDIContestList({
				orgCode : "TD"
			});
		},

		_setOption : function( key, value ){
			this._log("_setOption: _setOption called.");
			this._super( "_setOption", key, value );
		},

		_distroy : function(){
			this._log("_distroy: Widget distroyed!");
		},

		_getDIContestList : function(options){
			var orgCode = options.orgCode,
				state = options.state || "current",
				listType = options.listType || 0,
				xdomain = this._isXDomain(),
				uri  = this._getApiUrl("getContestList") + "/" + state + "/" + orgCode + "/" + listType + xdomain ? "?callback=?" : "",
				promise = this._api({
					url : uri
				});

				this._log("_getDIContestList: Is this call corss domain? answer: " + xdomain);

				return promise;
		},

		_api : function(options){
			if(!options){
				this._log("_api call, no options passed in.");
				return null;
			}

			var type = options.type || "GET",
				dataType = options.dataType || "json",
				cache = options.cache || false,
				opt = this.options,
				url =  options.url;

			this._log("_api: url:" + url);
			return $.ajax({
				type: type,
                dataType: dataType,
                cache: cache,
                url: url	
			});
		},
		_getApiUrl : function(method){
			var opt = this.options,
				protocol = opt.api.forceHttps? "https:" : "http:",
				cleanedApiHost = this._cleanApiHost()
				uri = protocol + "//" + cleanedApiHost + opt.api.methods[method];

				return uri;
		},
		_cleanApiHost : function(){
			var self = this;
			return this.options.api.url.replace(/((?:ht|f)tp:\/\/)?([^:\/\s]+\w+\.(?:com|net|org))/gi, function(_, protocol, rest){
				self._log("protocol: " + protocol);
				self._log("rest: " + rest);
				self._log("_ : " + _);
				return rest;
			});
		},
		_isXDomain : function(){
			if(this.options.api.forceHttps && document.location.protocol !== "https:"){
				this._log("_isXDomain : Forced to use Https, but currently on a non-secured page");
				return true;
			}

			if(!this.options.api.forceHttps && document.location.protocol !== "http:"){
				this._log("_isXDomain : currently on a secured page, but API call will be make using http.");
				return true;
			}

			this._log("Current host of the page:");
			this._log(document.location.host);
			this._log("Clean up api call host:");
			var opt = this.options,
			cleanApiHost = this._cleanApiHost();

			this._log("_isXDomain: Original api host: " + opt.api.url);
			this._log("_isXDomain: Cleaned api Host: " + cleanApiHost);

			return document.location.host !== cleanApiHost;
		},

		/*
			logger utility: only logs when verbose mode is on and console object exists
		*/
		_log : function(str, ignoreVerboseFlag){
			if((this.options.verbose || ignoreVerboseFlag) && console !== undefined && console.log !== undefined && console.log.constructor === Function){
				console.log(str);
			}
		}
	});
}));