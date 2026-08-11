<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration\Widget;

use Alimuzzaman\HtmlSocialShareButtons\Presentation\Rendering\RenderFacade;
use Alimuzzaman\HtmlSocialShareButtons\Application\Settings\SettingsRepository;
use Alimuzzaman\HtmlSocialShareButtons\Bootstrap\PluginConfig;
use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSetRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\NetworkRegistry;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend\AssetCollector;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration\BuilderLabels;

/**
 * The canonical implementation behind the historic widget public class.
 *
 * The public class name is supplied by the legacy API bridge. This class owns
 * neither that bridge nor any global state, so it is also safe to use from a
 * future namespaced widget registration.
 */
class ShareWidget extends \WP_Widget {
	private $renderer;
	private $settings;
	private $iconSets;
	private $networks;
	private $assets;
	private $config;

	public function __construct(
		RenderFacade $renderer,
		SettingsRepository $settings,
		IconSetRegistry $iconSets,
		NetworkRegistry $networks,
		AssetCollector $assets,
		PluginConfig $config
	) {
		$this->renderer = $renderer;
		$this->settings = $settings;
		$this->iconSets = $iconSets;
		$this->networks = $networks;
		$this->assets = $assets;
		$this->config = $config;

		parent::__construct(
			$this->config->widgetIdBase(),
			__( 'Html share button widget', 'html-social-share-buttons' ),
			array(
				'description' => __(
					"Html share button. It show lite share button only with html. It's not using any javascript whats anothers do.",
					'html-social-share-buttons'
				),
			)
		);
	}

	public function widget( $arguments, $instance ) {
		$instance = is_array( $instance ) ? $instance : array();
		$options = $this->options( $instance );
		$options['icons'] = $this->normalizeIconSelection( $options['icons'] );

		$beforeWidget = isset( $arguments['before_widget'] ) ? $arguments['before_widget'] : '';
		$afterWidget = isset( $arguments['after_widget'] ) ? $arguments['after_widget'] : '';
		$beforeTitle = isset( $arguments['before_title'] ) ? $arguments['before_title'] : '';
		$afterTitle = isset( $arguments['after_title'] ) ? $arguments['after_title'] : '';
		$title = apply_filters( 'widget_title', $options['title'] );

		echo wp_kses_post( $beforeWidget );
		if ( '' !== $title ) {
			echo wp_kses_post( $beforeTitle ) . esc_html( $title ) . wp_kses_post( $afterTitle );
		}

		if ( ! empty( $options['icons'] ) ) {
			$options['class'] = $this->config->widgetWrapperClass();
			$outcome = $this->renderer->render( $options );
			$this->assets->collect( $outcome );
			echo wp_kses_post( $outcome->html() );
		}

		echo wp_kses_post( $afterWidget );
	}

	public function update( $newInstance, $oldInstance ) {
		$newInstance = is_array( $newInstance ) ? $newInstance : array();

		return array(
			'title'        => isset( $newInstance['title'] )
				? sanitize_text_field( $this->scalar( $newInstance['title'], '' ) )
				: '',
			'icons'        => $this->normalizeIconSelection(
				isset( $newInstance['icons'] ) ? $newInstance['icons'] : array()
			),
			'iconset_type' => sanitize_key(
				$this->scalar( isset( $newInstance['iconset_type'] ) ? $newInstance['iconset_type'] : '', 'square' )
			),
			'iconset'      => sanitize_key(
				$this->scalar( isset( $newInstance['iconset'] ) ? $newInstance['iconset'] : '', 'default' )
			),
		);
	}

	public function form( $instance ) {
		$options = $this->options( is_array( $instance ) ? $instance : array() );
		$options['icons'] = $this->normalizeIconSelection( $options['icons'] );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Enter a Title', 'html-social-share-buttons' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $options['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'iconset' ) ); ?>"><?php esc_html_e( 'Select Button Style', 'html-social-share-buttons' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'iconset' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'iconset' ) ); ?>">
				<?php foreach ( $this->iconSets->all() as $iconSet ) : ?>
					<option value="<?php echo esc_attr( $iconSet->id() ); ?>" <?php selected( $options['iconset'], $iconSet->id() ); ?>><?php echo esc_html( BuilderLabels::iconSet( $iconSet->id(), $iconSet->label() ) ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'iconset_type' ) ); ?>"><?php esc_html_e( 'Select Type', 'html-social-share-buttons' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'iconset_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'iconset_type' ) ); ?>">
				<?php foreach ( $this->shapesFor( $options['iconset'] ) as $shape ) : ?>
					<option value="<?php echo esc_attr( $shape ); ?>" <?php selected( $options['iconset_type'], $shape ); ?>><?php echo esc_html( BuilderLabels::shape( $shape, ucfirst( $shape ) ) ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<fieldset>
			<legend><?php esc_html_e( 'Select Buttons', 'html-social-share-buttons' ); ?></legend>
			<?php foreach ( $this->networks->all() as $network ) : ?>
				<label>
					<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'icons' ) ); ?>[<?php echo esc_attr( $network->id() ); ?>]" value="1" <?php checked( isset( $options['icons'][ $network->id() ] ) ); ?> />
					<?php echo esc_html( BuilderLabels::network( $network->id(), $network->label() ) ); ?>
				</label><br />
			<?php endforeach; ?>
		</fieldset>
		<?php
	}

	private function options( array $instance ) {
		$settings = $this->settings->load();
		$defaults = array(
			'title'        => $settings->title(),
			'icons'        => $this->enabledNetworks( $settings->networkStates() ),
			'iconset_type' => $settings->defaultIconShape(),
			'iconset'      => $settings->iconSetId(),
		);
		$options = wp_parse_args( $instance, $defaults );
		$profileLinks = array_key_exists( 'profile_links', $instance )
			? ( is_array( $instance['profile_links'] ) ? $instance['profile_links'] : array() )
			: $settings->profileLinks();

		return array(
			'title'         => sanitize_text_field( $this->scalar( $options['title'], '' ) ),
			'icons'         => isset( $options['icons'] ) ? $options['icons'] : array(),
			'iconset_type'  => sanitize_key( $this->scalar( $options['iconset_type'], 'square' ) ),
			'iconset'       => sanitize_key( $this->scalar( $options['iconset'], 'default' ) ),
			'profile_links' => $profileLinks,
		);
	}

	private function enabledNetworks( array $states ) {
		$enabled = array();
		foreach ( $states as $networkId => $state ) {
			if ( $state && $this->networks->has( $networkId ) ) {
				$enabled[ $networkId ] = '1';
			}
		}

		return $enabled;
	}

	private function normalizeIconSelection( $icons ) {
		if ( ! is_array( $icons ) || empty( $icons ) ) {
			return array();
		}

		$keys = array_keys( $icons );
		$networkIds = range( 0, count( $keys ) - 1 ) === $keys ? $icons : $keys;
		$normalized = array();
		foreach ( $networkIds as $networkId ) {
			if ( ! is_scalar( $networkId ) ) {
				continue;
			}

			$networkId = sanitize_key( (string) $networkId );
			if ( 'twitter' === $networkId ) {
				$networkId = 'x';
			}
			if ( '' !== $networkId && $this->networks->has( $networkId ) ) {
				$normalized[ $networkId ] = '1';
			}
		}

		return $normalized;
	}

	private function shapesFor( $iconSetId ) {
		if ( $this->iconSets->has( $iconSetId ) ) {
			return $this->iconSets->get( $iconSetId )->shapes();
		}

		return array( 'square' );
	}

	private function scalar( $value, $fallback ) {
		return is_scalar( $value ) ? (string) $value : (string) $fallback;
	}
}
