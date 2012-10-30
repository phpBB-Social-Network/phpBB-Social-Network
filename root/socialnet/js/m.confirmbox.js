/**
*
* @package phpBB Social Network
* @version 0.7.2
* @copyright (c) 2010-2012 Kamahl & Culprit http://phpbbsocialnetwork.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/
function snConfirmBox(cbTitle,cbText,callbackConfirm,callbackLoad){
	(function($, snCB){
		if (snCB.enable) {

			$('#ui-dialog-title-dialog').html(cbTitle);
			$(snCB.dialogID).html(cbText);
			// $('#dialog').children('div').remove();
			// $('#dialog').children('span').remove();

			if (callbackConfirm == null || !$.isFunction(callbackConfirm)) {
				$(snCB.dialogID).dialog('option', {
					open : function(){
						snCB.dropShadow($(snCB.dialogID).parent('.ui-dialog'),snCB.shadowBox);							
						if (callbackLoad != null && $.isFunction(callbackLoad)) {
							callbackLoad.apply();
						}
					},
					buttons : [ {
						text : snCB.button_close,
						click : function(){
							$(this).dialog('close');
						}
					} ],
					close: function(){
						$(snCB.dialogID).parent('.ui-dialog').removeAttr('aria-shadow').prev('.ui-overlay').remove();
					}
					
				}).dialog('open');

			} else {
				$(snCB.dialogID).dialog('option', {
					open : function(){
						snCB.dropShadow($(snCB.dialogID).parent('.ui-dialog'),snCB.shadowBox);							
						if (callbackLoad != null && $.isFunction(callbackLoad)) {
							callbackLoad.apply();
						}
					},
					buttons : [ {
						text : snCB.button_confirm,
						click : function(){
							if ($.isFunction(callbackConfirm)) {
								callbackConfirm.apply();
							}
							$(this).dialog('close');
						},
						'class': 'sn-button-bold'
					}, {
						text : snCB.button_cancel,
						click : function(){
							$(this).dialog('close');
						}
					} ],
					close: function(){
						$(snCB.dialogID).parent('.ui-dialog').removeAttr('aria-shadow').prev('.ui-overlay').remove();
					}
				    
				}).dialog('open');
			}
			
			
		} else if (callbackConfirm != null && $.isFunction(callbackConfirm)) {
			callbackConfirm.apply();
		}
		
		
	}(jQuery, socialNetwork.confirmBox));
}
