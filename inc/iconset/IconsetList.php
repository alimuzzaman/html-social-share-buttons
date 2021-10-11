<?php
namespace HSSB\Iconset;

class IconsetList
{
    private $iconsets = [];
    
    /**
     * Undocumented function
     *
     * @param Iconset $iconset
     * @return void
     */
	public function push_iconset($iconset){
		$this->iconsets[$iconset->id]	= $iconset;
	}
    
    /**
     * Undocumented function
     *
     * @return Icon[]
     */
	public function get_iconsets(){
		return $this->iconsets;
	}
	
}
