/* Bundled at build time through src/js/vc-scripts.js. */
(function ($) {
	'use strict';

	$(document).ready(function () {
		$(document).on('change', '.iconset', function () {
			var iconsetId = $(this).val();

			$.post(
				window.ajaxurl,
				{
					action: 'get_iconset_details',
					iconset: iconsetId,
					nonce: window.zm_sh ? window.zm_sh.nonce : ''
				},
				function (response) {
					var icons = typeof response === 'string' ? JSON.parse(response) : response;
					var container = document.querySelector(
						'.wpb_el_type_checkbox .edit_form_line'
					);

					if (!container || !icons || typeof icons !== 'object') {
						return;
					}

					container.textContent = '';
					Object.keys(icons).forEach(function (id) {
						var input = document.createElement('input');
						var label = document.createElement('label');

						input.id = 'icons-' + id;
						input.value = id;
						input.className = 'wpb_vc_param_value icons checkbox';
						input.type = 'checkbox';
						input.name = 'icons';
						label.htmlFor = input.id;
						label.appendChild(input);
						label.appendChild(document.createTextNode(String(icons[id])));
						container.appendChild(label);
					});
				}
			);
		});
	});
})(window.jQuery);
