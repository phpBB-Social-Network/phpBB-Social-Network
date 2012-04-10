/**
 * 
 * @package phpBB Social Network
 * @version 0.6.3
 * @copyright (c) 2010-2012 Kamahl & Culprit http://phpbbsocialnetwork.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * 
 */
(function($) {
	$.sn.im = {
		opts : {
			_imCounter : 0,
			_namesChat : 'sn-im-chatTimer',
			_inCore : false,
			_aExpMin : 16,
			_aExpMax : 64,

			lastCheckTime : 0,
			timersMin : 1,
			timersMax : 60,
			curPosit : 0,
			maxChatBoxes : 4,
			sound : false,
			sendSequence : {
				alt : false,
				ctrl : false,
				shift : false,
				key : 13
			},
			closeSequence : {
				alt : false,
				ctrl : false,
				shift : false,
				key : 27
			},
			url : './socialnet/im.php',
			rootPath : './socialnet/',
			isOnline : false,
			namesMe : 'My username',
			newMessage : 'New message',
			youAreOffline : 'You are offline',
			pageTitle : 'page title',
			hideButton : false
		},

		init : function(options) {
			if (!$.sn._inited) {
				return false;
			}
			if ($.sn.enableModules.im == undefined || !$.sn.enableModules.im) {
				return false;
			}
			var opts = this.opts;
			$.sn._settings(opts, options);

			opts._imCounter = $.sn.getCookie('sn-im-curCheckTime', 1);
			opts.curPosit = $.sn.getCookie('sn_im_curPosit', 0);
			opts.pageTitle = $(document).attr('title');
			opts.soundFile = $('#sn-im-msgArrived a').attr('href');
			opts.soundFlashVars = $('#sn-im-msgArrived a').attr('title');

			this._resize();

			/** Bottom Button Click - NOT ONLINE LIST */
			$('.sn-im-chatBoxes .sn-im-button').live('click', function() {
				var self = this;
				var cBlock = $(this).next('.sn-im-block');
				var id = $(cBlock).attr('id');

				$('.sn-im-chatBoxes .sn-im-button.sn-im-opener').each(function() {
					if (self !== this) {
						$.sn.im._cwClose($(this).parents('.sn-im-chatBox'));
					}
				});

				$.sn.im._cwToggle($(this).parents('.sn-im-chatBox'));

				$(cBlock).find('.sn-im-message').focus();

			});
			/**
			 * Bottom Button Click - ONLINE LIST
			 */
			$('.sn-im-online.sn-im-button').live('click', function() {
				$.sn.im._onlineListLoad();
				$.sn.im._cwToggle($(this).parents('#sn-im-online'));
			})
			/**
			 * Title Click - CLOSE
			 */
			$('.sn-im-block .sn-im-title .sn-userName').live('click', function() {
				$.sn.im._cwClose($(this).parents('.sn-im-chatBox'));
			});
			$('.sn-im-online .sn-im-title').live('click', function() {
				$.sn.im._cwClose($(this).parents('#sn-im-online'));
			})
			/**
			 * FOCUS ON CHATBOX
			 */
			$('.sn-im-msgs').live('click', function() {
				$(this).next('.sn-im-textArea').find('.sn-im-message').focus();
			})

			/** TEXTAREA EXPANDER */
			$('textarea.sn-im-message').live('keyup', function(e) {
				$.sn.im._messageKey(this, e);
			}).TextAreaExpander($.sn.im.opts._aExpMin, $.sn.im.opts._aExpMax);

			/** OPEN CHAT BOX */
			$('.sn-im-canchat').live('click', function() {
				var uid = $.sn.getAttr($(this), 'user');

				$('.sn-im-chatBox').each(function() {
					$.sn.im._cwClose($(this));
				});

				if ($('#sn-im-chatBox' + uid).size() > 0) {
					$.sn.im._cwOpen($('#sn-im-chatBox' + uid));
				} else {
					$.sn.im._cwCreate(uid, $.sn.getAttr($(this), 'username'), true);
				}
			});

			/** DESTROY CHAT BOX */
			$('.sn-im-close').live('click', function() {
				var cb = $(this).parents('.sn-im-chatBox');
				$.sn.im._cwDestroy(cb);
				return false;
			});
			$('.sn-im-cbClose').live('click', function() {
				$(this).parents('.sn-im-chatBox').find('.sn-im-close').trigger('click');
			});

			/** IM LOGIN/LOGOUT */
			$('.sn-im-loginlogout label').on('click', function() {
				if ($(this).hasClass('sn-im-selected'))
					return;

				var lMode = $.trim($(this).attr('class'));
				var parent = $(this).parents('.sn-im-loginlogout');
				parent.find('label').toggleClass('sn-im-selected');

				$.ajax({
					type : 'post',
					cache : false,
					async : true,
					url : $.sn.im.opts.url,
					data : {
						mode : lMode
					},
					success : function(data) {
						$.sn.im.opts.isOnline = (data.login != undefined && data.login == true);
						var bgItem = $('#sn-im-onlineCount .label');
						var bg = bgItem.css('background-image');
						if ($.sn.im.opts.isOnline) {
							bg = bg.replace(/offline\.png/i, 'online.png');
							$.sn.im._startTimers();
						} else {
							bg = bg.replace(/online\.png/i, 'offline.png');
							$('.sn-im-close').each(function() {
								$.sn.im._cwDestroy($(this).parents('.sn-im-chatBox'));
								return false;
							});
							$('#sn-im').stopTime($.sn.im.opts._namesChat);

						}
						bgItem.css('background-image', bg);
						$.sn.im._onlineListLoad();
					}
				});
			});

			/** SOUND ON/OFF */
			$('.sn-im-sound').on('click', function() {
				var sA = $(this).hasClass('ui-icon-volume-on');
				$.ajax({
					type : 'post',
					cache : false,
					async : true,
					url : $.sn.im.opts.url,
					data : {
						mode : 'snImSound' + (sA ? 'Off' : 'On')
					},
					success : function(data) {
						if (data == null)
							return;
						var $sound = $('.sn-im-sound.ui-icon');
						$sound.toggleClass('ui-icon-volume-on ui-icon-volume-off');

						// jQuery.ui.tooltip must exists
						var descr = $sound.attr('aria-describedby');
						var $soundT = $('#' + descr + ' .ui-tooltip-content');
						if ($sound.hasClass('ui-icon-volume-on')) {
							$sound.attr('title', $sound.attr('title').replace('OFF', 'ON'));
							$soundT.html($soundT.html().replace('OFF', 'ON'));
						} else {
							$sound.attr('title', $sound.attr('title').replace('ON', 'OFF'));
							$soundT.html($soundT.html().replace('ON', 'OFF'));
						}
						$.sn.im.opts.sound = data.sound;
					}
				});

			});

			/** HIDE/SHOW FRIENDS GROUP */
			$('.sn-im-hideGroup').live('click', function() {
				var gid = $.sn.getAttr($(this), 'gid');
				var hidden = $(this).hasClass('ui-icon-arrowstop-1-n');

				$.ajax({
					type : 'post',
					cache : false,
					async : true,
					url : $.sn.im.opts.url,
					data : {
						mode : 'snImUserGroup' + (hidden ? 'Show' : 'Hide'),
						gid  : gid
					},
					success : function(data) {
    				$('#sub_gid'+gid).toggle();
						$('#gid_'+gid+' .sn-im-hideGroup').toggleClass('ui-icon-arrowstop-1-n ui-icon-arrowstop-1-s');
					}
				});
			});

			/** MESSAGE TIME */
			$('.sn-im-msg').live('mouseover', function() {
				$(this).find('.sn-im-msgTime').show();
				
			}).live('mouseout', function() {
				$(this).find('.sn-im-msgTime').hide();
			});
			$('.sn-im-msgs').live('mouseout', function() {
				$(this).find('.sn-im-msgTime').fadeOut(500);
			});

			/** Zobraz IM */
			$('#sn-im').removeAttr('style');
			this._scrollable();
			if ($('.sn-im-block .sn-im-msgs:visible').is(':visible')) {
				var $block = $('.sn-im-block .sn-im-msgs:visible').parents('.sn-im-block');
				this._cwClose($block);
				this._cwOpen($block, false);
			}
			this._startTimers();
		},
		/** INIT END */

		/** MESSAGE AREA - KEY UP */
		_messageKey : function(obj, e) {
			var code = (e.keyCode ? e.keyCode : e.which);

			if ($.sn.isKey(e, $.sn.im.opts.closeSequence)) {
				$.sn.im._cwDestroy($(obj).parents('.sn-im-chatBox'));
				return false;
			}
			if ($.sn.isKey(e, $.sn.im.opts.sendSequence)) {
				var msg = $(obj).val();
				var getC = $.sn.getCaret(obj) + ($.browser.msie && $.browser.version < 9 ? 1 : 0);
				if (getC != msg.length) {
					msg = msg.substring(0, getC - 1) + msg.substring(getC);
				}
				msg = msg.replace(/\s*/i, '');
				if (msg == '')
					return;

				var msgs = $(obj).parents('.sn-im-block').find('.sn-im-msgs');
				$.ajax({
					type : 'post',
					cache : false,
					async : true,
					url : $.sn.im.opts.url,
					data : {
						mode : 'sendMessage',
						uid : $.sn.getAttr($(obj), 'uid'),
						pp : $.sn.getAttr($(msgs).find('.sn-im-msg:last'), 'from'),
						message : msg
					},
					success : function(data) {
						msgs.append(data.message);
						msgs.scrollTop(99999);
						$.sn.im._onlineUsersCB(data.onlineUsers);
						$.sn.im._startTimers(true);
					}
				});
				$(obj).val('').css('height', $.sn.im.opts._aExpMin);
			}

		},

		/** CHECK - NEW MESSAGES */
		_core : function() {
			if ($.sn.im.opts._inCore)
				return;
			$.sn.im.opts._inCore = true;

			$.ajax({
				type : 'post',
				cache : false,
				async : false,
				url : $.sn.im.opts.url,
				data : {
					mode : 'coreIM',
					lastCheckTime : $.sn.im.opts.lastCheckTime
				},
				success : function(data) {

					if (data.message != undefined && data.message != null && data.message.length != 0) {
						// MSG is unread
						if (data.recd == false) {
							// Play sound
							if ($.sn.im.opts.sound) {
								if ($.browser.msie) {
									$('#sn-im-msgArrived').html('<object height="1" width="1" type="application/x-shockwave-flash" data="' + $.sn.im.opts.soundFile + '"><param name="movie" value="' + $.sn.im.opts.soundFile + '"><param name="FlashVars" value="' + $.sn.im.opts.soundFlashVars + '"></object>');
								} else {
									$('#sn-im-msgArrived').html('<embed src="' + $.sn.im.opts.soundFile + '" width="0" height="0" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" FlashVars="' + $.sn.im.opts.soundFlashVars + '"></embed>');
								}
							}
							// Title Alert
							$.titleAlert($.sn.im.opts.newMessage, {
								requireBlur : true,
								stopOnFocus : true,
								duration : 0,
								interval : 1500
							});
						}
						$.each(data.message, function(i, message) {
							var chatBox = '#sn-im-chatBox' + message.uid;
							if (message.chatBox == false) {
								$.sn.im._cwCreate(message.uid, message.userName, false);
								if ($('#sn-im-chatBoxes .sn-im-chatBox').length > 1) {
									$.sn.im._cwClose($(chatBox));
								}
							} else {
								var $msgs = $(chatBox).find('.sn-im-msgs');
								var $lmsg = $msgs.find('.sn-im-msg:last');
								var from = $.sn.getAttr($lmsg, 'from');
								if ( $msgs.find('.sn-im-msg[class*="'+message.time+'"]').size() == 0){
									$msgs.append(message.message);
									$msgs.scrollTop(99999);
									if (from == message.uid) {
										$msgs.find('.sn-im-msg:last').addClass('sn-im-noborderTop');
									}
								}
							}
							if ($(chatBox + ' .sn-im-block').is(':hidden')) {
								$.sn.im._unRead($(chatBox), 1);
							}
						});
						$.sn.im._startTimers(true);
					}
					$.sn.im.opts.lastCheckTime = data.lastCheckTime;
					
					$.sn.im._onlineList(data);
					$.sn.im._onlineUsersCB(data.onlineUsers);
					$.sn.im.opts._inCore = false;
				}
			});

		},

		/** CASOVAC PRO CORE */
		_startTimers : function(sh) {
			var tHandler = $('#sn-im');
			if (!$.sn.allow_load) {
				tHandler.stopTime($.sn.im.opts._namesChat);
				return;
			}
			if (typeof(sh) != 'undefined' && sh) {
				$.sn.im.opts._imCounter = $.sn.im.opts.timersMin;
				tHandler.stopTime($.sn.im.opts._namesChat);
			} else {
				$.sn.im.opts._imCounter++;
			}

			if ($.sn.im.opts._imCounter >= $.sn.im.opts.timersMax) {
				$.sn.im.opts._imCounter = $.sn.im.opts.timersMax;
			}
			$.sn.setCookie('sn-im-curCheckTime', $.sn.im.opts._imCounter);

			
			
			tHandler.oneTime($.sn.im.opts._imCounter * 1000, $.sn.im.opts._namesChat, function(i) {
				$.sn.im._core();
				$.sn.im._startTimers();
			});
		},

		/**
		 * Nacteni online listu
		 * 
		 * @param {Integer}
		 *            i Pocet volani procedury, generovano z pluginu timers
		 */
		_onlineListLoad : function(i) {
			if ($.sn.im.opts.isOnline == true) {
				$.ajax({
					type : 'post',
					cache : false,
					async : true,
					url : $.sn.im.opts.url,
					data : {
						mode : 'onlineUsers'
					},
					success : function(data) {
						$.sn.im._onlineList(data);
						$.sn.im._onlineUsersCB(data.onlineUsers);
					}
				});
			} else {
				$('#sn-im-onlineList').html('<div class="sn-im-userLine">' + $.sn.im.opts.youAreOffline + '</div>');
			}
		},

		_onlineList : function(data) {
			$('#sn-im-onlineCount span.count').html('(' + data.onlineCount + ')');
			$('#sn-im-onlineList').html(data.onlineList);
			// $('.sn-im-userLine').textOverflow('...', false);

		},

		/**
		 * ONLINE check for chatbox
		 */
		_onlineUsersCB : function(users) {
			if (users == undefined)
				return;
			$.each($('#sn-im-chatBoxes').children('.sn-im-chatBox'), function(idx, o) {
				if (users[$.sn.getAttr($(o), 'uid')] !== undefined) {
					$(o).find('.sn-im-status').removeClass('sn-im-offline').addClass('sn-im-online');
				} else {
					$(o).find('.sn-im-status').removeClass('sn-im-online').addClass('sn-im-offline');
				}
			});
		},

		_cwOpen : function(obj, focus) {
			if (typeof focus == 'undefined') {
				focus = true;
			}
			var id = obj.attr('id');
			var im_button = obj.find('.sn-im-button');
			im_button.addClass('sn-im-opener');
			obj.find('.sn-im-block').show();
			if (focus) {
				obj.find('.sn-im-message').focus();
			}
			obj.find('.sn-im-msgs').scrollTop(99999);
			
			$.sn.setCookie(id, true);

			$.sn.im._unRead(obj,0);
			//obj.find('.sn-im-unRead').html('0').hide();
			//$.sn.setCookie(id + 'Unread', 0);

		},
		_cwClose : function(obj) {
			var id = obj.attr('id');
			var im_button = obj.find('.sn-im-button');
			im_button.removeClass('sn-im-opener');
			obj.find('.sn-im-block').hide();

			$.sn.setCookie(id, false);
			$.sn.im._unRead(obj,0);
			//obj.find('.sn-im-unRead').html('0').hide();
			//$.sn.setCookie(id + 'Unread', 0);

		},
		_cwToggle : function(obj) {
			if (obj.find('.sn-im-button').hasClass('sn-im-opener'))
				$.sn.im._cwClose(obj);
			else
				$.sn.im._cwOpen(obj);
		},

		_cwDestroy : function(obj) {
			var id = obj.attr('id');
			var uidTo = $.sn.getAttr(obj, 'uid');
			$.ajax({
				type : 'post',
				cache : false,
				async : true,
				url : $.sn.im.opts.url,
				data : {
					mode : 'closeChatBox',
					uid : uidTo
				}
			});
			obj.remove();
			$.sn.setCookie(id, null);
			$.sn.setCookie(id + 'Unread', null);
			$.sn.im._scrollable(10);
		},

		_cwCreate : function(uid, userName, bAsync) {
			if ($.sn.im.opts.isOnline == 0 || $('#sn-im-chatBox' + uid).size() != 0)
				return;

			if (bAsync == undefined)
				bAsync = false;

			$.ajax({
				type : 'post',
				cache : false,
				url : $.sn.im.opts.url,
				async : bAsync,
				data : {
					mode : 'openChatBox',
					userTo : uid,
					usernameTo : userName
				},
				success : function(data) {
					if ($('#sn-im-chatBox' + uid).size() != 0)
						return;
					$('#sn-im-chatBoxes').append(data.html);
					var cb = $('#sn-im-chatBox' + uid);
					cb.find('.sn-im-message').TextAreaExpander($.sn.im.opts._aExpMin, $.sn.im.opts._aExpMax);
					$.sn.im._cwOpen(cb);
					$.sn.im._scrollable(20);
				}
			});
		},

		_unRead : function(chatBox, c) {
			var $snImUnread = $(chatBox).find('.sn-im-unRead');
			var endValue = parseInt($snImUnread.text());
			if (c == 0) {
				endValue = 0;
				$snImUnread.hide();
			} else {
				endValue += c;
				$snImUnread.show();
			}
			$snImUnread.html(endValue);
			$.sn.setCookie('sn-im-chatBox' + $.sn.getAttr($(chatBox),'uid') + 'Unread', endValue);

		},

		/**
		 * Posouvani chat boxiku
		 * 
		 * @param {Integer}
		 *            m operace pro scroll 10 - zavreni chat boxiku 20 -
		 *            vytvoreni chat boxiku 1 - posun vpravo 2 - posun vlevo 0 -
		 *            zaciname
		 */
		_scrollable : function(m) {
			var $nav = $('#sn-im-chatBoxes');
			if ($nav.children().length > this.maxChatBoxes) {
				switch (m) {
				case 10:
					if (this.opts.curPosit === 0) {
						this.opts.curPosit--;
					}
					break;
				case 20:
					this.opts.curPosit++;
					break;
				case 1:
					this.opts.curPosit--;
					break;
				case 2:
					this.opts.curPosit++;
					break;
				/*
				 * case 0: default: break;
				 */
				}
				$.sn.setCookie('sn_im_curPosit', this.opts.curPosit);
				for (i = 0; i < this.opts.maxChatBoxes; i++) {
					$snImCB.children(this.opts.curPosit + i).show();
				}
				$nav.children(':lt(' + this.opts.curPosit + '):visible').hide();
				$nav.children(':gt(' + (this.opts.curPosit + this.opts.maxChatBoxes - 1) + '):visible').hide();
			} else {
				$nav.children('.sn-im-chatBox').show();
			}

			if ($nav.children(':first').is(':visible')) {
				$('.sn-im-prev:visible').hide();
			} else {
				$('.sn-im-prev:hidden').show();
			}

			if ($nav.children(':last').is(':visible')) {
				$('.sn-im-next:visible').hide();
			} else {
				$('.sn-im-next:hidden').show();
			}
			if ($nav.children('.sn-im-chatBox').length === 0) {
				$('.sn-im-nav').hide();
			}
		},

		/**
		 * Resize bloku, ktere si to zadazi pri zmene okna
		 */
		_resize : function() {
			$('#sn-im #sn-im-onlineList').css('max-height', ($(window).height() - 100 > 50 ? $(window).height() - 100 : 50) + 'px');
		},

		_documentClick : function(event) {
			// ZAVRIT ONLINE LIST PRI KLIKNUTI MIMO
			if ($('#sn-im-onlineCount').hasClass('sn-im-opener')) {
				var s_obj = $('#sn-im-online #sn-im-onlineBlock:visible').parents('#sn-im').attr('id');
				if (!$(event.target).closest('#' + s_obj).size()) {
					$('#sn-im-onlineCount').trigger('click');
				}
			}

		}

	}

})(jQuery);