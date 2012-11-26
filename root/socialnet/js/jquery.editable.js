jQuery(document).ready(function($) {
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
		};
	}

	var $snEditable = function(selector, opts) {
		$(selector).snEditable(opts);
	};

	$.fn.snEditable = function(options) {
		var opts = {
			eventActivate: 'none',
			eventDeactivate: 'blur',
			inputClass: 'inputbox',
			cssInput: {
				margin: '-6px 0 -4px -4px',
				position: 'relative'
			},
			cssSelect: {
				margin: '-6px 0 -4px -5px',
				paddingLeft: 0,
				paddingRight: 0
			},
			cssText: {
				margin: '-2px 0 -6px -4px',
				position: 'relative',
				minHeight: '22px',
				maxHeigt: '100px'
			},
			datePicker: {
				dateFormat: 'd. MM yy',
				monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
				monthNamesShort: ['Jan1', 'Feb2', 'Mar3', 'Apr4', 'May5', 'Jun6', 'Jul7', 'Aug8', 'Sep9', 'Oct10', 'Nov11', 'Dec12'],
				isRTL: false,
				minDate: '-100Y',
				maxDate: '-1Y',
				changeMonth: true,
				changeYear: true,
				autoSize: true
			},
			endSeq: {
				alt: false,
				ctrl: false,
				shift: false,
				key: 13
			},
			ajaxOptions: {
				url: '',
				dataType: 'json'
			}
		};

		var methods = {
			addButton: function(obj) {
				var th = $(obj);
				th.append($('<span name="editable-button" class="sn-up-editableButton"></span>'));
				th.children('[name=editable-button]').click(function() {
					methods.editProccessStart(obj);
				});
			},
			editEndKeyPress: function(obj, input, e) {
				var endSeq = (opts.endSeq.alt == e.altKey && opts.endSeq.ctrl == e.ctrlKey && opts.endSeq.shift == e.shiftKey && opts.endSeq.key == e.keyCode);
				var idx = obj.editID;

				if (obj.editType == 'textarea') {
					if (endSeq) {
						if (obj.canClose == false) {
							obj.canClose = true;
							return;
						}
					} else {
						obj.canClose = false;
					}
				}

				if (!endSeq) {
					return;
				}
				obj.canClose = false;
				methods.editProccessEnd(obj, input);
				// $(this).trigger(opts.eventDeactivate);
			},
			editEnd: function(obj, input) {
				methods.editProccessEnd(obj, input);
			},
			editProccessEnd: function(obj, input) {

				var th = $(obj);
				var _value = $(input).val();
				var _value2 = _value;

				if (opts.eventDeactivate != false && opts.eventDeactivate != 'none') {
					th.off(opts.eventDeactivate);
				}

				if (obj.editType == 'select') {
					_value2 = obj.data[_value];
				}

				if (_value2 != obj.edit) {
					// store value
					var _response = {};
					$.ajax({
						type: 'post',
						async: false,
						cache: false,
						url: opts.ajaxOptions.url,
						dataType: opts.ajaxOptions.dataType,
						data: {
							mode: 'upEdit',
							field: obj.editName,
							value: _value,
							bbcode: obj.data.bbcode ? 1 : 0,
							date: obj.data.date ? 1 : 0,
							uid: obj.data.uid,
							bitfield: obj.data.bitfield
						},
						success: function(data) {
							_response = data;
						}
					});
					if (obj.convert) {
						if (obj.data.bbcode == true) {
							obj.origin = _response.origin;
							obj.edit = _response.edit;
						} else {
							obj.origin = _response.origin.replace(/\n/g, '<br />');
							//obj.origin = obj.origin.replace(obj.edit, _response.origin).replace(obj.edit, _response.origin).replace(obj.edit, _response.origin);
							obj.edit = _response.edit;

						}

					} else if (obj.editType == 'select') {
						obj.origin = obj.data[_response.origin];
						obj.edit = obj.data[_response.origin];
					} else {
						obj.origin = _response.origin;
						obj.edit = _response.edit;
					}
				}

				th.html(obj.origin);
				if (opts.eventActivate != false && opts.eventActivate != 'none') {
					th.on(opts.eventActivate, function() {
						methods.editStart(obj);
					});
				}
				methods.addButton(obj);
			},
			/** EDIT START * */
			editStart: function(obj) {
				methods.editProccessStart(obj);
			},
			editProccessStart: function(obj) {
				var th = $(obj);

				th.off(opts.eventActivate);
				var idx = obj.editID;
				var _deac = true;
				var _input = $('<input type="text" name="editable-' + idx + '" value="' + obj.edit + '" class="' + opts.inputClass + '" />').css(opts.cssInput);

				th.html(_input);
				var _position = obj.origin.length;
				switch (obj.editType) {
					case 'text':
						break;
					case 'password':
						_input = $('<input type="password" name="editable-' + idx + '" value="' + obj.edit + '" class="' + opts.inputClass + '" />').css(opts.cssInput);
						break;
					case 'date':
						_deac = false;

						_input.datepicker($.extend(opts.datePicker, {
							onClose: function() {
								if (opts.eventDeactivate != false && opts.eventDeactivate != 'none') {
									th.children('[name^="editable-"]').on(opts.eventDeactivate, function() {
										methods.editEnd(obj, _input);
									}).trigger(opts.eventDeactivate);
								} else {
									th.children('[name^="editable-"]').focus();
								}
							}
						}));
						break;
					case 'select':
						var nopt;
						_input = $('<select name="editable-' + idx + '" class="' + opts.inputClass + '" />').css(opts.cssSelect);
						$.each(obj.data, function(o_idx, o_item) {
							if (!isNaN(parseInt(o_idx))) {
								nopt = new Option(o_item, o_idx);
								if (o_item == obj.edit) {
									nopt.selected = true;
								}
								$(_input).append(nopt);
							}
						});
						th.html(_input);
						break;
					case 'textarea':
						var _t_height = th.height();
						if (_t_height < 100) {
							_t_height = 100;
						}
						_input = $('<textarea name="editable-' + idx + '" class="' + opts.inputClass + '" style="min-height:22px;max-height:100px"></textarea>').css(opts.cssText);
						_input.html(obj.edit);
						th.html(_input);
						$('textarea[name="editable-' + idx + '"]').elastic(false);
						_position = 0;
						break;
				}
				_input.css({
					width: th.width()
				});

				if (obj.editMax) {
					_input.bind('keypress', function() {
						return $(this).val().length <= obj.editMax;
					});
				}

				_input.focus().setCursorPosition(_position);
				th.children('[name^="editable-"]').keypress(function(e) {
					methods.editEndKeyPress(obj, this, e);
				});
				if (opts.eventDeactivate != false && opts.eventDeactivate != 'none' && _deac) {
					th.children('[name^="editable-"]').on(opts.eventDeactivate, function() {
						methods.editEnd(obj, this);
					});
				}
			}

		};
		$.extend(true, opts, options);

		this.map(function(idx) {
			var obj = this;
			th = $(this);

			obj.editID = idx;

			if ($.metadata) {

				var meta = $.metadata.get(obj);
				if (meta.input != undefined)
					obj.editType = meta.input;
				if (meta.name != undefined)
					obj.editName = meta.name;
				if (meta.value != undefined)
					obj.editValue = meta.value;
				if (meta.max != undefined)
					obj.editMax = meta.max;
				if (meta.data != undefined) {
					obj.editOptions = th.attr('class').replace(/^.*?data\s*?:\s*?([\[\{]{1}.*?[\}\]]{1})[^\}\]]*?.*?$/, '$1');
					eval('obj\.data = ' + obj.editOptions + ';');
				}
				if (meta.value != undefined)
					this.edit = meta.value;

			} else {
				obj.editType = th.attr('editType');
				obj.editName = th.attr('editName');
				obj.editValue = th.attr('editValue');
				obj.editMax = th.attr('editMax');
				eval('obj\.editOptions = ' + th.attr('editOptions') + ';');
			}
			if (obj.editType == undefined || obj.editName == undefined || (obj.editType == 'select' && obj.editOptions == undefined)) {
				return;
			}

			obj.origin = th.html();
			if (obj.edit == undefined) {
				obj.edit = this.origin;
			}

			if (obj.editType == 'textarea') {
				obj.edit = obj.edit.replace(/(<br>|<br \/>|<br\/>)/g, "\n");
			}

			obj.convert = this.edit != obj.origin;
			obj.canClose = false;
			if (obj.data == undefined) {
				obj.data = {
					bbcode: false,
					date: false,
					uid: '',
					bitfield: ''
				};
			}
			methods.addButton(obj);

			if (opts.eventActivate != false && opts.eventActivate != 'none') {
				th.on(opts.eventActivate, methods.editStart);
			}

		});
	};

});
