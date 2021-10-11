<?php
namespace HSSB;

class Icon implements iIcon{
    public $id;
    public $name;
    public $class;
    public $image;
    public $url;

    public function __construct($args)
    {
        $this->id    = $args['id'];
        $this->name  = $args['name'];
        $this->class = $args['class'];
        $this->image = $args['image'];
        $this->url   = $args['url'];
    }

    public function get_url($permalink, $title)
    {
        return str_replace(['%%permalink%%', '%%title%%'], [$permalink, $title], $this->url);
    }
}