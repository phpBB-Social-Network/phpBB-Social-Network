/**
 *
 * @package phpBB Social Network
 * @version 0.7.0
 * @copyright (c) 2010-2012 Kamahl & Culprit http://phpbbsocialnetwork.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */
(function($) {
	$.sn.up = {
	    url : '{U_UP_TAB_WALL}',
	    urlAJAX : '{U_UP_AJAXURL}',
	    urlSelectFriends : '{U_SELECT_FRIENDS}',
	    spinner : '<em>Loading&#8230;<\/em>',
	    nextText : '{L_NEXT}',
	    prevText : '{L_PREVIOUS}',
	    dateFormat : 'd. MM yy',
	    dayNames : [ 'Monday', 'Thusday', 'Wensday', 'Thursday', 'Friday', 'Suterday', 'Sunday' ],
	    dayNamesShort : [ '{L_SN_UP_SUNDAY_MIN}', '{L_SN_UP_MONDAY_MIN}', '{L_SN_UP_TUESDAY_MIN}', '{L_SN_UP_WEDNESDAY_MIN}', '{L_SN_UP_THURSDAY_MIN}', '{L_SN_UP_FRIDAY_MIN}', '{L_SN_UP_SATURDAY_MIN}' ],
	    monthNames : [ 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ],
	    monthNamesShort : [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ],
	    _inited : false,
	    tabReportUser : -1,
	    tabShowFullPage: 'false',
	    menuPosition : {
	        my : "right top",
	        at : "right bottom"
	    },

	    init : function(opts) {
		    if (!$.sn._inited) { return false; }
		    if ($.sn.enableModules.up == undefined || !$.sn.enableModules.up) { return false; }
		    $.sn._settings(this, opts);

		    if ($('#sn-up-profileTabs').size() > 0) {
			    var par_URL = $.sn.parseURL(window.location);
			    var c_url = $('#sn-up-profileTabs li:first a').attr('href');
			    if (par_URL.hash == 'socialnet_us' && par_URL.params.status_id != null) {
				    $.sn.setCookie('sn_up_profileTab', 0);
				    $('#sn-up-profileTabs li:first a').attr('href', $.sn.us.url + '?smode=status_one&status=' + par_URL.params.status_id + '&wall=' + par_URL.params.u);
			    }
			    $.sn.up.url = $.sn.up.url.replace(/&amp;/, '&');

			    $('#sn-up-profileTabs')
						.mousedown(function(event){
							$.sn.up.tabShowFullPage = (event.which == 3) ? 'true': 'false';
							if ( event.which == 3) return false;
						})
						.tabs({
				        ajaxOptions : {
				            cache : false,
				            async : false,
				            data : {fullPage: $.sn.up.tabShowFullPage}
				        },
				        cache : false,
				        spinner : $.sn.up.spinner,
				        cookie : {
				            name : $.sn.cookie.name + 'sn_up_profileTab',
				            path : $.sn.cookie.path,
				            domain : $.sn.cookie.domain,
				            secure : $.sn.cookie.secure
				        },
				        create : function(e, ui) {
					        if ($.sn.getCookie('sn-up-profileTab') == 0) { return false; }
				        },
				        select : function(e, ui) {
				        	$(ui.panel).html($.sn.up.spinner);
				        },
				        load : function(e, ui) {
					        $('#sn-us-wallInput').trigger('focusin').trigger('focusout');

						    if (!$.sn.isOutdatedBrowser) {
							    $('textarea.sn-us-mention').mentionsInput({
							        templates : {
							            wrapper : _.template('<div class="sn-us-mentions-input-box"></div>'),
							            autocompleteList : _.template('<div class="sn-us-mentions-autocomplete-list"></div>'),
							            mentionsOverlay : _.template('<div class="sn-us-mentions"><div></div></div>'),
							        },
							        onDataRequest : function(mode, query, callback) {
								        $.getJSON($.sn.us.url, {
								            smode : 'get_mention',
								            uname : query
								        }, function(data) {
									        data = _.filter(data, function(item) {
										        return item.name.toLowerCase().indexOf(query.toLowerCase()) > -1
									        });
									        callback.call(this, data);
								        });
							        }
							    });
						    }

					        $('.sn-up-editable').snEditable({
					            datePicker : {
					                dateFormat : $.sn.up.dateFormat,
					                monthNames : $.sn.up.monthNames,
					                monthNamesShort : $.sn.up.monthNamesShort
					            },
					            ajaxOptions : {
					                url : $.sn.up.urlAJAX,
					                cache : false
					            }
					        });

					        $.sn.comments.waterMark();
					        $("#sn-us-wallInput").watermark($.sn.us.watermark, {
					            useNative : false,
					            className : 'sn-us-watermark'
					        }).elastic().trigger('focusout');
					        $.sn._resize();
					        $.sn._textExpander();

				        }
				    })
						.removeAttr('style');

			    $('#sn-up-profileTabs li:first a').attr('href', c_url);
		    }

		    if ($('.sn-up-reportUser a').size() > 0 && $('#sn-up-profileTabs').size() > 0) {
			    $('.sn-up-reportUser a').click(function() {
				    $.sn.up.tabCurrent = $('#sn-up-profileTabs').tabs('option', 'selected');
				    if ($.sn.up.tabReportUser != -1) {
					    $('#sn-up-profileTabs').tabs('select', $.sn.up.tabReportUser);
					    return false;
				    }
				    $.sn.up.tabReportUser = $('#sn-up-profileTabs').tabs('length');
				    var tabTitle = $(this).text();
				    $('#sn-up-profileTabs').tabs('add', '#sn-up-profileTabs-reportUser', tabTitle).tabs('url', $.sn.up.tabReportUser, $(this).attr('href')).tabs('select', $.sn.up.tabReportUser);
				    var tabReportUser = $('#sn-up-profileTabs .ui-tabs-nav li a[href$=reportUser]');
				    tabReportUser.find('span').css({'float': $.sn.rtl?'right':'left'});
				    tabReportUser.prepend('<span class="ui-icon ui-icon-alert">'+tabTitle+'</span>');
				    tabReportUser.append('<span class="ui-icon ui-icon-secondary ui-icon-close sn-action-tabClose"></span>');
				    $('#sn-up-profileTabs').tabs('select', $.sn.up.tabReportUser);
				    tabReportUser.prev('.ui-icon.ui-icon-close').css('cursor', 'pointer').click(function() {
					    $('#sn-up-reportUser input[name=cancel]').trigger('click');
				    });
				    return false;
			    });
		    }

		    $('#sn-up-profileTabs .ui-tabs-nav li a[href$=reportUser] .sn-action-tabClose').live('click', function() {
			    $('#sn-up-profileTabs').tabs('select', $.sn.up.tabCurrent).tabs('remove', $.sn.up.tabReportUser);
			    $.sn.up.tabReportUser = -1;
			    return false;
		    });

		    $('.sn-up-viewAllFriends .ui-button a').click(function() {
			    $('html, body').animate({
				    scrollTop : 0
			    }, 'fast');
			    $("#sn-up-profileTabs").tabs('select', '#sn-up-profileTabs-friends');
			    return false;
		    });

		    this._ucpProfile();
		    this._ucpRelations();
		    this._upMenu.init();
		    this._emotes();
		    $.sn._resize();
	    },

	    _showHide : function(value_div, hide_div, values) {
		    var value = $(value_div).val();
		    var show = jQuery.inArray(value, values);

		    if (show != -1) {
			    $(hide_div).show();
		    } else {
			    $(hide_div).hide();
		    }
	    },

	    _ucpRelations : function() {
		    if ($('.sn-up-relations').size() == 0) { return; }

		    this._showHide("#sn-up-relationShipStatus", "#sn-up-relationShipPartner", [ "2", "3", "4", "5", "6" ]);
		    $("#sn-up-relationShipStatus").change(function() {
			    $.sn.up._showHide("#sn-up-relationShipStatus", "#sn-up-relationShipPartner", [ "2", "3", "4", "5", "6" ]);
		    });

		    this._showHide("#sn-up-familyStatus", "#sn-up-familyPartner", [ "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31", "32", "33", "34", "35", "36", "37", "38", "39", "40", "41" ]);
		    $("#sn-up-familyStatus").change(function() {
			    $.sn.up._showHide("#sn-up-familyStatus", "#sn-up-familyPartner", [ "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31", "32", "33", "34", "35", "36", "37", "38", "39", "40", "41" ]);
		    });

		    $.sn.up.urlSelectFriends = $.sn.up.urlSelectFriends.replace(/&amp;/g, '&');

		    $("#sn-up-relationShipUser, #sn-up-familyUser").autocomplete({
		        source : $.sn.up.urlSelectFriends,
		        minLength : 2,
		        delay : 300
		    });

		    $("#sn-up-annivesaryPicker").datepicker({
		        changeMonth : true,
		        changeYear : true,
		        appendText : '(dd-mm-yyyy)',
		        dateFormat : 'dd-mm-yy',
		        firstDay : 1,
		        nextText : $.sn.up.nextText,
		        prevText : $.sn.up.prevText,
		        yearRange : '1905:c',
		        dayNames : $.sn.up.dayNames,
		        dayNamesMin : $.sn.up.dayNamesShort,
		        monthNamesShort : $.sn.up.monthNamesShort
		    });
	    },

	    _ucpProfile : function() {
		    if ($('.sn-up-ucpProfile').size() == 0) { return; }

		    $('.sn-up-ucpProfile textarea[maxlength]').keyup(function() {
			    var limit = parseInt($(this).attr('maxlength'));
			    var text = $(this).val();
			    var chars = text.length;

			    if (chars > limit) {
				    var new_text = text.substr(0, limit);
				    $(this).val(new_text);
			    }
		    });

	    },

	    _emotes : function() {
		    if ($('.sn-up-emote').size() > 0) {
			    $('.sn-up-emote a').click(function() {
				    var $this = $(this);
				    $.ajax({
				        type : 'POST',
				        dataType : 'json',
				        url : $.sn.up.urlAJAX,
				        cache : false,
				        async : true,
				        data : {
				            mode : 'emote',
				            emote : $.sn.getAttr($this, 'emote'),
				            u : $.sn.getAttr($this, 'u')
				        },
				        success : function(data) {
					        snConfirmBox(data.cbTitle, data.cbText, function() {
						        if ($('#sn-up-profileTabs').tabs('option', 'selected') == 0) {
							        $('#sn-up-profileTabs').tabs('abort').tabs('load', 0);
						        }
					        });
				        }
				    });
			    });
		    }

	    },

	    _upMenu : {
	        init : function() {
		        var upMenu = this;
		        if ($('.sn-up-menu li').size() == 0) { return; }

		        $('.sn-up-menu').addClass('ui-menu ui-widget ui-widget-content ui-corner-all ui-menu-icons').css({
		            zIndex : 999,
		            position : 'relative'
		        }).children('li').addClass('ui-menu-item').attr('role', 'presentation').css({
			        width : 'auto'
		        }).children('a').addClass('ui-state-default ui-corner-all').attr('role', 'menuitem');

		        $('.sn-up-menu li').each(function(idx, item) {
			        var $subMenu = $(this).find('.sn-up-submenu');
			        $subMenu.hide();
			        if ($subMenu.size() == 0) { return; }

			        $(this).children('a').css('padding-right','1.7em').append('<span class="ui-menu-icon ui-icon ui-icon-secondary ui-icon-carat-1-s" style=""></span>');

			        $subMenu.addClass('ui-menu ui-widget ui-widget-content ui-menu-icons ui-corner-bottom ui-corner-' + ($.sn.rtl ? 'tr' : 'tl')).attr('role', 'menu');
			        // $subMenu.addClass('ui-menu ui-widget ui-widget-content
			        // ui-menu-icons ui-corner-bottom
			        // ui-corner-all').attr('role', 'menu');
			        $subMenu.children('li:not( .ui-menu-item ):has( a )').addClass('ui-menu-item').attr('role', 'menu-item');
			        $subMenu.children('li.ui-menu-item:has( a )').find('a').addClass('ui-corner-all').hover(function() {
				        $(this).toggleClass('ui-state-focus')
			        });

			        $(this).children('a').bind('click', function() {
				        if ($(this).attr('aria-expanded')) {
					        upMenu.close(this, $subMenu);
				        } else {
					        upMenu.open(this, $subMenu);
				        }
			        })

		        });
	        },

	        open : function(elem, $subMenu) {
		        this.close();
		        $(elem).attr('aria-expanded', 'true').addClass('ui-corner-top ui-state-active').removeClass('ui-corner-all');
		        var position = $.extend({}, {
		            of : elem,
		            offset : '0 -1'
		        }, $.sn.up.menuPosition);
		        $subMenu.show().position(position);
	        },
	        close : function(elem, $subMenu) {
		        $('.sn-up-submenu:visible').hide().prev('a').removeClass('ui-state-active ui-corner-top').addClass('ui-corner-all').removeAttr('aria-expanded');
		        // $('.sn-up-submenu:visible').hide().prev('a').removeClass('ui-state-active').removeAttr('aria-expanded');
	        }
	    },

	    _documentClick : function(event) {

		    if ($('.sn-up-menu li a[aria-expanded="true"]').size() != 0) {
			    var s_obj = $('.sn-up-menu li a[aria-expanded="true"]').parents('li');
			    if (!$(event.target).closest(s_obj).size()) {
				    this._upMenu.close();
			    }
		    }

		    // this._upMenu.close();
	    }
	}
}(jQuery))
