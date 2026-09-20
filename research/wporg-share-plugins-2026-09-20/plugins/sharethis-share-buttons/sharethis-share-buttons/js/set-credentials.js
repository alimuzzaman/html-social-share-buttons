/**
 * Credentials
 *
 * @package ShareThisShareButtons
 */

/* exported Credentials */
var Credentials = ( function( $, wp ) {
	'use strict';

	return {
		/**
		 * Holds data.
		 */
		data: {},

		/**
		 * Boot plugin.
		 *
		 * @param data
		 */
		boot: function( data ) {
			this.data = data;

			$( document ).ready(
				function() {
					this.init();
				}.bind( this )
			);
		},

		/**
		 * Initialize plugin.
		 */
		init: function() {
			const self = this;

			// Set default WP config.
			wp.ajax.post(
				'set_default_settings',
				{
					type: 'both',
					nonce: self.data.nonce
				}
			).always(
				function( link ) {
					self.registerAccount( self.data.email, Math.random() + '1_stsb_PW!' );
				}.bind( self )
			);
		},

		/**
		 * Send hash data to credential setting.
		 *
		 * @param secret
		 * @param propertyid
		 * @param token
		 * @param type
		 */

		setCredentials: function( secret, propertyid, token, type ) {
			var propSecret = propertyid + '-' + secret;
			const self     = this;

			// If hash exists send it to credential setting.
			wp.ajax.post(
				'set_credentials',
				{
					data: propSecret,
					token: token,
					nonce: this.data.nonce
				}
			).always(
				function( link ) {
					// Set default product configs.
					const iconfig = {
						alignment: 'center',
						color: 'social',
						enabled: false,
						font_size: 11,
						labels: 'cta',
						min_count: 10,
						padding: 8,
						radius: 4,
						networks: ['facebook', 'twitter', 'email', 'sms', 'sharethis'],
						show_total: true,
						size: 32,
						spacing: 8,
						language: 'en',
					};

					const sconfig = {
						alignment: 'left',
						color: 'social',
						enabled: false,
						labels: 'cta',
						min_count: 10,
						radius: 4,
						networks: ['facebook', 'twitter', 'email', 'sms', 'sharethis'],
						top: 200,
						show_mobile: true,
						show_total: true,
						show_desktop: true,
						show_mobile_buttons: true,
						mobile_breakpoint: 1024,
						spacing: 0,
						language: 'en'
					}

					const gconfig = {
						color: '#2e7d32',
						display: 'always',
						enabled: false,
						language: 'en',
						publisher_name: '',
						publisher_purposes: [],
						scope: 'global'
					}

					let theiData = {
						'token' : token,
						'id': propertyid,
						'product': 'inline-share-buttons',
						'config': iconfig
					};

					theiData = JSON.stringify( theiData );

					// Send new button status value.
					$.ajax(
						{
							url: 'https://platform-api.sharethis.com/v1.0/property/product',
							method: 'POST',
							async: false,
							contentType: 'application/json; charset=utf-8',
							data: theiData,
							success: function () {
								wp.ajax.post(
									'set_button_config',
									{
										button: 'inline',
										config: iconfig,
										first: true,
										nonce: self.data.nonce
									}
								).always(
									function ( results ) {
										let thesData = {
											'token' : token,
											'id': propertyid,
											'product': 'sticky-share-buttons',
											'config': sconfig
										};

										thesData = JSON.stringify( thesData );

										// Send new button status value.
										$.ajax(
											{
												url: 'https://platform-api.sharethis.com/v1.0/property/product',
												method: 'POST',
												async: false,
												contentType: 'application/json; charset=utf-8',
												data: thesData,
												success: function () {
													wp.ajax.post(
														'set_button_config',
														{
															button: 'sticky',
															config: sconfig,
															nonce: self.data.nonce
														}
													).always(
														function ( results ) {
															let thegData = {
																'token' : token,
																'id': propertyid,
																'product': 'gdpr-compliance-tool-v2',
																'config': gconfig
															};

															thegData = JSON.stringify( thegData );

															// Send new button status value.
															$.ajax(
																{
																	url: 'https://platform-api.sharethis.com/v1.0/property/product',
																	method: 'POST',
																	async: false,
																	contentType: 'application/json; charset=utf-8',
																	data: thegData,
																	success: function () {
																		wp.ajax.post(
																			'set_button_config',
																			{
																				button: 'gdpr',
																				config: gconfig,
																				nonce: self.data.nonce
																			}
																		).always(
																			function ( results ) {
																				window.location.reload();
																			}
																		);
																	}
																}
															);
														}
													);
												}
											}
										);
									}
								);
							}
						}
					);
				}.bind( this )
			);
		},

		/**
		 * Register new account.
		 *
		 * @param email
		 * @param pw
		 */
		registerAccount: function( email, pw ) {
			var result        = null,
				self          = this,
				url           = this.data.url,
				randomNumber  = Math.floor(
					(
					Math.random() * 10000000000000000
					) + 1
				),
				randomNumber2 = Math.floor(
					(
					Math.random() * 10000000000000000
					) + 1
				),
				theData       = JSON.stringify(
					{
						email: randomNumber + '@' + randomNumber2 + '.com',
						password: pw,
						custom: {
							onboarding_product: 'inline-share-buttons',
							onboarding_domain: url,
							is_wordpress: true,
							wordpress_email: email,
						}
					}
				);

			$.ajax(
				{
					url: 'https://sso.sharethis.com/register',
					method: 'POST',
					async: false,
					contentType: 'application/json; charset=utf-8',
					data: theData,
					success: function( results ) {
						result = results;

						// Create property.
						self.createProperty( result, url, '' );
					},
				}
			);
		},

		/**
		 * Create property for new account.
		 *
		 * @param accountInfo
		 * @param url
		 */
		createProperty: function( accountInfo, url, type ) {
			var result = null,
				self   = this,
				token  = accountInfo.token,
				theData;

			if ( 'string' === typeof accountInfo ) {
				token = accountInfo;
			}

			theData = JSON.stringify(
				{
					token: token,
					product: 'inline-share-buttons',
					domain: url,
					is_wordpress: true
				}
			);

			$.ajax(
				{
					url: 'https://platform-api.sharethis.com/v1.0/property',
					method: 'POST',
					async: false,
					contentType: 'application/json; charset=utf-8',
					data: theData,
					success: function( results ) {
						result = results;
						self.setCredentials( result.secret, result._id, token, type );
					}
				}
			);
		}
	};
} )( window.jQuery, window.wp );
