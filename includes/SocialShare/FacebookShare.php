<?php

namespace HSSB\SocialShare;

class FacebookShare implements SocialShareInterface {
    public function share($url, $title) {
        return 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($url);
    }

    public function linkToPage($username) {
        return 'https://www.facebook.com/' . $username;
    }
}