<?php

namespace HSSB;

use HSSB\SocialShare\FacebookShare;
use HSSB\SocialShare\TwitterShare;
use HSSB\SocialShare\LinkedInShare;
// Use other social media classes here...

class SocialSharePlugin {
    private $socialMediaClasses = [];

    public function __construct() {
        $this->socialMediaClasses = [
            'facebook' => new FacebookShare(),
            'twitter' => new TwitterShare(),
            'linkedin' => new LinkedInShare(),
            // Instantiate other social media classes here...
        ];


        new LocationManager($this);
    }

    public function generateShareButtons($url, $title) {
        // The selected social media platforms
        $selectedPlatforms = $this->getOption('platforms', []);
        $icon_set          = $this->getOption('icon_set', '');

        $buttons = '';
        foreach ($selectedPlatforms as $platform) {
            // Check if the platform is supported
            if (isset($this->socialMediaClasses[$platform])) {
                $buttons .= '<a href="' . $this->socialMediaClasses[$platform]->share($url, $title) . '">';
                $buttons .= '<img src="' . plugins_url('icons/' . $icon_set . '/' . $platform . '.png', __FILE__) . '" alt="' . ucfirst($platform) . '">';
                $buttons .= '</a>';
            }
        }
        return $buttons;
    }

    public function generatePageLinks($username) {
        // The selected social media platforms
        $selectedPlatforms = $this->getOption('platforms', []);

        $links = '';
        foreach ($selectedPlatforms as $platform) {
            // Check if the platform is supported
            if (isset($this->socialMediaClasses[$platform])) {
                $links .= '<a href="' . $this->socialMediaClasses[$platform]->linkToPage($username) . '">Visit ' . ucfirst($platform) . ' Page</a>';
            }
        }
        return $links;
    }

    public function getOption($key = '', $default = null) {
        $defaults = [
            'title'         => 'Share this with your friends',
            'excludes'      => '',
            'g_analytics'   => true,
            'auto_hide_btn' => true,
            'use_port'      => true,
            'nofollow'      => true,
            'iconset'       => 'flat',
            'show_in'       => [
                'show_left'        => 'square',
                'show_right'       => 'square',
                'show_before_post' => 'square',
                'show_after_post'  => 'square',
            ],
            'show_left'        => 'square',
            'show_right'       => 'square',
            'show_before_post' => 'square',
            'show_after_post'  => 'square',
            'icons'            => [
                'facebook'  => '1',
                'twitter'   => '1',
                'linkedin'  => '1',
                'bookmark'  => '1',
                'pinterest' => '1',
                'mail'      => '1',
            ],
        ];

        $value = get_option('hssb_settings', []);
        $value = wp_parse_args($value, $defaults);
        if($key) {
            return isset($value[$key]) ? $value[$key] : $default;
        }
        return $value;
    }


    // Add other methods here...
}