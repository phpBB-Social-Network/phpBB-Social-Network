/**
 * 
 * @package phpBB Social Network
 * @version 0.7.0
 * @copyright (c) 2010-2012 Kamahl & Culprit http://phpbbsocialnetwork.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * 
 */

(function($) {

	$.fn.metadataInit = function(param) {
		$(this).each(function() {
			$(this).attr(param, eval('\$(this).metadata().' + param + ';'));
		})
	};

	$.sn = {
	    mobileBrowser : $.browser.mobile,
	    _debug : false,
	    allow_load : true,
	    rtl : false,
	    expanderTextMore : '[read more]',
	    expanderTextLess : '[read less]',
	    browserOutdatedTitle : 'Your browser is outdated',
	    browserOutdated : 'Some of the features will not work on this browser, because it is outdated.',
	    showBrowserOutdated : false,
	    isOutdatedBrowser: false,
	    cookies : {},
	    cookie : {
	        name : '',
	        path : '',
	        domain : '',
	        secure : '0'
	    },
	    confirmBox : {
	        enable : false,
	        resizable : false,
	        draggable : true,
	        modal : true,
	        width : '{S_SN_CB_WIDTH}',
	        button_confirm : '{L_SN_US_CONFIRM}',
	        button_cancel : '{L_SN_US_CANCEL}',
	        button_close : '{L_SN_US_CLOSE}',
	        postMinChar : 10,
	        init : function() {
		        if ($.sn.confirmBox.enable) {
			        var $dialogHTML = $('<div class="ui-body-dialog"/>');
			        $dialogHTML.attr('id', 'dialog').css('display', 'none');
			        $dialogHTML.attr('title', 'Title Confirm Box');
			        $dialogHTML.html('Content Confirm Box');

			        $('body').append($dialogHTML);

			        $('#dialog').dialog({
			            width : this.width,
			            resizable : this.resizable,
			            draggable : this.draggable,
			            modal : this.modal,
			            show : this.show,
			            hide : this.show,
			            autoOpen : false,
			            dialogClass : 'sn-confirmBox'
			        });
			        this.center();
		        }

	        },
	        center : function() {
		        if ($('.ui-dialog').is(':visible')) {
			        $('#dialog').dialog('option', 'position', 'center');
			        var $dialog = $('.ui-dialog');
			        var position = $dialog.position();

			        $('#dialog').css({
			            height : $dialog.height() - $dialog.find('.ui-dialog-buttonpane').outerHeight(true) - $dialog.find('.ui-dialog-titlebar').outerHeight(true) - 10,
			            overflow : 'auto'
			        });

			        $('.ui-widget-shadow').css({
			            top : position.top,
			            left : position.left,
			            width : $dialog.width() + 6,
			            height : $dialog.height() + 6
			        });
		        }
	        }
	    },

	    enableModules : {
	        im : false,
	        us : false,
	        ap : false,
	        up : false,
	        ntf : false,
	        fms : false
	    },
	    _inited : false,
	    _DOMinited : false,
	    _resize : new Array(),

	    menuPosition : {
	        my : "right top",
	        at : "left top"
	    },

	    comments : {
	        deleteTitle : 'Delete',
	        deleteText : 'Delete Text',
	        watermark : 'Watermark',

	        init : function() {
		        // Delete comment
		        $(".sn-deleteComment").live('click', function() {
			        var comment_id = $.sn.getAttr($(this), "cid");
			        var mUrl = $.sn.getAttr($(this), 'url');
			        var comment = $('#sn-comment' + comment_id).find('.sn-commentText').html();
			        snConfirmBox($.sn.comments.deleteTitle, $.sn.comments.deleteText + '<hr />' + comment, function() {
				        $.ajax({
				            type : "POST",
				            url : mUrl,
				            cache : false,
				            data : {
				                smode : 'comment_delete',
				                c_id : comment_id
				            },
				            success : function(data) {
					            $('#sn-comment' + comment_id).fadeOut('slow').remove();
				            }
				        });
			        });
			        if ($('#dialog').find('.sn-expander-more').size() != 0) {
				        $('#dialog').find('.sn-expander-more, .sn-expander-less').remove();
				        $('#dialog').find('.sn-expander-details').show();
				        $('#dialog').find('.sn-expander-text').removeAttr('aria-expander');
			        }
		        });

		        $.sn.comments.waterMark();
	        },

	        waterMark : function() {
		        $(".sn-inputComment").watermark($.sn.comments.watermark, {
		            useNative : false,
		            className : 'sn-watermark'
		        }).elastic({blur:false});

	        }

	    },

	    // INITIALIZACE
	    init : function(opts) {
		    var self = this;

		    this._settings(this, opts);
		    if (this.strpos(this.cookie.name, '_', -1) != 0) {
			    this.cookie.name += '_';
		    }

		    this.confirmBox.init();

		    this._minBrowser();
//		    this.isOutdatedBrowser = true;
		    
		    $.metadata.setType("class");
		    $(window).resize(function() {
			    $.sn._resizeBlocks();
		    }).scroll(function() {
			    $.sn._scrollBlocks();
		    }).unload(function() {
			    $.sn._unloadBlocks();
		    });
		    $(document).click(function(event) {
			    $.sn._documentClick(event);
		    });

		    $('.sn-page-content').bind('DOMSubtreeModified', function() {
			    $.sn._DOMSubtreeModified();
		    });

		    this.rtl = $('body').hasClass('rtl');
		    if (!this.cookie.domain.match(/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/) && !this.cookie.domain.match(/^\./)) {
			    this.cookie.domain = '.' + this.cookie.domain;
		    }

		    if ($('ul.sn-menu').size() > 0) {
			    $('ul.sn-menu').menu({
				    position : $.sn.menuPosition
			    }).removeClass('ui-corner-all');
			    $('.sn-menu *').removeClass('ui-corner-all ui-corner-top ui-corner-bottom');
		    }

		    $('.sn-menu.ui-menu .ui-menu').live('mouseleave', function() {
			    $(this).delay(500).hide();
			    $(this).children('a.ui-state-active').removeClass('ui-state-active');
			    $(this).parent('.ui-menu-item').children('a.ui-state-active').delay(500).removeClass('ui-state-active');
			    $(this).attr({
			        'aria-expanded' : 'false',
			        'aria-hidden' : 'true'
			    });
		    });

		    $('input.ui-button').bind('mouseover mouseout', function() {
			    $(this).toggleClass('ui-state-hover');
		    });

		    $(document).oneTime(250, 'sn-page-height', function() {
			    $.sn._resize()
		    });
		    this.comments.init();

		    if (this._debug) this._debugInit();
	    },

	    parseURL : function(url) {
		    var a = document.createElement('a');
		    a.href = url;
		    return {
		        source : url,
		        protocol : a.protocol.replace(':', ''),
		        host : a.hostname,
		        port : a.port,
		        query : a.search,
		        params : (function() {
			        var ret = {}, seg = a.search.replace(/^\?/, '').split('&'), len = seg.length, i = 0, s;
			        for (; i < len; i++) {
				        if (!seg[i]) {
					        continue;
				        }
				        s = seg[i].split('=');
				        ret[s[0]] = s[1];
			        }
			        return ret;
		        })(),
		        file : (a.pathname.match(/\/([^\/?#]+)$/i) || [ , '' ])[1],
		        hash : a.hash.replace('#', ''),
		        path : a.pathname.replace(/^([^\/])/, '/$1'),
		        relative : (a.href.match(/tps?:\/\/[^\/]+(.+)/) || [ , '' ])[1],
		        segments : a.pathname.replace(/^\//, '').split('/')
		    };
	    },

	    isValidURL : function(url) {
		    var RegExp = /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i;
		    if (RegExp.test(url)) {
			    return true;
		    } else {
			    return false;
		    }
	    },

	    equalsArray : function(obj1, obj2) {
		    return $.param(obj1) === $.param(obj2);
	    },

	    isKey : function(e, needKey) {
		    var code = (e.keyCode ? e.keyCode : e.which);
		    return needKey.alt == e.altKey && needKey.ctrl == e.ctrlKey && needKey.shift == e.shiftKey && needKey.key == code;
	    },

	    getCaret : function(el) {
		    if (el.selectionStart) {
			    return el.selectionStart;
		    } else if (document.selection) {
			    el.focus();

			    var r = document.selection.createRange();
			    if (r == null) { return 0; }

			    var re = el.createTextRange(), rc = re.duplicate();
			    re.moveToBookmark(r.getBookmark());
			    rc.setEndPoint('EndToStart', re);

			    return rc.text.length;
		    }
		    return 0;
	    },

	    insertAtCaret : function(textarea, myValue) {
		    return textarea.each(function(i) {
			    if (document.selection) {
				    // For browsers like Internet Explorer
				    this.focus();
				    sel = document.selection.createRange();
				    sel.text = myValue;
				    this.focus();
			    } else if (this.selectionStart || this.selectionStart == '0') {
				    // For browsers like Firefox and Webkit based
				    var startPos = this.selectionStart;
				    var endPos = this.selectionEnd;
				    var scrollTop = this.scrollTop;
				    this.value = this.value.substring(0, startPos) + myValue + this.value.substring(endPos, this.value.length);
				    this.focus();
				    this.selectionStart = startPos + myValue.length;
				    this.selectionEnd = startPos + myValue.length;
				    this.scrollTop = scrollTop;
			    } else {
				    this.value += myValue;
				    this.focus();
			    }
			    $(this).trigger('paste');
		    });
	    },
	    serializeJSON : function(obj) {
		    var t = typeof (obj);
		    if (t != "object" || obj === null) {
			    // simple data type
			    if (t == "string") obj = '"' + obj + '"';
			    return String(obj);
		    } else {
			    // array or object
			    var json = [], arr = (obj && obj.constructor == Array);

			    $.each(obj, function(k, v) {
				    t = typeof (v);
				    if (t == "string") v = '"' + v + '"';
				    else if (t == "object" & v !== null) v = $.sn.serializeJSON(v)
				    json.push((arr ? "" : '"' + k + '":') + String(v));
			    });

			    return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
		    }
	    },

	    dropShadow : function(elem, attribs) {
		    return $(elem).each(function() {
			    var self = $(this);
			    if (self.is('[aria-shadow="true"]')) { return; }
			    var pself = self.position();

			    var shCSS = $.extend(true, {
			        position : 'absolute',
			        top : pself.top,
			        left : pself.left,
			        width : self.innerWidth() + parseInt(self.css('border-left-width')) + parseInt(self.css('border-right-width')),
			        height : self.innerHeight() + parseInt(self.css('border-top-width')) + parseInt(self.css('border-bottom-width'))
			    }, attribs);
			    if (attribs.opacity != undefined) {
				    shCSS = $.extend({}, shCSS, {
					    opacity : attribs.opacity
				    });
			    }
			    if (attribs.size != undefined) {
				    shCSS = $.extend({}, shCSS, {
				        margin : -attribs.size + 'px 0 0 ' + (-attribs.size) + 'px',
				        padding : attribs.size
				    });
			    }
			    if (attribs.cornerRadius != undefined) {
				    shCSS = $.extend({}, shCSS, {
					    borderRadius : attribs.cornerRadius
				    });

			    }
			    var $overlay = $('<div>').addClass('ui-overlay');
			    var $overlay_w = $('<div />').addClass('ui-widget-overlay');

			    if (attribs.overlayHidden) {
				    $overlay_w.appendTo($overlay);
			    }
			    $('<div />').addClass('ui-widget-shadow ui-corner-all sn-shadow').css(shCSS).appendTo($overlay);

			    $overlay.insertBefore(self);
			    self.css({
			        position : 'absolute',
			        top : pself.top,
			        left : pself.left,
			        width : self.width(),
			        height : self.height()
			    }).attr('aria-shadow', 'true');

		    });
	    },

	    metadataInit : function() {
	    },

	    getAttr : function(o, a) {
		    if (o.size() == 0) { return false }
		    ;

		    if (o.attr(a) == undefined) {
			    if (o.metadata()[a] == undefined) { return false; }
			    return o.metadata()[a];
		    } else {
			    return o.attr(a);
		    }
	    },
	    // SETTINGS FOR MODULES
	    _settings : function(obj, opts) {
		    if (opts == undefined || $.isEmptyObject(opts)) return;
		    if (obj._inited == undefined) obj._inited = false;
		    if (obj._inited) return;

		    $.extend(true, obj, opts);

		    obj._inited = true;

	    },

	    getCookie : function(cookieName, defaultValue) {
		    cookieName = cookieName.replace(/-/g, '_');

		    var myCookie = $.cookie(this.cookie.name + cookieName);
		    if (myCookie == null && defaultValue != undefined) {
			    myCookie = defaultValue;
		    }
		    return myCookie;
		    /*
			 * if (Object.keys(this.cookies).length == 0){ eval('this.cookies =
			 * $.extend({},this.cookies,'+$.cookie(this.cookie.name +
			 * 'sn_cookie').replace(/("(\{)|(\})")/g,'$2$3')+');'); } var ret =
			 * this.cookies[cookieName]; if ( typeof ret == 'undefined') ret =
			 * defaultValue; return ret;
			 */
	    },
	    setCookie : function(cookieName, value) {
		    cookieName = cookieName.replace(/-/g, '_');
		    $.cookie(this.cookie.name + cookieName, value, this.cookie);
		    // eval('this.cookies =
		    // \$.extend({},this.cookies,{'+cookieName+':value});');
		    // $.cookie(this.cookie.name + 'sn_cookie',
		    // this.serializeJSON(this.cookies), this.cookie);
	    },

	    strpos : function(haystack, needle, offset) {
		    if (offset < 0) {
			    offset = 0;
			    haystack = this.strrev(haystack);
		    }
		    var i = (haystack + '').indexOf(needle, (offset || 0));
		    return i === -1 ? false : i;
	    },
	    strrev : function(string) {
		    string = string + '';
		    var grapheme_extend = /(.)([\uDC00-\uDFFF\u0300-\u036F\u0483-\u0489\u0591-\u05BD\u05BF\u05C1\u05C2\u05C4\u05C5\u05C7\u0610-\u061A\u064B-\u065E\u0670\u06D6-\u06DC\u06DE-\u06E4\u06E7\u06E8\u06EA-\u06ED\u0711\u0730-\u074A\u07A6-\u07B0\u07EB-\u07F3\u0901-\u0903\u093C\u093E-\u094D\u0951-\u0954\u0962\u0963\u0981-\u0983\u09BC\u09BE-\u09C4\u09C7\u09C8\u09CB-\u09CD\u09D7\u09E2\u09E3\u0A01-\u0A03\u0A3C\u0A3E-\u0A42\u0A47\u0A48\u0A4B-\u0A4D\u0A51\u0A70\u0A71\u0A75\u0A81-\u0A83\u0ABC\u0ABE-\u0AC5\u0AC7-\u0AC9\u0ACB-\u0ACD\u0AE2\u0AE3\u0B01-\u0B03\u0B3C\u0B3E-\u0B44\u0B47\u0B48\u0B4B-\u0B4D\u0B56\u0B57\u0B62\u0B63\u0B82\u0BBE-\u0BC2\u0BC6-\u0BC8\u0BCA-\u0BCD\u0BD7\u0C01-\u0C03\u0C3E-\u0C44\u0C46-\u0C48\u0C4A-\u0C4D\u0C55\u0C56\u0C62\u0C63\u0C82\u0C83\u0CBC\u0CBE-\u0CC4\u0CC6-\u0CC8\u0CCA-\u0CCD\u0CD5\u0CD6\u0CE2\u0CE3\u0D02\u0D03\u0D3E-\u0D44\u0D46-\u0D48\u0D4A-\u0D4D\u0D57\u0D62\u0D63\u0D82\u0D83\u0DCA\u0DCF-\u0DD4\u0DD6\u0DD8-\u0DDF\u0DF2\u0DF3\u0E31\u0E34-\u0E3A\u0E47-\u0E4E\u0EB1\u0EB4-\u0EB9\u0EBB\u0EBC\u0EC8-\u0ECD\u0F18\u0F19\u0F35\u0F37\u0F39\u0F3E\u0F3F\u0F71-\u0F84\u0F86\u0F87\u0F90-\u0F97\u0F99-\u0FBC\u0FC6\u102B-\u103E\u1056-\u1059\u105E-\u1060\u1062-\u1064\u1067-\u106D\u1071-\u1074\u1082-\u108D\u108F\u135F\u1712-\u1714\u1732-\u1734\u1752\u1753\u1772\u1773\u17B6-\u17D3\u17DD\u180B-\u180D\u18A9\u1920-\u192B\u1930-\u193B\u19B0-\u19C0\u19C8\u19C9\u1A17-\u1A1B\u1B00-\u1B04\u1B34-\u1B44\u1B6B-\u1B73\u1B80-\u1B82\u1BA1-\u1BAA\u1C24-\u1C37\u1DC0-\u1DE6\u1DFE\u1DFF\u20D0-\u20F0\u2DE0-\u2DFF\u302A-\u302F\u3099\u309A\uA66F-\uA672\uA67C\uA67D\uA802\uA806\uA80B\uA823-\uA827\uA880\uA881\uA8B4-\uA8C4\uA926-\uA92D\uA947-\uA953\uAA29-\uAA36\uAA43\uAA4C\uAA4D\uFB1E\uFE00-\uFE0F\uFE20-\uFE26]+)/g;
		    string = string.replace(grapheme_extend, '$2$1');
		    return string.split('').reverse().join('');
	    },

	    _resizeBlocks : function() {
		    this._DOMinited = true;
		    var self = this;
		    self._resize();
		    $.each(self.enableModules, function(idx, value) {
			    if (value !== false && $.sn[idx] !== undefined && $.sn[idx]._resize !== undefined) {
				    $.sn[idx]._resize();
			    }
		    });
		    this._DOMinited = false;
	    },

	    _scrollBlocks : function() {
		    var self = this;
		    $.each(self.enableModules, function(idx, value) {
			    if (value !== false && $.sn[idx] !== undefined && $.sn[idx]._scroll !== undefined) {
				    $.sn[idx]._scroll();
			    }
		    });

		    $.sn.confirmBox.center();

	    },

	    _resize : function() {
		    this._DOMinited = true;
		    if ($('.sn-page').size() > 0) {
			    // $('.sn-page-content').removeAttr('style');
			    $('.sn-page-content').css({
				    minHeight : Math.max($('.sn-page-columnLeft').height(), $('.sn-page-columnRight').height())
			    });
		    }

		    $.sn.confirmBox.center();
		    this._DOMinited = false;
	    },

	    _documentClick : function(event) {
		    this._DOMinited = true;
		    var self = this;
		    $.each(self.enableModules, function(idx, value) {
			    if (value !== false && $.sn[idx] !== undefined && $.sn[idx]._documentClick !== undefined) {
				    $.sn[idx]._documentClick(event);
			    }
		    });
		    this._DOMinited = false;
	    },

	    _unloadBlocks : function() {
		    var self = this;
		    this._DOMinited = true;
		    $.each(self.enableModules, function(idx, value) {
			    if (value !== false && $.sn[idx] !== undefined && $.sn[idx]._unload !== undefined) {
				    $.sn[idx]._unload();
			    }
		    });
		    this._DOMinited = false;
	    },

	    _DOMSubtreeModified : function() {
		    // if (this._DOMinited) { return; }
		    this._DOMinited = true;
		    this._textExpander();
		    var self = this;
		    $.each(self.enableModules, function(idx, value) {
			    if (value !== false && $.sn[idx] !== undefined && $.sn[idx]._DOMChanged !== undefined) {
				    $.sn[idx]._DOMChanged();
			    }
		    });
		    $.sn._resize();
		    this._DOMinited = false;

	    },

	    _textExpander : function() {
		    if ($('.sn-expander-text:not([aria-expander="expander"])').size() != 0) {
			    $('.sn-expander-text:not([aria-expander="expander"])').attr('aria-expander', 'expander').expander({
			        slicePoint : 500,
			        widow : 1,
			        preserveWords : false,
			        expandText : $.sn.expanderTextMore,
			        userCollapseText : $.sn.expanderTextLess,
			        expandPrefix : '...',
			        userCollapsePrefix : ' ',
			        moreClass : 'sn-expander-more',
			        lessClass : 'sn-expander-less',
			        detailClass : 'sn-expander-details'
			    });
		    }
	    },

	    _minBrowser : function() {
		    var minBrowsers = {
		        msie : 8,
		        opera : 6,
		        webkit : 12,
		        mozilla : 6
		    };

		    var browser = '';
		    if ($.browser.msie) {
			    browser = 'msie';
		    } else if ($.browser.opera) {
			    browser = 'opera';
		    } else if ($.browser.mozilla) {
			    browser = 'mozilla';
		    } else if ($.browser.webkit || $.browser.safari) {
			    browser = 'webkit';
		    }
		    if (minBrowsers[browser] >= $.browser.version) {
			    if ($.sn.showBrowserOutdated && $.sn.getCookie('sn_showBrowserOutdated', 0) == 0){
			    	$.sn.setCookie('sn_showBrowserOutdated', 1);
			    	snConfirmBox($.sn.browserOutdatedTitle, $.sn.browserOutdated + '<br />' + $.browser.version);
			    	}
			    this.isOutdatedBrowser = true;
			    return false;
		    }

		    return true;
	    },

	    _debugInit : function() {
		    var dbg = $('<div />').attr('title', 'DEBUG');

		    var dbg_IM = $('<div />').attr('id', 'IM_timer');
		    var dbg_NTF = $('<div />').attr('id', 'NTF_timer');
		    var dbg_browser = $('<div />').attr('id', 'browser').html('Browser');

		    $.each($.browser, function(idx, val) {
			    dbg_browser.html(dbg_browser.html() + '<br />&nbsp; &nbsp;' + idx + ': ' + val);
		    })

		    var IM_downcount = 1;
		    var IM_counter = $.sn.im.opts._imCounter;
		    var NTF_downcount = $.sn.ntf.checkTime / 1000 - 1;
		    var NTF_counter = $.sn.ntf.checkTime / 1000 - 1;

		    dbg.appendTo('body');
		    dbg_browser.appendTo(dbg);
		    dbg_IM.appendTo(dbg);
		    dbg_NTF.appendTo(dbg);
		    dbg.dialog({
		        position : "left bottom",
		        buttons : {
			        "Close debug" : function() {
				        $(this).dialog("close");
			        }
		        }

		    });

		    $(document).scroll(function() {
			    dbg.dialog({
				    position : 'left bottom'
			    });
		    });
		    $(window).resize(function() {
			    dbg.dialog({
				    position : 'left bottom'
			    });
		    });

		    $(document).everyTime(1000, 'sn-debug', function() {
			    if ($.sn.im.opts._imCounter != IM_counter || IM_counter - IM_downcount < 0) {
				    IM_counter = $.sn.im.opts._imCounter;
				    IM_downcount = 0;
			    }
			    if (NTF_downcount < 0) {
				    NTF_downcount = NTF_counter;
			    }

			    dbg_IM.html('IM check: ' + $.sn.im.opts._imCounter + 's<br />IM check after: ' + (IM_counter - IM_downcount) + 's');
			    dbg_NTF.html('NTF check after: ' + (NTF_downcount) + 's');
			    IM_downcount++;
			    NTF_downcount--;
		    })
	    }

	}
}(jQuery));