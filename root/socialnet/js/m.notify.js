/**
 * 
 * @package phpBB Social Network
 * @version 0.7.0
 * @copyright (c) 2010-2012 Kamahl & Culprit http://phpbbsocialnetwork.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * 
 */
(function($) {
	$.sn.ntf = {
	    checkTime : 6000,
	    timerName : 'sn-ntf-ticker',
	    url : '',
	    position : $.sn.rtl ? 'bottom-right' : 'bottom-left',
	    glue : 'before',
	    closer : false,
	    life : 10000,
	    theme : 'black',

	    init : function(opts) {
		    if (!$.sn._inited) { return false; }
		    if ($.sn.enableModules.ntf == undefined || !$.sn.enableModules.ntf) { return false; }
		    $.sn._settings(this, opts);

		    $.extend($.jGrowl.defaults, {
		        position : $.sn.ntf.position,
		        glue : $.sn.ntf.glue,
		        closer : $.sn.ntf.closer,
		        life : $.sn.ntf.life,
		        theme : $.sn.ntf.theme
		    });

		    // $.jGrowl('Testing notification will start in few moments');

		    $('.sn-ntf-delete').live('click', function() {
		    	var $ntf_bl = $(this).parents('.sn-ntf-block');
			    var ntf_id = $.sn.getAttr($(this), 'ntf_id');
			    
			    
			    $.ajax({
			        type : 'post',
			        url : $.sn.ntf.url,
			        dataType : 'json',
			        data : {
			            type : 'delete',
			            nid : ntf_id
			        },
			        success: function(data){
			        	if ( data.del){
			        		$ntf_bl.remove();
			        		if ( $('#sn-page-content').children('.sn-ntf-block').size()==0){
			        			$('.sn-ntf-no-ntf').show();
			        		}
			        	}
			        }
			    })
		    });

		    $('.sn-ntf-markRead').live('click', function() {
		    	var $this = $(this);
		    	var $ntf_bl = $(this).parents('.sn-ntf-block');
			    var ntf_id = $.sn.getAttr($(this), 'ntf_id');
			    $.ajax({
			        type : 'post',
			        url : $.sn.ntf.url,
			        dataType : 'json',
			        data : {
			            type : 'markRead',
			            nid : ntf_id
			        },
			        success: function(data){
			        	$ntf_bl.removeClass('sn-ntf-unread');
		        		$this.remove();
			        }
			    })
		    });
		    
		    
		    
		    
		    if (!$.sn.allow_load) { return; }
		    $.sn.ntf._sn_ntf_check(0);
		    $(document).everyTime($.sn.ntf.checkTime, $.sn.ntf.timerName, function(i) {
			    $.sn.ntf._sn_ntf_check(i);
		    });
	    },

	    _sn_ntf_check : function(i) {
		    if (i > 50) {
			    $(document).stopTime($.sn.ntf.timerName);
			    return false;
		    }
		    $.ajax({
		        type : 'POST',
		        url : $.sn.ntf.url,
		        dataType : 'json',
		        success : function(data) {
			        if ($('#sn-ntf-cube') != null) {
				        $.sn.ntf._sn_ntf_cubes('#sn-ntf-cube', '#sn-ntf-cube', data.cnt);
			        }
			        if ($('#sn-ntf-notify') != null) {
				        $.sn.ntf._sn_ntf_cubes('#sn-ntf-notify', '#sn-ntf-notify a', data.cnt);
			        }

			        $.each(data.message, function(i, ntf) {
				        $.jGrowl(ntf);
			        });
		        }
		    });
	    },

	    _sn_ntf_cubes : function(s_obj, s_obj2, s_count) {
		    if (s_count == 0) {
			    $(s_obj).hide();
		    } else {
			    $(s_obj).show();
			    $(s_obj2).html(s_count + '');
		    }
	    }

	}

}(jQuery));
