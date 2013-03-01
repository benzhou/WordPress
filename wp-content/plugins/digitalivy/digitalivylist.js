digitalIvy.listApp.addView(function ($) {
    function listView() {
        this.config = {
            append: false
        };
        this.stateBarInitilized = false;
    };

    listView.prototype.init = function (opt) {
        opt = opt || {};
        digitalIvy.utils.logger("this in listView: ");
        digitalIvy.utils.logger(this);
        digitalIvy.utils.merge(this.config, opt);

        //Only initialize status bar once.
        //1. when search, we don't want to loose the text user typed.
        if (!this.stateBarInitilized) {
            this.renderStateBar(opt.listType);

            $("#diSearch").bind("keyup search", function (event) {
                digitalIvy.utils.logger("diSearch Keyup!, event:");
                digitalIvy.utils.logger(event);
                digitalIvy.utils.listener.fire("DI_SEARCH", { "data": [{ "matchStr": event.target.value}] });
            });
        }

        this.renderItems(opt.list, opt.allList, opt.listType, opt.contestLinkTarget);
    };

    listView.prototype.renderStateBar = function (listType) {
        listType = listType || 1;

        $("#container-contestStates").html([
            '<ul class="contest-sorting clearfix">',
            '<li data-loc="current" class="', listType === 1 ? 'active' : '', '"><a href="#/current">', this.config.labels.stateC, '</a></li>',
            '<li data-loc="upcoming" class="', listType === 2 ? 'active' : '', '"><a href="#/upcoming">', this.config.labels.stateU, '</a></li>',
            '<li class="last" data-loc="closed" class="', listType === 3 ? 'active' : '', '"><a href="#/closed">', this.config.labels.stateCl, '</a></li>',
            '</ul>',
            '<div class="contest-search-container">',
            '<input id="diSearch" class="contest-search" type="search" placeholder="', this.config.labels.searchPh, '">',
            '</div>',
            '<br class="clear">'
        ].join(''));

        this.stateBarInitilized = true;
    };

    listView.prototype.renderItems = function (list, allList, listType, contestLinkTarget) {
        var items = "",
            featuredItems = "",
            targetText = "";
        contestLinkTarget = contestLinkTarget || "";
        targetText = contestLinkTarget ? 'target=" + contestLinkTarget + "' : '';

        for (var i in allList) {
            //featuredItems += ['<li><a href="#"><img src="/Content/images/loading.gif" class="reflect" /></a></li>'].join('');
            if (allList[i].FeaturedInCarousel) {
                featuredItems += ['<li><a href="', allList[i].ContestUrl, '" ', targetText, '><img src="', allList[i].ThumbnailImage, '" class="reflect" /></a></li>'].join('');
            }
        }

        if (featuredItems.length > 0) {
            $("#featured-contest-items").html(['<h1>', this.config.labels.featured, '</h1>',
                '<div id="carousel" class="es-carousel-wrapper">',
                '<div class="es-carousel">',
                '<ul>',
                featuredItems,
                '</ul>',
                '</div>',
                '</div>'].join('')).fadeIn();

            $('#carousel').elastislide({
                imageW: 100,
                easing: 'easeInBack',
                onClick: function () {
                    return true;
                }
            });

            $("#carousel img").reflect();
        } else {
            $("#featured-contest-items").fadeOut().html("");
        }

        for (var item in list) {
            var lb = "", itm = list[item];
            switch (listType) {
                case 1:
                    if (itm.ContestType == 1) {
                        lb = itm.VotingStarted
                            ? itm.DaysTilVotingEnd == 0 ? this.config.labels.ugcCurrentVotingEndToday : this.config.labels.ugcCurrentVotingEnd.replace(/\{0\}/gi, itm.DaysTilVotingEnd)
                            : itm.DaysTilSubmissionEnd == 0 ? this.config.labels.ugcCurrentSubmissionEndToday : this.config.labels.ugcCurrentSubmissionEnd.replace(/\{0\}/gi, itm.DaysTilSubmissionEnd);
                    } else {
                        lb = itm.DaysTilContestEnd == 0 ? this.config.labels.sweepCurrentListEndToday : this.config.labels.sweepCurrentListEnd.replace(/\{0\}/gi, itm.DaysTilContestEnd);
                    }
                    break;
                case 2:
                    lb = this.config.labels.contestUpcoming.replace(/\{0\}/gi, (new Date(parseInt(itm.BeginDate.substr(6)))).toLocaleDateString());
                    break;
                case 3:
                    lb = this.config.labels.contestClosed;
                    break;
            }


            var itmHref = "";
            itmHref = '<a href="' + itm.ContestUrl + '" class="contest-enterbutton" ' + targetText + '>';

            items += ['<ul><li>',
                '<div class="contest-item">',
                '<div class="container-header"><h1>', itm.ContestName, '</h1></div>',
                '<div class="contest-contentcontainer clearfix">',
                '<div class="contest-thumb-container">',
                '<img class="contest-thumb" src="', itm.ThumbnailImage, '" />',
                '</div>',
                '<div class="contest-info clearfix">',
                '<p class="contest-description">', itm.MetaDescription, '</p>',
                '<p class="contest-dates"><span class="contest-icon-clock"></span>', lb, '</p>', itmHref,
                (listType === 1 ? this.config.labels.ListEnter : this.config.labels.ListView), '</a>',
                '</div>',
                '</div>',
                '</li></ul>'].join('');
        }

        if (list.length === 0) {
            switch (listType) {
                case 1:
                    items = '<ul><li><div class="contest-item-empty"><p class="contest-description">' + this.config.labels.EMPTYCURRENTLIST + '</p></div></li></ul>';
                    break;
                case 2:
                    items = '<ul><li><div class="contest-item-empty"><p class="contest-description">' + this.config.labels.EMPTYUPCOMINGLIST + '</p></div></li></ul>';
                    break;
                case 3:
                    items = '<ul><li><div class="contest-item-empty"><p class="contest-description">' + this.config.labels.EMPTYEXPIREDLIST + '</p></div></li></ul>';
                    break;
            }
        }


        if (this.config.append) {
            $("#container-contestListItem").append(items).fadeIn('slow');
        } else {
            $("#container-contestListItem").html(items).fadeIn('slow');
        }
    };

    return (new listView());
} (jQuery));

