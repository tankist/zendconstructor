Math.randRange = function(min, max) {
	return Math.floor(Math.random() * (max - min + 1)) + min;
};

var FormsController = Backbone.Controller.extend({
	routes : {
		
	}
});

$(function() {
	
	$('form').ajaxForm({
		dataType:'json',
		beforeSubmit: function() {
			$('.progress-container').fadeIn(500);
		},
		beforeSerialize: function(form, options) {
			var hC = $('#hiddenContainer').empty();
			
			function _createHiddenFields(hiddenFieldName) {
				var name = $(this).data('decoratorName'), 
					options = $(this).data('decoratorOptions');
				hiddenFieldName += '[' + this.index + ']';
				if (!name && !options) {
					return;
				}
				if (name) {
					$('<input type="hidden" name="' + hiddenFieldName + '[decoratorName]">').val(name).appendTo(hC);
				}
				if (options) {
					var optionsString = JSON.stringify(options);
					$('<input type="hidden" name="' + hiddenFieldName + '[decoratorOptions]">').val(optionsString).appendTo(hC);
				}
				$('<input type="hidden" name="' + hiddenFieldName + '[decoratorType]">').val(this.value).appendTo(hC);
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
	$('#optionsDialog input').uniform();
	
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
	
	$('fieldset.collapsible legend').click(function(e) {
		var $this = $(this);
		if ($this.hasClass('collapsed')) {
			$this.siblings('.bigListContainer').slideDown(function() {
				$this.removeClass('collapsed');
			});
		}
		else {
			$this.siblings('.bigListContainer').slideUp(function() {
				$this.addClass('collapsed');
			});
		}
		e.preventDefault();
	});
	
	(function() {
		
		var activeOption;
		
		$('#optionsDialog')
			.bind('save', function(e, name, kv) {
				activeOption
					.data('decoratorName', name)
					.data('decoratorOptions', kv);
			})
			.decoratorOptionsDialog({
				width:400,
				kvContainer:'#decorator-options',
				row:'decoratorOption',
				autoOpen : false
			});
		
		$('.bigListContainer')
			.excangeableList()
			.bind('options', function(e, option) {
				activeOption = option;
				$('#optionsDialog')
					.decoratorOptionsDialog('title', $(this).text() + ' options')
					.decoratorOptionsDialog('set', option.data('decoratorOptions') || {'':''})
					.decoratorOptionsDialog('name', option.data('decoratorName'))
					.decoratorOptionsDialog('open');
			});
	})();
	
	hideLoader();
});

function addEmptyRow() {
	var row = $($.trim(tmpl('row', {text:Math.randRange(1, 10000)})))
		.appendTo('#form-edit ul:first')
		.find('select, input').uniform().end()
		.find('input[title]').blur().end();
};

function hideLoader() {
	$('.progress-container').fadeOut(800);
};

$.fn.excangeableList = function() {
	return this.each(function() {
		var $this = $(this), 
			primary = $this.find('.primary'), 
			secondary = $this.find('.secondary');
		$this
			.find('.leftArrow').click(function(e) {
				primary.children(':selected').clone().appendTo(secondary);
				e.preventDefault();
			}).end()
			.find('.rightArrow').click(function(e) {
				secondary.children(':selected').remove();
				e.preventDefault();
			}).end()
			.find('.upArrow').click(function(e) {
				secondary.children(':selected').each(function() {
					var previous = this.previousSibling;
					while (previous && previous.selected) {
						previous = previous.previousSibling;
					}
					if (previous) {
						$(this).insertBefore(previous);
					}
				});
				e.preventDefault();
			}).end()
			.find('.downArrow').click(function(e) {
				secondary.children(':selected').each(function() {
					var next = this.nextSibling;
					while (next && next.selected) {
						next = next.nextSibling;
					}
					if (next) {
						$(this).insertAfter(next);
					}
				});
				e.preventDefault();
			});
		primary.delegate('option', 'dblclick', function(e) {
			$(this).clone().appendTo(secondary);
			e.preventDefault();
		});
		secondary.delegate('option', 'dblclick', function(e) {
			$this.trigger('options', [$(this)]);
			e.preventDefault();
		});
	})
};

$.widget('zc.keyValueDialog', $.ui.dialog, {
	widgetEventPrefix:'zc_',
	options:{
		row:'',
		kvContainer:'',
		kv:{}
	},
	_create:function() {
		var self = this, options = self.options, kv = options.kv;
		options.buttons = {
			OK : function() {
				self.element.trigger('save', [self.name(), self.kv()]);
				self.close();
			}
		};
		
		self.element
			.delegate('.add-option', 'click', function(e) {
				self.kv('');
				e.preventDefault();
			})
			.delegate('.remove-option', 'click', function(e) {
				$(this).parent().remove();
				self.element.trigger('remove-option');
				e.preventDefault();
			});
		
		var isEmptyKV = true;
		for (var key in kv) {
			if (kv.hasOwnProperty(key)) {
				self.kv(key, kv[key]);
				isEmptyKV = false;
			}
		}
		
		if (isEmptyKV) {
			self.kv('');
		}
		
		$.ui.dialog.prototype._create.call(this, options);
	},
	kv : function(key, value) {
		var options = this.options;
		if (arguments.length == 0) {
			//GET
			var kv = {};
			this.element.find(options.kvContainer).children().each(function() {
				var opts = $(this).find('input').map(function() {
					return this.value;
				});
				if (opts[0] == '') {
					return;
				}
				if (opts.length >= 2) {
					kv[opts[0]] = opts[1];
				}
			});
			return kv;
		}
		else {
			//ADD
			key = key || '', value = value || '';
			if (options.row) {
				this.element
					.find(options.kvContainer).append(tmpl(options.row, {key:key, value:value})).end()
					.find('input').uniform();
			}
			this.element.trigger('add-option', [key, value]);
			return this;
		}
	},
	clear:function() {
		this.element.find(this.options.kvContainer).empty();
		return this;
	},
	set:function(kv) {
		var _rowAdded = false;
		this.clear();
		for (var key in kv) {
			if (kv.hasOwnProperty(key)) {
				this.kv(key, kv[key]);
				_rowAdded = true;
			}
		}
		if (!_rowAdded) {
			this.kv('');
		}
		return this;
	}
});

$.widget('zc.decoratorOptionsDialog', $.zc.keyValueDialog, {
	name:function(name) {
		if (arguments.length == 0) {
			return this.element.find('input[name=decorator-name]').val();
		}
		else {
			this.element.find('input[name=decorator-name]').val(name);
			return this;
		}
		
	}
});