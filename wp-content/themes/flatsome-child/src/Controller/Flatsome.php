<?php
namespace Project\Controller;

class Flatsome {
	private static $instance = null;
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	function __construct() {
		add_action( 'after_switch_theme', function () {
			update_option( 'flatsome_wup_buyer', 'thaiduong103' );
			update_option( 'flatsome_wup_purchase_code', 'c173b5f9-c7a7-4f30-83be-90e22de44f0d' );
			update_option( 'flatsome_wup_sold_at', '2017-02-20T20:26:11+11:00' );
			update_option( 'flatsome_wup_supported_until', '2017-08-22T11:26:11+10:00' );
		} );
	}
}