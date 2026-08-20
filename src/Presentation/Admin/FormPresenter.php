<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin;

use Alimuzzaman\HtmlSocialShareButtons\Bootstrap\PluginConfig;
use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSetSelectionPolicy;
use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSetRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\NetworkRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Asset\IconSetAssetResolver;

/**
 * Canonical presenter behind the legacy zm_form helper.
 *
 * It owns the small settings-form fragments; the global class only passes its
 * historical arguments and stored option array through to these methods.
 */
final class FormPresenter {
	private $iconSets;
	private $networks;
	private $config;
	private $assets;

	public function __construct(
		IconSetRegistry $iconSets,
		NetworkRegistry $networks,
		PluginConfig $config,
		IconSetAssetResolver $assets
	) {
		$this->iconSets = $iconSets;
		$this->networks = $networks;
		$this->config = $config;
		$this->assets = $assets;
	}

	public function text( array $options, $id, $label, $name = false, $value = false ) {
		$name = $name ? $name : $this->optionField( $id );
		$value = false !== $value ? $value : ( isset( $options[ $id ] ) ? $options[ $id ] : '' );
		echo "<div class='row'><label for='" . esc_attr( $id ) . "' style='width:140px;line-height: 37px;'>" . esc_html( $label ) . "</label><input type='text' id='" . esc_attr( $id ) . "' name='" . esc_attr( $name ) . "' value='" . esc_attr( $value ) . "' style='width: 278px;height: 33px; background-color: #ffffff;border: 1.2px solid #B8B8B8;' ></div>";
	}

	public function textArea( array $options, $id, $label, $name = false, $value = false ) {
		$name = $name ? $name : $this->optionField( $id );
		$value = false !== $value ? $value : ( isset( $options[ $id ] ) ? $options[ $id ] : '' );
		echo "<div class='row'><label for='" . esc_attr( $id ) . "' style='width:140px;line-height: 37px;'>" . esc_html( $label ) . "</label><textarea type='text' id='" . esc_attr( $id ) . "' name='" . esc_attr( $name ) . "' style='width: 278px;background-color: #ffffff;border: 1.2px solid #B8B8B8;' placeholder='Exclude by Page ID, Page Title or Page Slug' >" . esc_textarea( $value ) . '</textarea></div>';
	}

	public function checkbox( array $options, $id, $label, $name = '', $selected = null, $className = '', $yes = '', $no = '', $idPrefix = '', $description = '' ) {
		$yes = $yes ? $yes : __( 'Yes', 'html-social-share-buttons' );
		$no = $no ? $no : __( 'No', 'html-social-share-buttons' );
		$className = $className ? $className : 'toggle-check';
		$selected = null === $selected ? checked( isset( $options[ $id ] ) ? $options[ $id ] : false, true, false ) : $selected;
		$name = $name ? $name : $this->optionField( $id );
		$fieldId = $idPrefix . $id;
		echo "<div class='row'><label for='" . esc_attr( $fieldId ) . "' title='" . esc_attr( $description ) . "'>" . esc_html( $label ) . "</label><input name='" . esc_attr( $name ) . "' id='" . esc_attr( $fieldId ) . "' " . wp_kses_post( $selected ) . " type='checkbox' value='1' data-id='" . esc_attr( $id ) . "' /><span class='for_label'><label for='" . esc_attr( $fieldId ) . "' class='" . esc_attr( $className ) . "' data-on='" . esc_attr( $yes ) . "' data-off='" . esc_attr( $no ) . "'></label></span></div>";
		if ( $description ) {
			echo '<p>' . esc_html( $description ) . '</p>';
		}
	}

	public function showOn( array $options, $id, $label, $selected = false, $className = 'toggle-check', $yes = '', $no = '' ) {
		$iconSet = $this->iconSet( isset( $options['iconset'] ) ? $options['iconset'] : 'default' );
		if ( ! $iconSet ) {
			return;
		}
		$yes = $yes ? $yes : __( 'Yes', 'html-social-share-buttons' );
		$no = $no ? $no : __( 'No', 'html-social-share-buttons' );
		$checked = ! empty( $options['show_in'][ $id ] ) ? "checked='checked'" : '';
		$shape = isset( $options[ $id ] ) ? $options[ $id ] : $iconSet->shapes()[0];
		echo "<div class='row toggle'><label for='" . esc_attr( $id ) . "'>" . esc_html( $label ) . "</label><input id='" . esc_attr( $id ) . "' " . wp_kses_post( $checked ) . " type='checkbox' name='" . esc_attr( $this->optionField( 'show_in' ) . '[' . $id . ']' ) . "' value='1'/><span class='for_label'><label for='" . esc_attr( $id ) . "' class='" . esc_attr( $className ) . "' data-on='" . esc_attr( $yes ) . "' data-off='" . esc_attr( $no ) . "'></label><div class='row show_on' style='margin-top:50px'>";
		foreach ( $iconSet->shapes() as $type ) {
			echo "<input type='radio' id='" . esc_attr( $id . '-' . $type ) . "' name='" . esc_attr( $this->optionField( $id ) ) . "' value='" . esc_attr( $type ) . "' " . wp_kses_post( checked( $shape, $type, false ) ) . " ><label for='" . esc_attr( $id . '-' . $type ) . "'><img src='" . esc_url( $this->config->paths()->assetsUrl() . 'image/' . $id . '-' . $type . '.png' ) . "'></label>";
		}
		echo '</div></span></div>';
	}

