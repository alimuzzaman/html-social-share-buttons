(function () {
	var KiwiLinkLength = function (input) {
		var tmp = '';

		for ( var i = 0; i < 23; i++ ) {
			tmp += 'o';
		}

		return input.replace(/(http:\/\/[\S]*)/g, tmp).length;
	};

	tinymce.PluginManager.add('KiwiClickToTweet', function (editor, url) {

		editor.addButton('KiwiClickToTweet', {
			title  : 'Click to Tweet',
			icon   : ' kicon-twitter',
			onclick: function () {

				var self = this._id;

				editor.windowManager.open({
					title   : 'Build "Click to Tweet"',
					class   : 'kiwi-click-to-tweet',
					body    : [
						{
							type     : 'textbox',
							multiline: true,
							style    : 'height:50px',
							name     : 'tweet',
							label    : 'The text that will be sent out on Twitter.',
							onkeyup  : function () {
								var value = jQuery('.mce-first textarea').val();
								var strLength = value.length;
								var handle = jQuery('#' + self).attr('data-handle');
								if ( value.indexOf('http') > -1 || value.indexOf('https') > -1 ) {
									linkSpace = 0;
								} else {
									linkSpace = 23;
								}

								if ( typeof handle === 'undefined' ) {
									var remaining = 140 - KiwiLinkLength(value) - linkSpace;
								} else {
									var remaining = 140 - KiwiLinkLength(value) - linkSpace - handle.length - 6;
								}

								if ( remaining > 1 || remaining == 0 ) {
									jQuery('.tweetCounter').css({ 'color': 'green' }).text(remaining + ' characters');
								} else if ( remaining == 1 ) {
									jQuery('.tweetCounter').css({ 'color': 'green' }).text(remaining + ' character');
								} else if ( remaining < 0 ) {
									jQuery('.tweetCounter').css({ 'color': 'red' }).text(remaining + ' characters');
								}
							},
							class    : 'tweetCounting'
						},
						{
							type        : 'label',
							name        : 'someHelpText',
							onPostRender: function () {
								var value = jQuery('.mce-first textarea').val();
								var strLength = value.length;
								var handle = jQuery('#' + self).attr('data-handle');                                

								if ( value.indexOf('http') > -1 || value.indexOf('https') > -1 ) {
									linkSpace = 0;
								} else {
									linkSpace = 23;
								}

								if ( typeof handle === 'undefined' ) {
									var remaining = 140 - KiwiLinkLength(value) - linkSpace;
								} else {
									var remaining = 140 - KiwiLinkLength(value) - linkSpace - handle.length - 6;
								}

								this.getEl().innerHTML =
										'<span style="float:right;">You have <span class="tweetCounter" style="color:green">' + remaining + ' characters</span> remaining.</span>';
							},
							text        : ''
						},
						{
							type     : 'textbox',
							multiline: true,
							style    : 'height:50px',
							name     : 'quote',
							label    : 'The quote as it will appear in your article.'
						}, {
							type        : 'label',
							name        : 'someHelpText2',
							onPostRender: function () {
								this.getEl().innerHTML =
										'<div style="width:650px;">&nbsp;</div>';
							},
							text        : ''
						}
					],
					onsubmit: function (e) {
						var value = jQuery('.mce-first textarea').val();
						var strLength = value.length;
						var remaining = 117 - strLength;
						if ( e.data.tweet === '' || e.data.quote === '' ) {
							editor.windowManager.alert('Please, fill in both fields.');
							return false;
						} else if ( remaining < 0 ) {
							editor.windowManager.alert('You have too many characters in your tweet.');
							return false;
						}

						editor.insertContent('[KiwiClickToTweet tweet="' + e.data.tweet.replace(/"/g, '\'') + '" quote="' + e.data.quote.replace(/"/g, '\'') + '"]');
					}
				});
			}
		});
	});
})();
