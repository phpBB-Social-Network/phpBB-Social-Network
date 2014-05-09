/**
 * @preserve phpBB Social Network 0.7.2 - Activity Page module
 * (c) 2010-2012 Kamahl & Culprit & Senky http://phpbbsocialnetwork.com
 * http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 *
 * @param {object} $ jQuery
 * @param {object} $sn socialNetwork
 * @returns {void}
 */
(function($, $sn) {
	$sn.ap = {
		url: './socialnet/activitypage.php',
		urlUsersAutocomplete: '{U_USERS_AUTOCOMPLETE}',
		blockOnlineUsers: false,
		tikTakOnline: 30000,
		tikTakName: 'sn-ap-onlineTicker',
		loadingNews: false,
		loadMoreTime: 4000,
		_isScrollingToLoadMore: false,

		init: function(opts) {
			if (!$sn._inited) {
				return false;
			}
			if ($sn.enableModules.ap == undefined || !$sn.enableModules.ap) {
				return false;
			}
			$sn._settings(this, opts);

			var self = this;
			if (this.blockOnlineUsers) {
				if ($sn.allow_load) {
					$(document).everyTime(self.tikTakOnline, self.tiktakName, function(i) {
						self.onlineList(i);
					});
				}
			}

			$('.sn-ap-getMore').click(function() {
				if ($('.ui-dialog').is(':visible')) {
					return;
				}
				if (self._isScrollingToLoadMore == true) {
					return;
				}
				var o_more = $(this);
				self._isScrollingToLoadMore = true;
				var o_loader = o_more.next('.sn-ap-statusLoader');
				$(o_loader).show();
				var o_prev = o_more.parents('.sn-more');
				var i_obj = $(o_prev).prev('div[id^=sn-ap-entry]');
				var i_lEntry = $sn.getAttr($(i_obj), 't');

				$.ajax({
					url: self.url,
					data: {
						mode: 'snApOlderEntries',
						lEntryTime: i_lEntry
					},
					error: function() {
						$(o_loader).hide();
						self._isScrollingToLoadMore = false;
					},
					success: function(data) {
						$(o_prev).before(data.content);
						$sn.comments.waterMark();
						$('div[id^=sn-ap-entry]:hidden').slideDown('slow');
						$(o_loader).hide();
						if (data.more === false) {
							$(o_more).remove();
						}
						$sn._textExpander();
						self._isScrollingToLoadMore = false;
					}
				});
			});

			$('.sn-ap-search input.inputbox').bind('focusin focusout', function() {
				$('.sn-ap-search').toggleClass('sn-inputbox-focus');
			});

			self.urlUsersAutocomplete = self.urlUsersAutocomplete.replace(/&amp;/g, '&');

			$("#sn-ap-searchUsersAutocomplete").autocomplete({
				source: self.urlUsersAutocomplete,
				minLength: 3,
				appendTo: '#sn-ap-searchAutocompleteContainer',
				delay: 300,
				select: function(event, ui) {
					$(this).parents('form').find('#sn-ap-searchUsersAutocomplete').val(ui.item.value);
					$(this).parents('form').submit();
				}
			}).parents('form').submit(function() {
				if ($('#sn-ap-searchUsersAutocomplete').val() == '') {
					return false;
				}
			});

			$('a.sn-ap-loadNews').click(function() {
				self.loadNews();
				return false;
			});
			$sn._textExpander();
		},

		/**
		 * Load online users
		 * @param {integer} i counter
		 */
		onlineList: function(i) {
			$.ajax({
				type: 'post',
				cache: false,
				async: true,
				url: this.url,
				timeout: 1000,
				data: {
					mode: 'onlineUsers'
				},
				success: function(data) {
					$('#sn-ap .sn-ap-onlineUsers').html(data.list);
				}
			});
		},

		/**
		 * Load new entries
		 */
		loadNews: function() {
			if ($('.ui-dialog').is(':visible')) {
				return;
			}
			if (this.loadingNews) {
				return;
			}
			this.loadingNews = true;
			var o_next = $('.sn-ap-loadNewsOver');
			var o_lEntry = $('.sn-page-content').find('div[id^=sn-ap-entry]:first');
			if ($(o_lEntry).size() != 0)
				var i_lEntry = $sn.getAttr($(o_lEntry), 't');
			else
				var i_lEntry = 0;
			$.ajax({
				url: this.url,
				async: false,
				cache: false,
				data: {
					mode: 'snApNewestEntries',
					lEntryTime: i_lEntry
				},
				success: function(data) {
					if ($(data.content).size() > 0) {
						$(o_next).after(data.content);
						$(".sn-us-inputComment").watermark($sn.us.watermarkComment, {
							useNative: false,
							className: 'sn-us-watermark'
						}).elastic();
						if ($(o_next).parent('div').children('div:not([id^=sn-ap-entry])[id^=sn-us]').size() == 0) {
							$(o_next).parent('div').children('div[id^=sn-ap-entry]:hidden').fadeIn('slow').removeAttr('style');
							$(o_next).parent('div').children('div:not([id^=sn-ap-entry])[id^=sn-us]').fadeOut('fast').remove();
						} else {
							$(o_next).parent('div').children('div[id^=sn-ap-entry]:hidden').show().removeAttr('style');
							$(o_next).parent('div').children('div:not([id^=sn-ap-entry])[id^=sn-us]').remove();
						}
					}

					$sn.comments.waterMark();
					$sn.ap.loadingNews = false;
				}
			});
		},

		_scroll: function() {
			if ($('.sn-more').size() > 0 && $('.sn-ap-getMore').size() > 0 && this._isScrollingToLoadMore == false) {

				if ($(window).scrollTop() >= $('.sn-ap-getMore').offset().top - $(window).height() + $('.sn-ap-getMore').parent().height()) {

					$(document).oneTime(this.loadMoreTime, 'sn-ap-checkScrollDown', function() {
						if ($('.sn-ap-getMore').size() == 0 || $sn.ap._isScrollingToLoadMore == true) {
							return;
						}

						if ($(window).scrollTop() >= $('.sn-ap-getMore').offset().top - $(window).height() + $('.sn-ap-getMore').parent().height()) {
							$('.sn-ap-getMore').trigger('click');
						}
					});
				}
			}
		}
	};
}(jQuery, socialNetwork));