digitalIvy.listApp.addController((function ($) {
    var controller = function () {
        var listView = digitalIvy.listApp.getView("listView"),
            self = this,
            nextPage = 1;

        this.addRoute(["/", "/current/"], function (e) {
            digitalIvy.utils.logger("*****************list current view, next line is this keyword in current context");
            digitalIvy.utils.logger(this);
            digitalIvy.utils.logger("*****************next line is self in current context");
            digitalIvy.utils.logger(self);
            digitalIvy.utils.logger("*****************next line is event context");
            digitalIvy.utils.logger(e);
            digitalIvy.utils.logger("*****************next line is self.appConfig");
            digitalIvy.utils.logger(self.appConfig);

            nextPage = 1;
            var searchedList = false;

            if (!self.appConfig.disablePaging) {
                $(document).unbind('scroll');
                $(document).bind('scroll', function () {
                    //If the list is searched list, do nothing
                    if (searchedList) return;

                    var closeToBottom = ($(window).scrollTop() + $(window).height() > $(document).height() - 100);
                    if (closeToBottom) {
                        digitalIvy.utils.logger("close to Button now! nextPageNumber: " + nextPage);
                        digitalIvy.api.listContests({ "org": self.appConfig.org, "contestState": "current", "page": nextPage }, function (data, allList) {
                            if (data.length > 0) {
                                listView.init({ "append": true, "list": data, "allList": allList, "labels": self.appConfig.labels, "listType": 1 });
                                nextPage += 1;
                            } else {
                                $(document).unbind('scroll');
                            }
                        });
                    }
                });
            }

            var l = digitalIvy.utils.listener;
            if (l.listening("DI_SEARCH")) l.kill("DI_SEARCH");

            l.listen("DI_SEARCH", function (event) {
                nextPage = 1;

                searchedList = event.matchStr && event.matchStr.length > 0;

                digitalIvy.utils.logger("SSSSSSSSSearch started! searchFor: " + event.matchStr);
                digitalIvy.api.listContests({ "org": self.appConfig.org, "contestState": "current", "page": nextPage, matchStr: event.matchStr }, function (data, allList) {
                    listView.init({ "append": false, "list": data, "allList": allList, "labels": self.appConfig.labels, "listType": 1 });
                    nextPage += 1;
                });
            });

            $("#container-list-header h1").text(self.appConfig.labels.headers.current);

            digitalIvy.api.listContests({ "org": self.appConfig.org, "contestState": "current", "page": nextPage }, function (data, allList) {
                digitalIvy.utils.logger("controller route handler going to initialize listView!");
                listView.init({ "append": false, "list": self.appConfig.disablePaging ? allList : data, "allList": allList, "labels": self.appConfig.labels, "listType": 1, "contestLinkTarget": self.appConfig.contestLinkTarget });
                digitalIvy.utils.logger("controller route handler going to increase next page number! nextPageNumber: " + nextPage);
                nextPage += 1;
            });
        });
    };

    controller.prototype = new digitalIvy.listApp.Controller();

    return new controller();
})(jQuery));

