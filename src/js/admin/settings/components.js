export function createSettingsComponents(runtime) {
	var e = runtime.createElement;
	var SelectControl = runtime.SelectControl;
	var ToggleControl = runtime.ToggleControl;
	var text = runtime.text;
	var toBoolean = runtime.toBoolean;
	var useState = runtime.useState;

	function ToggleInput(props) {
		return e('div', { className: 'zm_native_toggle' + (props.className ? ' ' + props.className : '') }, [
			props.headerContent || null,
			e(ToggleControl, {
				key: 'control',
				label: props.label,
				name: props.name,
				value: '1',
				checked: toBoolean(props.checked),
				onChange: function (checked) {
					props.onChange(checked ? 1 : 0);
				},
				'aria-describedby': props['aria-describedby'],
				disabled: !!props.disabled,
				__nextHasNoMarginBottom: true
			})
		]);
	}

	function ExpandableTogglePanel(props) {
		var enabled = toBoolean(props.checked);
		var headerContent = props.headerContent;

		if (props.title) {
			headerContent = e('div', { key: 'identity', className: 'zm_panel_identity' }, [
				props.marker || null,
				e('div', { key: 'copy', className: 'zm_panel_copy' }, [
					e('h3', { key: 'title' }, props.title),
					props.description ? e('p', { key: 'description' }, props.description) : null
				])
			]);
		}

		return e('div', { className: 'zm_expandable_toggle_panel' + (props.className ? ' ' + props.className : '') + (enabled ? ' is-enabled' : '') }, [
			e(ToggleInput, {
				key: 'toggle',
				className: 'zm_panel_toggle' + (props.headerClassName ? ' ' + props.headerClassName : ''),
				headerContent: headerContent,
				label: props.label,
				name: props.name,
				checked: enabled,
				onChange: props.onChange,
				disabled: props.disabled
			}),
			enabled ? e('div', { key: 'details', className: 'zm_expandable_toggle_panel_details' + (props.detailsClassName ? ' ' + props.detailsClassName : '') }, props.children) : (props.preservedControl || null)
		]);
	}

	function SettingsLoader() {
		return e('div', {
			className: 'zm_settings_loader zm_settings_loader--react',
			role: 'status',
			'aria-live': 'polite'
		}, [
			e('span', { key: 'spinner', className: 'zm_settings_loader_spinner', 'aria-hidden': 'true' }),
			e('span', { key: 'label' }, text('loading', 'Loading settings...'))
		]);
	}

	function SectionHeader(props) {
		return e('div', { className: 'zm_section_header' }, [
			e('h2', { key: 'title' }, props.title),
			props.description ? e('p', { key: 'description' }, props.description) : null
		]);
	}

	function FrontendJsBadge(props) {
		var badgeId = (props.id || 'feature') + '-frontend-js-help';
		var label = props.label || text('frontendJsBadge', 'Adds frontend JavaScript');
		var description = props.description || text('frontendJsBadgeHelp', 'Enabling this feature adds JavaScript to public pages on your site.');
		var state = useState(false);
		var isOpen = state[0];
		var setOpen = state[1];
		return e('span', {
			className: 'zm_frontend_js_badge' + (isOpen ? ' is-tooltip-open' : ''),
			tabIndex: 0,
			role: 'button',
			'aria-label': label,
			'aria-describedby': badgeId,
			'aria-expanded': isOpen,
			title: description,
			'data-tooltip': description,
			onMouseEnter: function () { setOpen(true); },
			onMouseLeave: function () { setOpen(false); },
			onFocus: function () { setOpen(true); },
			onBlur: function () { setOpen(false); },
			onClick: function (event) { event.preventDefault(); event.stopPropagation(); setOpen(!isOpen); },
			onKeyDown: function (event) {
				if (event.key === 'Escape') {
					event.preventDefault();
					setOpen(false);
				}
			}
		}, [
			e('span', { key: 'text', 'aria-hidden': 'true' }, 'JS'),
			e('span', { key: 'help', id: badgeId, className: 'screen-reader-text' }, description)
		]);
	}

	function PlacementInput(props) {
		var iconset = props.iconset || { types: [] };
		var types = iconset.types || [];
		var profileLinkMode = props.profileLinkMode === 'none' ? 'none' : 'inherit';
		return e(ExpandableTogglePanel, {
			className: 'zm_placement_item',
			detailsClassName: 'zm_placement_details',
			marker: e('span', { key: 'diagram', className: 'zm_panel_marker zm_placement_diagram zm_placement_diagram--' + props.id, 'aria-hidden': 'true' }, [
				e('span', { key: 'copy', className: 'zm_placement_diagram_copy' }),
				e('span', { key: 'buttons', className: 'zm_placement_diagram_buttons' })
			]),
			title: props.label,
			description: props.description,
			label: props.enabled ? text('enabled', 'Enabled') : text('disabled', 'Disabled'),
			name: 'zm_shbt_fld[show_in][' + props.id + ']',
			checked: props.enabled,
			onChange: function (checked) {
				props.onEnabled(checked ? 1 : 0);
			},
			preservedControl: e('div', { key: 'preserved-controls' }, [
				e('input', {
					key: 'preserved-type',
					type: 'hidden',
					name: 'zm_shbt_fld[' + props.id + ']',
					value: props.type
				}),
				e('input', {
					key: 'preserved-profiles',
					type: 'hidden',
					name: 'zm_shbt_fld[profile_link_placements][' + props.id + ']',
					value: profileLinkMode
				})
			])
		}, [
			e(SelectControl, {
				key: 'type',
				label: text('buttonShape', 'Button shape'),
				name: 'zm_shbt_fld[' + props.id + ']',
				value: props.type,
				options: types.map(function (type) {
					return { label: type, value: type };
				}),
				onChange: props.onType,
				__nextHasNoMarginBottom: true
			}),
			e(SelectControl, {
				key: 'profile-links',
				label: text('profileLinks', 'Profile links'),
				name: 'zm_shbt_fld[profile_link_placements][' + props.id + ']',
				value: profileLinkMode,
				options: [
					{ label: text('profileLinksInherit', 'Show configured profile links'), value: 'inherit' },
					{ label: text('profileLinksNone', 'Hide profile links in this placement'), value: 'none' }
				],
				onChange: props.onProfileLinkMode,
				__nextHasNoMarginBottom: true
			})
		]);
	}

	function CheckboxInput(props) {
		var label = props.label;
		var feature = props.frontendJsFeature;
		var helpId = feature && props.id ? (feature.id || props.id) + '-frontend-js-warning' : null;
		if (feature) {
			label = e('span', { className: 'zm_checkbox_label_with_badge' }, [
				e('span', { key: 'label' }, props.label),
				e(FrontendJsBadge, {
					key: 'badge',
					id: props.id,
					label: feature.label,
					description: feature.description
				})
			]);
		}
		return e('div', { className: 'zm_checkbox_input' }, [e(ToggleInput, {
			key: 'toggle',
			id: props.id,
			label: label,
			name: props.name,
			checked: props.checked,
			onChange: props.onChange,
			dataId: props.dataId,
			'aria-describedby': helpId
		}), props.help ? e('p', { key: 'help', id: helpId, className: 'components-base-control__help' }, props.help) : null]);
	}

	return {
		ToggleInput: ToggleInput,
		ExpandableTogglePanel: ExpandableTogglePanel,
		SettingsLoader: SettingsLoader,
		SectionHeader: SectionHeader,
		PlacementInput: PlacementInput,
		CheckboxInput: CheckboxInput,
		FrontendJsBadge: FrontendJsBadge,
	};
}
