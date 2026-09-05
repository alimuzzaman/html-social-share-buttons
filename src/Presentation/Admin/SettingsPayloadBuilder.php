<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin;

use Alimuzzaman\HtmlSocialShareButtons\Application\Settings\SettingsStateStore;
use Alimuzzaman\HtmlSocialShareButtons\Application\Settings\SettingsCodec;
use Alimuzzaman\HtmlSocialShareButtons\Bootstrap\PluginConfig;
use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSetSelectionPolicy;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\ButtonAppearance;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\NetworkRegistry;

final class SettingsPayloadBuilder {
	private $settings;
	private $content;
	private $iconSets;
	private $networks;
	private $pluginFile;
	private $config;
	private $codec;

	public function __construct(
		SettingsStateStore $settings,
		ExcludedContentLookup $content,
		IconSetPayloadBuilder $iconSets,
		NetworkRegistry $networks,
		$pluginFile,
		PluginConfig $config,
		SettingsCodec $codec
	) {
		$this->settings = $settings;
		$this->content = $content;
		$this->iconSets = $iconSets;
		$this->networks = $networks;
		$this->pluginFile = (string) $pluginFile;
		$this->config = $config;
		$this->codec = $codec;
	}

	public function build() {
		$stored = $this->settings->readStored( null );
		$loaded = $this->settings->load();
		$options = $this->codec->encode( $loaded, is_array( $stored ) ? $stored : array() );
		// The codec keeps sparse storage; the form needs explicit effective values.
		$options['iconset_type'] = $loaded->defaultIconShape();
		$options['button_appearance'] = $loaded->buttonAppearance();
		$options['icons'] = $loaded->networkStates();
		$options['profile_links'] = $loaded->profileLinks();
		$options['auto_hide_btn'] = $loaded->autoHideEnabled();
		$options['nofollow'] = $loaded->noFollow();
		$options['show_for_current_user'] = $loaded->showForCurrentUser();
		$options['show_for_logged_in_user'] = $loaded->showForLoggedInUser();
		$options['show_for_logged_out_user'] = $loaded->showForLoggedOutUser();
		$excluded = $this->content->resolve( $options['excludes'] );

		return array(
			'ajax_url'                       => admin_url( 'admin-ajax.php' ),
			'nonce'                          => wp_create_nonce( $this->config->adminNonceAction() ),
			'assets_img'                     => plugins_url( 'assets/img', $this->pluginFile ),
			'iconsets'                       => $this->iconSets->settingsPayload( $options['iconset'] ),
			'options'                        => $options,
			'share_template_defaults'        => $this->defaultTemplates(),
			'share_template_preview_samples' => ShareTemplatePreview::samples(),
			'share_template_overrides'       => isset( $options['share_templates'] ) && is_array( $options['share_templates'] )
				? $options['share_templates']
				: array(),
			'exclude_items'                  => $excluded['items'],
			'exclude_custom'                 => $excluded['custom'],
			'defaultIconset'                 => IconSetSelectionPolicy::NEW_DEFAULT_ID,
			'button_appearances'             => $this->buttonAppearances(),
			'strings'                        => $this->interfaceStrings(),
		);
	}

	private function defaultTemplates() {
		$templates = array();
		foreach ( $this->networks->all() as $network ) {
			$templates[ $network->id() ] = $network->defaultShareTemplate();
		}

		return $templates;
	}

	private function buttonAppearances() {
		return array(
			array(
				'value' => ButtonAppearance::LEGACY,
				'label' => __( 'Legacy (current)', 'html-social-share-buttons' ),
				'help'  => __( 'Keep the current size, spacing, and hover behavior for the selected icon set.', 'html-social-share-buttons' ),
			),
			array(
				'value' => ButtonAppearance::MINIMAL,
				'label' => __( 'Minimal (recommended)', 'html-social-share-buttons' ),
				'help'  => __( 'Use consistent 44-pixel targets, balanced spacing, and a subtle hover lift.', 'html-social-share-buttons' ),
			),
			array(
				'value' => ButtonAppearance::FRAMED,
				'label' => __( 'Framed', 'html-social-share-buttons' ),
				'help'  => __( 'Add a clean, shape-aware outline around each button.', 'html-social-share-buttons' ),
			),
			array(
				'value' => ButtonAppearance::SOFT_SHADOW,
				'label' => __( 'Soft shadow', 'html-social-share-buttons' ),
				'help'  => __( 'Place each icon on a quiet raised surface with a subtle shadow.', 'html-social-share-buttons' ),
			),
		);
	}

