<?php
namespace HSSB;


abstract class Iconset implements iIconset{
	public		$id;
	public		$name;
	public		$types;
	private		$icons;
	private     $preview_img	= "preview.png";

	
	function __construct(){
        
		
	}

    /**
     * Undocumented function
     *
     * @param Icon $icon
     * @return void
     */
	public function push_icon($icon){
		$this->icons[$icon->id]	= $icon;
	}
	
    /**
     * Undocumented function
     *
     * @return Icon[]
     */
	public function get_icons(){
		return $this->icons;
	}
	
	public function get_icons_id_name(){
		$new	= array();
		foreach( $this->icons as $id => $icon)
			$new[$id]	= $icon->name;
		return $new;
	}

	public function get_iconset_preview(){
		return $this->preview_img;
	}
	
	
}