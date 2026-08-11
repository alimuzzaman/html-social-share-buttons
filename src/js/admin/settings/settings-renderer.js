import { buildCode } from './code-generator';
import {
	excludeIds,
	excludeToken,
	getIconPreview,
	hasOwn,
	toBoolean,
} from './settings-model';
import {
	parseShareTemplateParameters,
	serializeShareTemplateParameters,
	splitShareTemplate,
} from '../share-template';
import { renderProfileLinksSection } from '../profile-links';

export function attachSettingsRenderer(App, dependencies) {
	var e = dependencies.createElement;
	var data = dependencies.data;
	var iconsets = dependencies.iconsets;
	var findIconset = dependencies.findIconset;
	var ensureType = dependencies.ensureType;
	var Button = dependencies.components.Button;
	var FormTokenField = dependencies.components.FormTokenField;
	var SelectControl = dependencies.components.SelectControl;
	var Snackbar = dependencies.components.Snackbar;
	var TextControl = dependencies.components.TextControl;
	var ExpandableTogglePanel = dependencies.settingsComponents.ExpandableTogglePanel;
	var SectionHeader = dependencies.settingsComponents.SectionHeader;
	var PlacementInput = dependencies.settingsComponents.PlacementInput;
	var CheckboxInput = dependencies.settingsComponents.CheckboxInput;
	var text = dependencies.text;
	function format(template, values) {
		var nextIndex = 0;
		return template.replace(/%(?:(\d+)\$)?s/g, function (match, position) {
			var index = position ? Number(position) - 1 : nextIndex++;
			return typeof values[index] === 'undefined' ? match : values[index];
		});
	}

	App.prototype.render = function () {
		var self = this;
		var options = this.state.options;
		var currentIconset = findIconset(options.iconset);
		var generated = buildCode(options, this.state.modalType);
		var modalOutput = this.state.modalMode === 'php' ? generated.php : generated.shortcode;
		var modalTitle = this.state.modalMode === 'php' ? '<\\?> ' + text('getPhpCode', 'Get PHP Code') : '[] ' + text('getShortcode', 'Get Shortcode');
		var socialNetworkColumns = [[], []];
		var networkPreviewType = ensureType(currentIconset, options.show_before_post || options.show_after_post || options.show_left || options.show_right);
		var profileNetworks = iconsets.length ? (iconsets[0].icons || []) : [];

		if (currentIconset && currentIconset.icons && currentIconset.icons.length) {
			currentIconset.icons.forEach(function (icon, index) {
				socialNetworkColumns[index % 2].push(icon);
			});
		}
		var placementColumns = [
			[
				{ id: 'show_left', label: text('leftSide', 'Left side'), description: text('leftSideDescription', 'A vertical rail on the left edge of the screen.') },
				{ id: 'show_before_post', label: text('beforePost', 'Before post'), description: text('beforePostDescription', 'A row of buttons placed above post content.') }
			],
			[
				{ id: 'show_right', label: text('rightSide', 'Right side'), description: text('rightSideDescription', 'A vertical rail on the right edge of the screen.') },
				{ id: 'show_after_post', label: text('afterPost', 'After post'), description: text('afterPostDescription', 'A row of buttons placed below post content.') }
			]
		];

		return e('div', { className: 'zm_settings_shell' + (this.state.isDirty ? ' is-dirty' : '') }, [
			e('div', { key: 'top-grid', className: 'zm_settings_top_grid' }, [
				e('section', { key: 'header', className: 'zm_settings_section zm_settings_section--intro' }, [
				e(SectionHeader, {
					key: 'section-header',
					title: text('header', 'Header'),
					description: text('headerDescription', 'Set the text shown with the share buttons and choose pages where buttons should stay hidden.')
				}),
				e(TextControl, {
					key: 'title-field',
					id: 'title',
					label: text('enterTitle', 'Enter a title'),
					name: 'zm_shbt_fld[title]',
					value: options.title,
					onChange: function (value) {
						self.update('title', value);
					},
					__next40pxDefaultSize: true,
					__nextHasNoMarginBottom: true
				}),
				e('div', { key: 'exclude-field', className: 'zm_exclude_control' }, [
					e(FormTokenField, {
						key: 'tokens',
						label: text('excludeContent', 'Exclude pages or posts'),
						value: this.state.excludeItems.map(excludeToken),
						suggestions: this.state.excludeSuggestions,
						help: text('excludeHelp', 'Search published pages and posts, or press Enter to add a custom value.'),
						placeholder: text('excludePlaceholder', 'Search pages, posts, or add a custom value'),
						tokenizeOnBlur: true,
						__next40pxDefaultSize: true,
						onInputChange: function (value) {
							self.searchExcludeContent(value);
						},
						onChange: function (values) {
							self.updateExcludeTokens(values);
						}
					}),
					e('input', {
						key: 'value',
						type: 'hidden',
						id: 'excludes',
						name: 'zm_shbt_fld[excludes]',
						value: excludeIds(this.state.excludeItems)
					})
				])
				]),
				e('section', { key: 'icon-style', className: 'zm_settings_section' }, [
				e(SectionHeader, {
					key: 'section-header',
					title: text('iconStyle', 'Icon Style'),
					description: text('iconStyleDescription', 'Choose the icon pack used for every placement and generated code snippet.')
				}),
				e('div', { key: 'icon-style-panel', className: 'zm_icon_style_panel' }, [
					e(SelectControl, {
						key: 'iconset-field',
						id: 'iconset',
						label: text('buttonStyle', 'Button style'),
						name: 'zm_shbt_fld[iconset]',
						value: options.iconset,
						options: iconsets.map(function (item) {
							return { label: item.name, value: item.id };
						}),
						onChange: function (value) {
							self.update('iconset', value);
						},
						__next40pxDefaultSize: true,
						__nextHasNoMarginBottom: true
					}),
					e('div', { key: 'preview', className: 'button-style-img' }, [
						e('span', { key: 'label' }, text('preview', 'Preview')),
						e('img', {
							key: 'image',
							src: currentIconset ? currentIconset.preview_img : '',
							alt: options.iconset
						})
					])
					])
				])
			]),
			e('section', { key: 'placement', className: 'zm_settings_section' }, [
				e(SectionHeader, {
					key: 'section-header',
					title: text('displayPlacement', 'Display placement'),
					description: text('displayPlacementDescription', 'Turn each placement on or off and pick its shape.')
				}),
				e('div', { key: 'placement-columns', className: 'zm_network_columns zm_placement_columns' }, placementColumns.map(function (column, columnIndex) {
					return e('div', { key: 'placement-column-' + columnIndex, className: 'zm_network_column zm_placement_column' }, column.map(function (placement) {
						return e(PlacementInput, {
							key: placement.id,
							id: placement.id,
							label: placement.label,
							description: placement.description,
							iconset: currentIconset,
							type: ensureType(currentIconset, options[placement.id]),
							enabled: options.show_in[placement.id],
							onEnabled: function (value) {
								self.update('show_in.' + placement.id, value);
							},
							onType: function (type) {
								self.update(placement.id, type);
							}
						});
					}));
				}))
			]),
			e('section', { key: 'social', className: 'zm_settings_section' }, [
				e(SectionHeader, {
					key: 'section-header',
					title: text('socialNetworks', 'Social Networks'),
					description: text('socialNetworksDescription', 'Select the share buttons that should be available in the output.')
				}),
					e('div', { key: 'network-columns', className: 'zm_network_columns' }, socialNetworkColumns.map(function (column, columnIndex) {
						return e('div', { key: 'network-column-' + columnIndex, className: 'zm_network_column' }, column.map(function (icon) {
					var enabled = toBoolean(options.icons[icon.id]);
					var template = hasOwn(self.state.shareTemplateOverrides, icon.id) ? self.state.shareTemplateOverrides[icon.id] : '';
					var defaultTemplate = data.share_template_defaults && data.share_template_defaults[icon.id] ? data.share_template_defaults[icon.id] : (options.share_templates[icon.id] || '');
					var isCustomTemplate = String(template).trim().length > 0;
					var templateParts = splitShareTemplate(template || defaultTemplate, defaultTemplate);
					var defaultTemplateParts = splitShareTemplate(defaultTemplate);
					var templateParameters = parseShareTemplateParameters(templateParts.query, defaultTemplateParts.query);
					var serializedTemplate = isCustomTemplate ? templateParts.prefix + serializeShareTemplateParameters(templateParameters) : '';
					var autocomplete = self.state.templateAutocomplete;
					return e(ExpandableTogglePanel, {
						key: icon.id,
						className: 'zm_network_item',
						detailsClassName: 'zm_network_template',
							marker: e('span', { key: 'icon', className: 'zm_panel_marker zm_network_marker', 'aria-hidden': 'true' }, getIconPreview(icon, networkPreviewType) ? e('img', {
								key: 'image',
								src: getIconPreview(icon, networkPreviewType),
							alt: ''
						}) : icon.name.substring(0, 1)),
						title: icon.name,
						description: icon.id === 'mail' ? text('shareByEmail', 'Share the current page by email.') : format(text('shareOnNetwork', 'Share the current page on %s.'), [icon.name]),
						label: enabled ? text('enabled', 'Enabled') : text('disabled', 'Disabled'),
						name: 'zm_shbt_fld[icons][' + icon.id + ']',
						checked: enabled,
						onChange: function (value) {
							self.update('icon_' + icon.id, value);
						}
					}, [
							e('div', { key: 'template-heading', className: 'zm_network_template_heading' }, [
								e('span', { key: 'label', className: 'zm_network_template_label' }, format(text('shareTemplate', '%s share template'), [icon.name])),
								e(Button, {
									key: 'reset',
									isLink: true,
									type: 'button',
									className: 'zm_template_reset',
									disabled: !isCustomTemplate,
									onClick: function () { self.resetShareTemplate(icon.id); }
								}, text('restoreDefaults', 'Restore defaults'))
							]),
							e('p', { key: 'prefix-row', className: 'zm_template_prefix_row' }, [
								e('span', { key: 'prefix-label' }, text('shareUrl', 'Share URL')),
								e('code', { key: 'prefix', className: 'zm_template_prefix', title: templateParts.prefix }, templateParts.prefix)
							]),
							e('fieldset', { key: 'parameters', className: 'zm_template_parameters' }, [
								e('legend', { key: 'legend' }, [
									e('span', { key: 'label' }, text('shareParameters', 'Share parameters')),
									e('span', { key: 'hint', className: 'zm_template_parameters_hint' }, text('parameterNamesManaged', 'Parameter names are managed automatically'))
								]),
								e('div', { key: 'parameter-list', className: 'zm_template_parameter_list' + (autocomplete && autocomplete.platform === icon.id ? ' is-autocomplete-active' : '') }, templateParameters.map(function (parameter, parameterIndex) {
									var inputId = 'share_template_' + icon.id + '_' + parameterIndex;
									var fieldKey = self.templateFieldKey(icon.id, parameterIndex);
									var isAutocompleteActive = autocomplete && autocomplete.platform === icon.id && autocomplete.index === parameterIndex;
									var listboxId = 'share_template_suggestions_' + icon.id + '_' + parameterIndex;
									var activeOptionId = isAutocompleteActive ? listboxId + '_option_' + autocomplete.selectedIndex : null;
									return e('div', { key: inputId, className: 'zm_template_parameter' }, [
										e('span', { key: 'name', className: 'zm_template_parameter_name' }, parameter.name || text('parameter', 'Parameter')),
										e('div', {
											key: 'value-' + (self.templateEditorVersions[fieldKey] || 0),
											id: inputId,
											className: 'zm_template_parameter_editor',
											contentEditable: true,
											suppressContentEditableWarning: true,
											role: 'combobox',
											'aria-label': format(text('parameterValueLabel', '%1$s %2$s value'), [icon.name, parameter.name || text('parameter', 'Parameter')]),
											'aria-autocomplete': 'list',
											'aria-haspopup': 'listbox',
											'aria-controls': isAutocompleteActive ? listboxId : null,
											'aria-expanded': !!isAutocompleteActive,
											'aria-activedescendant': activeOptionId,
											ref: function (node) { self.templateFields[fieldKey] = node; },
											onFocus: function () { self.setActiveTemplateField(icon.id, parameterIndex); },
											onInput: function (event) { self.handleTemplateInput(icon.id, parameterIndex, event); },
											onKeyDown: function (event) { self.handleTemplateKeyDown(icon.id, parameterIndex, event); },
											onBlur: function () { self.handleTemplateBlur(); },
											onPaste: function (event) { self.handleTemplatePaste(icon.id, parameterIndex, event); },
											onDrop: function (event) { event.preventDefault(); }
										}, self.renderTemplateValue(parameter.value)),
										isAutocompleteActive ? self.renderTemplateAutocomplete(icon.id, parameterIndex, autocomplete) : null
									]);
								}))
							]),
							e('input', {
								key: 'serialized-template',
								type: 'hidden',
								name: 'zm_shbt_fld[share_templates][' + icon.id + ']',
								value: serializedTemplate
							}),
								e('p', { key: 'help', className: 'components-base-control__help' }, isCustomTemplate ? text('customTemplateSaved', 'Custom template saved for this platform.') : text('canonicalTemplateUsed', 'Using the canonical template shown as the placeholder.'))
						]);
						}));
					}))
			]),
			renderProfileLinksSection({
				createElement: e,
				TextControl: TextControl,
				SectionHeader: SectionHeader,
				values: options.profile_links,
				networks: profileNetworks,
				activeIcons: currentIconset ? currentIconset.icons : [],
				previewType: networkPreviewType,
				getIconPreview: getIconPreview,
				text: text,
				sectionClassName: 'zm_settings_section zm_profile_links_section',
				fieldName: function (networkId) {
					return 'zm_shbt_fld[profile_links][' + networkId + ']';
				},
				onChange: function (networkId, value) {
					self.update('profile_links.' + networkId, value);
				}
			}),
			e('section', { key: 'advanced', className: 'zm_settings_section zm_settings_section--advanced' }, [
				e(SectionHeader, {
					key: 'section-header',
					title: text('advancedOptions', 'Advanced options'),
					description: text('advancedOptionsDescription', 'Fine tune tracking, behavior, and link output.')
				}),
					e('div', { key: 'advanced-grid', className: 'zm_network_grid' }, [
						e(CheckboxInput, {
							key: 'g-analytics',
							id: 'g_analytics',
						label: text('googleAnalytics', 'Google Social analytics'),
						name: 'zm_shbt_fld[g_analytics]',
						checked: options.g_analytics,
						onChange: function (value) {
							self.update('g_analytics', value);
						}
						}),
						e(CheckboxInput, {
							key: 'auto-hide',
							id: 'auto_hide_btn',
						label: text('autoHide', 'Auto hide button'),
						name: 'zm_shbt_fld[auto_hide_btn]',
						checked: options.auto_hide_btn,
						onChange: function (value) {
							self.update('auto_hide_btn', value);
						}
						}),
						e(CheckboxInput, {
							key: 'use-port',
							id: 'use_port',
						label: text('usePort', 'Use port on the url.'),
						name: 'zm_shbt_fld[use_port]',
						checked: options.use_port,
						onChange: function (value) {
							self.update('use_port', value);
						}
						}),
						e(CheckboxInput, {
							key: 'nofollow',
							id: 'nofollow',
						label: text('noFollow', 'No follow social link'),
						name: 'zm_shbt_fld[nofollow]',
						checked: options.nofollow,
						onChange: function (value) {
							self.update('nofollow', value);
						}
					})
				])
			]),
			e('section', { key: 'generator', className: 'zm_settings_section' }, [
				e(SectionHeader, {
					key: 'section-header',
					title: text('codeGenerator', 'Code generator'),
					description: text('codeGeneratorDescription', 'Generate embed code from the same icon set and selected networks.')
				}),
					e('div', { key: 'generator-actions', className: 'zm_settings_generator_actions' }, [
						e('button', {
							key: 'php',
							type: 'button',
							className: 'get_phpcode button button-default',
							onClick: function (event) {
							self.openModal('php', event.currentTarget);
							}
						}, '<\\?> ' + text('getPhpCode', 'Get PHP Code')),
						e('button', {
							key: 'shortcode',
							type: 'button',
							className: 'get_shortcode button button-default',
							onClick: function (event) {
							self.openModal('shortcode', event.currentTarget);
							}
						}, '[] ' + text('getShortcode', 'Get Shortcode'))
				])
			]),
			this.state.notice ? e('div', {
				key: 'toaster',
				className: 'zm_settings_toaster is-' + this.state.notice.status
			}, e(Snackbar, {
				onRemove: function () {
					self.setState({ notice: null });
				}
			}, this.state.notice.message)) : null,
			e('div', {
				key: 'code-modal',
				className: 'zm-sh-thick-box',
				id: 'zm-sh-thick-box',
				role: 'dialog',
				'aria-modal': 'true',
				'aria-labelledby': 'zm-sh-code-modal-title',
				style: {
					display: this.state.modalOpen ? 'block' : 'none',
					position: 'fixed',
					top: 0,
					left: 0,
					right: 0,
					bottom: 0,
					zIndex: 99999
				}
			}, [
				e('div', { key: 'backdrop', className: 'backdrop', 'aria-hidden': 'true', onClick: function () { self.closeModal(); } }),
				e('div', { key: 'panel', className: 'zm-tabs', onMouseDown: function (event) { event.stopPropagation(); }, onKeyDown: function (event) { self.handleModalKeyDown(event); } }, [
					e('h3', { key: 'title', id: 'zm-sh-code-modal-title', className: 'title' }, modalTitle),
					e('button', { key: 'close', type: 'button', className: 'close', ref: function (node) { self.modalCloseButton = node; }, onClick: function () { self.closeModal(); } }, text('close', 'Close')),
					e(SelectControl, {
						key: 'type-picker',
						id: 'shortcode-iconset-type',
						label: text('buttonShape', 'Button shape'),
						value: self.state.modalType,
						options: ((currentIconset && currentIconset.types) || ['square']).map(function (type) {
							return { label: type, value: type };
						}),
						onChange: function (value) {
							self.setState({ modalType: value });
						},
						__next40pxDefaultSize: true,
						__nextHasNoMarginBottom: true
					}),
					e('div', { key: 'output', className: 'tab', id: 'tab-2' },
						e('textarea', {
							id: 'copy_shortcode',
							style: { width: '100%', height: '200px' },
							value: modalOutput,
							readOnly: true
						}, [])
					)
				])
			])
		]);
	};

}
