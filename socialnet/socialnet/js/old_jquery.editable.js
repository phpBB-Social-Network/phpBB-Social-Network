/*******************************************************************************
 * **********************************************************************************************************\
 * jQuery Plugin editable
 * ***********************************************************************************************************
 * REQUIRES: jQuery UI datepicker, jQuery TextAreaExpander
 * 
 * ***********************************************************************************************************
 * RECOMMENDED: jQuery metadata
 * 
 * ***********************************************************************************************************
 * USAGE: $.editable(selector,options) $(selector).editable(options)
 * 
 * ***********************************************************************************************************
 * OPTIONS: eventActivate (text) - event to activate edit inputClass (text) -
 * class used for input boxes, default "inputbox" cssInput (object) - additional
 * CSS for input, such as positioning cssSelect (object) - additional CSS for
 * select, such as positioning datePicker (object) - datePicker options, view at
 * http://jqueryui.com/demos/datepicker/#options endSeq (object) - end sequence
 * for input alt, ctrl, shift, key code ajaxOptions (object) - ajax options,
 * view at http://api.jquery.com/jQuery.ajax/
 * 
 ******************************************************************************/
(function($) {
	if ($.fn.setCursorPosition == undefined) {
		$.fn.setCursorPosition = function(pos) {
			if ($(this).get(0).setSelectionRange) {
				$(this).get(0).setSelectionRange(pos, pos);
			} else if ($(this).get(0).createTextRange) {
				var range = $(this).get(0).createTextRange();
				range.collapse(true);
				range.moveEnd('character', pos);
				range.moveStart('character', pos);
				range.select();
			}
		}
	}

	$.snEditable = function(selector, opts) {
		$(selector).snEditable(opts);
	};

	$.fn.snEditable = function(options) {
		var opts = {
			eventActivate : 'none',
			eventDeactivate : 'blur',
			inputClass : 'inputbox',
			cssInput : {
				margin : '-6px 0 -4px -4px',
				position : 'relative'
			},
			cssSelect : {
				margin : '-6px 0 -4px -5px',
				paddingLeft : 0,
				paddingRight : 0
			},
			datePicker : {
				dateFormat : 'd. MM yy',
				monthNames : [ 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ],
				monthNamesShort : [ 'Jan1', 'Feb2', 'Mar3', 'Apr4', 'May5', 'Jun6', 'Jul7', 'Aug8', 'Sep9', 'Oct10', 'Nov11', 'Dec12' ],
				isRTL : $.sn.rtl,
				minDate : '-100Y',
				maxDate : '-1Y',
				changeMonth : true,
				changeYear : true,
				autoSize : true
			},
			endSeq : {
				alt : false,
				ctrl : false,
				shift : false,
				key : 13
			},
			ajaxOptions : {
				url : '',
				dataType : 'json'
			}
		};
		$.extend(true, opts, options);

		opts.data = new Array();
		opts.origins = new Array();
		opts.edit = new Array();
		opts.convert = new Array();
		opts.canClose = new Array();

		opts.methods = {
			addButton : function(th) {
				th.append($('<span name="editable-button" class="sn-up-editableButton"></span>'));
				// th.children('[name=editable-button]').click(function(){th.trigger(opts.eventActivate)});
				th.children('[name=editable-button]').click(function() {
					opts.methods.editProccessStart(th);
				});
			},
			editEndKeyPress : function(e) {
				var endSeq = (opts.endSeq.alt != e.altKey || opts.endSeq.ctrl != e.ctrlKey || opts.endSeq.shift != e.shiftKey || opts.endSeq.key != e.keyCode);
				var idx = $(this).parent('[edit-id]').attr('edit-id');

				if ($(this).parent('[edit-id]').attr('edit-type') == 'textarea') {
					if (!endSeq) {
						if (opts.canClose[idx] == false) {
							opts.canClose[idx] = true;
							return;
						}
					} else {
						opts.canClose[idx] = false;
					}
				}

				if (endSeq) {
					return;
				}
				opts.canClose[idx] = false;
				opts.methods.editProccessEnd($(this).parent('[edit-id]'), this);
				// $(this).trigger(opts.eventDeactivate);
			},
			editEnd : function() {
				var th = $(this).parent('[edit-id]');
				opts.methods.editProccessEnd(th, this);
			},
			editProccessEnd : function(obj, input) {
				var th = $(obj);
				var idx = th.attr('edit-id');
				var _value = $(input).val();

				if (opts.eventDeactivate != false && opts.eventDeactivate != 'none') {
					th.off(opts.eventDeactivate);
				}

				if (_value != opts.edit[idx]) {
					// store value
					var _response = {};
					$.ajax({
						type : 'post',
						async : false,
						cache : false,
						url : opts.ajaxOptions.url,
						dataType : opts.ajaxOptions.dataType,
						data : {
							mode : 'upEdit',
							field : th.attr('edit-name'),
							value : _value,
							bbcode : opts.data[idx].bbcode ? 1 : 0,
							date : opts.data[idx].date ? 1 : 0,
							uid : opts.data[idx].uid,
							bitfield : opts.data[idx].bitfield
						},
						success : function(data) {
							_response = data;
						}
					});
					if (opts.convert[idx]) {
						if (opts.data[idx].bbcode == true) {
							opts.origins[idx] = _response.origin;
							opts.edit[idx] = _response.edit;
						} else {
							opts.origins[idx] = opts.origins[idx].replace(edit[idx], _response.origin).replace(opts.edit[idx], _response.origin).replace(opts.edit[idx], _response.origin);
							opts.edit[idx] = _response.edit;
						}

					} else if (th.attr('edit-type') == 'select') {
						opts.origins[idx] = opts.data[idx][_response.origin];
						opts.edit[idx] = opts.data[idx][_response.origin];
					} else {
						opts.origins[idx] = _response.origin;
						opts.edit[idx] = _response.edit;
					}

				}

				th.html(opts.origins[idx]);
				if (opts.eventActivate != false && opts.eventActivate != 'none') {
					th.on(opts.eventActivate, opts.methods.editStart);
				}
				opts.methods.addButton(th);
			},
			editStart : function() {
				opts.methods.editProccessStart(this);
			},
			editProccessStart : function(obj) {
				var th = $(obj)
				if (th.attr('edit-id') == undefined) {
					return;
				}
				th.off(opts.eventActivate);
				var idx = th.attr('edit-id');
				var _deac = true;
				var _input = $('<input type="text" name="editable-' + idx + '" value="' + opts.edit[idx] + '" class="' + opts.inputClass + '" />').css(opts.cssInput);

				th.html(_input);
				var _position = opts.origins[idx].length;
				switch (th.attr('edit-type')) {
				case 'text':
					break;
				case 'password':
					_input = $('<input type="password" name="editable-' + idx + '" value="' + opts.edit[idx] + '" class="' + opts.inputClass + '" />').css(opts.cssInput);
					break;
				case 'date':
					_deac = false;

					_input.datepicker($.extend(opts.datePicker, {
						onClose : function() {
							if (opts.eventDeactivate != false && opts.eventDeactivate != 'none') {
								th.children('[name^="editable-"]').on(opts.eventDeactivate, opts.methods.editEnd).trigger(opts.eventDeactivate);
							} else {
								th.children('[name^="editable-"]').focus();
							}
						}
					}));
					break;
				case 'select':
					_input = $('<select name="editable-' + idx + '" class="' + opts.inputClass + '" />').css(opts.cssSelect);
					$.each(data[idx], function(o_idx, o_item) {
						if (!isNaN(parseInt(o_idx))) {
							$(_input).append(new Option(o_item, o_idx));
						}
					});
					th.html(_input);
					break;
				case 'textarea':
					var _t_height = th.height();
					if (_t_height < 100) {
						_t_height = 100;
					}
					_input = $('<textarea name="editable-' + idx + '" class="' + opts.inputClass + '"></textarea>').css(opts.cssSelect);
					_input.text(opts.edit[idx]);
					_input.TextAreaExpander(22, 100);
					th.html(_input);
					_position = 0;
					break;
				}
				_input.css({
					width : th.width()
				});
				_input.focus().setCursorPosition(_position);
				th.children('[name^="editable-"]').keypress(opts.methods.editEndKeyPress);
				if (opts.eventDeactivate != false && opts.eventDeactivate != 'none' && _deac) {
					th.children('[name^="editable-"]').on(opts.eventDeactivate, opts.methods.editEnd);
				}
			}
		};

		this.map(function(idx) {
			var th = $(this);

			if (th.attr('edit-id') == undefined) {
				th.attr('edit-id', idx);
			}
			if ($.metadata) {

				var meta = $.metadata.get(this);
				if (meta.input != undefined)
					th.attr('edit-type', meta.input);
				if (meta.name != undefined)
					th.attr('edit-name', meta.name);
				if (meta.value != undefined)
					th.attr('edit-value', meta.value);
				if (meta.data != undefined) {
					th.attr('edit-options', th.attr('class').replace(/^.*?data\s*?:\s*?([\[\{]{1}.*?[\}\]]{1})[^\}\]]*?.*?$/, '$1'));
				}
				if (meta.value != undefined)
					opts.edit[idx] = meta.value;

			}
			if (th.attr('edit-type') == undefined || th.attr('edit-name') == undefined || (th.attr('edit-type') == 'select' && th.attr('edit-options') == undefined)) {
				return;
			}

			this.origin = th.html();
			
			opts.origins[idx] = th.html();
			if (opts.edit[idx] == undefined)
				opts.edit[idx] = opts.origins[idx];
			opts.convert[idx] = opts.edit[idx] != opts.origins[idx];
			opts.canClose[idx] = false;
			opts.data[idx] = {
				bbcode : false,
				date : false,
				uid : '',
				bitfield : ''
			};
			if (th.attr('edit-options') != undefined) {
				eval('opts\.data[idx] = \$\.extend(opts\.data[idx],' + th.attr('edit-options') + ');');
			}
			opts.methods.addButton(th);

			// th.height(th.height());
			if (opts.eventActivate != false && opts.eventActivate != 'none') {
				th.on(opts.eventActivate, opts.methods.editStart);
			}
		});

	}
})(jQuery);
