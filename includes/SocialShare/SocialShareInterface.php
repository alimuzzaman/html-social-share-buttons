<?php

namespace HSSB\SocialShare;

interface SocialShareInterface {
    public function share($url, $title);
    public function linkToPage($username);
}