<?php

namespace HSSB\SocialShare;

class LinkedInShare implements SocialShareInterface {
    public function linkToPage($url) {
        // implementation of the 'linkToPage' method
    }

    public function share($url, $title) {
        return 'https://www.linkedin.com/shareArticle?mini=true&url=' . urlencode($url) . '&title=' . urlencode($title);
    }
}