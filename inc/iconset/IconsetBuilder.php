<?php
namespace HSSB\Iconset;

class IconsetBuilder
{

    public function __construct()
    {
        $iconset_list = new IconsetList();
        $iconset_list->push_iconset(new Bordered);
        // ....
        // $iconset_list->push_iconset(new SomethingElse);

        print_r($iconset_list);
    }
}