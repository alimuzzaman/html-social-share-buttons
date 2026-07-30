<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Admin;

final class LegacyIconSetPayloadBuilder {
	private $iconsets;

	public function __construct( $iconsets ) {
		$this->iconsets = $iconsets;
	}

	public function build() {
		$payload = array();

		foreach ( $this->iconsets->get_iconsets() as $iconset ) {
			$icons = array();
			foreach ( $iconset->get_icons() as $icon ) {
				$iconType = isset( $iconset->types[0] ) ? $iconset->types[0] : 'square';
				$previewUrls = array();
				if ( isset( $icon['image'] ) ) {
					foreach ( (array) $iconset->types as $type ) {
						$previewUrls[ $type ] = esc_url_raw( $iconset->url . $type . '/' . $icon['image'] );
					}
				}
				$icons[] = array(
					'id'           => (string) $icon['id'],
					'name'         => esc_html( $icon['name'] ),
					'preview_url'  => isset( $icon['image'] )
						? esc_url_raw( $iconset->url . $iconType . '/' . $icon['image'] )
						: '',
					'preview_urls' => $previewUrls,
				);
			}

			$payload[] = array(
				'id'          => esc_attr( $iconset->id ),
				'name'        => esc_html( $iconset->name ),
				'preview_img' => esc_url_raw( $iconset->get_iconset_preview() ),
				'types'       => array_values( (array) $iconset->types ),
				'icons'       => $icons,
			);
		}

		return $payload;
	}
}
