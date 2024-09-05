<?php 
namespace Adminz\Helper;

class TinyMce{
	function __construct() {
		$arr = [
			'table'
		];

		add_filter("mce_buttons", function ($buttons) use ($arr){
	        array_push(
	        	$buttons,
	            "alignjustify",
	            "subscript",
	            "superscript"
	        );
	        foreach ($arr as $key) {
	        	array_push(
		        	$buttons,
		        	$key
		        );
	        }
	        // default: wp-includes/class-wp-editor.php
	        // see more https://www.tiny.cloud/docs-3x/reference/buttons/
	        return $buttons;
	    },99999);

		add_filter( 'mce_external_plugins', function ( $plugins ) use ($arr) {
			foreach ($arr as $key) {
	        	$plugins[$key] = ADMINZ_DIR_URL . 'includes/tinymce-plugins/'.$key.'/plugin.min.js';	
	        }
	      	return $plugins;
		});
	}
	
}