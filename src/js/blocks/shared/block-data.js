/**
 * Read editor data supplied by the server registration without requiring
 * browser APIs that are newer than the WordPress 5.3 support floor.
 *
 * @param {string} globalName Localized data object name.
 * @return {Object} Editor data.
 */
export function editorData( globalName ) {
	const localized = window[ globalName ] || {};

	return {
		apiVersion: Number( localized.apiVersion ) || 1,
		iconsets: localized.iconsets || {},
		legacyIconsets: localized.legacyIconsets || {},
		iconsetAssets: localized.iconsetAssets || {},
		inheritedIconset: localized.inheritedIconset || 'bootstrap-solid',
		buttonAppearance: [ 'minimal', 'framed', 'soft-shadow' ].indexOf( localized.buttonAppearance ) === -1
			? 'legacy'
			: localized.buttonAppearance,
		profileLinks: localized.profileLinks || {},
	};
}