	public function iconFields( array $options, $label, $labelPrefix, $className = 'toggle-check', $yes = '', $no = '' ) {
		echo "<div class='row' style='margin-bottom:20px'><h2>" . esc_html( $label ) . '</h2></div>';
		foreach ( $this->icons( $options ) as $icon ) {
			$id = $icon->id();
			$this->checkbox( $options, $id, $labelPrefix . ' ' . $icon->label(), $this->optionField( 'icons' ) . '[' . $id . ']', checked( ! empty( $options['icons'][ $id ] ), true, false ), $className, $yes, $no, 'icon_' );
		}
	}

	public function iconFieldsWidget( array $options, $id, $name, $selectedIcons, $label, $labelPrefix, $iconSet ) {
		echo "<div class='row' style='margin-bottom:20px'><h2>" . esc_html( $label ) . '</h2></div>';
		$selectedIcons = is_array( $selectedIcons ) ? $selectedIcons : array();
		foreach ( $this->icons( array( 'iconset' => $iconSet ) ) as $icon ) {
			$network = $icon->id();
			$this->checkbox( $options, $id . '_' . $network, $labelPrefix . ' ' . $icon->label(), $name . '[' . $network . ']', checked( ! empty( $selectedIcons[ $network ] ), true, false ) );
		}
	}

	public function dropdown( array $options, $id, $label, $items, $name = false, $selected = false ) {
		echo "<div class='row'><label for='" . esc_attr( $id ) . "'>" . esc_html( $label ) . "</label><select id='" . esc_attr( $id ) . "' name='" . esc_attr( $name ) . "'>";
		foreach ( (array) $items as $item ) {
			echo "<option value='" . esc_attr( $item ) . "' " . wp_kses_post( selected( $selected, $item, false ) ) . '>' . esc_html( ucwords( $item ) ) . '</option>';
		}
		echo '</select></div>';
	}

	public function selectIconset( array $options, $id, $label, $items = null, $name = false, $selected = 'default' ) {
		$selected = $selected ? $selected : ( isset( $options[ $id ] ) ? $options[ $id ] : 'default' );
		$items = is_array( $items ) ? $items : $this->iconSetList( $selected );
		$name = $name ? $name : $this->optionField( $id );
		echo "<div class='row'><label for='" . esc_attr( $id ) . "'>" . esc_html( $label ) . "</label><select id='" . esc_attr( $id ) . "' name='" . esc_attr( $name ) . "'>";
		foreach ( $items as $itemId => $itemLabel ) {
			echo "<option value='" . esc_attr( $itemId ) . "' " . wp_kses_post( selected( $selected, $itemId, false ) ) . '>' . esc_html( $itemLabel ) . '</option>';
		}
		$iconSet = $this->iconSet( $selected );
		echo '</select><div class="button-style-img"><img src="' . esc_url( $iconSet ? $this->assets->previewUrl( $iconSet ) : '' ) . '" alt="" class="" /></div></div>';
	}

	private function iconSet( $id ) {
		return $this->iconSets->has( $id ) ? $this->iconSets->get( $id ) : ( $this->iconSets->has( 'default' ) ? $this->iconSets->get( 'default' ) : null );
	}

	private function icons( array $options ) {
		$set = $this->iconSet( isset( $options['iconset'] ) ? $options['iconset'] : 'default' );
		$icons = array();
		if ( ! $set ) {
			return $icons;
		}
		foreach ( $set->iconFiles() as $id => $unused ) {
			if ( $this->networks->has( $id ) ) {
				$icons[] = $this->networks->get( $id );
			}
		}

		return $icons;
	}

	private function iconSetList( $selectedId = '' ) {
		$list = array();
		foreach ( IconSetSelectionPolicy::choices( $this->iconSets, $selectedId ) as $set ) {
			$list[ $set->id() ] = 'default' === $set->id()
				? __( 'Default (legacy)', 'html-social-share-buttons' )
				: $set->label();
		}

		return $list;
	}

	private function optionField( $key ) {
		return $this->config->optionName() . '[' . $key . ']';
	}
}
