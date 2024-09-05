<?php 

add_action( 'after_setup_theme', function () {
    load_child_theme_textdomain( 'childtheme_domain', get_stylesheet_directory() . '/languages' );
} );

require __DIR__ ."/vendor/autoload.php";
new \Project\Controller\Flatsome; // test
// new \Project\Controller\Test; // test