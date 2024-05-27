<?php

namespace HSSB;

class PluginManager {
    /**
     * @var SocialSharePlugin
     */
    private $socialSharePlugin;

    /**
     * Initialize the plugin
     */
    public function init() {
        // Initialize the SocialSharePlugin
        $this->socialSharePlugin = new SocialSharePlugin();
        // Register activation and deactivation hooks
        register_activation_hook(HSSB_PLUGIN_DIR . 'html-social-share-buttons.php', [$this, 'activate']);
        register_deactivation_hook(HSSB_PLUGIN_DIR . 'html-social-share-buttons.php', [$this, 'deactivate']);

        // Load the plugin's text domain for internationalization
        add_action('plugins_loaded', [$this, 'load_textdomain']);

        // Add other actions and filters here...
    }

    /**
     * Activate the plugin
     */
    public function activate() {
        $stored_version = get_option('hssb_version');

        if (false === $stored_version) {
            // The plugin is being activated for the first time
            // Activation code here...
        } elseif (version_compare($stored_version, HSSB_VERSION, '<')) {
            // The plugin is being upgraded
            // Upgrade code here...
        }

        // Update the version in the options table
        update_option('hssb_version', HSSB_VERSION);
    }

    /**
     * Deactivate the plugin
     */
    public function deactivate() {
        // Deactivation code here...
    }

    /**
     * Load the plugin's text domain
     */
    public function load_textdomain() {
        load_plugin_textdomain('html-social-share-buttons', false, HSSB_PLUGIN_DIR . '/languages/');
    }

    // Add other methods here...
}