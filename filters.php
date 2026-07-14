<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

new zm_sh_filters;
class zm_sh_filters{

	function __construct(){
		add_filter("zm_sh_placeholder", array($this, "zm_sh_placeholder"));
		add_filter("zm_sh_ico_link", array($this, "ico_link"));
	}

	function zm_sh_placeholder($item){
		$parmalink		= zm_sh_curentPageURL();
		$title			= $this->make_title($parmalink);
		$description	= get_bloginfo ( 'description' );
		$image_url		= $this->image_url($parmalink);
		$item 			= str_replace( "%%permalink%%",		rawurlencode((string) $parmalink),		$item);
		$item 			= str_replace( "%%title%%",			rawurlencode((string) $title),			$item);
		$item 			= str_replace( "%%description%%",	rawurlencode((string) $description),	$item);
		$item 			= str_replace( "%%imageurl%%",		rawurlencode((string) $image_url),		$item);
		return $item;
	}

	function ico_link($ico_link){


		return $ico_link;
	}

	function make_title($url){
		$home = get_home_url();
		if($home == $url or $home . "/" == $url){
			$title	= get_bloginfo ( 'name' );
		}
		elseif($postid = url_to_postid( $url )){
			$title 	= get_the_title( $postid );
		}
		else{
			$title 	= get_the_title( );
		}
		return apply_filters('zm_sh_title', $title);
	}

	function image_url($url) {
		global $post;
		if ( ! is_object( $post ) || empty( $post->ID ) ) return '';
		$thumb_id	= get_post_thumbnail_id($post->ID);
		$attachmetn_url	= wp_get_attachment_url( $thumb_id);
		$imageurl = $attachmetn_url;

		if(!$imageurl){
			$postid = url_to_postid( $url );
			$linked_post = $postid ? get_post( $postid, 'OBJECT' ) : null;
			if ( ! is_object( $linked_post ) ) return '';
			$content	= $linked_post->post_content;
			$content	= str_replace('zm_sh_btn', '', $content);
			$content	= do_shortcode($content);
			$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches);
			if(isset($matches[1][0]))
				$imageurl = $matches[1][0];
		}
		return $imageurl;
	}
}
