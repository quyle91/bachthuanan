<?php 
namespace Project\Controller;

class Shortcode{
	private static $instance = null;
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

    function __construct(){
        $this->bta_san_pham_moi();
        $this->bta_san_pham_tieubieu();
        $this->bta_search();
    }

    function enable_search_category(){
        return true;
    }

    function bta_search(){
		$___                     = new \Adminz\Helper\FlatsomeELement;
		$___->shortcode_name     = __FUNCTION__;
		$___->shortcode_title    = __FUNCTION__;
		$___->shortcode_icon     = 'text';
		$___->options            = [
			//
		];
		$___->shortcode_callback = function ($atts, $content = null) {
			ob_start();
            $name = 'header_search_categories';
            add_filter("theme_mod_{$name}", [$this, 'enable_search_category']);
			?>
            [search]
            <?php
            return do_shortcode( ob_get_clean() );
		};
		$___->general_element();
    }

    function bta_san_pham_moi(){
        $___                     = new \Adminz\Helper\FlatsomeELement;
        $___->shortcode_name     = __FUNCTION__;
        $___->shortcode_title    = __FUNCTION__;
        $___->shortcode_icon     = 'text';
        $___->options            = [ 
            //
        ];
        $___->shortcode_callback = function ($atts, $content=null) {
			ob_start();
			?>
            <div class="bta_tabs">
                [tabgroup title="<?= __('Sản phẩm mới', 'bta') ?>" style="simple" nav="normal"]
                    [tab title="<?= __('Tất cả', 'bta') ?>"]
                        [ux_products type="row" products="4"]
                    [/tab]
                    <?php
                        $terms = get_terms( 
                            [ 
                                'taxonomy' => 'product_cat', 
                                'hide_empty' => true,
                                'parent' => 0,
                            ]
                        );
					    foreach ((array)$terms as $key => $term) {
                            ?>
                            [tab title="<?= $term->name ?>"]
                                [ux_products type="row" cat="<?= $term->term_id ?>" products="4"]
                            [/tab]
                            <?php
                        }
                    ?>
                [/tabgroup]
            </div>
            <?php
            return do_shortcode( ob_get_clean() );
        };
        $___->general_element();
    }

    function bta_san_pham_tieubieu(){
		$___                     = new \Adminz\Helper\FlatsomeELement;
		$___->shortcode_name     = __FUNCTION__;
		$___->shortcode_title    = __FUNCTION__;
		$___->shortcode_icon     = 'text';
		$___->options            = [
			//
		];
		$___->shortcode_callback = function ($atts, $content = null) {
			ob_start();
			?>
            <div class="bta_tabs">
                [tabgroup title="<?= __( 'Sản phẩm tiêu biểu', 'bta' ) ?>" style="simple" nav="normal"]
				    [tab title="<?= __( 'Tất cả', 'bta' ) ?>"]
                        [ux_products type="row" show="featured" products="8"]
                    [/tab]
                    <?php
                        $terms = get_terms( 
                            [ 
                                'taxonomy' => 'product_cat', 
                                'hide_empty' => true,
                                'parent' => 0,
                            ]
                        );
					    foreach ((array)$terms as $key => $term) {
                            ?>
                            [tab title="<?= $term->name ?>"]
                                [ux_products type="row" cat="<?= $term->term_id ?>" products="8" show="featured"]
                            [/tab]
                            <?php
                        }
                    ?>
                [/tabgroup]
            </div>
            <?php
            return do_shortcode( ob_get_clean() );
		};
		$___->general_element();
    }
}