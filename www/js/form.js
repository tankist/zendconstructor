var formDataDefaultValuesGenerator = function() {
	return {
		className : 'Form',
		prepareFunctionName : '',
		decorators : [],
		elementDecorators : [],
		elements : {}
	};
};

Math.randRange = function(min, max) {
	return Math.floor(Math.random() * (max - min + 1)) + min;
};

$(function() {

	function addEmptyRow() {
		var row = $(addEmptyRowTmpl({text:Math.randRange(1, 10000)}))
			.appendTo('#form-edit ul:first')
			.find('input[title]').blur().end();
	}

	function hideLoader() {
		$('.progress-container').fadeOut(800);
	}

	$.extend(doT.templateSettings, {
		varname : 'vars'
	});

	var addEmptyRowTmpl = doT.template(document.getElementById('row').innerHTML);
	
	$('form').ajaxForm({
		dataType:'json',
		beforeSubmit: function() {
			$('.progress-container').fadeIn(500);
		},
		beforeSerialize: function(form, options) {
			function wrapValueToObject(keys, value) {
				var ar = value, k = keys.reverse();
				for (var i=0;i<k.length;i++) {
					var _e = ar;
					ar = {};
					ar[k[i]] = _e;
				}
				return ar;
			}
			
			var hC = $('#hiddenContainer').empty();
			
			var formData = formDataDefaultValuesGenerator();
			
			$('input[rel], select[rel]').each(function() {
				var rel = $(this).attr('rel');
				if (!rel) return;
				$.extend(true, formData, wrapValueToObject(rel.split('.'), this.value));
			});
			
			//Serialize form decorators
			$('#selectedFormDecorators option').each(function () {
				var decorator = $(this).data('decorator') || {};
				decorator.decorator = this.value;
				formData.decorators.push(decorator);
			});
			//Serialize common form elements decorators
			$('#selectedFormElementDecorators option').each(function () {
				var decorator = $(this).data('decorator') || {};
				decorator.decorator = this.value;
				formData.elementDecorators.push(decorator);
			});
			//Serialize form elements
			$('ul.form-elements li').each(function() {
				var elements = {}, matches, _opts = $(this).find('input, select').serializeArray();
				for (var i=0;i<_opts.length;i++) {
					if (matches = _opts[i].name.match(/item\[([\w\d\.]+)\]/i)) {
						matches = matches[1].split('.');
						var element_id = matches[0] * 1;
						$.extend(true, elements, wrapValueToObject(matches, _opts[i].value));
					}
				}
				$.extend(true, formData.elements, elements);
			});
			
			$.post(form.attr('action'), formData, function(data) {
				if (!data.code) {
					throw new Error('Response failed');
				}
				
				if ($('#formClass').length == 0) {
					$('<div class="result"><div id="formClass" class="php"><pre></pre></div></div>').appendTo($('.result-container').empty());
				}
				
				$('#formClass').find('pre').html(data.code).each(function(index, element) {
					hljs.highlightBlock(element);
				});
				hideLoader();
				document.location.hash = 'result';
			});
			
			return false;
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
		})
		.delegate('a.decoratorsLink', 'click', function(e) {
			$('#elementDecoratorsDialog').dialog('open');
			e.preventDefault();
		});
	
	addEmptyRow();
	
	$('.sortable').sortable({
		handle:'span.drag',
		cancel:':input,button,.non-sortable,select',
		placeholder: 'ui-state-highlight oh'
	}); 
	
	$('fieldset.collapsible legend').click(function(e) {
		var $this = $(this);
		if ($this.hasClass('collapsed')) {
			$this.parent().find('.bigListContainer').slideDown(function() {
				$this.removeClass('collapsed');
			});
		}
		else {
			$this.parent().find('.bigListContainer').slideUp(function() {
				$this.addClass('collapsed');
			});
		}
		e.preventDefault();
	});
	
	(function() {
		
		var activeOption;
		
		$('#optionsDialog')
			.bind('save', function(e, name, kv) {
				activeOption.data('decorator', {
					name : name,
					options : kv
				});
			})
			.decoratorOptionsDialog({
				width:400,
				kvContainer:'#decorator-options',
				row:document.getElementById('decoratorOption').innerHTML,
				autoOpen : false
			});
		
		$('.bigListContainer')
			.excangeableList()
			.bind('options', function(e, option) {
				activeOption = option;
				var decorator = option.data('decorator') || {};
				$('#optionsDialog')
					.decoratorOptionsDialog('option', 'title', $(activeOption).text() + ' options')
					.decoratorOptionsDialog('set', decorator.options || {'':''})
					.decoratorOptionsDialog('name', decorator.name || '')
					.decoratorOptionsDialog('open');
			});
	})();
	
	(function() {
		
		$('#elementDecoratorsDialog')
			.dialog({
				title : 'Декораторы элемента',
				width : 600,
				minWidth : 300,
				autoOpen : false,
				buttons : {
					OK : function() {
						
					},
					Cancel : function(e) {
						$(this).dialog('close');
					}
				},
				modal : true
			});
		
	})();
	
	hideLoader();
});

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
		kv:{},
		modal : true
	},
	_create:function() {
		var self = this, options = self.options, kv = options.kv;
		options.buttons = {
			OK : function() {
				self.element.trigger('save', [self.name(), self.kv()]);
				self.close();
			},
			Cancel : function() {
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

		options.kvTemplate = doT.template(options.row);
		
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
					.find(options.kvContainer).append(options.kvTemplate({key:key, value:value})).end();
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