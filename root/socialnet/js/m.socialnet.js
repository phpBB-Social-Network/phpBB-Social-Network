/**
 * @preserve phpBB Social Network 0.7.2 - Core
 * (c) 2010-2012 Kamahl & Culprit & Senky http://phpbbsocialnetwork.com
 * http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Extend jQuery Library
 * @param {object} $ jQuery
 * @returns {void}
 */
(function($) {
	$.fn.metadataInit = function(param) {
		$(this).each(function() {
			$(this).attr(param, eval('\$(this).metadata().' + param + ';'));
		});
	};
}(jQuery));

/**
 * Declaration of phpBB Social Network object
 * @param {object} $ jQuery
 * @returns {object} socialNetwork
 */
var socialNetwork = (function($) {
	return {
		mobileBrowser: $.browser.mobile,
		_debug: false,
		allow_load: true,
		rtl: false,
		user_id: 1,
		expanderTextMore: '[read more]',
		expanderTextLess: '[read less]',
		browserOutdatedTitle: 'Your browser is outdated',
		browserOutdated: 'Some of the features will not work on this browser, because it is outdated.',
		showBrowserOutdated: false,
		isOutdatedBrowser: false,
		cookies: {},
		cookie: {
			name: '',
			path: '',
			domain: '',
			secure: '0'
		},
		enableModules: {
			im: false,
			us: false,
			ap: false,
			up: false,
			ntf: false,
			fms: false
		},
		_inited: false,
		_DOMinited: false,
		menuPosition: {
			my: "right top",
			at: "left top"
		},
		// INITIALIZACE
		init: function(opts) {
			var self = this;
			this._settings(this, opts);
			if (this.strpos(this.cookie.name, '_', -1) != 0) {
				this.cookie.name += '_';
			}

			this.confirmBox.init();

			this._minBrowser();

			$.metadata.setType("class");
			$(window).resize(function() {
				self._resizeBlocks();
			}).scroll(function() {
				self._scrollBlocks();
			}).unload(function() {
				self._unloadBlocks();
			});
			$(document).click(function(event) {
				self._documentClick(event);
			});

			$('.sn-page-content').bind('DOMSubtreeModified', function() {
				self._DOMSubtreeModified();
			});

			this.rtl = $('body').hasClass('rtl');
			if (!this.cookie.domain.match(/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/) && !this.cookie.domain.match(/^\./)) {
				this.cookie.domain = '.' + this.cookie.domain;
			}

			if ($('ul.sn-menu').size() > 0) {
				$('ul.sn-menu').menu({
					position: this.menuPosition
				}).removeClass('ui-corner-all');
				$('.sn-menu *').removeClass('ui-corner-all ui-corner-top ui-corner-bottom');
			}

			$('.sn-menu.ui-menu .ui-menu').live('mouseleave', function() {
				$(this).delay(500).hide();
				$(this).children('a.ui-state-active').removeClass('ui-state-active');
				$(this).parent('.ui-menu-item').children('a.ui-state-active').delay(500).removeClass('ui-state-active');
				$(this).attr({
					'aria-expanded': 'false',
					'aria-hidden': 'true'
				});
			});

			$('input.ui-button').bind('mouseover mouseout', function() {
				$(this).toggleClass('ui-state-hover');
			});

			$(document).oneTime(250, 'sn-page-height', function() {
				self._resize();
			});

			if (this._debug)
				this._debugInit();
		},
		/**
		 * Parse HTTP URL to segments
		 * @param {string} url HTTP url string
		 * @returns {object} Object with URL segments
		 */
		parseURL: function(url) {
			var a = document.createElement('a');
			a.href = url;
			return {
				source: url,
				protocol: a.protocol.replace(':', ''),
				host: a.hostname,
				port: a.port,
				query: a.search,
				params: (function() {
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
				file: (a.pathname.match(/\/([^\/?#]+)$/i) || [, ''])[1],
				hash: a.hash.replace('#', ''),
				path: a.pathname.replace(/^([^\/])/, '/$1'),
				relative: (a.href.match(/tps?:\/\/[^\/]+(.+)/) || [, ''])[1],
				segments: a.pathname.replace(/^\//, '').split('/')
			};
		},
		/**
		 * Control if inserted string is valid URL
		 * @param {string} url HTTP url string
		 * @returns {boolean}
		 */
		isValidURL: function(url) {
			var RegExp = /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i;
			if (RegExp.test(url)) {
				return true;
			} else {
				return false;
			}
		},
		equalsArray: function(obj1, obj2) {
			return $.param(obj1) === $.param(obj2);
		},
		isKey: function(e, needKey) {
			var code = (e.keyCode ? e.keyCode : e.which);
			return needKey.alt == e.altKey && needKey.ctrl == e.ctrlKey && needKey.shift == e.shiftKey && needKey.key == code;
		},
		getCaret: function(el) {
			if (el.selectionStart) {
				return el.selectionStart;
			} else if (document.selection) {
				el.focus();

				var r = document.selection.createRange();
				if (r == null) {
					return 0;
				}

				var re = el.createTextRange(), rc = re.duplicate();
				re.moveToBookmark(r.getBookmark());
				rc.setEndPoint('EndToStart', re);

				return rc.text.length;
			}
			return 0;
		},
		insertAtCaret: function(textarea, myValue) {
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
		/**
		 * Create string from object
		 * @see http://www.sitepoint.com/javascript-json-serialization/
		 * @param {object} obj
		 * @returns {string} serialized object
		 */
		serializeJSON: function(obj) {
			var t = typeof (obj);
			if (t != "object" || obj === null) {
				// simple data type
				if (t == "string") {
					obj = '"' + obj + '"';
				}
				return String(obj);
			} else {
				// array or object
				var json = [], arr = (obj && obj.constructor == Array);

				$.each(obj, function(k, v) {
					t = typeof (v);
					if (t == "string") {
						v = '"' + v + '"';
					}
					else if (t == "object" & v !== null) {
						v = this.serializeJSON(v);
					}
					json.push((arr ? "" : '"' + k + '":') + String(v));
				});

				return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
			}
		},
		getAttr: function(o, a) {
			if (o.size() == 0) {
				return false;
			}
			if (o.attr(a) == undefined) {
				if (o.metadata()[a] == undefined) {
					return false;
				}
				return o.metadata()[a];
			} else {
				return o.attr(a);
			}
		},
		_settings: function(obj, opts) {
			if (opts == undefined || $.isEmptyObject(opts)) {
				return;
			}
			if (obj._inited == undefined) {
				obj._inited = false;
			}
			if (obj._inited) {
				return;
			}

			$.extend(true, obj, opts);

			obj._inited = true;

		},
		getCookie: function(cookieName, defaultValue) {
			cookieName = cookieName.replace(/-/g, '_');

			var myCookie = $.cookie(this.cookie.name + cookieName);
			if (myCookie == null && defaultValue != undefined) {
				myCookie = defaultValue;
			}
			return myCookie;
		},
		setCookie: function(cookieName, value) {
			cookieName = cookieName.replace(/-/g, '_');
			$.cookie(this.cookie.name + cookieName, value, this.cookie);
		},
		strpos: function(haystack, needle, offset) {
			if (offset < 0) {
				offset = 0;
				haystack = this.strrev(haystack);
			}
			var i = (haystack + '').indexOf(needle, (offset || 0));
			return i === -1 ? false : i;
		},
		strrev: function(string) {
			string = string + '';
			var grapheme_extend = /(.)([\uDC00-\uDFFF\u0300-\u036F\u0483-\u0489\u0591-\u05BD\u05BF\u05C1\u05C2\u05C4\u05C5\u05C7\u0610-\u061A\u064B-\u065E\u0670\u06D6-\u06DC\u06DE-\u06E4\u06E7\u06E8\u06EA-\u06ED\u0711\u0730-\u074A\u07A6-\u07B0\u07EB-\u07F3\u0901-\u0903\u093C\u093E-\u094D\u0951-\u0954\u0962\u0963\u0981-\u0983\u09BC\u09BE-\u09C4\u09C7\u09C8\u09CB-\u09CD\u09D7\u09E2\u09E3\u0A01-\u0A03\u0A3C\u0A3E-\u0A42\u0A47\u0A48\u0A4B-\u0A4D\u0A51\u0A70\u0A71\u0A75\u0A81-\u0A83\u0ABC\u0ABE-\u0AC5\u0AC7-\u0AC9\u0ACB-\u0ACD\u0AE2\u0AE3\u0B01-\u0B03\u0B3C\u0B3E-\u0B44\u0B47\u0B48\u0B4B-\u0B4D\u0B56\u0B57\u0B62\u0B63\u0B82\u0BBE-\u0BC2\u0BC6-\u0BC8\u0BCA-\u0BCD\u0BD7\u0C01-\u0C03\u0C3E-\u0C44\u0C46-\u0C48\u0C4A-\u0C4D\u0C55\u0C56\u0C62\u0C63\u0C82\u0C83\u0CBC\u0CBE-\u0CC4\u0CC6-\u0CC8\u0CCA-\u0CCD\u0CD5\u0CD6\u0CE2\u0CE3\u0D02\u0D03\u0D3E-\u0D44\u0D46-\u0D48\u0D4A-\u0D4D\u0D57\u0D62\u0D63\u0D82\u0D83\u0DCA\u0DCF-\u0DD4\u0DD6\u0DD8-\u0DDF\u0DF2\u0DF3\u0E31\u0E34-\u0E3A\u0E47-\u0E4E\u0EB1\u0EB4-\u0EB9\u0EBB\u0EBC\u0EC8-\u0ECD\u0F18\u0F19\u0F35\u0F37\u0F39\u0F3E\u0F3F\u0F71-\u0F84\u0F86\u0F87\u0F90-\u0F97\u0F99-\u0FBC\u0FC6\u102B-\u103E\u1056-\u1059\u105E-\u1060\u1062-\u1064\u1067-\u106D\u1071-\u1074\u1082-\u108D\u108F\u135F\u1712-\u1714\u1732-\u1734\u1752\u1753\u1772\u1773\u17B6-\u17D3\u17DD\u180B-\u180D\u18A9\u1920-\u192B\u1930-\u193B\u19B0-\u19C0\u19C8\u19C9\u1A17-\u1A1B\u1B00-\u1B04\u1B34-\u1B44\u1B6B-\u1B73\u1B80-\u1B82\u1BA1-\u1BAA\u1C24-\u1C37\u1DC0-\u1DE6\u1DFE\u1DFF\u20D0-\u20F0\u2DE0-\u2DFF\u302A-\u302F\u3099\u309A\uA66F-\uA672\uA67C\uA67D\uA802\uA806\uA80B\uA823-\uA827\uA880\uA881\uA8B4-\uA8C4\uA926-\uA92D\uA947-\uA953\uAA29-\uAA36\uAA43\uAA4C\uAA4D\uFB1E\uFE00-\uFE0F\uFE20-\uFE26]+)/g;
			string = string.replace(grapheme_extend, '$2$1');
			return string.split('').reverse().join('');
		},
		_resizeBlocks: function() {
			this._DOMinited = true;
			var self = this;
			self._resize();
			$.each(self.enableModules, function(idx, value) {
				if (value !== false && self[idx] !== undefined && self[idx]._resize !== undefined) {
					self[idx]._resize();
				}
			});
			this._DOMinited = false;
		},
		_scrollBlocks: function() {
			var self = this;
			$.each(self.enableModules, function(idx, value) {
				if (value !== false && self[idx] !== undefined && self[idx]._scroll !== undefined) {
					self[idx]._scroll();
				}
			});
		},
		_resize: function() {
			this._DOMinited = true;
			if ($('.sn-page').size() > 0) {
				// $('.sn-page-content').removeAttr('style');
				$('.sn-page-content').css({
					minHeight: Math.max($('.sn-page-columnLeft').height(), $('.sn-page-columnRight').height())
				});
			}
			this._DOMinited = false;
		},
		_documentClick: function(event) {
			this._DOMinited = true;
			var self = this;
			$.each(self.enableModules, function(idx, value) {
				if (value !== false && self[idx] !== undefined && self[idx]._documentClick !== undefined) {
					self[idx]._documentClick(event);
				}
			});
			this._DOMinited = false;
		},
		_unloadBlocks: function() {
			var self = this;
			this._DOMinited = true;
			$.each(self.enableModules, function(idx, value) {
				if (value !== false && self[idx] !== undefined && self[idx]._unload !== undefined) {
					self[idx]._unload();
				}
			});
			this._DOMinited = false;
		},
		_DOMSubtreeModified: function() {
			// if (this._DOMinited) { return; }
			this._DOMinited = true;
			this._textExpander();
			var self = this;
			$.each(self.enableModules, function(idx, value) {
				if (value !== false && self[idx] !== undefined && self[idx]._DOMChanged !== undefined) {
					self[idx]._DOMChanged();
				}
			});
			this._resize();
			this._DOMinited = false;

		},
		_textExpander: function() {
			if ($('.sn-expander-text:not([aria-expander="expander"])').size() != 0) {
				$('.sn-expander-text:not([aria-expander="expander"])').attr('aria-expander', 'expander').expander({
					slicePoint: 500,
					widow: 1,
					preserveWords: false,
					expandText: this.expanderTextMore,
					userCollapseText: this.expanderTextLess,
					expandPrefix: '...',
					userCollapsePrefix: ' ',
					moreClass: 'sn-expander-more',
					lessClass: 'sn-expander-less',
					detailClass: 'sn-expander-details'
				});
			}
		},
		_minBrowser: function() {
			var minBrowsers = {
				msie: 8,
				opera: 6,
				webkit: 12,
				mozilla: 6
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
				if (this.showBrowserOutdated && this.getCookie('sn_showBrowserOutdated', 0) == 0) {
					this.setCookie('sn_showBrowserOutdated', 1);
					snConfirmBox(this.browserOutdatedTitle, this.browserOutdated + '<br />' + $.browser.version);
				}
				this.isOutdatedBrowser = true;
				return false;
			}

			return true;
		},
		_debugInit: function() {
			var self = this;
			var dbg = $('<div />').attr('title', 'DEBUG');

			var dbg_IM = $('<div />').attr('id', 'IM_timer');
			var dbg_NTF = $('<div />').attr('id', 'NTF_timer');
			var dbg_browser = $('<div />').attr('id', 'browser').html('Browser');

			$.each($.browser, function(idx, val) {
				dbg_browser.html(dbg_browser.html() + '<br />&nbsp; &nbsp;' + idx + ': ' + val);
			});

			var IM_downcount = 1;
			var IM_counter = this.im.opts._imCounter;
			var NTF_downcount = this.ntf.checkTime / 1000 - 1;
			var NTF_counter = this.ntf.checkTime / 1000 - 1;

			dbg.appendTo('body');
			dbg_browser.appendTo(dbg);
			dbg_IM.appendTo(dbg);
			dbg_NTF.appendTo(dbg);
			dbg.dialog({
				position: "left bottom",
				buttons: {
					"Close debug": function() {
						$(this).dialog("close");
					}
				}

			});

			$(document).scroll(function() {
				dbg.dialog({
					position: 'left bottom'
				});
			});
			$(window).resize(function() {
				dbg.dialog({
					position: 'left bottom'
				});
			});

			$(document).everyTime(1000, 'sn-debug', function() {
				if (self.im.opts._imCounter != IM_counter || IM_counter - IM_downcount < 0) {
					IM_counter = self.im.opts._imCounter;
					IM_downcount = 0;
				}
				if (NTF_downcount < 0) {
					NTF_downcount = NTF_counter;
				}

				dbg_IM.html('IM check: ' + self.im.opts._imCounter + 's<br />IM check after: ' + (IM_counter - IM_downcount) + 's');
				dbg_NTF.html('NTF check after: ' + (NTF_downcount) + 's');
				IM_downcount++;
				NTF_downcount--;
			});
		}
	};
}(jQuery));

/**
 * Declaration for phpBB Social Network confirmBox
 * @param {object} $ jQuery
 * @param {object} $sn socialNetwork
 * @returns {void}
 */
(function($, $sn) {
	$sn.confirmBox = {
		dialogID: '#dialog',
		dialogClass: 'sn-confirmBox',
		enable: false,
		resizable: false,
		draggable: true,
		modal: true,
		width: '{S_SN_CB_WIDTH}',
		button_confirm: '{L_SN_US_CONFIRM}',
		button_cancel: '{L_SN_US_CANCEL}',
		button_close: '{L_SN_US_CLOSE}',
		shadowBox: {
			size: 8,
			backgroundColor: '#000',
			zIndex: $('#dialog').parent('.ui-dialog').css('z-index')
		},
		overlay: null,
		postMinChar: 10,
		_buttonsHeight: null,
		_titleHeight: null,
		_paddingHeight: null,
		init: function() {
			var self = this;
			if (this.enable) {

				$('<div class="ui-body-dialog"/>').attr('id', this.dialogID.replace('#', '')).css({
					display: 'none'
				})
				.attr('title', 'Title Confirm Box')
				.html('Content Confirm Box')
				.appendTo('body');

				$(this.dialogID).dialog({
					width: this.width,
					resizable: this.resizable,
					draggable: this.draggable,
					modal: this.modal,
					show: this.show,
					hide: this.show,
					autoOpen: true,
					dialogClass: 'sn-confirmBox',
					drag: function(event, ui) {
						self.overlay.children('div.ui-widget-shadow').css({
							top: ui.offset.top,
							left: ui.offset.left
						});
					},
					resize: function(event, ui) {
						self.overlay.children('div.ui-widget-shadow').css({
							top: ui.position.top,
							left: ui.position.left
						});
						self.correctSize();
					},
					resizeStop: function() {
						self.dropShadow($('.ui-dialog'), self.shadowBox);
						self.correctSize();
					},
					buttons: [{
							text: self.button_close,
							click: function() {
							}
						}]
				});
				var $dialog = $(this.dialogID).parent('.ui-dialog');
				this._buttonsHeight = $dialog.find('.ui-dialog-buttonpane').outerHeight();
				this._titleHeight = $dialog.find('.ui-dialog-titlebar').outerHeight();
				this._paddingHeight = parseInt($dialog.css('padding-top')) + parseInt($dialog.css('padding-bottom')) + parseInt($(this.dialogID).css('padding-top')) + parseInt($(this.dialogID).css('padding-bottom'));
				$(self.dialogID).dialog('close');
			}
		},
		correctSize: function() {
			$(this.dialogID).height($(this.dialogID).parent('.ui-dialog').height() - this._titleHeight - this._buttonsHeight - this._paddingHeight);
			if (this.overlay != null) {
				this.overlay.children('div.ui-widget-shadow').css({
					height: $(this.dialogID).parent('.ui-dialog').outerHeight(),
					width: $(this.dialogID).parent('.ui-dialog').outerWidth()
				});
			}
		},
		dropShadow: function(elem, attribs) {
			var snCB = this;
			return $(elem).each(function() {
				var self = $(this);

				var pself = self.position();
				var shCSS = $.extend(true, {
					position: 'absolute',
					top: pself.top,
					left: pself.left,
					width: self.innerWidth() + parseInt(self.css('border-left-width')) + parseInt(self.css('border-right-width')),
					height: self.innerHeight() + parseInt(self.css('border-top-width')) + parseInt(self.css('border-bottom-width'))
				}, attribs);

				if (self.is('[aria-shadow="true"]')) {
					$('.ui-overlay .ui-widget-shadow').css(shCSS);
					return;
				}

				if (attribs.opacity != undefined) {
					shCSS = $.extend({}, shCSS, {
						opacity: attribs.opacity
					});
				}
				if (attribs.size != undefined) {
					shCSS = $.extend({}, shCSS, {
						margin: -attribs.size + 'px 0 0 ' + (-attribs.size) + 'px',
						padding: attribs.size
					});
				}
				if (attribs.cornerRadius != undefined) {
					shCSS = $.extend({}, shCSS, {
						borderRadius: attribs.cornerRadius
					});

				}
				snCB.overlay = $('<div>').addClass('ui-overlay');
				var $overlay_w = $('<div />').addClass('ui-widget-overlay');

				if (attribs.overlayHidden) {
					$overlay_w.appendTo(snCB.overlay);
				}
				$('<div />').addClass('ui-widget-shadow ui-corner-all sn-shadow').css(shCSS).appendTo(snCB.overlay);

				snCB.overlay.insertBefore(self);
				self.css({
					position: 'absolute',
					top: pself.top,
					left: pself.left,
					width: self.width(),
					height: self.height()
				}).attr('aria-shadow', 'true');

			});
		}
	};
}(jQuery, socialNetwork));

/**
 * Declaration for phpBB Social Network comments
 * @param {object} $ jQuery
 * @param {object} $sn socialNetwork
 * @returns {void}
 */
(function($, $sn) {
	$sn.comments = {
		deleteTitle: 'Delete',
		deleteText: 'Delete Text',
		watermark: 'Watermark',
		init: function() {
			var self = this;
			var confirmBox = $sn.confirmBox;
			// Delete comment
			$(".sn-deleteComment").live('click', function() {
				var comment_id = $sn.getAttr($(this), "cid");
				var mUrl = $sn.getAttr($(this), 'url');
				var comment = $('#sn-comment' + comment_id).html();
				snConfirmBox(self.deleteTitle, self.deleteText + '<hr />' + comment, function() {
					$.ajax({
						type: "POST",
						url: mUrl,
						cache: false,
						data: {
							smode: 'comment_delete',
							c_id: comment_id
						},
						success: function(data) {
							$('#sn-comment' + comment_id).fadeOut('slow').remove();
						}
					});
				});
				if ($(confirmBox.dialogID).find('.sn-expander-more').size() != 0) {
					$(confirmBox.dialogID).find('.sn-expander-more, .sn-expander-less').remove();
					$(confirmBox.dialogID).find('.sn-expander-details').show();
					$(confirmBox.dialogID).find('.sn-expander-text').removeAttr('aria-expander');
				}
			});

			self.waterMark();
		},
		waterMark: function() {
			$(".sn-inputComment").watermark($sn.comments.watermark, {
				useNative: false,
				className: 'sn-watermark'
			}).elastic({
				showNewLine: true,
				parentElement: '.sn-shareComment',
				submitElement: 'input[name="sn-us-buttonComment"]'
			});
		}
	};
}(jQuery, socialNetwork));