	private function interfaceStrings() {
		return array(
			'loading'                     => __( 'Loading settings...', 'html-social-share-buttons' ),
			'saving'                      => __( 'Saving...', 'html-social-share-buttons' ),
			'saved'                       => __( 'Settings saved.', 'html-social-share-buttons' ),
			'saveError'                   => __( 'Settings could not be saved. Try again.', 'html-social-share-buttons' ),
			'saveChanges'                 => __( 'Save Changes', 'html-social-share-buttons' ),
			'postTitle'                   => __( 'Post title', 'html-social-share-buttons' ),
			'postTitleDescription'        => __( 'The title of the shared post', 'html-social-share-buttons' ),
			'permalink'                   => __( 'Permalink', 'html-social-share-buttons' ),
			'permalinkDescription'        => __( 'The canonical post URL', 'html-social-share-buttons' ),
			'featuredImageUrl'            => __( 'Featured image URL', 'html-social-share-buttons' ),
			'featuredImageDescription'    => __( "The post's featured image", 'html-social-share-buttons' ),
			'defaultTitle'                => __( 'Share this with your friends', 'html-social-share-buttons' ),
			'enabled'                     => __( 'Enabled', 'html-social-share-buttons' ),
			'disabled'                    => __( 'Disabled', 'html-social-share-buttons' ),
			'buttonShape'                 => __( 'Button shape', 'html-social-share-buttons' ),
			'profileLinks'                => __( 'Profile links', 'html-social-share-buttons' ),
			'profileLinksInherit'         => __( 'Show configured profile links', 'html-social-share-buttons' ),
			'profileLinksNone'            => __( 'Hide profile links in this placement', 'html-social-share-buttons' ),
			'header'                      => __( 'Header', 'html-social-share-buttons' ),
			'headerDescription'           => __( 'Set the text shown with the share buttons and choose pages where buttons should stay hidden.', 'html-social-share-buttons' ),
			'enterTitle'                  => __( 'Enter a title', 'html-social-share-buttons' ),
			'excludeContent'              => __( 'Exclude pages or posts', 'html-social-share-buttons' ),
			'excludeHelp'                 => __( 'Search published pages and posts, or press Enter to add a custom value.', 'html-social-share-buttons' ),
			'excludePlaceholder'          => __( 'Search pages, posts, or add a custom value', 'html-social-share-buttons' ),
			'appearance'                  => __( 'Appearance', 'html-social-share-buttons' ),
			'appearanceDescription'       => __( 'Choose the icon set and how the buttons are presented on your site. Appearance applies everywhere the plugin renders buttons.', 'html-social-share-buttons' ),
			'iconSet'                     => __( 'Icon set', 'html-social-share-buttons' ),
			'buttonAppearance'            => __( 'Button appearance', 'html-social-share-buttons' ),
			'preview'                     => __( 'Preview', 'html-social-share-buttons' ),
			'previewSample'               => __( 'Preview sample', 'html-social-share-buttons' ),
			'previewHelp'                 => __( 'Uses sample content without saving or opening a share service. Live URLs may differ if another plugin customizes sharing.', 'html-social-share-buttons' ),
			'previewSampleContent'        => __( 'Sample content', 'html-social-share-buttons' ),
			'previewLoading'              => __( 'Preparing preview…', 'html-social-share-buttons' ),
			'previewError'                => __( 'Preview could not be loaded. Try again. Your settings have not changed.', 'html-social-share-buttons' ),
			'previewDefault'              => __( 'Resolved URL using the default template:', 'html-social-share-buttons' ),
			'previewResolved'             => __( 'Resolved URL:', 'html-social-share-buttons' ),
			'previewEmpty'                => __( 'This template does not produce a usable link.', 'html-social-share-buttons' ),
			'postDescription'             => __( 'Post description', 'html-social-share-buttons' ),
			'postDescriptionHelp'         => __( 'The shared page description', 'html-social-share-buttons' ),
			'displayPlacement'            => __( 'Display placement', 'html-social-share-buttons' ),
			'displayPlacementDescription' => __( 'Turn each placement on or off and pick its shape.', 'html-social-share-buttons' ),
			'leftSide'                    => __( 'Left side', 'html-social-share-buttons' ),
			'leftSideDescription'         => __( 'A vertical rail on the left edge of the screen.', 'html-social-share-buttons' ),
			'beforePost'                  => __( 'Before post', 'html-social-share-buttons' ),
			'beforePostDescription'       => __( 'A row of buttons placed above post content.', 'html-social-share-buttons' ),
			'rightSide'                   => __( 'Right side', 'html-social-share-buttons' ),
			'rightSideDescription'        => __( 'A vertical rail on the right edge of the screen.', 'html-social-share-buttons' ),
			'afterPost'                   => __( 'After post', 'html-social-share-buttons' ),
			'afterPostDescription'        => __( 'A row of buttons placed below post content.', 'html-social-share-buttons' ),
			'socialNetworks'              => __( 'Social Networks', 'html-social-share-buttons' ),
			'socialNetworksDescription'   => __( 'Select the share buttons that should be available in the output.', 'html-social-share-buttons' ),
			'shareByEmail'                => __( 'Share the current page by email.', 'html-social-share-buttons' ),
			/* translators: %s is the social network name. */
			'shareOnNetwork'              => __( 'Share the current page on %s.', 'html-social-share-buttons' ),
			/* translators: %s is the social network name. */
			'shareTemplate'               => __( '%s share template', 'html-social-share-buttons' ),
			'restoreDefaults'             => __( 'Restore defaults', 'html-social-share-buttons' ),
			'shareUrl'                    => __( 'Share URL', 'html-social-share-buttons' ),
			'shareParameters'             => __( 'Share parameters', 'html-social-share-buttons' ),
			'parameterNamesManaged'       => __( 'Parameter names are managed automatically', 'html-social-share-buttons' ),
			'parameter'                   => __( 'Parameter', 'html-social-share-buttons' ),
			/* translators: 1: social network name, 2: parameter name. */
			'parameterValueLabel'         => __( '%1$s %2$s value', 'html-social-share-buttons' ),
			'customTemplateSaved'         => __( 'Custom template saved for this platform.', 'html-social-share-buttons' ),
			'canonicalTemplateUsed'       => __( 'Using the canonical template shown as the placeholder.', 'html-social-share-buttons' ),
			'socialProfileLinks'          => __( 'Social profile links', 'html-social-share-buttons' ),
			'profileLinksDescription'     => __( 'Add direct profile or contact destinations beside the share buttons. Leave a field empty to hide it.', 'html-social-share-buttons' ),
			'emailDestination'            => __( 'Email destination', 'html-social-share-buttons' ),
			/* translators: %s is the social network name. */
			'profileUrl'                  => __( '%s profile URL', 'html-social-share-buttons' ),
			'emailDestinationHelp'        => __( 'Use one mailto: address without subject or body parameters.', 'html-social-share-buttons' ),
			'httpsLinksOnly'              => __( 'HTTPS links only.', 'html-social-share-buttons' ),
			'audience'                    => __( 'Audience', 'html-social-share-buttons' ),
			'audienceDescription'         => __( 'Choose which visitors can see share buttons across automatic placements, blocks, shortcodes, widgets, and builder integrations.', 'html-social-share-buttons' ),
			'currentUser'                 => __( 'Current user (content author)', 'html-social-share-buttons' ),
			'loggedInUser'                => __( 'Other logged-in users', 'html-social-share-buttons' ),
			'loggedOutUser'               => __( 'Logged-out users', 'html-social-share-buttons' ),
			'advancedOptions'             => __( 'Advanced options', 'html-social-share-buttons' ),
			'advancedOptionsDescription'  => __( 'Fine tune behavior and link output.', 'html-social-share-buttons' ),
			'analyticsRetired'            => __( 'Google Social analytics has been retired. The plugin no longer adds tracking scripts.', 'html-social-share-buttons' ),
			'autoHide'                    => __( 'Auto hide button', 'html-social-share-buttons' ),
			'canonicalUrls'               => __( 'Sharing uses WordPress canonical URLs, including any configured port.', 'html-social-share-buttons' ),
			'noFollow'                    => __( 'No follow social link', 'html-social-share-buttons' ),
			'codeGenerator'               => __( 'Code generator', 'html-social-share-buttons' ),
			'codeGeneratorDescription'    => __( 'Generate embed code from the same icon set and selected networks.', 'html-social-share-buttons' ),
			'getPhpCode'                  => __( 'Get PHP Code', 'html-social-share-buttons' ),
			'getShortcode'                => __( 'Get Shortcode', 'html-social-share-buttons' ),
			'close'                       => __( 'Close', 'html-social-share-buttons' ),
			'insertPlaceholder'           => __( 'Insert share parameter placeholder', 'html-social-share-buttons' ),
		);
	}
}
