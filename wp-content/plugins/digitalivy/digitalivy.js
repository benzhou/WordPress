var digitalIvy = function ($) {
    var config = {
        debug: false,
        rootId: "#diRoot",
        apiKey: "dev",
        app: {
            apiKey: "",
            authToken: ""
        },
        api: {
            url: "localhost:50812",
            forceHttps: false,
            methods: {
                getContestList: "/Home/GetContestList"
            }
        }
    };
    var inited = false;

    var init = function (opt) {
        config = digitalIvy.utils.merge(config, opt);
        return true;
    },

		user = function () {
		    var profile = {
		        authToken: ""
		    };

		    var loadAuthToken = function (token) {
		        profile.authToken = token;
		    };

		    var setAuthToken = function () {
		        return profile.authToken;
		    }

		    return {
		        loadAuthToken: loadAuthToken,
		        setAuthToken: setAuthToken
		    };
		} (),

		api = function () {
		    var prependHost = function (path) {
		        if (config.api.url == "") {
		            return path;
		        } else {
		            return [(config.api.forceHttps || 'https:' == document.location.protocol ? 'https://' : ''), config.api.url, path].join('');
		        }
		    };

		    var cache = new Cache();

		    var listContests = function (opt, cb, failCb) {
		        if (!opt || !opt.org) {
		            var em = "listContests cannot find opt param";
		            digitalIvy.utils.logger(em);
		            if (failCb && failCb.constructor == Function) { failCb.apply(this, [em]); }
		        }

		        //Load from cache first
		        var state = opt.contestState || "current",
		            cacheKey = ["listContests_", opt.org, "_", state.toLowerCase()].join(""),
		            cachedItem = cache.getItem(cacheKey),
		            matchStr = opt.matchStr || "",
		            page = opt.page || 1,
		            pageSize = opt.pageSize || 10,
		            matchStrRegEx = new RegExp(digitalIvy.utils.regExpEscape(matchStr.toLowerCase()), "gi");

		        digitalIvy.utils.logger("cached item for contestList: ");
		        digitalIvy.utils.logger(cachedItem);
		        //digitalIvy.utils.logger(opt.forceReload || cachedItem !== null);

		        if (opt.forceReload || cachedItem == null) {
		            var uri = prependHost([config.api.methods.getContestList, '/', state, '/', config.filterId, '/', config.contestListType, config.api.url !== (document.location.protocol + "//" + document.location.host) ? "?callback=?" : ""].join(''));
		            $.ajax({
		                type: "GET",
		                dataType: "json",
		                cache: false,
		                url: uri,
		                success: function (obj) {
		                    var t = $.isArray(obj) ? obj : [];
		                    cache.setItem(cacheKey, t, { expirationSliding: 60 });

		                    if (cb && cb.constructor == Function) {
		                        cb.apply(this, [digitalIvy.utils.pager(matchStr.length > 0 ? digitalIvy.utils.searcher(t, function (list, i, item) {
		                            //return item.ContestName.toLowerCase().indexOf(matchStr) > -1;
		                            return matchStrRegEx.test(item.ContestName);
		                        }) : t, page, pageSize), t]);
		                    }
		                }
		            });

		            return;
		        }

		        if (cb && cb.constructor == Function) {
		            cb.apply(this, [digitalIvy.utils.pager(matchStr.length > 0 ? digitalIvy.utils.searcher(cachedItem, function (list, i, item) {
		                digitalIvy.utils.logger("item.ContestName: " + item.ContestName + " matchStr exists : " + item.ContestName.indexOf(matchStr));
		                return matchStrRegEx.test(item.ContestName);
		            }) : cachedItem, page, pageSize), cachedItem]);
		        }
		    };

		    var loadContest = function (opt) {
		        $.ajax({
		            type: "GET",
		            cache: false,
		            url: "",
		            dataType: _opts.dataType,
		            success: function (obj) {

		            }
		        });
		    };

		    return {
		        listContests: listContests
		    };
		} ();

    utils = function () {
        var _logger = function (msg) {
            if (config.debug) { console.log(msg); }
        };

        var _merge = function (original, update) {
            var value = '';
            for (var key in update) {
                if (update.hasOwnProperty(key)) {
                    value = ((!!update[key]) && update[key].constructor == String) ? update[key].replace(/<[^>]*>/g, '') : update[key];
                    original[key] = value;
                }
            }
            return original;
        };

        var _listener = function () {
            /**
            * @param {Object} evts the container for all event listeners
            * @private
            */
            var evts = {};

            /**
            * listen - subscribe to events
            * @param {String} evtName the name to be listening for
            * @param {Function} method the function to be fired
            * @param {Object} scope the value for the 'this' object inside the function
            */
            var _listen = function (evtName, method, scope) {
                if (method && method.constructor == Function) {
                    evts[evtName] = evts[evtName] || [];
                    evts[evtName][evts[evtName].length] = { method: method, scope: scope };
                }
            };

            /**
            * fire - call all the event handlers
            * @param {String} evtName the name of the event listner to invoke
            * @param {Boolean} remove Whether or not to unsubscribe all listeners
            */
            var _fire = function (evtName, args) {
                args = args || [];
                if (evts.hasOwnProperty(evtName)) {
                    var evt = evts[evtName].reverse();
                    for (var i = evt.length; i--; ) {
                        try {
                            evt[i].method.apply(evt[i].scope || [], (args.data || []));
                        } catch (e) {
                            if (args && args.data && args.data.push) {
                                evt[i].method(args.data[0]);
                            } else {
                                evt[i].method();
                            }
                        }
                    }
                    if (args.remove) { kill(evtName); }
                }
            };

            var _listening = function (evtName) {
                return !!evts[evtName];
            };

            var _kill = function (evtName) {
                if (evts.hasOwnProperty(evtName)) { delete evts[evtName]; }
            };

            // return the public functions
            return { listen: _listen, fire: _fire, kill: _kill, listening: _listening };
        } ();

        var _getLocationHash = function (loc) {
            var l = loc || window.location.hash || "";
            return l.replace(new RegExp("^[#/]+|/$", "g"), "");
        };

        var _getURLParameter = function (name) {
            return decodeURI(
                (RegExp(name + '=' + '(.+?)(&|$)').exec(location.search) || [, null])[1]
            );
        };

        var searcher = function (list, match) {
            digitalIvy.utils.logger("searcher list length: " + list.length);
            if (!$.isArray(list)) return [];
            if (!match || (match.length && match.length == 0)) return list;

            var result = [];
            $.each(list, function (i, item) {
                if (match.constructor == Function) {
                    if (match(list, i, item)) result.push(item);
                } else {
                    if (item.indexOf(match) > 0) result.push(item);
                }

            });

            return result;
        };

        var pager = function (list, pageNum, pageSize) {
            if (!$.isArray(list) || list.length == 0 || pageNum < 1 || pageSize < 1) return [];
            digitalIvy.utils.logger("pager list length: " + list.length);
            //digitalIvy.utils.logger(list);
            var startIndex = (pageNum - 1) * pageSize;
            digitalIvy.utils.logger("pager page number: " + pageNum + " startIndex: " + startIndex + " pageSize: " + pageSize);
            digitalIvy.utils.logger("pager sliced: ");
            digitalIvy.utils.logger(list.slice(startIndex, startIndex + pageSize));
            return list.slice(startIndex, startIndex + pageSize);
        };

        regExpEscape = function (text) {
            return text.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&");
        }

        return {
            merge: _merge, listener: _listener, logger: _logger, getURLParameter: _getURLParameter, getLocationHash: _getLocationHash, pager: pager, searcher: searcher, regExpEscape: regExpEscape
        };
    } ();

    return {
        init: init,
        user: user,
        api: api,
        utils: utils,
        config: config
    };
} (jQuery);

