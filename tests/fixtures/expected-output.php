<?php
/**
 * Expected HTML Output Fixtures
 * 
 * This file contains expected HTML output patterns for testing
 * that the new implementation produces identical output to current version
 */

namespace HtmlSocialShare\Tests\Fixtures;

class ExpectedOutput {
    
    /**
     * Expected HTML for left placement
     */
    public static function left_placement() {
        return '<div class="zmshbt left default square">' . "\n" .
               '<a class="facebook" target="_blank" href="http://www.facebook.com/sharer.php?u=https://example.com&t=Example+Title"></a>' . "\n" .
               '<a class="twitter" target="_blank" href="http://twitter.com/share?url=https://example.com&text=Example+Title"></a>' . "\n" .
               '</div>';
    }
    
    /**
     * Expected HTML for right placement
     */
    public static function right_placement() {
        return '<div class="zmshbt right default square">' . "\n" .
               '<a class="facebook" target="_blank" href="http://www.facebook.com/sharer.php?u=https://example.com&t=Example+Title"></a>' . "\n" .
               '<a class="twitter" target="_blank" href="http://twitter.com/share?url=https://example.com&text=Example+Title"></a>' . "\n" .
               '</div>';
    }
    
    /**
     * Expected HTML for before_post placement
     */
    public static function before_post_placement() {
        return '<div class="zmshbt in_shortcode default square">' . "\n" .
               '<a class="facebook" target="_blank" href="http://www.facebook.com/sharer.php?u=https://example.com&t=Example+Title"></a>' . "\n" .
               '<a class="twitter" target="_blank" href="http://twitter.com/share?url=https://example.com&text=Example+Title"></a>' . "\n" .
               '</div>';
    }
    
    /**
     * Expected HTML for after_post placement
     */
    public static function after_post_placement() {
        return '<div class="zmshbt in_shortcode default square">' . "\n" .
               '<a class="facebook" target="_blank" href="http://www.facebook.com/sharer.php?u=https://example.com&t=Example+Title"></a>' . "\n" .
               '<a class="twitter" target="_blank" href="http://twitter.com/share?url=https://example.com&text=Example+Title"></a>' . "\n" .
               '</div>';
    }
    
    /**
     * Expected HTML for widget placement
     */
    public static function widget_placement() {
        return '<div class="zmshbt in_widget default square">' . "\n" .
               '<a class="facebook" target="_blank" href="http://www.facebook.com/sharer.php?u=https://example.com&t=Example+Title"></a>' . "\n" .
               '<a class="twitter" target="_blank" href="http://twitter.com/share?url=https://example.com&text=Example+Title"></a>' . "\n" .
               '</div>';
    }
    
    /**
     * Expected HTML for shortcode
     */
    public static function shortcode() {
        return '<div class="zmshbt in_shortcode default square">' . "\n" .
               '<a class="facebook" target="_blank" href="http://www.facebook.com/sharer.php?u=https://example.com&t=Example+Title"></a>' . "\n" .
               '<a class="twitter" target="_blank" href="http://twitter.com/share?url=https://example.com&text=Example+Title"></a>' . "\n" .
               '</div>';
    }
    
    /**
     * Expected HTML with nofollow attribute
     */
    public static function with_nofollow() {
        return '<div class="zmshbt in_shortcode default square">' . "\n" .
               '<a class="facebook" target="_blank" rel="nofollow" href="http://www.facebook.com/sharer.php?u=https://example.com&t=Example+Title"></a>' . "\n" .
               '<a class="twitter" target="_blank" rel="nofollow" href="http://twitter.com/share?url=https://example.com&text=Example+Title"></a>' . "\n" .
               '</div>';
    }
    
    /**
     * Expected CSS for default iconset
     */
    public static function default_iconset_css() {
        return '.zmshbt.default.square a.facebook { background-image: url("assets/iconset/default_square/facebook.png"); }' . "\n" .
               '.zmshbt.default.square a.twitter { background-image: url("assets/iconset/default_square/twitter.png"); }';
    }
    
    /**
     * All supported networks and their URL templates
     */
    public static function network_url_templates() {
        return [
            'facebook' => 'http://www.facebook.com/sharer.php?u=%%permalink%%&t=%%title%%',
            'twitter' => 'http://twitter.com/share?url=%%permalink%%&text=%%title%%',
            'linkedin' => 'http://www.linkedin.com/shareArticle?url=%%permalink%%&title=%%title%%',
            'pinterest' => 'http://pinterest.com/pin/create/button/?url=%%permalink%%&description=%%title%%&media=%%image%%',
            'googlepluse' => 'https://plus.google.com/share?url=%%permalink%%',
            'mail' => 'mailto:?subject=%%title%%&body=%%permalink%%',
        ];
    }
    
    /**
     * Available iconsets and types
     */
    public static function iconset_combinations() {
        return [
            'default' => ['square', 'circle'],
            'flat' => ['square', 'circle'],
            'long_shadow' => ['square', 'circle'],
            'prajin' => ['square', 'circle'],
        ];
    }
}
