<?php 

define( 'BTA_DIR_URL', get_stylesheet_directory_uri());
define( 'BTA_DIR', get_stylesheet_directory());
define( 'BTA_VER', wp_get_theme()->get( 'Version' ) );


require __DIR__ ."/vendor/autoload.php";

$GLOBALS['bta'] = [
    'Enqueue' => \Project\Controller\Enqueue::get_instance(),
    'Flatsome' => \Project\Controller\Flatsome::get_instance(),
    'Woocommerce' => \Project\Controller\Woocommerce::get_instance(),
    'Shortcode' => \Project\Controller\Shortcode::get_instance(),
];

add_action( 'after_setup_theme', function () {
	load_child_theme_textdomain( 'childtheme_domain', get_stylesheet_directory() . '/languages' );
} );


// add_action('init', function(){
//     $args = [
//         'post_type' => ['product'],
//         'post_status' => ['publish'],
//         'posts_per_page' => -1,
//     ];
    
//     $__the_query = new \WP_Query( $args );
//     if ( $__the_query->have_posts() ) {
    
//         while ( $__the_query->have_posts() ) : $__the_query->the_post();
//             // echo '<pre>'.get_the_title().'</pre>';
//             wp_update_post(
//                 [
//                     // 'post_excerpt' => 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem.',
//                     'post_content' => 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem. ',
//                     'meta_input'  => [
//                         // '_product_image_gallery' => '39,38,41,24,37,26'
//                     ]
//                 ]
//             );
//         endwhile;
    
//         wp_reset_postdata();
    
//     }else{
//         echo __( 'Sorry, no posts matched your criteria.' );
//     }
//     die;
// });