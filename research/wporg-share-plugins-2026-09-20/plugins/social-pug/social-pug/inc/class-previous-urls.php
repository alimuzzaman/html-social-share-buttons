<?php
namespace Mediavine\Grow;

use Mediavine\Grow\Settings;

class Previous_URLs {

	/** @var array Settings for the plugin that options are pulled from */
	private $settings;

	/** @var int $post_id ID of the post we are getting previous URLs for */
	private $post_id;

	/**
	 * @param int $post_id Post ID to get previous URLS for
	 * @param array $settings Settings for the plugin
	 */
	public function __construct( int $post_id, array $settings ) {
		$this->post_id  = $post_id;
		$this->settings = $settings;
	}

	/**
	 * Get all forms of previous urls for a post
	 *
	 * @return array|mixed
	 */
	public function get_previous_urls() {
		$previous_domain_structure_urls = $this->get_previous_domain( $this->get_previous_permalink_structure() );
		$previous_post_urls             = $this->get_previous_post_urls();

		$previous_urls = array_merge( $previous_domain_structure_urls, $previous_post_urls );

		/**
		 * Filter the previous URLs for the post before returning.
		 * @param array $previous_urls
		 * @param int   $post_id
		 */
		$previous_urls = apply_filters( 'dpsp_get_post_previous_urls', $previous_urls, $this->post_id );

		// Exclude the post's current URL if it's found in the post previous URLs array
		$current_url = get_permalink( $this->post_id );

		if ( in_array( $current_url, $previous_urls, true ) ) {
			unset( $previous_urls[ array_search( $current_url, $previous_urls, true ) ] );
			$previous_urls = array_values( $previous_urls );
		}

		// Update the post previous URLs
		update_post_meta( $this->post_id, 'dpsp_post_previous_urls', $previous_urls );

		return $previous_urls;
	}

	/**
	 * Get URLs for the post based on the site's previous permalink structure, this is from global site settings
	 *
	 * @param array $previous_urls Previous URLs
	 *
	 * @return array
	 */
	private function get_previous_permalink_structure( array $previous_urls = [] ) : array {
		if ( ! empty( $this->settings['previous_permalink_share_counts'] ) ) {
			// Get the predefined permalink structure
			$permalink = ( ! empty( $this->settings['previous_permalink_structure'] ) ? $this->settings['previous_permalink_structure'] : '' );

			// Get the custom permalink structure if is set
			if ( ! empty( $this->settings['previous_permalink_structure'] ) && 'custom' === $this->settings['previous_permalink_structure'] ) {
				$permalink = ( ! empty( $this->settings['previous_permalink_structure_custom'] ) ? $this->settings['previous_permalink_structure_custom'] : '' );
			}

			// Get the previous permalink and if it exists add it to the list
			$post_previous_permalink = dpsp_get_post_permalink( $this->post_id, $permalink );

			if ( false !== $post_previous_permalink ) {
				$previous_urls[] = $post_previous_permalink;
			}
		}

		return $previous_urls;
	}

	/**
	 * Get the post URL using a previous domain, this is from global site settings
	 *
	 * @param array $previous_urls
	 *
	 * @return array
	 */
	private function get_previous_domain( array $previous_urls = [] ) : array {
		if ( ! empty( $this->settings['previous_domain_share_counts'] ) ) {
			// Get site URL
			$site_url = get_site_url();

			// Get current base domain
			$base_domain = preg_replace( '#^www\.(.+\.)#i', '$1', parse_url( $site_url, PHP_URL_HOST ) );

			// Go through each URL and change the domain
			foreach ( $previous_urls as $previous_url ) {
				if ( empty( $this->settings['previous_base_domain'] ) ) {
					continue;
				}
				$previous_urls[] = str_replace( $base_domain, $this->settings['previous_base_domain'], $previous_url );
			}

			// If there aren't any previous permalinks, in the case of a domain change,
			// the current post's URL must be taken into account
			if ( empty( $previous_urls ) ) {
				$previous_urls[] = str_replace( $base_domain, $this->settings['previous_base_domain'], get_permalink( $this->post_id ) );
			}
		}
		return $previous_urls;
	}

	/**
	 * Get the manually set previous URLs for a post from the post meta, this is for an individual post
	 *
	 * @return array
	 */
	private function get_previous_post_urls() : array {
		$post_previous_urls = dpsp_maybe_convert_post_meta_to_json( $this->post_id, 'dpsp_post_single_previous_urls', true );
		return ( ! empty( $post_previous_urls ) ? $post_previous_urls : [] );
	}
}
