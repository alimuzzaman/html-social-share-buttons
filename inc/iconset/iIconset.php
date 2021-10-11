<?php
namespace HSSB;


interface iIconset{
	/*
	Set the icon set dir and iconset url.
	*/
	// function set_dir_and_url($__FILE__);
	/*
	Add icon to current iconset
	*/
	public function push_icon($icon);

	public function get_icons();

	public function get_icons_id_name();
    
	/*
	Get full url for iconset preview image
	*/
	public function get_iconset_preview();

}