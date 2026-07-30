import {
	parseShareTemplateParameters,
	serializeShareTemplateParameters,
	splitShareTemplate,
} from '../../../admin/share-template';
import { hasOwn } from './settings-model';

export function attachTemplateEditorBehavior(App, dependencies) {
	var $ = dependencies.$;
	var data = dependencies.data;
	var e = dependencies.createElement;
	var sharePlaceholders = dependencies.sharePlaceholders;

	App.prototype.resetShareTemplate = function (platform) {
		this.update('share_templates.' + platform, '');
	};

	App.prototype.getTemplateParts = function (platform) {
		var template = hasOwn(this.state.shareTemplateOverrides, platform) ? this.state.shareTemplateOverrides[platform] : '';
		var defaultTemplate = data.share_template_defaults && data.share_template_defaults[platform] ? data.share_template_defaults[platform] : '';
		return {
			current: splitShareTemplate(template || defaultTemplate, defaultTemplate),
			defaultValue: splitShareTemplate(defaultTemplate)
		};
	};

	App.prototype.updateShareTemplateQuery = function (platform, query) {
		var parts = this.getTemplateParts(platform);
		this.update('share_templates.' + platform, parts.current.prefix + query);
	};

	App.prototype.getShareTemplateParameters = function (platform) {
		var parts = this.getTemplateParts(platform);
		return parseShareTemplateParameters(parts.current.query, parts.defaultValue.query);
	};

	App.prototype.updateShareTemplateParameter = function (platform, index, value) {
		var parameters = this.getShareTemplateParameters(platform);
		if (!parameters[index]) {
			return;
		}
		parameters[index].value = value;
		parameters[index].hasEquals = true;
		this.updateShareTemplateQuery(platform, serializeShareTemplateParameters(parameters));
	};

	App.prototype.setActiveTemplateField = function (platform, index) {
		this.activeTemplateField[platform] = index;
	};

	App.prototype.templateFieldKey = function (platform, index) {
		return platform + ':' + index;
	};

	App.prototype.getTemplateSelection = function (field) {
		var selection;
		var range;
		var before;
		var selected;

		if (!field || !window.getSelection) {
			return null;
		}
		selection = window.getSelection();
		if (!selection || !selection.rangeCount) {
			return null;
		}
		range = selection.getRangeAt(0);
		if (!field.contains(range.startContainer) || !field.contains(range.endContainer)) {
			return null;
		}
		before = range.cloneRange();
		before.selectNodeContents(field);
		before.setEnd(range.startContainer, range.startOffset);
		selected = range.cloneRange();
		return {
			start: before.toString().length,
			end: before.toString().length + selected.toString().length
		};
	};

	App.prototype.setTemplateSelection = function (field, start, end) {
		var selection;
		var range;
		var nodes = [];
		var index = 0;

		function collect(node) {
			var child;
			if (node.nodeType === 3) {
				nodes.push(node);
				return;
			}
			for (child = node.firstChild; child; child = child.nextSibling) {
				collect(child);
			}
		}

		function pointAt(offset) {
			var node;
			var length;
			for (index = 0; index < nodes.length; index++) {
				node = nodes[index];
				length = node.nodeValue.length;
				if (offset <= length) {
					return { node: node, offset: offset };
				}
				offset -= length;
			}
			return nodes.length ? { node: nodes[nodes.length - 1], offset: nodes[nodes.length - 1].nodeValue.length } : { node: field, offset: 0 };
		}

		if (!field || !document.createRange || !window.getSelection) {
			return;
		}
		collect(field);
		range = document.createRange();
		var startPoint = pointAt(start);
		var endPoint = pointAt(end);
		range.setStart(startPoint.node, startPoint.offset);
		range.setEnd(endPoint.node, endPoint.offset);
		selection = window.getSelection();
		selection.removeAllRanges();
		selection.addRange(range);
	};

	App.prototype.restoreTemplateSelection = function (fieldKey) {
		var selection = this.templateSelections[fieldKey];
		var field = this.templateFields[fieldKey];
		if (!selection || !field) {
			return;
		}
		field.focus();
		this.setTemplateSelection(field, selection.start, selection.end);
		delete this.templateSelections[fieldKey];
	};

	App.prototype.scheduleTemplateSelectionRestore = function (fieldKey) {
		var self = this;
		window.setTimeout(function () {
			self.restoreTemplateSelection(fieldKey);
		}, 0);
	};

	App.prototype.getTemplateAutocompletePosition = function (field) {
		var fieldRect;
		var root;
		var rootRect;
		var selection;
		var range;
		var caretRect;
		var width = 264;
		var height = 154;
		var viewportWidth = window.innerWidth || 0;
		var viewportHeight = window.innerHeight || 0;
		var left;
		var top;

		if (!field || !field.getBoundingClientRect) {
			return { left: 8, top: 38 };
		}
		fieldRect = field.getBoundingClientRect();
		root = field.closest ? field.closest('.zm_network_template') : null;
		rootRect = root && root.getBoundingClientRect ? root.getBoundingClientRect() : fieldRect;
		caretRect = fieldRect;
		if (window.getSelection) {
			selection = window.getSelection();
			if (selection && selection.rangeCount) {
				range = selection.getRangeAt(0).cloneRange();
				range.collapse(false);
				if (range.getBoundingClientRect) {
					caretRect = range.getBoundingClientRect() || fieldRect;
				}
			}
		}
		left = caretRect.left - rootRect.left;
		top = caretRect.bottom - rootRect.top + 6;
		left = Math.max(8, Math.min(left, Math.max(8, rootRect.width - width - 8)));
		if (viewportWidth) {
			left = Math.max(8, Math.min(left, viewportWidth - rootRect.left - width - 8));
		}
		if (viewportHeight) {
			top = Math.min(top, Math.max(8, viewportHeight - rootRect.top - height - 8));
		}
		return { left: Math.round(left), top: Math.round(Math.max(8, top)) };
	};

	App.prototype.openTemplateAutocomplete = function (platform, index, field, replaceRange) {
		this.setState({
			templateAutocomplete: {
				platform: platform,
				index: index,
				selectedIndex: 0,
				position: this.getTemplateAutocompletePosition(field),
				replaceRange: replaceRange || null
			}
		});
	};

	App.prototype.closeTemplateAutocomplete = function () {
		if (this.state.templateAutocomplete) {
			this.setState({ templateAutocomplete: null });
		}
	};

	App.prototype.handleTemplateDocumentPointerDown = function (event) {
		var target = event.target;
		if (!target || !target.closest || (!target.closest('.zm_template_parameter_editor') && !target.closest('.zm_template_autocomplete'))) {
			this.closeTemplateAutocomplete();
		}
	};

	App.prototype.handleTemplateInput = function (platform, index, event) {
		var field = event.currentTarget || event.target;
		var value = field && typeof field.textContent === 'string' ? field.textContent : '';
		var fieldKey = this.templateFieldKey(platform, index);
		var selection = this.getTemplateSelection(field);
		var isComposing = !!(event.isComposing || (event.nativeEvent && event.nativeEvent.isComposing));

		this.setActiveTemplateField(platform, index);
		if (selection) {
			this.templateSelections[fieldKey] = selection;
		}
		this.updateShareTemplateParameter(platform, index, value);
		if (!isComposing && selection && selection.start === selection.end && value.substring(0, selection.start).slice(-2) === '%%') {
			this.openTemplateAutocomplete(platform, index, field, {
				start: selection.start - 2,
				end: selection.start
			});
		} else {
			this.closeTemplateAutocomplete();
		}
		this.scheduleTemplateSelectionRestore(fieldKey);
	};

	App.prototype.handleTemplateKeyDown = function (platform, index, event) {
		var autocomplete = this.state.templateAutocomplete;
		var isActive = autocomplete && autocomplete.platform === platform && autocomplete.index === index;
		var field = event.currentTarget || event.target;

		if (event.isComposing || (event.nativeEvent && event.nativeEvent.isComposing) || event.keyCode === 229) {
			return;
		}
		if (event.ctrlKey && (event.key === ' ' || event.key === 'Spacebar')) {
			event.preventDefault();
			this.openTemplateAutocomplete(platform, index, field);
			return;
		}
		if (!isActive) {
			return;
		}
		if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
			event.preventDefault();
			this.setState({
				templateAutocomplete: $.extend({}, autocomplete, {
					selectedIndex: (autocomplete.selectedIndex + (event.key === 'ArrowDown' ? 1 : sharePlaceholders.length - 1)) % sharePlaceholders.length
				})
			});
		} else if (event.key === 'Enter') {
			event.preventDefault();
			this.insertSharePlaceholder(platform, sharePlaceholders[autocomplete.selectedIndex].syntax);
		} else if (event.key === 'Escape') {
			event.preventDefault();
			this.closeTemplateAutocomplete();
		}
	};

	App.prototype.handleTemplateBlur = function () {
		var self = this;
		window.setTimeout(function () {
			self.closeTemplateAutocomplete();
		}, 0);
	};

	App.prototype.handleTemplatePaste = function (platform, index, event) {
		var field = event.currentTarget || event.target;
		var clipboard = event.clipboardData || window.clipboardData;
		var text = clipboard && clipboard.getData ? clipboard.getData('text/plain') : '';
		var selection;
		var range;
		var textNode;

		event.preventDefault();
		if (!field || !document.createTextNode || !window.getSelection) {
			return;
		}
		selection = window.getSelection();
		if (!selection || !selection.rangeCount) {
			return;
		}
		range = selection.getRangeAt(0);
		range.deleteContents();
		textNode = document.createTextNode(text);
		range.insertNode(textNode);
		range.setStartAfter(textNode);
		range.collapse(true);
		selection.removeAllRanges();
		selection.addRange(range);
		this.handleTemplateInput(platform, index, { currentTarget: field });
	};

	App.prototype.renderTemplateValue = function (value) {
		var parts = String(value || '').split(/(%%(?:permalink|title|imageurl)%%)/g);
		return parts.map(function (part, index) {
			if (/^%%(?:permalink|title|imageurl)%%$/.test(part)) {
				return e('span', {
					key: index,
					className: 'zm_template_placeholder',
					title: part
				}, part);
			}
			return part;
		});
	};

	App.prototype.renderTemplateAutocomplete = function (platform, index, autocomplete) {
		var self = this;
		var listboxId = 'share_template_suggestions_' + platform + '_' + index;
		return e('div', {
			key: 'autocomplete',
			id: listboxId,
			className: 'zm_template_autocomplete',
			role: 'listbox',
			'aria-label': 'Insert share parameter placeholder',
			style: {
				left: autocomplete.position.left + 'px',
				top: autocomplete.position.top + 'px'
			}
		}, sharePlaceholders.map(function (placeholder, placeholderIndex) {
			var optionId = listboxId + '_option_' + placeholderIndex;
			return e('div', {
				key: placeholder.syntax,
				id: optionId,
				className: 'zm_template_autocomplete_option' + (autocomplete.selectedIndex === placeholderIndex ? ' is-selected' : ''),
				role: 'option',
				'aria-selected': autocomplete.selectedIndex === placeholderIndex,
				onMouseDown: function (event) {
					event.preventDefault();
					self.setActiveTemplateField(platform, index);
					self.insertSharePlaceholder(platform, placeholder.syntax);
				}
			}, [
				e('span', { key: 'label', className: 'zm_template_autocomplete_label' }, placeholder.label),
				e('span', { key: 'description', className: 'zm_template_autocomplete_description' }, placeholder.description),
				e('code', { key: 'syntax', className: 'zm_template_autocomplete_syntax' }, placeholder.syntax)
			]);
		}));
	};

	App.prototype.insertSharePlaceholder = function (platform, placeholder) {
		var parameters = this.getShareTemplateParameters(platform);
		var index = typeof this.activeTemplateField[platform] === 'number' ? this.activeTemplateField[platform] : 0;
		var fieldKey = this.templateFieldKey(platform, index);
		var field = this.templateFields[fieldKey];
		var current = parameters[index] ? parameters[index].value : '';
		var selection = this.getTemplateSelection(field);
		var start = selection ? selection.start : current.length;
		var end = selection ? selection.end : start;
		var autocomplete = this.state.templateAutocomplete;
		var replaceRange = autocomplete && autocomplete.platform === platform && autocomplete.index === index ? autocomplete.replaceRange : null;

		if (replaceRange && replaceRange.start >= 0 && replaceRange.end >= replaceRange.start && replaceRange.end <= current.length) {
			start = replaceRange.start;
			end = replaceRange.end;
		}
		var next = current.substring(0, start) + placeholder + current.substring(end);

		this.templateEditorVersions[fieldKey] = (this.templateEditorVersions[fieldKey] || 0) + 1;
		this.templateSelections[fieldKey] = { start: start + placeholder.length, end: start + placeholder.length };
		this.updateShareTemplateParameter(platform, index, next);
		this.closeTemplateAutocomplete();
		this.scheduleTemplateSelectionRestore(fieldKey);
	};
}
