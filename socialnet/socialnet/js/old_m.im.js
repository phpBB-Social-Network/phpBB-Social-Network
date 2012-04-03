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
		currentCheckTime : 0,
		textOverflow : '...',
		url : './socialnet/im.php',
		rootPath : './socialnet/',
		newMessage : '{L_SN_IM_NEW_MESSAGE}',
		pageTitle : '',
		namesMe : '{S_SN_USERNAME}',
		youAreOffline : '{L_SN_IM_YOU_ARE_OFFLINE}',
		sound : false,
		curChatBoxPos : 0,
		maxChatBoxes : 4,
		isOnline : false,
		timersChatOpen : 5,
		timersChatClose : 60,
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
		_imCounter : 0,
		_imChatClose : 60000,
		_imChatOpen : 1000,
		_namesChat : 'sn-im-chatTimer',
		_namesOnline : 'sn-im-onlineListTimer',
		lastCheckTime : 0,

		init : function(opts) {
			if (!$.sn._inited) {
				return false;
			}
			if ($.sn.enableModules.im == undefined || !$.sn.enableModules.im) {
				return false;
			}
			$.sn._settings(this, opts);

			if ($.sn.mobileBrowser) {
				// $('#' +
				// this.baseClassName).addClass('sn-im-dockWrapper-mobile');
			}

			this.currentCheckTime = $.sn.getCookie('sn-im-curCheckTime');
			if (this.currentCheckTime != null) {
				this._imCounter = this.currentCheckTime;
			}

			this.pageTitle = $(document).attr('title');

			this._resize();
			// $.metadataInit();

			$('#sn-im .sn-im-button').textOverflow('...', false);

			// BOTTOM BUTTON CLICK
			$('.sn-im-chatBoxes .sn-im-button').live('click', function() {
				var cBlock = $(this).next('.sn-im-block');
				$(cBlock).toggle();
				$(this).toggleClass('sn-im-opener');
				$(cBlock).toggleClass('sn-im-openBlock');

				$.sn.setCookie($(cBlock).attr('id'), $(this).hasClass('sn-im-opener'));
				$('> .sn-im-textArea textarea.sn-im-message', cBlock).focus();
				$('> .sn-im-unRead', this).hide().html('0');
				$.sn.setCookie($(cBlock).attr('id') + 'Unread', '0');
				var snMsgs = $(this).next('.sn-im-block').children('.sn-im-msgs');
				$(snMsgs).scrollTop(99999);

				$('.sn-im-chatBoxes .sn-im-button:not([id=' + $(cBlock).attr('id') + '])').each(function() {
					var oBlock = $(this).next('.sn-im-block');
					if ($(oBlock).attr('id') === $(cBlock).attr('id')) {
						return;
					}

					$(oBlock).hide().removeClass('sn-im-openBlock');
					$(this).removeClass('sn-im-opener');
					$.sn.setCookie($(oBlock).attr('id'), false);
				});
			}).each(function() {
				// Nastav neprectene
				var cBlock = $(this).next('.sn-im-block');
				var unReadCnt = $.sn.getCookie($(cBlock).attr('id') + 'Unread');
				if (unReadCnt !== 0 && unReadCnt !== null) {
					$('> .sn-im-unRead', this).show().html(unReadCnt);
				}
			});

			$('.sn-im-unRead').each(function() {
				$(this).css('display', $(this).html() === '0' ? 'none' : 'inline');
			});

			// Otevreni/zavreni Online listu pri kliknuti na button
			$('#sn-im-online .sn-im-button').click(function() {
				var cBlock = $(this).next('.sn-im-block');
				$(cBlock).toggle();
				$(this).toggleClass('sn-im-opener');
				var hasCls = $(this).hasClass('sn-im-opener');
				$.sn.setCookie($(cBlock).attr('id'), hasCls);
				if (hasCls) {
					$.sn.im._onlineListLoad();
				}
			});

			// ODHLASENI/PRIHLASENI Z IM - DISABLE IM
			$('.sn-im-loginlogout label').click(function() {
				if ($(this).hasClass('sn-im-selected')) {
					return;
				}

				var parent = $(this).parents('.sn-im-loginlogout');
				parent.children('label').toggleClass('sn-im-selected');
				var lMode = $.trim($('.sn-im-selected').attr('class').replace('sn-im-selected', ''));

				$.ajax({
					type : 'post',
					async : true,
					cache : false,
					url : $.sn.im.url,

					data : {
						mode : lMode
					},
					success : function(data) {
						var bgItem = $('#sn-im-onlineCount .label');

						if (data.login === true) {
							$('#sn-im-onlineList').html('');
							$.sn.im.isOnline = true;
							// startTimers(true);
							var bg = bgItem.css('background-image');
							bg = bg.replace(/offline\.png/i, 'online.png');
							bgItem.css('background-image', bg);
							$.sn.im._onlineListLoad();
						}

						if (data.logout === true) {
							// stopTimers();
							$('#sn-im-onlineCount span.count').html('(0)');
							$('#sn-im-onlineList').html('<div class="sn-im-userLine">' + $.sn.im.youAreOffline + '</div>');
							$('.sn-im-close').each(function() {
								$(this).trigger('click');
							});
							var bg = bgItem.css('background-image');
							bg = bg.replace(/online\.png/i, 'offline.png');
							bgItem.css('background-image', bg);

							$.sn.im.isOnline = false;
						}
					}
				});

			});

			// Zapni / vypni sound
			$('.sn-im-sound').click(function() {
				var sound_attr = $('.sn-im-sound.ui-icon').hasClass('ui-icon-volume-on');
				var $mode = 'snImSound' + (sound_attr ? 'Off' : 'On');
				$.ajax({
					type : 'post',
					cache : false,
					async : true,
					url : $.sn.im.url,
					data : {
						mode : $mode
					},
					success : function(data) {
						if (data == null) {
							return false;
						}
						$('.sn-im-sound.ui-icon').toggleClass('ui-icon-volume-on ui-icon-volume-off');
						$.sn.im.sound = data.sound;
					}
				});

			});

			$('.sn-im-title .sn-userName').live('click', function() {
				$.sn.im._windowRollDown($(this).parents('.sn-im-title').parents('.sn-im-group'));
			});
			$('#sn-im-onlineBlock .sn-im-title').on('click', function() {
				$.sn.im._windowRollDown($(this).parents('.sn-im-group'));
			});
			// otevreni chat boxu
			$('.sn-im-canchat').live('click', function() {
				var uid = $.sn.getAttr($(this), 'user');

				$('.sn-im-chatBox').each(function() {
					$(this).children('.sn-im-button').removeClass('sn-im-opener');
					var snImBlock = $(this).children('.sn-im-block');
					snImBlock.hide().removeClass('sn-im-openBlock');
					$.sn.setCookie(snImBlock.attr('id'), false);
				});
				if ($('#sn-im-chatBox' + uid).size() > 0) {
					$('#sn-im-chatBox' + uid + ' .sn-im-button').addClass('sn-im-opener');
					$('#sn-im-chatBox' + uid + ' .sn-im-block').show().addClass('sn-im-openBlock');
					$.sn.setCookie('sn-im-chatBoxBlock' + uid, true);
					$('#sn-im-chatBox' + uid + ' textarea.sn-im-message').focus();
					return;
				}

				$.sn.im._makeChatBox(uid, $.sn.getAttr($(this), 'username'), true);
			});
			// zavreni chat boxu
			$('.sn-im-close').live('click', function() {
				var but = $(this).parents('.sn-im-chatBox');
				var uid_to = $(but).attr('id').replace('sn-im-chatBox', '');

				$.ajax({
					type : 'post',
					cache : false,
					async : true,
					url : $.sn.im.url,
					data : {
						mode : 'closeChatBox',
						uid : uid_to
					}
				});
				$.sn.setCookie('sn-im-chatBoxBlock' + uid_to, null);
				$.sn.setCookie('sn-im-chatBoxBlock' + uid_to + 'Unread', -1);
				$(but).remove();
				$.sn.im._scrollable(10);
			});

			$('.sn-im-cbClose').parents('a').on('click', function() {
				$(this).parents('.sn-im-block').prev('.sn-im-button').find('.sn-action-close').trigger('click');
			});

			$('.sn-im-msgs').on('click', function() {
				$(this).next('.sn-im-textArea').find('.sn-im-message').trigger('focus');
			})

			$('.sn-im-chatBox textarea.sn-im-message').TextAreaExpander(16, 64).bind('keyup', function(e) {
				$.sn.im._messageKey(this, e);
			}).metadataInit('uid');

			$.sn.im.soundFile = $('#sn-im-msgArrived a').attr('href');
			$.sn.im.soundFlashVars = $('#sn-im-msgArrived a').attr('title');

			$.sn.im.curPosit = $.sn.getCookie('sn_im_curPosit');
			if ($.sn.im.curPosit === null) {
				$.sn.setCookie('sn_im_curPosit', 0);
				$.sn.im.curPosit = 0;
			}

			// Vrati time ago pre message
			$('.sn-im-msg').live('mouseover', function() {
				var cObj = $(this).children('.sn-im-msgTime');
				// $('.'+$.sn.im.baseClassName+'-msgTime:visible').hide();

				var mTime = $.sn.getAttr($(this), 'time');

				$.ajax({
					type : 'post',
					cache : false,
					async : true,
					url : $.sn.im.url,
					data : {
						mode : 'msg_time',
						msg_time : mTime
					},
					success : function(data) {
						$(cObj).html(data.timeAgo).show();
					}
				});
			}).live('mouseout', function() {
				var cObj = $(this).children('.sn-im-msgTime');
				var $this = this;
				$(document).oneTime(250, $($this).attr('time'), function() {
					$(cObj).hide();
				});
			});

			$('.sn-im-nav.sn-im-prev').click(function() {
				$.sn.im._scrollable(1);
			});
			$('.sn-im-nav.sn-im-next').click(function() {
				$.sn.im._scrollable(2);
			});

			this._startTimers();

			$('#sn-im').removeAttr('style');
			this._scrollable();
			$('.sn-im-chatBoxes .sn-im-button').each(function() {
				var snMsgs = $(this).next('.sn-im-block').children('.sn-im-msgs');
				$(snMsgs).scrollTop(99999);
			});
		},

		/**
		 * Vyzvedavani zprav
		 * 
		 * @param {Integer}
		 *            i Pocet volani procedury, generovano z pluginu timers
		 */
		_coreChat : function(i) {
			if (!$.sn.allow_load) {
				return;
			}

			if (!$.sn.im.isOnline) {
				$.sn.im._stopTimers();
				return;
			}

			$.ajax({
				type : 'post',
				cache : false,
				async : false,
				url : $.sn.im.url,
				data : {
					mode : 'coreIM',
					lastCheckTime : $.sn.im.lastCheckTime
				},
				success : function(data) {
					$.sn.im.lastCheckTime = data.lastCheckTime;
					$('#sn-im-onlineCount span.count').html('(' + data.onlineCount + ')');
					if (data.message != null && data.message.length != 0) {
						$.sn.im._startTimers(true);
						if ($.sn.im.sound) {
							if ($.browser.msie) {
								$('#sn-im-msgArrived').html('<object height="1" width="1" type="application/x-shockwave-flash" data="' + $.sn.im.soundFile + '"><param name="movie" value="' + $.sn.im.soundFile + '"><param name="FlashVars" value="' + $.sn.im.soundFlashVars + '"></object>');
							} else {
								$('#sn-im-msgArrived').html('<embed src="' + $.sn.im.soundFile + '" width="0" height="0" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" FlashVars="' + $.sn.im.soundFlashVars + '"></embed>');
							}
						}
						$.titleAlert($.sn.im.newMessage, {
							requireBlur : true,
							stopOnFocus : true,
							duration : 0,
							interval : 1500
						});
						$.each(data.message, function(i, message) {
							if (message.chatBox === false) {
								$.sn.im._makeChatBox(message.uid, message.userName, false);
								if ($('#sn-im-chatBoxes').children().length > 1) {
									$('#sn-im-chatBox' + message.uid + ' .sn-im-button').removeClass('sn-im-opener');
									$('#sn-im-chatBox' + message.uid + ' .sn-im-block').hide().removeClass('sn-im-openBlock');
									$.sn.setCookie($('#sn-im-chatBox' + message.uid + ' .sn-im-block').attr('id'), false);
									$('#sn-im-chatBoxBlock' + message.uid).hide();
								}
							} else {
								var msgs = $('#sn-im-chatBoxBlock' + message.uid + ' .sn-im-msgs');
								var from = $.sn.getAttr($(msgs).find('.sn-im-msg:last'), 'from');
								$(msgs).append(message.message).scrollTop(99999);
								if (from == message.uid) {
									$(msgs).find('.sn-im-msg:last').addClass('sn-im-noborderTop');
								}
							}
							if ($('#sn-im-chatBoxBlock' + message.uid).is(':hidden')) {
								var $snImUnRead = $('#sn-im-chatBox' + message.uid + ' .sn-im-unRead');
								$snImUnRead.show();
								$snImUnRead.html(parseInt($snImUnRead.html()) + 1);
								$.sn.setCookie('sn-im-chatBoxBlock' + message.uid + 'Unread', $snImUnRead.html());
							}

						});
					}

					$.sn.im._onlineUsersCB(data.onlineUsers);

					$('#sn-im-onlineList').html(data.onlineList);
					$('.sn-im-userLine').textOverflow('...', false);
				}
			});
		},
		/**
		 * Resize bloku, ktere si to zadazi pri zmene okna
		 */
		_resize : function() {
			$('#sn-im #sn-im-onlineList').css('max-height', ($(window).height() - 100 > 50 ? $(window).height() - 100 : 50) + 'px');
		},

		_getCaret : function(el) {
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

		/**
		 * Event pri upusteni klavesy pro odeslani zpravy
		 * 
		 * @param {Object}
		 *            obj textarea ktere se to tyka
		 * @param {Object}
		 *            e informace z klavesnice
		 */
		_messageKey : function(obj, e) {
			var code = (e.keyCode ? e.keyCode : e.which);
			var endSeq = this.sendSequence;
			var clsSeq = this.closeSequence;
			var msgs = $('> .sn-im-msgs', $(obj).parents('.sn-im-block'));
			if (clsSeq.alt == e.altKey && clsSeq.ctrl == e.ctrlKey && clsSeq.shift == e.shiftKey && clsSeq.key == code) {
				$(obj).parents('.sn-im-block').prev('.sn-im-button').find('.sn-action-close').trigger('click');
			} else if (endSeq.alt == e.altKey && endSeq.ctrl == e.ctrlKey && endSeq.shift == e.shiftKey && endSeq.key == code) {
				var msg = $(obj).val();
				var getC = $.sn.im._getCaret(obj) + ($.browser.msie && $.browser.version < 9 ? 1 : 0);
				if (getC != msg.length) {
					msg = msg.substring(0, getC - 1) + msg.substring(getC);
				}
				msg = msg.replace(/\s*/i, '');

				if (msg !== '') {
					// var uid_to = $.ajax({
					$.ajax({
						type : 'post',
						cache : false,
						async : true,
						url : $.sn.im.url,
						data : {
							mode : 'sendMessage',
							uid : $(obj).attr('uid'),
							pp : $.sn.getAttr($(msgs).find('.sn-im-msg:last'), 'from'),
							message : msg
						},
						success : function(data) {
							msgs.append(data.message);
							msgs.scrollTop(99999);
							$.sn.im._onlineUsersCB(data.onlineUsers);
						}
					});
				}
				$(obj).val('').css('height', 16);

				// $.sn.im._stopTimers();

				$.sn.im._startTimers(true);
			}

			if ($(obj).attr('h_old') !== $(obj).height()) {
				$(msgs).css({
					height : ($(msgs).height() - ($(obj).height() - $(obj).attr('h_old'))) + 'px'
				}).scrollTop(99999);
			}
			$(obj).attr('h_old', $(obj).height());
			lPressKey = code;

		},

		/**
		 * Vytvoreni chatboxu
		 * 
		 * @param {Integer}
		 *            uid Indetifikator uzivatele
		 * @param {String}
		 *            userName Jmeno uzivatele
		 * @param {Boolean}
		 *            bAsync Otevrit asynchrone ci synchrone
		 */
		_makeChatBox : function(uid, userName, bAsync) {
			if ($.sn.im.isOnline == 0) {
				return;
			}
			if ($('#sn-im-chatBox' + uid).size() > 0) {
				return;
			}
			$.ajax({
				type : 'post',
				cache : false,
				url : $.sn.im.url,
				async : bAsync,
				data : {
					mode : 'openChatBox',
					userTo : uid,
					usernameTo : userName
				},
				success : function(data) {
					$('#sn-im-chatBoxes').append(data.html);
					$('.sn-im-chatBox textarea.sn-im-message').TextAreaExpander(16, 64).bind('keyup', function(e) {
						$.sn.im._messageKey(this, e);
					}).metadataInit('uid');

					$('#sn-im .sn-im-button').textOverflow('...', false);
					$('#sn-im-chatBox' + uid + ' textarea.sn-im-message').focus();
					$('#sn-im-chatBox' + uid + ' .sn-im-msgs').scrollTop(99999);
					$.sn.setCookie('sn-im-chatBoxBlock' + uid, true);
					$.sn.im._scrollable(20);
					if ($.sn.mobileBrowser) {
						// scrollIm();
					}
				}
			});
		},

		/**
		 * Nacteni online listu
		 * 
		 * @param {Integer}
		 *            i Pocet volani procedury, generovano z pluginu timers
		 */
		_onlineListLoad : function(i) {
			if ($.sn.im.isOnline == true) {
				$.ajax({
					type : 'post',
					cache : false,
					async : true,
					url : $.sn.im.url,
					data : {
						mode : 'onlineUsers'
					},
					success : function(data) {
						$('#sn-im-onlineCount span.count').html('(' + data.onlineCount + ')');
						$('#sn-im-onlineList').html(data.onlineList);
						$('.sn-im-userLine').textOverflow('...', false);

						$.sn.im._onlineUsersCB(data.onlineUsers);
					}
				});
			}
		},

		/**
		 * Zastaveni casovacu
		 */
		_stopTimers : function() {
			$('#sn-im').stopTime($.sn.im._namesChat);
			// $(document).stopTime($.sn.im._namesOnline);
		},

		/**
		 * Spusteni casovacu
		 * 
		 * @param {Boolean}
		 *            sh false - dlouhy cas, true - kratky cas
		 */
		_startTimers : function(sh) {
			jQuery(function($) {
				if (sh) {
					$.sn.im._imCounter = 1;
					$('#sn-im').stopTime($.sn.im._namesChat);
				} else {
					$.sn.im._imCounter++;
				}
				$.sn.setCookie('sn-im-curCheckTime', $.sn.im._imCounter - 1);

				var tiktakChat = $.sn.im._imCounter * $.sn.im._imChatOpen;
				if (tiktakChat > $.sn.im._imChatClose) {
					tiktakChat = $.sn.im._imChatClose;
				}
				if (!$.sn.allow_load) {
					return;
				}

				$('#sn-im').oneTime(tiktakChat*1000, $.sn.im._namesChat, function(i) {
					$.sn.im._coreChat(i);
					$.sn.im._startTimers();
				});
			});
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
					if (this.curPosit === 0) {
						this.curPosit--;
					}
					break;
				case 20:
					this.curPosit++;
					break;
				case 1:
					this.curPosit--;
					break;
				case 2:
					this.curPosit++;
					break;
				/*
				 * case 0: default: break;
				 */
				}
				$.sn.setCookie('sn_im_curPosit', this.curPosit);
				for (i = 0; i < this.maxChatBoxes; i++) {
					$snImCB.children(this.curPosit + i).show();
				}
				$nav.children(':lt(' + this.curPosit + '):visible').hide();
				$nav.children(':gt(' + (this.curPosit + this.maxChatBoxes - 1) + '):visible').hide();
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

		_windowRollDown : function(block) {
			$('> .sn-im-button', block).removeClass('sn-im-opener');
			$('> .sn-im-block', block).hide().removeClass('sn-im-openBlock');

			$.sn.setCookie($('> .sn-im-block', block).attr('id'), false);

		},

		_documentClick : function(event) {
			// ZAVRIT ONLINE LIST PRI KLIKNUTI MIMO
			if ($('#sn-im-onlineCount').hasClass('sn-im-opener')) {
				var s_obj = $('#sn-im-online #sn-im-onlineBlock:visible').parents('#sn-im-online').attr('id');
				if (!$(event.target).closest('#' + s_obj).size()) {
					$('#sn-im-onlineCount').trigger('click');
				}
			}

		}

	}
}(jQuery));
