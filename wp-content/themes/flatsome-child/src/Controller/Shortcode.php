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
        $this->override_testimonial();
        $this->bta_testimonial();
    }

    function bta_testimonial(){
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
            [adminz_slider_custom slide_width="50%" slide_width__sm="100%" slide_item_padding="20px" slide_align="left" hide_nav="true" nav_pos="outside" nav_color="dark" bullets="false"]
                <?php
                    $options = get_field( 'testimonials', 'option' );
                    foreach ((array)$options as $key => $single) {
                        ?>
                        [adminz_slider_custom_item_wrap]
                            [testimonial 
                                stars="<?= $single['star'] ?>"
                                image="<?= $single['avatar'] ?>" 
                                image_width="121" 
                                name="<?= $single['name'] ?>" 
                                company="<?= $single['position'] ?>"
                                ]
                            <p><?= $single['text'] ?></p>
                            [/testimonial]
                        [/adminz_slider_custom_item_wrap]
                        <?php
                    }
				?>
            [/adminz_slider_custom]

            <?php
            return do_shortcode( ob_get_clean() );
		};
		$___->general_element();
    }

    function override_testimonial(){
        add_action('init', function(){
            remove_shortcode('testimonial');
            add_shortcode('testimonial', function($atts, $content = null){
                global $flatsome_opt;
                $sliderrandomid = rand();
                extract(shortcode_atts(array(
                    'name' => '',
                    'class' => '',
                    'visibility' => '',
                    'company' => '',
                    'stars' => '5',
                    'font_size' => '',
                    'text_align' => '',
                    'image'  => '',
                    'image_width' => '80',
                    'pos' => 'left',
                    'link' => '',
                ), $atts));
                ob_start();

                $classes = array('testimonial-box');
                $classes_img = array('icon-box-img','testimonial-image','circle');
                
                $classes[] = 'icon-box-'.$pos;
                if ( $class ) $classes[] = $class;
                if ( $visibility ) $classes[] = $visibility;

                if($pos == 'center') $classes[] = 'text-center';
                if($pos == 'left' || $pos == 'top') $classes[] = 'text-left';
                if($pos == 'right') $classes[] = 'text-right';
                if($font_size) $classes[] = 'is-'.$font_size;
                if($image_width) $image_width = 'width: '.intval($image_width).'px';

                    $star_row = '';
                    if ($stars == '1'){$star_row = '<div class="star-rating"><span style="width:25%"><strong class="rating"></strong></span></div>';}
                    else if ($stars == '2'){$star_row = '<div class="star-rating"><span style="width:35%"><strong class="rating"></strong></span></div>';}
                    else if ($stars == '3'){$star_row = '<div class="star-rating"><span style="width:55%"><strong class="rating"></strong></span></div>';}
                    else if ($stars == '4'){$star_row = '<div class="star-rating"><span style="width:75%"><strong class="rating"></strong></span></div>';}
                    else if ($stars == '5'){$star_row = '<div class="star-rating"><span style="width:100%"><strong class="rating"></strong></span></div>';}

                $classes = implode(" ", $classes);
                $classes_img = implode(" ", $classes_img);
                ?>
                <div class="icon-box <?php echo esc_attr( $classes ); ?>">

                    <!-- text -->
                    <div class="icon-box-text p-last-0">

                        <!-- star --> 
                        <?php if($stars > 0) echo $star_row; ?>

                        <!-- content -->
                        <div class="testimonial-text line-height-small italic test_text first-reset last-reset is-italic">
                            <?php echo do_shortcode( $content ); ?>
                        </div>
                    </div>
                    <div class="flex">
                            <!-- image -->
                        <?php if ( $image ) { ?>
							<div class="<?php echo esc_attr( $classes_img ); ?>" style="<?php if ( $image_width ) echo $image_width; ?>">
								<?php echo flatsome_get_image( $image, $size = 'thumbnail', $alt = $name ); ?>
							</div>
						<?php } ?>
					
						<!-- testimonial -->
						<div class="testimonial-meta pt-half">
							<strong class="testimonial-name test_name primary-color is-larger"><?php echo wp_kses_post( $name ); ?></strong>
                            </br>
							<span class="testimonial-company test_company"><?php echo wp_kses_post( $company ); ?></span>
                        </div>
                    </div>
                </div>

                <?php
                $content = ob_get_contents();
                ob_end_clean();
                return $content;
            });
        });
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