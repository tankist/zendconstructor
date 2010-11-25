$(function() {
	
	$('form').ajaxForm({
		dataType:'json',
		beforeSubmit: function() {
			$('.progress-container').fadeIn(500);
		},
		beforeSerialize: function(form, options) {
			var hC = $('#hiddenContainer').empty();
			
			function _createHiddenFields(hiddenFieldName) {
				var name = $(this).data('decoratorName'), options = $(this).data('decoratorOptions');
				if (!name && !options) {
					return;
				}
				if (name)
					$('<input type="hidden" name="' + hiddenFieldName + '[' + this.index + '][decoratorName]">').val(name).appendTo(hC);
				if (options)
					$('<input type="hidden" name="' + hiddenFieldName + '[' + this.index + '][decoratorOptions]">').val(JSON.stringify(options)).appendTo(hC);
				$('<input type="hidden" name="' + hiddenFieldName + '[' + this.index + '][decoratorType]">').val(this.value).appendTo(hC);
			}
			
			$('#selectedFormDecorators option').each(function () {
				_createHiddenFields.call(this, 'formDecorators');
			});
			$('#selectedFormElementDecorators option').each(function () {
				_createHiddenFields.call(this, 'formElementDecorators');
			});
			
			$('select.secondary option').each(function() {
				this.selected = true;
			});
		},
		success: function(data) {
			if (!data.code) {
				throw new Error('Response failed');
			}
			
			if ($('#formClass').length == 0) {
				$('<div class="result"><div id="formClass" class="php"></div></div>').appendTo($('.result-container').empty());
			}
			
			$('#formClass').html(data.code).each(function(index, element) {
				hljs.highlightBlock(element);
			});
			hideLoader();
			document.location.hash = 'result';
		}
	});
	
	$('#form-edit')
		.delegate('.plus', 'click', function() {
			addEmptyRow();
			return false;
		})
		.delegate('.remove', 'click', function() {
			$(this).parents('li:first').remove();
			return false;
		})
		.delegate('input[title]', 'focus', function() {
			var _this = $(this);
			if (_this.val() == _this.attr('title')) {
				_this.val('').removeClass("defaultTextActive");
			}
		})
		.delegate('input[title]', 'blur', function() {
			var _this = $(this);
			if (_this.val() == '') {
				_this.val(_this.attr('title')).addClass("defaultTextActive");
			}
		});
	
	$('#form-edit ').find('select, input, button').uniform();
	$('.optionsDialog input').uniform();
	
	addEmptyRow();
	addEmptyRow();
	addEmptyRow();
	addEmptyRow();
	addEmptyRow();
	addEmptyRow();
	
	$('.sortable').sortable({
		cancel:':input,button,.non-sortable,select',
		cursor: 'move',
		placeholder: 'ui-state-highlight'
	}); 
	
	$('.bigListContainer').excangeableList({
		onSecondaryOptionDblClick:function() {
			var _option = $(this);
			$('.optionsDialog')
				.decoratorOptionsDialog({
					title:$(this).text() + ' options',
					width:400,
					kvContainer:'#decorator-options',
					onSave:function(kv) {
						_option.data('decoratorName', this.getName());
						_option.data('decoratorOptions', kv);
					},
					row:'decoratorOption'
				})
				.decoratorOptionsDialog('setKVRows', _option.data('decoratorOptions') || {'':''})
				.decoratorOptionsDialog('setName', _option.data('decoratorName'));
		}
	});
	
	hideLoader();
});

function addEmptyRow() {
	var row = $($.trim(tmpl('row', {text:getRandomInt(1, 10000)})))
		.appendTo('#form-edit ul:first')
		.find('select, input').uniform().end()
		.find('input[title]').blur().end();
};

function getRandomInt(min, max) {
	return Math.floor(Math.random() * (max - min + 1)) + min;
};

function hideLoader() {
	$('.progress-container').fadeOut(800);
};

