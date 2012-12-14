/**
 * @preserve phpBB Social Network 0.7.2 - User Status module
 * (c) 2010-2012 Kamahl & Culprit & Senky http://phpbbsocialnetwork.com
 * http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Declaration for phpBB Social Network User Status module
 * @param {object} $ jQuery
 * @param {object} $sn socialNetwork
 * @returns {void}
 */
(function($, $sn) {
	$sn.us = {
		loadMoreTime: 4000,
		watermark: '',
		emptyStatus: '',
		watermarkComment: '',
		emptyComment: '',
		deleteStatusTitle: '',
		deleteStatusText: '',
		deleteActivityTitle: '',
		deleteActivityText: '',
		url: './socialnet/userstatus.php',
		urlFetch: './socialnet/fetch.php',
		_inited: false,
		_isScrollingToLoadMore: false,
		init: function(opts) {
			if (!$sn._inited) {
				return false;
			}
			if ($sn.enableModules.us == undefined || !$sn.enableModules.us) {
				return false;
			}
			$sn._settings(this, opts);

			this._resize();

			$("#sn-us-wallInput").watermark($sn.us.watermark, {
				useNative: false,
				className: 'sn-us-watermark'
			}).live('focusin keyup input cut paste', function() {
				var snUsShare = $(this).val();
				$(this).parents('.sn-us-share').children('input[name=sn-us-wallButton]').show();
				if ($sn.isValidURL(snUsShare) == true) {
					$('input[name="sn-us-fetchButton"]').show();
				} else {
					$('input[name="sn-us-fetchButton"]').hide();
					$('input[name="sn-us-fetchClear"]').trigger('click');
				}
			}).elastic({
				parentElement: '.sn-us-share',
				submitElement: '.sn-us-wallButton, .sn-us-fetchButton'
			}).trigger('blur');

			// Delete status
			$(".sn-us-deleteStatus").live('click', function() {
				var status_id = $sn.getAttr($(this), 'sid');
				var wallid = $sn.getAttr($(this), 'wid');
				$.ajax({
					type: 'POST',
					url: $sn.us.url,
					dataType: 'json',
					data: {
						smode: 'get_status',
						status: status_id,
						wall: wallid
					},
					success: function(data) {
						snConfirmBox($sn.us.deleteStatusTitle, $sn.us.deleteStatusText + '<hr />' + data.content, function() {
							$.ajax({
								type: "POST",
								url: $sn.us.url,
								cache: false,
								data: {
									smode: 'status_delete',
									s_id: status_id
								},
								success: function(data) {
									$('#sn-us-status' + status_id).parents('.sn-ap-textBlock').fadeOut('slow').remove();
									if ($('#sn-us-status' + status_id).size() != 0) {
										$('#sn-us-status' + status_id).remove();
									}
								}
							});
						});
						$('#dialog').find('.sn-action-delete').remove();
					}
				});
			});

			// Delete entry
			$(".sn-ap-deleteEntry").live('click', function() {
				var entry_id = $sn.getAttr($(this), 'eid');

				$.ajax({
					type: 'POST',
					url: $sn.us.url,
					dataType: 'json',
					data: {
						smode: 'get_activity',
						entry_id: entry_id
					},
					success: function(data) {
						snConfirmBox($sn.us.deleteActivityTitle, $sn.us.deleteActivityText + '<hr />' + data.content, function() {
							$.ajax({
								type: "POST",
								url: $sn.us.url,
								cache: false,
								data: {
									smode: 'delete_activity',
									entry_id: entry_id
								},
								success: function(data) {
									$('#sn-ap-entry' + entry_id).fadeOut('slow').remove();
								}
							});
						});
					}
				});
			});

			$('.sn-us-fetchData .sn-us-fetchDesc').elastic();

			// Share status on Wall
			$('.sn-us-share input[name=sn-us-wallButton]').live('click', function() {
				var status_text = $("#sn-us-wallInput").val();
				status_text = status_text.replace(/^\s+|\s+$/g, '');
				if (status_text == '' || status_text == $sn.us.watermark) {
					snConfirmBox($sn.us.emptyStatus, $sn.us.emptyStatus);
					$('.sn-us-share input[name=sn-us-wallButton]').hide();
					$('#sn-us-wallInput').val('').trigger('cut');

				} else {
					var now = Math.floor(new Date().valueOf() / 1000);

					var wall_id = $sn.getAttr($(this), 'wall');
					if (wall_id == '') {
						wall_id = 0;
					}

					var bPage = $sn.isValidURL($('#sn-us-wallInput').val()) && ($('.sn-us-fetchData .title').html() != '') ? 1 : 0;
					var bImage = $('#sn-us-noImg').is(':checked');
					var bVideo = $('#sn-us-noVideo').is(':checked');
					var $cImage = $('.sn-us-fetchImgs img:visible');

					var mentions_collection = {};
					if (!$sn.isOutdatedBrowser) {
						$('textarea.sn-us-mention').mentionsInput('getMentions', function(data) {
							mentions_collection = JSON.stringify(data);
						}).mentionsInput('val', function(text) {
							status_text = text;
						});
					}

					$.ajax({
						type: "POST",
						url: $sn.us.url,
						cache: false,
						data: {
							smode: 'status_share_wall',
							status: status_text,
							mentions: mentions_collection,
							wall: wall_id,
							isPage: bPage,
							page: {
								title: $('.sn-us-fetchData .title').html(),
								url: $('.sn-us-fetchData .url a').attr('href'),
								desc: $('.sn-us-fetchData .sn-us-fetchDesc').val(),
								image: bImage ? '' : $cImage.attr('src'),
								imageH: bImage ? '' : $sn.getAttr($cImage, 'imgH'),
								imageW: bImage ? '' : $sn.getAttr($cImage, 'imgW'),
								video: bVideo ? '' : $('.sn-us-fetchVideo').html(),
								videoI: bVideo ? '' : $('.sn-us-fetchVideoInfo').html(),
								videoP: bVideo ? '' : $('.sn-us-fetchVideoProvider').html()
							}
						},
						success: function(data) {
							if (data == '') {
								snConfirmBox($sn.us.emptyStatus, $sn.us.emptyStatus);
							} else {
								$('.sn-us-noStatus').remove();
								$('.sn-ap-noEntry').remove();
								if ($('.sn-ap-loadNewsOver').size() != 0) {
									$(data).hide().insertAfter('.sn-ap-loadNewsOver').slideDown('slow');
								} else {
									$(data).hide().prependTo('#sn-us-profile').slideDown('slow');
								}
								$('input[name=sn-us-fetchClear]').trigger('click');
								$('input[name=sn-us-fetchButton]').hide();
								$sn.comments.waterMark();
								$('.sn-us-statusBlock .sn-actions').removeAttr('style');
							}
							$('#sn-us-wallInput').val('').watermark($sn.us.watermark, {
								useNative: false,
								className: 'sn-us-watermark'
							}).trigger('paste');
							$('.sn-us-share .sn-us-wallButton').hide();
						}
					});
				}
			});

			// Show and hide the text "Write a comment" && Resize comment textarea
			$('.sn-us-commentStatus').live('click', function() {
				var o_commArea = $(this).parents('.sn-us-statusBox').find('.sn-us-inputComment');
				o_commArea.focus();
				return false;
			});

			$(".sn-us-inputComment").live('focusin', function() {
				$('.sn-us-buttonCommentOver:visible').hide();
				$(this).next('.sn-us-buttonCommentOver').show();
			});

			// Post comment
			$(".sn-us-shareComment input[name=sn-us-buttonComment]").live('click', function() {
				var element = $(this);
				var status_id = $sn.getAttr(element, "sid");
				var snUsCommentText = $("#sn-us-textarea" + status_id).val();
				var now = Math.floor(new Date().valueOf() / 1000);

				snUsCommentText = snUsCommentText.replace(/^\s+|\s+$/g, '');

				if (snUsCommentText == '' || snUsCommentText == $sn.us.watermarkComment) {
					snConfirmBox($sn.comments.empty, $sn.comments.empty);
					$(element).parents('.sn-us-shareComment').find('.sn-us-inputComment').val('');
				} else {
					$.ajax({
						type: "POST",
						url: $sn.us.url,
						cache: false,
						data: {
							smode: 'comment_share',
							comment: snUsCommentText,
							s_id: status_id
						},
						success: function(data) {
							var $parr = $(element).parents('.sn-us-shareComment');
							if (data.match(/^Error:/i)) {
								snConfirmBox('Error', data.replace(/^Error: /i, ''));
								element.parents('.sn-ap-textBlock').remove();
							} else if (data == '') {
								snConfirmBox($sn.comments.empty, $sn.comments.empty);
							} else {
								$parr.before(data);
								$parr.prev('.sn-us-commentBlock').slideDown();
							}
							$('.sn-us-buttonCommentOver:visible').hide();
							$parr.find('.sn-us-inputComment').val('').trigger('paste');
							$sn.comments.waterMark();
						}
					});
				}
			});

			// Load more comments
			$('.sn-us-getMoreComments').live('click', function() {
				var o_loader = $(this).next('.sn-us-commentsLoader');
				o_loader.show();

				var o_g_m_c = this;
				var b_last = $(this).hasClass('before');
				if (b_last) {
					var lastCommentID = $sn.getAttr($(this).parents('div.sn-more').prev('.sn-commentBlock'), 'cid');
				} else {
					var lastCommentID = $sn.getAttr($(this).parents('div.sn-more').next('.sn-commentBlock'), 'cid');
				}

				var StatusID = $sn.getAttr($(this), 'id');
				var userID = $sn.getAttr($(this), 'user');

				$.ajax({
					type: 'POST',
					cache: false,
					url: $sn.us.url,
					data: {
						smode: 'comment_more',
						lCommentID: lastCommentID,
						s_id: StatusID,
						u: userID
					},
					success: function(data) {
						if (b_last) {
							$('#sn-us-status' + StatusID + ' .sn-more').before(data.comments);
						} else {
							$('#sn-us-status' + StatusID + ' .sn-more').after(data.comments);
						}
						$('#sn-us-status' + StatusID + ' .sn-us-commentBlock:hidden').show();
						if (data.moreComments == false) {
							$('#sn-us-status' + StatusID + ' .sn-more').remove();
						} else {
							$(o_g_m_c).children('.sn-commentsCount').html(data.moreComments);
						}
						o_loader.hide();
					}
				});
				return false;
			});

			// Load more statuses
			$('.sn-us-getMore').live('click', function() {
				if ($('.ui-dialog').is(':visible')) {
					return;
				}
				if ($sn.us._isScrollingToLoadMore == true) {
					return;
				}
				var t_obj = $(this);
				$sn.us._isScrollingToLoadMore = true;
				var o_prev = t_obj.parents('.sn-more');
				var i_obj = $(o_prev).prev('div[id^=sn-ap-entry]');
				var i_lEntry = $sn.getAttr($(i_obj), 't');
				var i_lStatusID = $sn.getAttr($(this).parents('div.sn-more').prev('.sn-us-statusBlock'), 'sid');

				$.ajax({
					type: 'POST',
					cache: false,
					url: $sn.us.url,
					data: {
						smode: 'status_more',
						ltime: i_lEntry,
						u: $sn.getAttr($(this), 'user')
					},
					beforeSubmit: function() {
						$('.sn-us-statusLoader').show();
					},
					error: function() {
						$('.sn-us-statusLoader').hide();
						$sn.us._isScrollingToLoadMore = false;
					},
					success: function(data) {
						$('.sn-us-statusLoader').hide();
						o_prev.before(data.statuses);
						$('div[id^=sn-ap-entry]:hidden').slideDown('slow');

						if (data.moreStatuses == false) {
							$('.sn-more .sn-us-getMore').remove();
						}
						$sn.comments.waterMark();
						$sn.us._isScrollingToLoadMore = false;
					}
				});
				return false;
			});

			// Fetch
			$('input[name=sn-us-fetchButton]').live('click', function() {
				$('.sn-us-fetchBlock .loader').show();
				$('.sn-us-fetchBlock .sn-us-fetchPreview').hide();
				$('.sn-us-thumbs').hide();

				if ($('#sn-us-wallInput').size() > 0) {
					var fetchURL = $('#sn-us-wallInput').val();
				}
				$.ajax({
					type: 'POST',
					url: $sn.us.urlFetch,
					data: {
						action: 'load',
						url: fetchURL
					},
					dataType: 'json',
					error: function(data) {
						$('input[name=sn-us-fetchClear]').trigger('click');
						snConfirmBox('Error', data.responseText);
					},
					success: function(data) {
						if (data == null) {
							$('input[name=sn-us-fetchClear]').trigger('click');
							snConfirmBox('Error', 'No data returned');
							return;
						}
						$('.sn-us-fetchImgs img').remove();
						if (data.images.length == 0) {
							$('.sn-us-fetchImages').hide();
							$('.sn-us-thumbs .sn-us-thumbsImg').hide();
						} else {
							var idx_img = 0;
							$.each(data.images, function(i, image) {
								var $img = $('<img />', {
									src: image.img,
									'class': '{imgH:' + image.height + ',imgW:' + image.width + '}',
									load: function() {
										$(this).attr('id', 'sn-us-fetchPreviewImg_' + idx_img).css({
											display: (idx_img > 0 ? 'none' : 'inline-block'),
											maxHeight: 150,
											width: 100
										});
										idx_img++;
										$('.sn-us-fetchImgs').append($img);
										$('.sn-us-fetchImages .sn-us-fetchThumb .mPic').html(idx_img);
									}
								});
							});
							$('.sn-us-fetchImages').show();
							$('.sn-us-thumbs .sn-us-thumbsImg').show();
						}

						$('.sn-us-fetchImages .sn-us-fetchThumb .cPic').html('1');

						$('.sn-us-fetchData .title').html(data.title);
						$('.sn-us-fetchData .sn-us-fetchDesc').val(data.desc).trigger('paste');
						$('.sn-us-fetchData .url a').html(data.url).attr('href', data.url);

						if (data.video.object.length != 0) {
							$('.sn-us-fetchVideo').show();
							$('.sn-us-thumbs .sn-us-thumbsVideo').show();
						} else {
							$('.sn-us-fetchVideo').hide();
							$('.sn-us-thumbs .sn-us-thumbsVideo').hide();

						}
						$('.sn-us-fetchVideo').html(data.video.object);
						$('.sn-us-fetchVideoInfo').html(data.video.info);
						$('.sn-us-fetchVideoProvider').html(data.video.provider);

						$('.sn-us-fetchBlock .loader').hide();
						$('.sn-us-fetchPreview').show();
						$('.sn-us-thumbs').show();
						$('input[name=sn-us-fetchButton]').hide();
						$('input[name=sn-us-fetchClear]').show();

						$sn.us._resize();
						$('.sn-us-fetchDesc').trigger('paste');

					}
				});
			}).hide();

			$('.sn-us-fetchData .title').html('');
			$('input[name=sn-us-fetchClear]').live('click', function() {
				$('.sn-us-fetchBlock .loader').hide();
				$('.sn-us-fetchImgs').html('');
				$('.sn-us-fetchData .title').html('');
				$('.sn-us-fetchData .sn-us-fetchDesc').html('');
				$('.sn-us-fetchData .url a').html('').attr('href', '');
				$('.sn-us-fetchVideo').html('');
				$('.sn-us-fetchVideoInfo').html('');
				$('.sn-us-fetchVideoProvider').html('');

				$('.sn-us-fetchBlock .sn-us-fetchPreview').hide();
				$('.sn-us-thumbs').hide();
				$(this).hide();
				if ($sn.isValidURL($('#sn-us-wallInput').val()) == true) {
					$('input[name="sn-us-fetchButton"]').show();
				}
			}).hide();

			$('#sn-us-noImg').change(function() {
				$('.sn-us-fetchImages .sn-us-fetchImgs').toggle();
				$('.sn-us-fetchImages .sn-us-fetchImgNav').toggle();
				$('.sn-us-fetchImages .sn-us-fetchThumb').toggle();
			});

			$('#sn-us-noVideo').change(function() {
				$('.sn-us-fetchVideo').toggle();
			});

			$('.sn-us-fetchImgsNext').live('click', function() {
				$sn.us.changePicture(+1);
			});
			$('.sn-us-fetchImgsPrev').live('click', function() {
				$sn.us.changePicture(-1);
			});

			if ($.browser.msie && $.browser.version < "9.0") {
				$('.sn-us-videoOverlay').removeAttr('style').css({
					opacity: '0.4',
					background: '#000',
					width: '150px',
					height: '150px',
					position: 'absolute',
					// marginLeft : '-154px',
					cursor: 'pointer'
				});
			}
			$('.sn-us-videoOverlay').live('click', function() {
				var obj = $(this).prev('div.sn-us-page-Video').children('object');
				var emb = $(obj).children('embed');
				$(this).parent('.sn-us-page-Preview').next('.clear').removeAttr('style').show();

				$(this).attr({
					height: $(obj).attr('height'),
					width: $(obj).attr('width')
				});
				$(emb).css({
					height: $(obj).attr('height'),
					width: $(obj).attr('width')
				}).removeAttr('style');
				$(obj).removeAttr('style');
				$(this).removeAttr('style');
				$(this).hide();
			});

			$sn.comments.init();
			if (!$sn.isOutdatedBrowser) {
				$('textarea.sn-us-mention').mentionsInput({
					templates: {
						wrapper: _.template('<div class="sn-us-mentions-input-box"></div>'),
						autocompleteList: _.template('<div class="sn-us-mentions-autocomplete-list"></div>'),
						mentionsOverlay: _.template('<div class="sn-us-mentions"><div></div></div>')
					},
					onDataRequest: function(mode, query, callback) {
						$.getJSON($sn.us.url, {
							smode: 'get_mention',
							uname: query
						}, function(data) {
							data = _.filter(data, function(item) {
								return item.name.toLowerCase().indexOf(query.toLowerCase()) > -1;
							});
							callback.call(this, data);
						});
					}
				});
			}
		},
		changePicture: function(dir) {
			var c_pic = parseInt($('.sn-us-fetchImgs img:visible').attr('id').replace(/^sn-us-fetchPreviewImg_/i, ''));

			if (c_pic + dir < 0) {
				var c_pic = parseInt($('.sn-us-fetchImgs img').length);
			} else if (c_pic + dir >= parseInt($('.sn-us-fetchImgs img').length)) {
				var c_pic = -1;
			}
			$('.sn-us-fetchImgs img:visible').hide();
			$('#sn-us-fetchPreviewImg_' + (c_pic + dir)).show();
			$('.sn-us-fetchImages .sn-us-fetchThumb .cPic').html((c_pic + dir + 1));
		},
		_resize: function() {
			$('#sn-us').css({
				left: (($(document).width() - $('#sn-us').width()) / 2) + 'px'
			});

			$('.sn-us-fetchDesc').css({
				width: $('.sn-us-fetchData').width() - $('.sn-us-fpreviews').width() - 2 * parseInt($('.sn-us-fpreviews').css('padding-right')) - 2 * parseInt($('.sn-us-fpreviews').css('padding-left'))
			});
		},
		_scroll: function() {
			if ($('.sn-more').size() > 0 && $('.sn-us-getMore').size() > 0 && $sn.ap._isScrollingToLoadMore == false) {

				if ($(window).scrollTop() >= $('.sn-us-getMore').offset().top - $(window).height() + $('.sn-us-getMore').parent().height()) {

					$(document).oneTime($sn.us.loadMoreTime, 'sn-us-checkScrollDown', function() {
						if ($('.sn-us-getMore').size() == 0 || $sn.ap._isScrollingToLoadMore == true) {
							return;
						}

						if ($(window).scrollTop() >= $('.sn-us-getMore').offset().top - $(window).height() + $('.sn-us-getMore').parent().height()) {
							$('.sn-us-getMore').trigger('click');
						}
					});
				}
			}
		}
	};
}(jQuery, socialNetwork));
