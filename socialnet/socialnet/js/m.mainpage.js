/**
 * 
 * @package phpBB Social Network
 * @version 0.6.3
 * @copyright (c) 2010-2012 Kamahl & Culprit http://phpbbsocialnetwork.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * 
 */
(function($) {
	$.sn.mp = {
		url : './socialnet/mainpage.php',
		urlUsersAutocomplete : '{U_USERS_AUTOCOMPLETE}',
		blockOnlineUsers : false,
		tikTakOnline : 15000,
		tikTakName : 'sn-mp-onlineTicker',
		loadingNews : false,
		loadMoreTime : 2000,

		init : function(opts) {
			if (!$.sn._inited) {
				return false;
			}
			if ($.sn.enableModules.mp == undefined || !$.sn.enableModules.mp) {
				return false;
			}
			$.sn._settings(this, opts);

			if (this.blockOnlineUsers) {
				if ($.sn.allow_load) {
					$(document).everyTime($.sn.mp.tikTakOnline, $.sn.mp.tiktakName, function(i) {
						$.sn.mp.onlineList(i);
					});
				}
			}

			$('.sn-mp-getMore').click(function() {
				var o_loader = $(this).next('.sn-mp-statusLoader');
				$(o_loader).show();
				var o_prev = $(this).parents('.sn-more');
				var i_obj = $(o_prev).prev('div[id^=sn-mp-entry]');
				var i_lEntry = $.sn.getAttr($(i_obj), 't');

				$.ajax({
					url : $.sn.mp.url,
					data : {
						mode : 'snMpOlderEntries',
						lEntryTime : i_lEntry
					},
					error : function() {
						$(o_loader).hide();
					},
					success : function(data) {
						$(o_prev).before(data.content);
						$.sn.comments.waterMark();
						$('div[id^=sn-mp-entry]:hidden').slideDown('slow');
						$(o_loader).hide();
						if (data.more === false) {
							$(o_prev).remove();
						}
					}
				});
			});

			$('.sn-mp-search input.inputbox').bind('focusin focusout', function() {
				$('.sn-mp-search').toggleClass('sn-inputbox-focus');
			});

			$.sn.mp.urlUsersAutocomplete = $.sn.mp.urlUsersAutocomplete.replace(/&amp;/g, '&');

			$("#sn-mp-searchUsersAutocomplete").autocomplete({
				source : $.sn.mp.urlUsersAutocomplete,
				minLength : 2,
				delay : 300
			});

			$('a.sn-mp-loadNews').click(function() {
				$.sn.mp.loadNews();
				return false;
			});

			/**
			 * $(document).focusin(function() { $.sn.mp.loadNews(); });
			 */
			/*
			if ($('.sn-mp-search').size() > 0) {
				//$('.sn-mp-search').fadeIn('fast').removeAttr('style');
				$(document).oneTime(100, 'sn-mp-search', function() {
					$.sn.mp._resize();
				});
			}*/
		},

		/**
		 * Nacti online usery
		 */
		onlineList : function(i) {
			$.ajax({
				type : 'post',
				cache : false,
				async : true,
				url : $.sn.mp.url,
				timeout : 1000,

				data : {
					mode : 'onlineUsers'
				},
				success : function(data) {
					$('#sn-mp .sn-mp-onlineUsers').html(data.list);
				}
			});
		},

		/**
		 * Nacti nove zaznamy
		 */
		loadNews : function() {
			if ($.sn.mp.loadingNews) {
				return;
			}
			$.sn.mp.loadingNews = true;
			var o_next = $('.sn-mp-loadNewsOver');
			var o_lEntry = $('.sn-page-content').find('div[id^=sn-mp-entry]:first');
			if ($(o_lEntry).size() != 0)
				var i_lEntry = $.sn.getAttr($(o_lEntry), 't');
			else
				var i_lEntry = 0;
			$.ajax({
				url : $.sn.mp.url,
				async : false,
				cache : false,
				data : {
					mode : 'snMpNewestEntries',
					lEntryTime : i_lEntry
				},
				success : function(data) {
					if ($(data.content).size() > 0) {
						$(o_next).after(data.content);
						$(".sn-us-inputComment").watermark($.sn.us.watermarkComment, {
							useNative : false,
							className : 'sn-us-watermark'
						}).TextAreaExpander(22, 100);
						if ($(o_next).parent('div').children('div:not([id^=sn-mp-entry])[id^=sn-us]').size() == 0) {
							$(o_next).parent('div').children('div[id^=sn-mp-entry]:hidden').fadeIn('slow').removeAttr('style');
							$(o_next).parent('div').children('div:not([id^=sn-mp-entry])[id^=sn-us]').fadeOut('fast').remove();
						} else {
							$(o_next).parent('div').children('div[id^=sn-mp-entry]:hidden').show().removeAttr('style');
							$(o_next).parent('div').children('div:not([id^=sn-mp-entry])[id^=sn-us]').remove();
						}
					}

					$.sn.comments.waterMark();
					$.sn.mp.loadingNews = false;
				}
			});
		},

		_resize : function() {
/*
			if ($('.sn-mp-search').size() > 0) {
				var snMpSearch = $('.sn-mp-search input[type=image]');
				var snMpInput = $('.sn-mp-search input.inputbox');
				var snMpSearchLabel = snMpSearch.val();

				snMpInput.css('width', $('.sn-mp-search').width() - snMpSearch.width() - parseInt(snMpInput.css('padding-left')) - parseInt(snMpInput.css('padding-right')) - -parseInt(snMpInput.css('margin-left')) - parseInt(snMpInput.css('margin-right')) - 1);
			}
*/
		},

		_scroll : function() {
			if ($('.sn-more').size() > 0 && $('.sn-mp-getMore').size() > 0) {
				if ($(window).scrollTop() == $(document).height() - $(window).height()) {
					$(document).oneTime($.sn.mp.loadMoreTime, 'sn-mp-checkScrollDown', function() {
						if ($(window).scrollTop() == $(document).height() - $(window).height()) {
							$('.sn-mp-statusLoader').show();
							$('.sn-mp-getMore').trigger('click');
						}
					});
				}
			}
		}

	}
}(jQuery))
