<?php 

define( 'BTA_DIR_URL', get_stylesheet_directory_uri());
define( 'BTA_DIR', get_stylesheet_directory());
define( 'BTA_VER', wp_get_theme()->get( 'Version' ) );


require __DIR__ ."/vendor/autoload.php";

$GLOBALS['bta'] = [
    'Enqueue' => \Project\Controller\Enqueue::get_instance(),
    'Flatsome' => \Project\Controller\Flatsome::get_instance(),
    'Shortcode' => \Project\Controller\Shortcode::get_instance(),
];

add_action( 'after_setup_theme', function () {
	load_child_theme_textdomain( 'childtheme_domain', get_stylesheet_directory() . '/languages' );
} );