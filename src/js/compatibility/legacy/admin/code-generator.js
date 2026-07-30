export function buildCode(state, type) {
	var enabled = [];
	for (var iconId in state.icons) {
		if (state.icons[iconId]) {
			enabled.push(iconId);
		}
	}
	var shortcode = "[zm_sh_btn iconset='" + state.iconset + "' iconset_type='" + type + "' icons='" + enabled.join() + "']";
	var php = "<?php\n if(function_exists('zm_sh_btn')){\n\t";
	php += "$options['iconset']\t\t= '" + state.iconset + "';\n\t";
	php += "$options['iconset_type']\t= '" + type + "';\n\t";
	php += "$options['class']\t\t\t= 'in_php_function';\n\t";
	php += "$options['icons']\t\t\t= array( '" + enabled.join("', '") + "' );\n";
	php += "\techo zm_sh_btn($options);\n}";
	php += "\n?>";
	return {
		shortcode: shortcode,
		php: php,
	};
}
