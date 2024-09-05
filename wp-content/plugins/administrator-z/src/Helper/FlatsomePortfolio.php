<?php 
namespace Adminz\Helper;
class FlatsomePortfolio{

    public $args;

    function __construct($args) {
        $this->args = $args;

        // rename
		if ( $args['portfolio_name'] ?? "" ) {
			add_filter( 'featured_itemposttype_args', [ $this, 'change_featured' ], 10, 1 );
		}

        // category
		if ( $args['portfolio_category'] ?? "" ) {
			add_filter( 'featured_itemposttype_category_args', [ $this, 'change_featured_category' ], 10, 1 );
		}

        // tag
		if ( $args['portfolio_tag'] ?? "" ) {
			add_filter( 'featured_itemposttype_tag_args', [ $this, 'change_featured_tag' ], 10, 1 );
		}

        // sync
		if ( $args['portfolio_product_tax'] ?? "" ) {
            $sync = new \Adminz\Helper\TaxonomySync();
            $sync->taxname = $args['portfolio_product_tax'];
            $sync->post_type = 'featured_item';
            $sync->init();
		}

    }
    function change_featured($return){
        $customname = $this->args['portfolio_name'];
        $return['labels'] = [ 
            'name'               => $customname,
            'singular_name'      => $customname,
            'add_new'            => __( 'Add New', 'flatsome-admin' ),
            'add_new_item'       => __( 'Add New', 'flatsome-admin' ),
            'edit_item'          => 'Edit ' . $customname,
            'new_item'           => 'Add new ' . $customname,
            'view_item'          => 'View ' . $customname,
            'search_items'       => 'Search ' . $customname,
            'not_found'          => __( 'No items found', 'flatsome-admin' ),
            'not_found_in_trash' => __( 'No items found in trash', 'flatsome-admin' ),
        ]; 
        $return['rewrite'] = [ 
            'slug' => sanitize_title( $customname )
        ];
        return $return;
    }
    function change_featured_category($return){
        $customtag = $this->args['portfolio_category'];
        $return['labels'] = [ 
            'name'                       => $customtag,
            'singular_name'              => $customtag,
            'menu_name'                  => $customtag,
            'edit_item'                  => __( 'Edit Tag', 'flatsome-admin' ),
            'update_item'                => __( 'Update Tag', 'flatsome-admin' ),
            'add_new_item'               => __( 'Add New Tag', 'flatsome-admin' ),
            'new_item_name'              => __( 'New Tag Name', 'flatsome-admin' ),
            'parent_item'                => __( 'Parent Tag', 'flatsome-admin' ),
            'parent_item_colon'          => __( 'Parent Tag:', 'flatsome-admin' ),
            'all_items'                  => __( 'All Tags', 'flatsome-admin' ),
            'search_items'               => __( 'Search Tags', 'flatsome-admin' ),
            'popular_items'              => __( 'Popular Tags', 'flatsome-admin' ),
            'separate_items_with_commas' => __( 'Separate tags with commas', 'flatsome-admin' ),
            'add_or_remove_items'        => __( 'Add or remove tags', 'flatsome-admin' ),
            'choose_from_most_used'      => __( 'Choose from the most used tags', 'flatsome-admin' ),
            'not_found'                  => __( 'No tags found.', 'flatsome-admin' ),
        ];
        $return['rewrite'] = [ 
            'slug' => sanitize_title( $customtag )
        ];
        return $return;
    }
    function change_featured_tag( $return){
        $customcat = $this->args['portfolio_tag'];
        $return['labels']  = [ 
            'name'                       => $customcat,
            'singular_name'              => $customcat,
            'menu_name'                  => $customcat,
            'edit_item'                  => __( 'Edit Category', 'flatsome-admin' ),
            'update_item'                => __( 'Update Category', 'flatsome-admin' ),
            'add_new_item'               => __( 'Add New Category', 'flatsome-admin' ),
            'new_item_name'              => __( 'New Category Name', 'flatsome-admin' ),
            'parent_item'                => __( 'Parent Category', 'flatsome-admin' ),
            'parent_item_colon'          => __( 'Parent Category:', 'flatsome-admin' ),
            'all_items'                  => __( 'All Categories', 'flatsome-admin' ),
            'search_items'               => __( 'Search Categories', 'flatsome-admin' ),
            'popular_items'              => __( 'Popular Categories', 'flatsome-admin' ),
            'separate_items_with_commas' => __( 'Separate categories with commas', 'flatsome-admin' ),
            'add_or_remove_items'        => __( 'Add or remove categories', 'flatsome-admin' ),
            'choose_from_most_used'      => __( 'Choose from the most used categories', 'flatsome-admin' ),
            'not_found'                  => __( 'No categories found.', 'flatsome-admin' ),
        ];
        $return['rewrite'] = [ 
            'slug' => sanitize_title( $customcat )
        ];
        return $return;
    }
}