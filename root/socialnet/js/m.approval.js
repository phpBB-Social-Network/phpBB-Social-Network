/**
 * 
 * @package phpBB Social Network
 * @version 0.6.3
 * @copyright (c) 2010-2012 Kamahl & Culprit http://phpbbsocialnetwork.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * 
 */
(function($) {

	$.sn.fms = {
		url : '',
		urlFMS : '',
		noFriends : '{ FAS FRIENDGROUP NO TOTAL }',
		_init : false,

		_load : function(m, s, u) {
			var i_bl = m;
			if (m == 'friends')
				i_bl = 'friend';

			$.ajax({
				url : $.sn.fms.url,
				data : {
					mode : m,
					fmsf : s,
					usr : u
				},
				success : function(data) {
					$('#ucp_' + i_bl + ' .inner').html(data);
					$('.sn-fms-friend span').textOverflow('..');
					$sn.fms._runLoadInit(m);
				}
			});
		},

		usersLoad : function(m, s, l, u, c, r, p) {
			var data = $.ajax({
				url : $.sn.fms.urlFMS,
				data : {
					mode : m,
					fmsf : s,
					flim : l,
					usr : u,
					chkbx : c,
					sl : r,
					pl : p
				},
				dataType : 'json',
				success : function(data) {

					if (!r) {
						$('#sn-fms-usersBlockPagination-' + m).html(data.pagination);
					}
					$('#sn-fms-usersBlockContent-' + m).html(data.content);
					$.sn.fms.callbackInit(m);
				}
			});
		},

		callbackInit : function(mode) {
			if ($.sn.fms._inits[mode]) {
				return $.sn.fms._inits[mode].apply(this, Array.prototype.slice.call(arguments, 1));
			} else if (typeof mode === 'object' || !mode) {
				return mode.init.apply(this, arguments);
			} else {
				// $.error('Method ' + mode + ' does not exist on
				// jQuery.sn.fms');
			}
		},

		_inits : {
			friend : function() {
				$.sn.fms._inits._simple('friend');
				$.sn.fms._inits.group();
			},
			approve : function() {
				$.sn.fms._inits._simple('approve');
			},
			cancel : function() {
				$.sn.fms._inits._simple('cancel');
			},
			group : function() {
				$('.sn-fms-friendsBlock.ufg .sn-fms-users > div').draggable({
					helper : 'clone',
					appendTo : 'body',
					revert : 'invalid'
				});

			},
			_simple : function(m) {
				$('#sn-fms-usersBlockContent-' + m).parents('form[id^=ucp] .inner').children('fieldset.submit-buttons').children('input').attr('disabled', 'disabled').addClass('disabled');

			}
		},

		_groupChange : function(s_sub, i_gid, i_uid) {
			$.ajax({
				type : 'POST',
				url : $.sn.fms.url,
				dataType : 'json',
				data : {
					mode : 'group',
					sub : s_sub,
					gid : i_gid,
					uid : i_uid
				},
				success : function(data) {
					if (data.error) {
						alert(data.text);
					}
				}
			});

		},

		_loadFriends : function(toobj) {
			if (toobj.html() != '') {
				return false
			}

			$.ajax({
				type : 'POST',
				url : $.sn.fms.urlFMS,
				dataType : 'json',
				async : false,
				data : {
					mode : 'friendgroup',
					gid : $.sn.getAttr(toobj, 'gid')
				},
				success : function(data) {
					toobj.append(data.content);
				}
			})

		},

		_changeButtons : function(obj, chCls) {

			var snFmsButtons = $(obj).parents('form[id^=ucp] .inner').children('fieldset.submit-buttons').children('input');
			if ($(obj).parent().children('.sn-fms-friend.' + chCls).size() != 0) {
				$(snFmsButtons).removeAttr('disabled').removeClass('disabled');
			} else {
				$(snFmsButtons).attr('disabled', 'disabled').addClass('disabled');
			}
		},

		init : function(opts) {
			if (!$.sn._inited) {
				return false;
			}
			if ($.sn.enableModules.fms == undefined || !$.sn.enableModules.fms) {
				return false;
			}
			$.sn._settings(this, opts);

			$.sn.fms._initUcpFormAdd();

			$.sn.fms._initUcpForms();
			$.sn.fms._initUcpHistory();

			$.sn.fms._initUpGroupMenu();
			// GROUPS ACCORDION
			$.sn.fms._initGroupAccordion();

		},

		_initUcpFormAdd : function() {
			if ($('form#ucp #add').size() == 0 || $('form#ucp #usernames').size() != 0) {
				return;
			}

			$('form#ucp #add').bind('keyup change', function() {
				if ($(this).val() == '') {
					$('form#ucp input[name=submit]').attr('disabled', 'disabled').addClass('disabled');
				} else {
					$('form#ucp input[name=submit]').removeClass('disabled').removeAttr('disabled');
				}
			});
			if ($('form#ucp #add').val() == '') {
				$('form#ucp input[name=submit]').attr('disabled', 'disabled').addClass('disabled');
			} else {
				$('form#ucp input[name=submit]').removeClass('disabled').removeAttr('disabled');
			}
			$('form#ucp input[name=reset]').click(function() {
				$('form#ucp input[name=submit]').attr('disabled', 'disabled').addClass('disabled');
			});

			$('form#ucp').mouseover(function() {
				$('form#ucp #add').trigger('keyup');
			});

		},

		_initUcpForms : function() {
			if ($('form[id=ucp_friend]').size() == 0 && $('form[id=ucp_approve]').size() == 0 && $('form[id=ucp_cancel]').size() == 0) {
				return;
			}
			$.sn.fms.callbackInit('friend');
			$('.sn-fms-friend').live('click', function() {
				var chCls = 'checked';
				$(this).toggleClass(chCls);
				$(this).children('input[type=checkbox]').attr('checked', $(this).hasClass(chCls));

				$.sn.fms._changeButtons(this, chCls);

			});
			$('.sn-fms-friend span').textOverflow('..');

			$('.sn-fms-friend a').live('click', function() {
				window.location = $(this).attr('href');
				return false;
			});

			$('[id^=ucp_] a.mark').click(function() {
				var $s_block = $(this).attr('class');
				$s_block = $s_block.replace('mark ', '');
				$('#sn-fms-usersBlockContent-' + $s_block + ' .sn-fms-friend').addClass('checked');
				$('#sn-fms-usersBlockContent-' + $s_block + ' .sn-fms-friend input[type=checkbox]').attr('checked', 'checked');
				$.sn.fms._changeButtons($('#sn-fms-usersBlockContent-' + $s_block + ' .sn-fms-friend'), 'checked');
				return false;
			});
			$('[id^=ucp_] a.unmark').click(function() {
				var $s_block = $(this).attr('class');
				$s_block = $s_block.replace('unmark ', '');

				$('#sn-fms-usersBlockContent-' + $s_block + ' .sn-fms-friend').removeClass('checked');
				$('#sn-fms-usersBlockContent-' + $s_block + ' .sn-fms-friend input[type=checkbox]').removeAttr('checked');
				$.sn.fms._changeButtons($('#sn-fms-usersBlockContent-' + $s_block + ' .sn-fms-friend'), 'checked');
				return false;
			});
			$('input[type=reset]').live('click', function() {
				$('.sn-fms-friend').removeClass('checked');
				$('.sn-fms-friend input[type=checkbox]').removeAttr('checked');
			});

		},

		_initUcpHistory : function() {
			$('.sn-im-history-conversation').click(function() {
				window.location = $.sn.getAttr($(this),'u');
			})
		},

		_initGroupAccordion : function() {
			// Group Accordion with droppable
			if ($('#sn-fms-groupAccordion').size() == 0) {
				return;
			}

			$('#sn-fms-groupAccordion').accordion({
				collapsible : false,
				clearStyle : true,
				event : "click",
				changestart : function(e, ui) {
					$.sn.fms._loadFriends(ui.newContent);
				}
			});
			$.sn.fms._loadFriends($('#sn-fms-groupAccordion').children('div').first());

			$('#sn-fms-groupAccordion > div').droppable({
				drop : function(event, ui) {
					var $drag = $(this).children('div[title="' + ui.draggable.attr('title').replace(/([ #;&,.+*~\':"!^$[\]()=><|\/@])/g,'\\\\$1') + '"]'); 
					if ($drag.size() > 0) {
						console.log($drag);
						console.log($(this).children('div[title="Opera\'s &lt;Uncle&gt;"]'));
						console.log('Is In');
						return;
					}

					var i_gid = $.sn.getAttr($(this), 'gid');
					var i_uid = $.sn.getAttr(ui.draggable, 'uid');
					var o_cnt = $('h3[id=sn-fms-grp' + i_gid + '-header] span.counter');
					var i_cnt = parseInt(o_cnt.html());
					if (i_cnt == 0) {
						$(this).html('');
					}
					$(this).append(ui.draggable.clone().css({
						zIndex : 1500,
						opacity : 1
					}));
					o_cnt.html(i_cnt + 1);
					$.sn.fms._groupChange('add', i_gid, i_uid);
				},
				activate : function(event, ui) {
					ui.draggable.css({
						opacity : 0.5
					});
				},
				deactivate : function(event, ui) {
					ui.draggable.css({
						opacity : 1
					});
				}
			}).sortable({
				helper : 'clone',
				placeholder : 'sn-fms-friend move ui-state-highlight',
				start : function(e, ui) {
					$('.sn-fms-friend.move.ui-state-highlight').css({
						height : ui.item.height() + 'px'
					});
				},
				appendTo : 'body',
				recieve : function(e, ui) {
					sortableIn = 1;
				},
				over : function(e, ui) {
					sortableIn = 1;
				},
				out : function(e, ui) {
					sortableIn = 0;
				},
				beforeStop : function(e, ui) {
					if (sortableIn == 0) {

						var i_gid = $.sn.getAttr($(this), 'gid');
						var i_uid = $.sn.getAttr(ui.item, 'uid');
						var d_item = ui.item.clone();
						$('body').append(d_item.css({
							position : 'absolute',
							top : ui.position.top,
							left : ui.position.left
						}));

						ui.item.remove();
						$('body > .sn-fms-friend').addClass('red').effect('explode', {}, 1000).remove();
						var o_cnt = $('h3[id=sn-fms-grp' + i_gid + '-header] span.counter');
						o_cnt.html(parseInt(o_cnt.html()) - 1);
						$.sn.fms._groupChange('remove', i_gid, i_uid);
					}
				},
				stop : function(e, ui) {
					var i_gid = $.sn.getAttr($(this), 'gid');
					var o_cnt = parseInt($('h3[id=sn-fms-grp' + i_gid + '-header] span.counter').text());
					if (o_cnt == 0) {
						$(this).html($.sn.fms.noFriends);
					}

				}
			});

			$.sn.fms.callbackInit('group');

			$('#sn-fms-groupAccordion .sn-fms-groupDelete').click(function() {

				var i_gid = $.sn.getAttr($(this), 'gid');
				$('#sn-fms-groupAccordion > [id^="sn-fms-grp' + i_gid + '"]').remove();
				$.sn.fms._groupChange('delete', i_gid, -1);
				
			});

		},

		_initUpGroupMenu : function() {
			if ($('.sn-up-menu li').size() > 0) {

				$('.sn-fms-groups a').click(function() {
					var gid = $.sn.getAttr($(this), 'gid');
					var uid = $.sn.getAttr($(this), 'uid');
					var $chld = $(this).children('.ui-icon');
					var sub = $chld.hasClass('ui-icon-check') ? 'remove' : 'add';
					$.sn.fms._groupChange(sub, gid, uid);
					$chld.toggleClass('ui-icon-check ui-icon-no');
					return false;
				});
			}

		}

	}

}(jQuery));
