<?php
namespace Project\Controller;

class Woocommerce {
	private static $instance = null;
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	function __construct() {
        $this->remove_hooks();
        $this->add_title_filter_shop();
        $this->shortcode();
        $this->cat_item_thumbnail();
        $this->add_header_product_image();
	}

    function add_header_product_image(){
		add_action( 'template_redirect', function(){
			if ( is_woocommerce() and is_single()) {
				add_action( 'flatsome_before_breadcrumb', function () {
                    // kiểm tra có phải breadcrumb trong maincontent ko
                    if(did_action( 'woocommerce_before_main_content' )){
					    ob_start();
					}
				} );
				add_action( 'flatsome_after_breadcrumb', function () {
                    // kiểm tra có phải breadcrumb trong maincontent ko
                    if(did_action( 'woocommerce_before_main_content' )){
					    ob_get_clean();
                    }
				} );
			}
        }, 99 );

		add_action('flatsome_product_title', function(){
            if ( is_product() ) {
                ob_start();
                ?>
                <div class="strong shop-page-title is-xlarge" style="margin-bottom: 0.5em;">
                    <?= __('Sản phẩm chi tiết', 'bta') ?>
                </div>
                <?php
                echo ob_get_clean();
            }
        });

		add_action( 'flatsome_after_header', function(){
			if ( is_product() ) {
				wc_get_template_part( 'single-product/headers/header-product', 'featured-center' );
			}
        }, 10 );

        add_action('wp_head', function(){
            if ( is_product() ) {
                ?>
                <style type="text/css">
                    .shop-page-title.featured-title .title-bg{
                        background-image: url(<?php echo get_theme_mod( 'header_shop_bg_image' ); ?>) !important;
                    }
                </style>
                <?php
            }
        },101);
    }

    function cat_item_thumbnail(){
        add_filter('list_product_cats', function($cat_name, $cat){
            if($thumbnail_id = get_term_meta($cat->term_id, 'thumbnail_id', true)){
                $cat_name = implode(" ", [ 
					wp_get_attachment_image( $thumbnail_id, 'icon', false, [ 'class' => 'bta_cat_thumb', 'width' => '30px', 'height' => '30px' ] ),
                    $cat_name
                ]);
            }
            return $cat_name;
        },10,2);
    }

    function shortcode(){
        add_shortcode('bta_gia', function(){
            ob_start();

            // ?orderby=price-desc
            // ?orderby=price
            // ?

			global $wp;
            $current_url = home_url( add_query_arg( array(), $wp->request ) );
            if ( !empty( $_SERVER['QUERY_STRING'] ) ) {
                $current_url .= '?' . $_SERVER['QUERY_STRING'];
            }

            $list = [
                [
                    'text' => __("Tất cả", 'bta'),
                    'link' => remove_query_arg( 'orderby', $current_url ),
                    'checked' => (!isset($_GET['orderby'])),
                ],
                [
                    'text' => __("Thấp - cao", 'bta'),
                    'link' => add_query_arg( ['orderby'=> 'price'], $current_url ),
                    'checked' => ( $_GET['orderby'] ?? false ) == 'price',
                ],
                [
                    'text' => __("Cao - thấp", 'bta'),
                    'link' => add_query_arg( [ 'orderby' => 'price-desc' ], $current_url ),
                    'checked' => ( $_GET['orderby'] ?? false ) == 'price-desc',
                ],
            ];

            ?>
            <div class="bta_gia">
                <?php 
                    foreach ((array)$list as $key => $value) {
                        ?>
                        <label for="<?= esc_attr($key) ?>">
                            <a href="<?= esc_url($value['link']); ?>">
                                <input type="checkbox" <?= $value['checked'] ? "checked" : ""; ?>>
                                <?= esc_attr($value['text']) ?>
                            </a>
                        </label>
                        <?php
                    }
                ?>
                <script type="text/javascript">
                    document.addEventListener('DOMContentLoaded',function(){
                        const bta_gia = document.querySelector(".bta_gia");
                        const inputs = bta_gia.querySelectorAll('input');
                        inputs.forEach(input => {
                            input.addEventListener('change', function(){
                                const link = input.closest('label').querySelector("a");
                                link.click();
                            });
                        });
                    });
                </script>
            </div>

            <?php
            return ob_get_clean();
        });
    }

    function remove_hooks(){
        add_action('init', function(){
		    remove_action( 'flatsome_category_title_alt', 'flatsome_woocommerce_catalog_ordering', 30 );
		});
    }

    function remove_flatsome_category_title(){
        add_action('init', function(){
        });
    }

    function add_title_filter_shop(){
		add_action( 'woocommerce_before_shop_loop', function(){
            ob_start();
            ?>
            <div class="row bta_woocommerce_before_shop_loop row-small">
                <div class="col small-6 large-6">
                    <?php 
                    ob_start();
                    woocommerce_result_count();
                    echo str_replace('hide-for-medium', '', ob_get_clean());
                    ?>
                </div>
                <div class="col small-6 large-6">
                    <?php woocommerce_catalog_ordering() ?>
                </div>
            </div>
            <?php
            echo do_shortcode(ob_get_clean());
        } );
    }
}