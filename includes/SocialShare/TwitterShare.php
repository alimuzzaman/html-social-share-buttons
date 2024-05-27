<?php

namespace HSSB\SocialShare;

class TwitterShare implements SocialShareInterface {
    public function linkToPage($url) {
        return 'https://twitter.com/intent/tweet?url=' . urlencode($url);
    }

    public function share($url, $title) {
        return 'https://twitter.com/intent/tweet?url=' . urlencode($url) . '&text=' . urlencode($title);
    }
}