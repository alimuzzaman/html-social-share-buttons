import { createClipboardController } from './share-clipboard';

(function () {
	'use strict';
	var clipboard = createClipboardController();
	function findLink(target) {
		while (target && target !== document) {
			if (target.getAttribute && target.getAttribute('data-hssb-copy-link') === '1') {
				return target;
			}
			target = target.parentNode;
		}
		return null;
	}

	document.addEventListener('click', function (event) {
		var link = findLink(event.target);
		if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
			return;
		}
		var url = link.getAttribute('href');
		if (url) {
			event.preventDefault();
			clipboard.copy(link, url);
		}
	});
}());

export {};
