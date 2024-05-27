<?php
namespace HSSB;

class LocationManager {
    private $plugin;

    public function __construct(SocialSharePlugin $plugin) {
        $this->plugin = $plugin;

        // Add the hooks
        add_filter('the_content', [$this, 'addContent']);
        add_action('wp_footer', [$this, 'addFooter']);
    }

    public function getCurrentPageUrl() {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

    public function addContent($content) {
        $show_in = $this->plugin->getOption('show_in', []);
        $url = $this->getCurrentPageUrl();
        $title = $this->plugin->getOption('title', '');

        if (isset($show_in['show_before_post']) && $show_in['show_before_post'] == 'square') {
            $content = $this->plugin->generateShareButtons($url, $title) . $content;
        }

        if (isset($show_in['show_after_post']) && $show_in['show_after_post'] == 'square') {
            $content .= $this->plugin->generateShareButtons($url, $title);
        }

        return $content;
    }

    public function addFooter() {
        $show_in = $this->plugin->getOption('show_in', []);
        $url = $this->getCurrentPageUrl();
        $title = $this->plugin->getOption('title', '');

        if (isset($show_in['show_left']) && $show_in['show_left'] == 'square') {
            echo $this->plugin->generateShareButtons($url, $title);
        }

        if (isset($show_in['show_right']) && $show_in['show_right'] == 'square') {
            echo $this->plugin->generateShareButtons($url, $title);
        }
    }
}
?>