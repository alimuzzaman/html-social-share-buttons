<?php
namespace HtmlSocialShare\Admin;

use HtmlSocialShare\IconRegistry;

class IconsController
{
    private $registry;

    public function __construct(IconRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function index(): string
    {
        $icons = $this->registry->listIcons();
        $out = "<ul>";
        foreach ($icons as $i) {
            $out .= '<li>' . htmlspecialchars($i, ENT_QUOTES, 'UTF-8') . '</li>';
        }
        $out .= "</ul>";
        return $out;
    }
}