digitalIvy.listApp = function ($) {
    var config = {
        locMonitor: {
            monitorInterval: 150
        },
        contestLinkTarget: "_blank",
        disablePaging: false,
        labels: {
            ListEnter: "Enter",
            ListView: "View Contest",
            sweepCurrentListEnd: "Ends in {0} days",
            sweepCurrentListEndToday: "Ends today",
            ugcCurrentSubmissionEnd: "Submissions end in {0} days",
            ugcCurrentSubmissionEndToday: "Submissions end today",
            ugcCurrentVotingEnd: "Voting ends in {0} days",
            ugcCurrentVotingEndToday: "Voting ends today",
            contestUpcoming: "This contest starts on {0}",
            contestClosed: "This contest has closed",
            stateC: "Current",
            stateU: "Upcoming",
            stateCl: "Closed",
            searchPh: "Search for Contest",
            featured: "Featured Contests",
            EMPTYUPCOMINGLIST: "Currently, there are no upcoming contests scheduled. Please check back soon.",
            EMPTYCURRENTLIST: "Currently, there are no active contests. Please check back soon.",
            EMPTYEXPIREDLIST: "No contests have ended in the last 30 days.",
            headers: {
                current: "Currently Active Contests",
                upcoming: "Upcoming Contests",
                closed: "Expired Contests"
            }
        }
    };
    var routes = [];
    var controllers = [];
    var views = {};

    var run = function (opt) {
        $.extend(config, opt);
        digitalIvy.init(config);
        locMonitor.start();
    };

    var Controller = function () { };

    Controller.prototype = {
        addRoute: function (routeList, handler) {
            if (!$.isArray(routeList)) routeList = [routeList];
            $.each(routeList, function (i, route) {
                digitalIvy.utils.logger("addRoute called");
                digitalIvy.utils.logger("route: ");
                digitalIvy.utils.logger(route);

                route = digitalIvy.utils.getLocationHash(route);
                var params = [];
                var pattern = route.replace(
						new RegExp("(/):([^/]+)", "gi"),
						function ($0, $1, $2) {
						    digitalIvy.utils.logger("route pattern: $0:" + $0 + " $1:" + $1 + " $2:" + $2);
						    // Add the named parameter.
						    params.push($2);

						    // Replace with a capturing group. This captured group will be used
						    // to create a named parameter if this route gets matched.
						    return ($1 + "([^/]+)");
						}
					);

                routes.push({
                    controller: this,
                    params: params,
                    test: new RegExp(("^" + pattern + "$"), "i"),
                    handler: handler
                });
            });
        },
        appConfig: config
    };

    var addController = function (controller) {
        controllers.push(controller);
    };

    var addView = function (view) {
        var constructor = view.constructor;
        var className = constructor.toString().match(new RegExp("^function\\s+([^\\s\\(]+)", "i"))[1];
        views[className] = view;
    };

    var getView = function (viewName) {
        return views[viewName];
    };

    var locMonitor = function () {
        var locationMonitorInterval = null;
        var currentLoc = null;

        var start = function () {
            digitalIvy.utils.logger("locMonitor started.");

            if (!digitalIvy.utils.listener.listening("diLocChanged"))
                digitalIvy.utils.listener.listen("diLocChanged", onLocChange);

            locationMonitorInterval = setInterval(function () {
                var liveLoc = digitalIvy.utils.getLocationHash();
                if (currentLoc == null || currentLoc !== liveLoc) {
                    digitalIvy.utils.logger("loc changed, current: " + currentLoc + " live: " + liveLoc);
                    setLoc(liveLoc);
                }
            }, config.locMonitor.monitorInterval);
        };

        var stop = function () {
            digitalIvy.utils.logger("locMonitor stopped.");
            clearInterval(locationMonitorInterval);
        };

        var setLoc = function (loc, parameters) {
            loc = digitalIvy.utils.getLocationHash(loc);
            var previousLoc = currentLoc;
            currentLoc = loc;
            window.location.hash = ("#/" + loc);
            digitalIvy.utils.listener.fire("diLocChanged", { "data": [{ "fromLoc": previousLoc, "toLoc": currentLoc, "params": parameters}] });
        };

        var onLocChange = function (onLocChangeEvent) {
            digitalIvy.utils.logger("locMonitor onLocChanged. here is the onLocChangeEvent object");
            digitalIvy.utils.logger(onLocChangeEvent);

            //This value is used to provide a mechanism to stop the routing routing below.
            var keepRouting = true;

            var routeFound = false;

            var eventContext = {
                application: self,
                fromLoc: onLocChangeEvent.fromLoc,
                toLoc: onLocChangeEvent.toLoc,
                params: $.extend({}, onLocChangeEvent.params)
            };

            //stop loc monitoring while we are processing. preventing someone click really fast
            stop();

            $.each(routes, function (i, route) {
                if (!keepRouting)
                    return;

                var matches = null;

                if (matches = onLocChangeEvent.toLoc.match(route.test)) {
                    matches.shift();

                    $.each(
							matches,
							function (index, value) {
							    eventContext.params[route.params[index]] = value;
							}
						);

                    var result = route.handler.apply(
							route.controller,
							[eventContext].concat(matches)
						);

                    if (
							(typeof (result) == "boolean") &&
							!result) {
                        // Cancel routing.
                        keepRouting = false;
                    }

                    // Flag that a route was found.
                    routeFound = true;
                }
            });

            //restart once it is processed.
            start();
        };

        return { start: start };
    } ();

    return {
        run: run,
        Controller: Controller,
        addController: addController,
        addView: addView,
        getView: getView
    };
} (jQuery);