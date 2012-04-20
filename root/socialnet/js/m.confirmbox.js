/**
*
* @package phpBB Social Network
* @version 0.6.3
* @copyright (c) 2010-2012 Kamahl & Culprit http://phpbbsocialnetwork.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/
function snConfirmBox(cbTitle,cbText,callbackConfirm,callbackLoad){
	jQuery(function($){
		if ($.sn.confirmBox.enable) {

			//console.log( $.sn.confirmBox);
			
			$('#ui-dialog-title-dialog').html(cbTitle);
			$('#dialog').html(cbText);
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
				        text : $.sn.confirmBox.button_close,
				        click : function(){
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
				        text : $.sn.confirmBox.button_confirm,
				        click : function(){
					        if ($.isFunction(callbackConfirm)) {
						        callbackConfirm.apply();
					        }
					        $(this).dialog('close');
				        }
				    }, {
				        text : $.sn.confirmBox.button_cancel,
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