function getSelectedOptions(select) {
	var options = select.options, selectedOptions = [];
	for(var i=0;i<options.length;i++) {
		if (options[i].selected) {
			selectedOptions.push(options[i]);
		}
	}
	return selectedOptions;
}

$.fn.excangeableList = function(config) {
	var cfg = $.extend({
		onSecondaryOptionDblClick:function() {}
	}, config);
	return this.each(function() {
		var $this = $(this), 
			primary = $this.find('.primary'), 
			secondary = $this.find('.secondary');
		$this.find('.leftArrow').click(function() {
			var selectedOptions = getSelectedOptions(primary.get(0));
			$(selectedOptions).clone().appendTo(secondary);
			return false;
		});
		$this.find('.rightArrow').click(function() {
			var selectedOptions = getSelectedOptions(secondary.get(0));
			$(selectedOptions).remove();
			return false;
		});
		$this.find('.upArrow').click(function() {
			var selectedOptions = getSelectedOptions(secondary.get(0));
			$(selectedOptions).each(function() {
				var previous = this.previousSibling;
				while (previous && previous.selected) {
					previous = previous.previousSibling;
				}
				if (previous) {
					$(this).insertBefore(previous);
				}
			});
			return false;
		});
		$this.find('.downArrow').click(function() {
			var selectedOptions = getSelectedOptions(secondary.get(0));
			$(selectedOptions).each(function() {
				var next = this.nextSibling;
				while (next && next.selected) {
					next = next.nextSibling;
				}
				if (next) {
					$(this).insertAfter(next);
				}
			});
			return false;
		});
		primary.delegate('option', 'dblclick', function() {
			$(this).clone().appendTo(secondary);
			return false;
		});
		secondary.delegate('option', 'dblclick', cfg.onSecondaryOptionDblClick);
	})
};

$.widget('zc.keyValueDialog', $.ui.dialog, {
	widgetEventPrefix:'zc_',
	options:{
		onSave:function() {},
		row:'',
		kvContainer:'',
		kv:{}
	},
	kv:{},
	_create:function() {
		debugger;
		var self = this;
		self.options.buttons = {
			OK:function() {
				if (typeof self.options.onSave == 'function') {
					self.options.onSave.call(self, self.getKV());
				}
				self.close();
			}
		};
		
		self.element
			.delegate('.add-option', 'click', function() {
				self.addKVRow();
				return false;
			})
			.delegate('.remove-option', 'click', function() {
				$(this).parent().remove();
				return false;
			});
		
		var isEmptyKV = true;
		for (var key in self.options.kv) {
			if (self.options.kv.hasOwnProperty(key)) {
				self.addKVRow(key, self.options.kv[key]);
				isEmptyKV = false;
			}
		}
		
		if (isEmptyKV) {
			self.addKVRow();
		}
		
		$.ui.dialog.prototype._create.call(this, self.options);
	},
	getKV:function() {
		var kv = {};
		this.element.find(this.options.kvContainer).children().each(function() {
			var opts = $(this).find('input').map(function() {
				return this.value;
			});
			if (opts.length >= 2) {
				kv[opts[0]] = opts[1];
			}
		});
		return kv;
	},
	addKVRow:function() {
		var key = arguments[0] || '', value = arguments[1] || '';
		if (this.options.row) {
			this.element
				.find(this.options.kvContainer).append(tmpl(this.options.row, {key:key, value:value})).end()
				.find('input').uniform();
		}
		return this;
	},
	clearKVRows:function() {
		this.element.find(this.options.kvContainer).empty();
		return this;
	},
	setKVRows:function(kv) {
		this.clearKVRows();
		for (var key in kv) {
			this.addKVRow(key, kv[key]);
		}
		return this;
	}
});

$.widget('zc.decoratorOptionsDialog', $.zc.keyValueDialog, {
	getName:function() {
		return this.element.find('input[name=decorator-name]').val();
	},
	setName:function(name) {
		this.element.find('input[name=decorator-name]').val(name);
		return this;
	}
});