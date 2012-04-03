/**
*
* @package phpBB Social Network
* @version 0.6.3
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
			if (!$.sn._inited) {
				return false;
			}
			if ($.sn.enableModules.ntf == undefined || !$.sn.enableModules.ntf) {
				return false;
			}
			$.sn._settings(this, opts);

			$.extend($.jGrowl.defaults, {
				position : $.sn.ntf.position,
				glue : $.sn.ntf.glue,
				closer : $.sn.ntf.closer,
				life : $.sn.ntf.life,
				theme : $.sn.ntf.theme
			});

			// $.jGrowl('Testing notification will start in few moments');

			if (!$.sn.allow_load) {
				return;
			}
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
