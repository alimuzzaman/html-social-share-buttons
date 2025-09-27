<?php
namespace HtmlSocialShare\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ShareButtonsWidget extends Widget_Base {
    public function get_name() {
        return 'html_social_share_buttons';
    }

    public function get_title() {
        return __( 'HTML Social Share Buttons', 'html-social-share' );
    }

    public function get_icon() {
        return 'eicon-share';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    protected function _register_controls() {
        // Controls will be added in later tasks
    }

    protected function render() {
        // Output rendering will be implemented in a later task
        echo '<div class="html-social-share-buttons">[Share buttons will render here]</div>';
    }
}
