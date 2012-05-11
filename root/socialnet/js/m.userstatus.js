/**
 * 
 * @package phpBB Social Network
 * @version 0.6.3
 * @copyright (c) 2010-2012 Kamahl & Culprit http://phpbbsocialnetwork.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * 
 */
(function($) {
	$.sn.us = {
	    loadMoreTime : 4000,
	    watermark : '',
	    emptyStatus : '',
	    watermarkComment : '',
	    emptyComment : '',
	    deleteStatusTitle : '',
	    deleteStatusText : '',
	    url : './socialnet/userstatus.php',
	    urlFetch : './socialnet/fetch.php',
	    _inited : false,

	    init : function(opts) {
		    if (!$.sn._inited) { return false; }
		    if ($.sn.enableModules.us == undefined || !$.sn.enableModules.us) { return false; }
		    $.sn._settings(this, opts);

		    this._resize();

		    $("#sn-us-wallInput").watermark($.sn.us.watermark, {
		        useNative : false,
		        className : 'sn-us-watermark'
		    }).TextAreaExpander(22, 150).css({
			    height : 22
		    }).live('focusin keyup input', function() {
			    var snUsShare = $(this).val();
			    $(this).parent('.sn-us-share').children('input[name=sn-us-wallButton]').show();
			    if ($.sn.isValidURL(snUsShare) == true) {
				    $('input[name="sn-us-fetchButton"]').show();
			    } else {
				    $('input[name="sn-us-fetchButton"]').hide();
				    $('input[name="sn-us-fetchClear"]').trigger('click');
			    }
		    }).live('focusout', function() {
			    var self = $(this);
			    var snUsShare = self.val();
			    if (snUsShare == '') {
				    var snUsButton = self.parent('.sn-us-share').children('input[name=sn-us-wallButton]');
				    snUsButton.hide();
				    $('input[name="sn-us-fetchButton"]').hide();
				    $('input[name="sn-us-fetchClear"]').hide();
			    }
		    });

		    // Delete status
		    $(".sn-us-deleteStatus").live('click', function() {
			    var status_id = $.sn.getAttr($(this), 'sid');
			    var wallid = $.sn.getAttr($(this), 'wid');
			    $.ajax({
			        type : 'POST',
			        url : $.sn.us.url,
			        dataType : 'json',
			        data : {
			            smode : 'get_status',
			            status : status_id,
			            wall : wallid
			        },
			        success : function(data) {
				        snConfirmBox($.sn.us.deleteStatusTitle, $.sn.us.deleteStatusText + '<hr />' + data.content, function() {
					        $.ajax({
					            type : "POST",
					            url : $.sn.us.url,
					            cache : false,
					            data : {
					                smode : 'status_delete',
					                s_id : status_id
					            },
					            success : function(data) {
						            $('#sn-us-status' + status_id).parents('.sn-ap-textBlock').fadeOut('slow').remove();
					            }
					        });
				        });
			        }
			    })

		    });

		    $('.sn-us-fetchData .sn-us-fetchDesc').TextAreaExpander(18, 70);

		    // Share status on Wall
		    $('.sn-us-share input[name=sn-us-wallButton]').live('click', function() {
			    var status_text = $("#sn-us-wallInput").val();

			    status_text = status_text.replace(/^\s+|\s+$/g, '');

			    if (status_text == '' || status_text == $.sn.us.watermark) {
				    snConfirmBox($.sn.us.emptyStatus, $.sn.us.emptyStatus);
			    } else {
				    var now = Math.floor(new Date().valueOf() / 1000);

				    var wall_id = $.sn.getAttr($(this), 'wall');
				    if (wall_id == '') {
					    wall_id = 0;
				    }

				    var bPage = $.sn.isValidURL($('#sn-us-wallInput').val()) && ($('.sn-us-fetchData .title').html() != '') ? 1 : 0;
				    var bImage = $('#sn-us-noImg').is(':checked');
				    var bVideo = $('#sn-us-noVideo').is(':checked');
				    var $cImage = $('.sn-us-fetchImgs img:visible');
				    $.ajax({
				        type : "POST",
				        url : $.sn.us.url,
				        cache : false,
				        data : {
				            smode : 'status_share_wall',
				            status : status_text,
				            wall : wall_id,
				            isPage : bPage,
				            page : {
				                title : $('.sn-us-fetchData .title').html(),
				                url : $('.sn-us-fetchData .url a').attr('href'),
				                desc : $('.sn-us-fetchData .sn-us-fetchDesc').val(),
				                image : bImage ? '' : $cImage.attr('src'),
				                imageH : bImage ? '' : $.sn.getAttr($cImage, 'imgH'),
				                imageW : bImage ? '' : $.sn.getAttr($cImage, 'imgW'),
				                video : bVideo ? '' : $('.sn-us-fetchVideo').html(),
				                videoI : bVideo ? '' : $('.sn-us-fetchVideoInfo').html(),
				                videoP : bVideo ? '' : $('.sn-us-fetchVideoProvider').html()
				            }
				        },
				        success : function(data) {
					        $('.sn-us-noStatus').remove();
					        $('.sn-ap-noEntry').remove();
					        if ($('.sn-ap-loadNewsOver').size() != 0) {
						        $(data).hide().insertAfter('.sn-ap-loadNewsOver').slideDown('slow');
					        } else {
						        $(data).hide().prependTo('#sn-us-profile').slideDown('slow');
					        }
					        $('input[name=sn-us-fetchClear]').trigger('click');
					        $('input[name=sn-us-fetchButton]').hide();
					        $('#sn-us-wallInput').val('').height(22).watermark($.sn.us.watermark, {
					            useNative : false,
					            className : 'sn-us-watermark'
					        });
					        $.sn.comments.waterMark();
					        $('.sn-us-statusBlock .sn-actions').removeAttr('style');
				        }
				    });
			    }
		    });

		    // Show and hide the text "Write a comment" && Resize comment
		    // textarea
		    $('.sn-us-commentStatus').live('click', function() {
			    // $(this).parents('.sn-us-statusBlock').children('.sn-us-shareComment
			    // .sn-us-inputComment').trigger('focusin');
			    var o_commArea = $(this).parents('.sn-us-statusBox').find('.sn-us-inputComment');

			    o_commArea.focus();
			    // $('.sn-us-buttonCommentOver:visible').hide();
			    // $(o_commArea).next('.sn-us-buttonCommentOver').show();
			    return false;
		    });

		    $(".sn-us-inputComment").live('focusin', function() {
			    $('.sn-us-buttonCommentOver:visible').hide();
			    $(this).next('.sn-us-buttonCommentOver').show();
		    });

		    // Post comment
		    $(".sn-us-shareComment input[name=sn-us-buttonComment]").live('click', function() {
			    var element = $(this);
			    var status_id = $.sn.getAttr(element, "sid");
			    var snUsCommentText = $("#sn-us-textarea" + status_id).val();
			    var now = Math.floor(new Date().valueOf() / 1000);

			    snUsCommentText = snUsCommentText.replace(/^\s+|\s+$/g, '');

			    if (snUsCommentText == '' || snUsCommentText == $.sn.us.watermarkComment) {
				    snConfirmBox($.sn.comments.empty, $.sn.comments.empty);
			    } else {
				    $.ajax({
				        type : "POST",
				        url : $.sn.us.url,
				        cache : false,
				        data : {
				            smode : 'comment_share',
				            comment : snUsCommentText,
				            s_id : status_id
				        },
				        success : function(data) {
					        var $parr = $(element).parents('.sn-us-shareComment');
					        $parr.before(data);
					        $parr.prev('.sn-us-commentBlock').slideDown();
					        $parr.find('.sn-us-inputComment').val('');
					        $.sn.comments.waterMark();
				        }
				    });
			    }
			    $('.sn-us-buttonCommentOver:visible').hide();
		    });

		    // Nacteni dalsich komentaru
		    $('.sn-us-getMoreComments').removeAttr('href').live('click', function() {
			    var o_loader = $(this).next('.sn-us-commentsLoader');
			    o_loader.show();

			    var o_g_m_c = this;
			    var b_last = $(this).hasClass('before');
			    if (b_last) {
				    var lastCommentID = $.sn.getAttr($(this).parents('div.sn-more').prev('.sn-commentBlock'), 'cid');
			    } else {
				    var lastCommentID = $.sn.getAttr($(this).parents('div.sn-more').next('.sn-commentBlock'), 'cid');
			    }

			    var StatusID = $.sn.getAttr($(this), 'id');// $(this).parents('.sn-us-statusBlock').attr('id').replace(/^sn-us-status/i,
			    // '');
			    var userID = $.sn.getAttr($(this), 'user');

			    $.ajax({
			        type : 'POST',
			        cache : false,
			        url : $.sn.us.url,
			        data : {
			            smode : 'comment_more',
			            lCommentID : lastCommentID,
			            s_id : StatusID,
			            u : userID
			        },
			        success : function(data) {
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
		    });

		    // Nacteni dalsich statusu
		    $('.sn-us-getMore').live('click', function() {

			    var t_obj = $(this);
			    var o_prev = t_obj.parents('.sn-more');
			    var i_obj = $(o_prev).prev('div[id^=sn-ap-entry]');
			    var i_lEntry = $.sn.getAttr($(i_obj), 't');
			    var i_lStatusID = $.sn.getAttr($(this).parents('div.sn-more').prev('.sn-us-statusBlock'), 'sid');

			    $.ajax({
			        type : 'POST',
			        cache : false,
			        url : $.sn.us.url,
			        data : {
			            smode : 'status_more',
			            ltime : i_lEntry,
			            u : $.sn.getAttr($(this), 'user')
			        },
			        beforeSubmit : function() {
				        $('.sn-us-statusLoader').show();
			        },
			        error : function() {
				        $('.sn-us-statusLoader').hide();
			        },
			        success : function(data) {
				        $('.sn-us-statusLoader').hide();
				        o_prev.before(data.statuses);
				        $('div[id^=sn-ap-entry]:hidden').slideDown('slow');

				        if (data.moreStatuses == false) {
					        $('.sn-more .sn-us-getMore').remove();
				        }
				        $.sn.comments.waterMark();
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
			        type : 'POST',
			        url : $.sn.us.urlFetch,
			        data : {
			            action : 'load',
			            url : fetchURL
			        },
			        dataType : 'json',
			        error : function(data) {
				        $('input[name=sn-us-fetchClear]').trigger('click');
				        snConfirmBox('Error', data.responseText);
			        },
			        success : function(data) {
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
						            src : image.img,
						            class : '{imgH:' + image.height + ',imgW:' + image.width + '}',
						            load : function() {
							            $(this).attr('id', 'sn-us-fetchPreviewImg_' + idx_img).css({
								            display : (idx_img > 0 ? 'none' : 'inline-block'),
								            maxHeight : 150,
								            width : 100
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
				        $('.sn-us-fetchData .sn-us-fetchDesc').val(data.desc);
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

				        $('.sn-us-fetchDesc').css({
					        width : $('.sn-us-fetchData').width() - $('.sn-us-fpreviews').width() - 2 * parseInt($('.sn-us-fpreviews').css('padding-right')) - 2 * parseInt($('.sn-us-fpreviews').css('padding-left'))
				        });
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
			    if ($.sn.isValidURL($('#sn-us-wallInput').val()) == true) {
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
			    $.sn.us.changePicture(+1);
		    })
		    $('.sn-us-fetchImgsPrev').live('click', function() {
			    $.sn.us.changePicture(-1);
		    })

		    if ($.browser.msie && $.browser.version < "9.0") $('.sn-us-videoOverlay').removeAttr('style').css({
		        opacity : '0.4',
		        background : '#000',
		        width : '150px',
		        height : '150px',

		        position : 'absolute',
		        // marginLeft : '-154px',
		        cursor : 'pointer'
		    });

		    $('.sn-us-videoOverlay').live('click', function() {
			    var obj = $(this).prev('div.sn-us-page-Video').children('object');
			    $(this).parent('.sn-us-page-Preview').next('.clear').removeAttr('style').show();

			    $(this).attr({
			        height : $(obj).attr('height'),
			        width : $(obj).attr('width')
			    });
			    $(this).parent('.sn-us-page-Video').appendTo('<div class="clear">aaa</div>');

			    $(obj).removeAttr('style');
			    /*
				 * .children('embed').removeAttr('style').attr({ height :
				 * $(obj).attr('height'), width : $(obj).attr('width') });
				 */
			    $(this).removeAttr('style');
			    $(this).hide();
		    });

		    // Nacteni dalsich statusu pri scroll na konec stranky

	    },

	    changePicture : function(dir) {
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

	    _resize : function() {
		    $('#sn-us').css({
			    left : (($(document).width() - $('#sn-us').width()) / 2) + 'px'
		    });

	    },

	    // Close comment if clicked outside
	    _documentClick : function(event) {
		    if ($('.sn-us-buttonCommentOver:visible input[name=sn-us-buttonComment]').size() > 0) {
			    var c_obj = $.sn.getAttr($('.sn-us-buttonCommentOver:visible input[name=sn-us-buttonComment]'), 'sid');

			    if (c_obj != '' && !$(event.currentTarget.activeElement).closest('.sn-us-inputComment[id$=' + c_obj + '],.sn-us-shareComment').size()) {
				    $('.sn-us-buttonCommentOver:visible').hide();
			    }
		    }
	    },

	    _scroll : function() {
		    if ($('.sn-more').size() > 0 && $('.sn-us-getMore').size() > 0) {
			    if ($(window).scrollTop() == $(document).height() - $(window).height()) {

				    $(document).oneTime($.sn.us.loadMoreTime, 'sn-us-checkScrollDown', function() {
					    if ($(window).scrollTop() == $(document).height() - $(window).height()) {
						    $('.sn-us-getMore').trigger('click');
					    }
				    });
			    }
		    }
	    }

	}
}(jQuery))
