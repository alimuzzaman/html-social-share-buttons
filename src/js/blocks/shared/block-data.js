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
		iconsets: localized.iconsets || {},
		iconsetAssets: localized.iconsetAssets || {},
		inheritedIconset: localized.inheritedIconset || 'default',
		profileLinks: localized.profileLinks || {},
	};
}
