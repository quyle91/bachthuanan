<?php

add_filter( 'show_admin_bar', '__return_false' );

/**
 * Add Angular app attribute to html tag.
 *
 * @param  string $output
 * @return string
 */
add_filter( 'language_attributes', function ( $output ) {
  return $output . ' ng-app="uxBuilder" ng-strict-di';
} );

/**
 * Add ux-builder class and Angular attributes to body element.
 *
 * @param  array $classes
 * @return array
 */
add_filter( 'body_class', function ( $classes ) {
  $classes[] = 'ux-builder-iframe';
  return $classes;
} );

/**
 * Overwrite content for a specified post.
 *
 * @param  string $content
 * @param  number $post_id
 * @param  string $context
 * @return string
 */
add_filter( 'post_content', function ( $content, $post_id, $context ) {
  if ( array_key_exists( 'edit_post_id', $_GET ) && $_GET['edit_post_id'] == $post_id ) {
    return '<post-wrapper></post-wrapper>';
  }
  return $content;
}, 10, 3 );

/**
 * Override meta data with custom values from the url.
 * This makes it possible to change page tempalte etc. in the builder.
 *
 * @param  string  $value
 * @param  number  $object_id
 * @param  string  $meta_key
 * @param  boolean $single
 */
add_filter( 'get_post_metadata', function ( $value, $object_id, $meta_key, $single ) {
  $post_id = null;

  if ( array_key_exists( 'post_id', $_GET) ) $post_id = intval( $_GET['post_id'] );
  if ( array_key_exists( 'edit_post_id', $_GET) ) $post_id = intval( $_GET['edit_post_id'] );

  if ( array_key_exists( $meta_key, $_GET ) && $post_id == $object_id ) {
      return sanitize_text_field( $_GET[$meta_key] );
  }

  return $value;
}, 10, 4 );

/**
 * Change post content to prevent shortcodes
 * beeing rendered before the builder content.
 *
 * @param  WP_Post[] $posts
 * @param  WP_Query  $query
 * @return WP_Post[]
 */
add_action( 'the_posts', function ( $posts, $query ) {
  // Do nothing if another post is beeing edited or is not the main query.
  if ( array_key_exists( 'edit_post_id', $_GET ) || ! $query->is_main_query() ) {
    return $posts;
  }
  // Get current post if no posts are found. Happens when editing a draft.
  if ( empty ( $posts ) && ( array_key_exists( 'page_id', $_GET ) || array_key_exists( 'p', $_GET ) ) ) {
    $posts[] = get_post( isset( $_GET['page_id'] ) ? $_GET['page_id'] : $_GET['p'] );
  }
  // Check if this post is beeing edited in builder.
  if ( count( $posts ) == 1 && $posts[0]->ID == $_GET['post_id'] ) {
    // Change post content to an Angular component.
    $posts[0]->post_content = '<post-wrapper></post-wrapper>';
    // Wrap post excerpt to let the excerpt option change its content when changed.
    $posts[0]->post_excerpt = '<div class="uxb-post-excerpt">' . $posts[0]->post_excerpt . '</div>';
  }
  return $posts;
}, 10, 2 );

/**
 * Add assets to the iframe.
 */
add_action( 'ux_builder_enqueue_scripts', function ( $context ) {
  $version = UX_BUILDER_VERSION;
  wp_enqueue_style( 'dashicons' );
  wp_enqueue_style( 'ux-builder-core', ux_builder_asset( 'css/builder/core/content.css' ), null, $version );
  flatsome_enqueue_asset( 'ux-builder-vendors', 'builder/vendors/vendors', array( 'underscore', 'jquery-ui-sortable' ) );
  flatsome_enqueue_asset( 'ux-builder-core', 'builder/core/content', array( 'underscore' ) );
}, 0 );

/**
 * Add assets to the editor.
 */
add_action( 'wp_enqueue_scripts', function () {
  do_action( 'ux_builder_enqueue_scripts', 'content' );
}, 5 );



/**
 * Add the tools components to footer.
 */
add_action( 'wp_print_footer_scripts', function () {
  echo '<app-tools></app-tools>';
} );

/**
 * Don’t redirect to cart when empty cart.
 */
add_filter( 'woocommerce_checkout_redirect_empty_cart', '__return_false', 999 );
