<?php



	add_shortcode("zm_sh_btn", 'zm_sh_shortcode_cb');

	function zm_sh_shortcode_cb($atts){
		global $zm_sh;
		if(isset($zm_sh->excluded) and $zm_sh->excluded == true) return;
		$atts = shortcode_atts(array(
									'title'			=> '',
									'iconset'		=> "default",
									'url'			=> "%%permalink%%",
									'icons'			=> array(
															"facebook"		=> "on",
															"twitter"		=> "on",
															"linkedin"		=> "on",
															"googlepluse"	=> "on",
															"bookmark"		=> "on",
															"pinterest"		=> "on",
															"mail"			=> "on",
															),
									'iconset_type'	=> "square",
									'class'			=> "in_shortcode",
								),
								$atts,
								'zm_sh_btn'
							);

		// Sanitize all user inputs to prevent XSS
		$atts['title'] = sanitize_text_field($atts['title']);
		$atts['iconset'] = sanitize_key($atts['iconset']);
		$atts['url'] = esc_url_raw($atts['url']);
		$atts['iconset_type'] = sanitize_key($atts['iconset_type']);
		$atts['class'] = sanitize_html_class($atts['class']);

		$icons = $atts['icons'];
		$icons = explode(",", $icons);
		// Sanitize each icon name
		$icons = array_map('sanitize_key', $icons);
		$atts['icons'] = array_flip($icons);
		return $zm_sh->zm_sh_btn($atts);
	}