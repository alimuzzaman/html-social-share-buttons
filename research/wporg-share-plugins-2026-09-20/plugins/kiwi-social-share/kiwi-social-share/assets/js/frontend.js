jQuery(document).ready(function (e) {
	try {
		kiwi.frontend.highlightShare(e);
		kiwi.frontend.modalPopup(e);
		kiwi.frontend.handleFloatingBarDisplay(e);
	} catch ( err ) {
		console.warn(err);
	}
});