digitalIvy.listApp.addController((function ($) {
    var controller = function () {
        var listView = digitalIvy.listApp.getView("listView"),
            self = this,
            nextPage = 1;

        this.addRoute("/upcoming/", function (e) {
            digitalIvy.utils.logger("*****************list upcoming view. next line is this keyword in current context");
            digitalIvy.utils.logger(this);
            nextPage = 1;
            var searchedList = false;
            if (!self.appConfig.disablePaging) {
                $(document).unbind('scroll');
                $(document).bind('scroll', function () {
                    //If the list is searched list, do nothing
                    if (searchedList) return;

                    var closeToBottom = ($(window).scrollTop() + $(window).height() > $(document).height() - 100);
                    if (closeToBottom) {
                        digitalIvy.utils.logger("close to Button now! nextPageNumber: " + nextPage);
                        digitalIvy.api.listContests({ "org": self.appConfig.org, "contestState": "upcoming", "page": nextPage }, function (data, allList) {
                            if (data.length > 0) {
                                listView.init({ "append": true, "list": data, "allList": allList, "labels": self.appConfig.labels, "listType": 2 });
                                nextPage += 1;
                            } else {
                                $(document).unbind('scroll');
                            }
                        });
                    }
                });
            }
            var l = digitalIvy.utils.listener;
            if (l.listening("DI_SEARCH")) l.kill("DI_SEARCH");

            l.listen("DI_SEARCH", function (event) {
                nextPage = 1;

                searchedList = event.matchStr && event.matchStr.length > 0;

                digitalIvy.utils.logger("SSSSSSSSSearch started! searchFor: " + event.matchStr);
                digitalIvy.api.listContests({ "org": self.appConfig.org, "contestState": "upcoming", "page": nextPage, matchStr: event.matchStr }, function (data, allList) {
                    listView.init({ "append": false, "list": data, "allList": allList, "labels": self.appConfig.labels, "listType": 2 });
                    nextPage += 1;
                });

            });

            $("#container-list-header h1").text(self.appConfig.labels.headers.upcoming);

            digitalIvy.api.listContests({ "org": self.appConfig.org, "contestState": "upcoming", "page": nextPage }, function (data, allList) {
                digitalIvy.utils.logger("controller route handler going to initialize listView!");
                listView.init({ "append": false, "list": self.appConfig.disablePaging ? allList : data, "allList": allList, "labels": self.appConfig.labels, "listType": 2, "contestLinkTarget": self.appConfig.contestLinkTarget });
                digitalIvy.utils.logger("controller route handler going to increase next page number! nextPageNumber: " + nextPage);
                nextPage += 1;
            });
        });
    };

    controller.prototype = new digitalIvy.listApp.Controller();

    return new controller();
})(jQuery));

digitalIvy.listApp.addController((function ($) {
    var controller = function () {
        var listView = digitalIvy.listApp.getView("listView"),
            self = this,
            nextPage = 1;

        this.addRoute("/closed/", function (e) {
            digitalIvy.utils.logger("*****************list closed view. next line is this keyword in current context");
            digitalIvy.utils.logger(this);
            nextPage = 1;
            var searchedList = false;

            if (!self.appConfig.disablePaging) {

                $(document).unbind('scroll');
                $(document).bind('scroll', function () {
                    //If the list is searched list, do nothing
                    if (searchedList) return;

                    var closeToBottom = ($(window).scrollTop() + $(window).height() > $(document).height() - 100);
                    if (closeToBottom) {
                        digitalIvy.utils.logger("close to Button now! nextPageNumber: " + nextPage);
                        digitalIvy.api.listContests({ "org": self.appConfig.org, "contestState": "closed", "page": nextPage }, function (data, allList) {
                            if (data.length > 0) {
                                listView.init({ "append": true, "list": data, "allList": allList, "labels": self.appConfig.labels, "listType": 3 });
                                nextPage += 1;
                            } else {
                                $(document).unbind('scroll');
                            }
                        });
                    }
                });
            }

            var l = digitalIvy.utils.listener;
            if (l.listening("DI_SEARCH")) l.kill("DI_SEARCH");

            l.listen("DI_SEARCH", function (event) {
                nextPage = 1;

                searchedList = event.matchStr && event.matchStr.length > 0;

                digitalIvy.utils.logger("SSSSSSSSSearch started! searchFor: " + event.matchStr);
                digitalIvy.api.listContests({ "org": self.appConfig.org, "contestState": "closed", "page": nextPage, matchStr: event.matchStr }, function (data, allList) {
                    listView.init({ "append": false, "list": data, "allList": allList, "labels": self.appConfig.labels, "listType": 3 });
                    nextPage += 1;
                });



            });

            $("#container-list-header h1").text(self.appConfig.labels.headers.closed);
            digitalIvy.api.listContests({ "org": self.appConfig.org, "contestState": "closed", "page": nextPage }, function (data, allList) {
                digitalIvy.utils.logger("controller route handler going to initialize listView!");
                listView.init({ "append": false, "list": self.appConfig.disablePaging ? allList : data, "allList": allList, "labels": self.appConfig.labels, "listType": 3, "contestLinkTarget": self.appConfig.contestLinkTarget });
                digitalIvy.utils.logger("controller route handler going to increase next page number! nextPageNumber: " + nextPage);
                nextPage += 1;
            });
        });
    };

    controller.prototype = new digitalIvy.listApp.Controller();

    return new controller();
})(jQuery));        
 
