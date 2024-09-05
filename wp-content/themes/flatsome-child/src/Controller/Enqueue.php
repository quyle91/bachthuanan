<?php
namespace Project\Controller;

class Enqueue {
	private static $instance = null;
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	function __construct() {
		add_action('wp_enqueue_scripts', [$this, 'wp_enqueue_scripts']);
	}

	function wp_enqueue_scripts(){

		wp_register_style( 'bta-css-inline', '', );
		wp_enqueue_style( 'bta-css-inline' );

		wp_add_inline_style(
			'bta-css-inline', 
			'
				:root{
					--text-tim-kiem: "'.__('Tìm kiếm', 'bta').'";
				}
			'
		);

		wp_enqueue_style(
			'bta-css',
			BTA_DIR_URL . "/assets/css/bta.css",
			[],
			BTA_VER,
			'all'
		);
	}
}