/**
*
* @package phpBB Social Network
* @version 0.6.3
* @copyright (c) 2010-2012 Kamahl & Culprit http://phpbbsocialnetwork.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/
jQuery(document).ready(function($){

	if (confirmBox_cfg.enable) {
		var $dialogHTML = $('<div />');
		$dialogHTML.attr('id', 'dialog').css('display', 'none');
		$dialogHTML.attr('title', 'Title Confirm Box');
		$dialogHTML.html('Content Confirm Box');

		$('body').append($dialogHTML);

		$('#dialog').dialog({
		    width : confirmBox_cfg.width,
		    resizable : confirmBox_cfg.resizable,
		    draggable : confirmBox_cfg.draggable,
		    modal : confirmBox_cfg.modal,
		    show : confirmBox_cfg.show,
		    hide : confirmBox_cfg.show,
		    autoOpen : false,
		    dialogClass : 'snConfirmBox'
		});

		$('a[href*="&confirmBox=1"]').click(function(){
			$.ajax({
			    type : 'GET',
			    url : $(this).attr('href'),
			    dataType : 'html',
			    success : function(data){
				    $.snConfirmBox(data);
				    if ($('#dialog form#confirm').size() > 0) {
					    $('#dialog form#confirm').ajaxForm({
						    success : function(data,statusText,xhr,$form){
							    $.snConfirmBox(data);
						    }
					    });
				    }
			    }
			});

			return false;
		});

		// POSTING - New Topic, Reply, PM
		$('form#postform[action*="&confirmBox=1"] input[name=post]').click(function(){
			var b_CB = true;
			if ($('form#postform[action*="&confirmBox=1"][action*="i=pm"]').size() > 0) {
				if ($('form#postform[action*="&confirmBox=1"][action*="i=pm"] input[name^="remove_u"]').size() == 0) {
					b_CB = false;
				}
			}
			if ($('form#postform[action*="&confirmBox=1"] input#subject').val() == '') {
				b_CB = false;
			}
			if ($('form#postform[action*="&confirmBox=1"] #message').size() > 0) {
				var s_post = $('form#postform[action*="&confirmBox=1"] #message').val();
				if (s_post.length < confirmBox_cfg.postMinChar) {
					b_CB = false;
				}
			}
			// alert(b_CB + '\n' +
			// $('form#postform[action*="&confirmBox=1"][action*="i=pm"]
			// input[name^="remove_u"]').size() + '\n' +
			// $('form#postform[action*="&confirmBox=1"] input#subject').val() +
			// '\n' + $('form#postform[action*="&confirmBox=1"]
			// #message').val());
			if (b_CB) {
				$('form#postform[action*="&confirmBox=1"]').ajaxForm({
				    beforeSubmit : function(formData,jqForm,options){
					    var queryString = $.param(formData);
				    },
				    success : function(data,statusText,xhr,$form){
					    $.snConfirmBox(data);
				    }
				});
			}
		});
		$('form#postform[action*="&confirmBox=1"]').attr('snCB', 'added');

		// PM DELETE
		$('form#viewfolder[action*="&confirmBox=1"] input[name=submit_mark]').click(function(){
			var b_CB = $('form#viewfolder[action*="&confirmBox=1"] select[name=mark_option]').val() == 'delete_marked';

			b_CB = b_CB && $('form#viewfolder[action*="&confirmBox=1"] input[name^="marked_msg"]:checked').size() > 0;

			if (b_CB) {
				$('form#viewfolder[action*="&confirmBox=1"]').ajaxForm({
				    beforeSubmit : function(formData,jqForm,options){
					    var queryString = $.param(formData);
				    },
				    success : function(data,statusText,xhr,$form){
					    $.snConfirmBox(data);
					    $('#dialog form#confirm').ajaxForm({
						    success : function(data,statusText,xhr,$form){
							    $.snConfirmBox(data);
						    }
					    });

				    }
				});
			}
		});
		$('form#viewfolder[action*="&confirmBox=1"]').attr('snCB', 'added');

		// POSTING - All others
		$('form[action*="&confirmBox=1"][snCB!=added]').ajaxForm({
		    beforeSubmit : function(formData,jqForm,options){
			    var queryString = $.param(formData);
		    },
		    success : function(data,statusText,xhr,$form){
			    $.snConfirmBox(data);
			    if ($('#dialog form#confirm').size() > 0) {
				    $('#dialog form#confirm').ajaxForm({
					    success : function(data,statusText,xhr,$form){
						    $.snConfirmBox(data);
					    }
				    });
			    }
		    }
		});

	}
});

(function($){
	$.snConfirmBox = function(dData){
		var cbTitle = dData.match(/<h2>(.*)<\/h2>/i);
		var cbMessage = dData.replace(cbTitle[0], '');
		$('#ui-dialog-title-dialog').html(cbTitle[1]);
		$('#dialog').html(cbMessage);

		var dButtons = new Array();
		if ($('#dialog a').size() > 0) {
			var bText = $('#dialog a').size() == 1 ? confirmBox_cfg.button_close : '';
			$('#dialog a').each(function(){
				var obj = this;
				var button = {
				    text : bText != '' ? bText : $(obj).text(),
				    click : function(){
					    window.location = $(obj).attr('href');
					    $('#dialog').dialog('close');
				    }
				};
				dButtons.push(button);
			});

		}

		if ($('#dialog fieldset[class=submit-buttons] input[type=submit]').size() > 0) {
			$('#dialog fieldset[class=submit-buttons] input[type=submit]').each(function(){
				var objID = '#dialog fieldset[class=submit-buttons] input[type=submit][value=' + $(this).val() + ']';
				var button = {
				    text : $(objID).val(),
				    click : function(){
					    if ($(objID).attr('name') != 'cancel') {
						    $(objID).trigger('click');
					    }
					    $(this).dialog('close');
				    }
				};
				dButtons.push(button);
			});
		}

		$('#dialog').dialog('option', {
			buttons : dButtons
		}).dialog('open');
	}

})(jQuery);

function snConfirmBox(cbTitle,cbText,callbackConfirm,callbackLoad){
	jQuery(function($){

		if (confirmBox_cfg.enable) {

			$('#dialog').children('a').button().hide();
			// $('#dialog').children('div').remove();
			// $('#dialog').children('span').remove();

			if (callbackConfirm == null || !$.isFunction(callbackConfirm)) {
				$('#dialog').dialog('option', {
				    open : function(){
					    if (callbackLoad != null && $.isFunction(callbackLoad)) {
						    callbackLoad.apply();
					    }
				    },
				    buttons : [ {
				        text : confirmBox_cfg.button_close,
				        click : function(){
					        if ($('#dialog a:first').size() > 0) {
						        var redi = $('#dialog a:first').attr('href');
						        var red = $.parseURL(redi);
						        var loc = $.parseURL(window.location);
						        if (red.file != loc.file || $.equalsArray(red.params, loc.params) != 1) {
							        if (redi !== false) {
								        window.location = redi;
							        }
						        }
					        }
					        $(this).dialog('close');
				        }
				    } ]
				}).dialog('open');

			} else {
				$('#dialog').dialog('option', {
				    open : function(){
					    if (callbackLoad != null && $.isFunction(callbackLoad)) {
						    callbackLoad.apply();
					    }
				    },
				    buttons : [ {
				        text : confirmBox_cfg.button_confirm,
				        click : function(){
					        if ($.isFunction(callbackConfirm)) {
						        callbackConfirm.apply();
					        }
					        $(this).dialog('close');
				        }
				    }, {
				        text : confirmBox_cfg.button_cancel,
				        click : function(){
					        $(this).dialog('close');
				        }
				    } ]
				}).dialog('open');
			}
		} else if (callbackConfirm != null && $.isFunction(callbackConfirm)) {
			callbackConfirm.apply();
		}
	});
}