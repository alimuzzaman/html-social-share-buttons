jQuery(document).ready(function (e) {
	try {
		kiwi.init(e);

		//kiwi.interactions.upsellModalPage(e);
		//kiwi.interactions.handleModalPageForm(e);
		//kiwi.interactions.registration(e);
	}
	catch ( err ) {
		console.warn(err.message);
	}

});

jQuery(document).on('KiwiObjectsLoaded', function () {
	kiwi.interface.preloader(jQuery);
});