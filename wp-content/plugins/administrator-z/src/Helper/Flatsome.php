<?php
namespace Adminz\Helper;

class Flatsome {
	public $adminz_theme_locations = [];
	function __construct() {
        $this->default_menu();
        $this->logo_mobile();
        $this->blog_shortcode();
	}

    function blog_shortcode(){
		// default template taxonomy
		add_action( 'pre_get_posts', function ($query) {
			if ( !is_archive() ) return;
			if (
				// nếu là shortcode blog_posts của flatsome
				isset( $query->query_vars['post_type'] ) and
				$query->query_vars['post_type'] == [ 'post', 'featured_item' ] and
				isset( $query->query_vars['orderby'] ) and
				$query->query_vars['orderby'] == 'post__in'
			) {
				$query->set( 'post_type', array_merge( [ get_post_type() ], $query->get( 'post_type' ) ) );
			}
		} );

		// blog image size
		add_filter( 'post_thumbnail_size', function ($size) {
			if ( is_admin() && is_main_query() ) {
				return $size;
			}
			return 'large';
		}, 10, 1 );
    }

    function default_menu(){
		$this->adminz_theme_locations = [ 
			'desktop' => [ 
				'additional-menu' => 'Additional Menu',
				'another-menu'    => 'Another Menu',
				'extra-menu'      => 'Extra Menu',
			],
			'sidebar' => [ 
				'additional-menu-sidebar' => 'Additional Menu - Sidebar',
				'another-menu-sidebar'    => 'Another Menu - Sidebar',
				'extra-menu-sidebar'      => 'Extra Menu - Sidebar',
			],

		];
		$this->create_adminz_header_element();
		$this->adminz_register_my_menus();
    }

	function create_adminz_header_element() {
		add_filter( 'flatsome_header_element', [ $this, 'adminz_register_header_element' ] );
		add_action( 'flatsome_header_elements', [ $this, 'adminz_do_header_element' ] );
	}

	function adminz_register_my_menus() {
		foreach ( $this->adminz_theme_locations as $key => $value ) {
			register_nav_menus( $value );
		}
	}

	function adminz_register_header_element( $arr ) {
		foreach ( $this->adminz_theme_locations as $navtype => $navgroup ) {
			foreach ( $navgroup as $key => $value ) {
				$arr[ $key ] = $value;
			}
		}
		return $arr;
	}

	function adminz_do_header_element( $slug ) {
		foreach ( $this->adminz_theme_locations as $navtype => $navgroup ) {
			foreach ( $navgroup as $key => $value ) {
				$walker = 'FlatsomeNavDropdown';
				if ( $navtype == 'sidebar' ) $walker = 'FlatsomeNavSidebar';

				if ( $slug == $key ) {
					flatsome_header_nav( $key, $walker );
				}
			}

		}
	}

    function logo_mobile(){
		/*1.add zalo top header top*/
		add_action( 'customize_register', function ($wp_customize) {
			$wp_customize->add_setting(
				'adminz_logo_mobile_max_width', array( 'default' => '' )
			);
			$wp_customize->add_control( 'adminz_logo_mobile_max_width', array(
				'label'   => __( 'Adminz Logo max width (px)', 'administrator-z' ),
				'section' => 'header_mobile',
			) );
		} );
		add_action( 'wp_footer', function () {
			if ( $maxwidth = get_theme_mod( 'adminz_logo_mobile_max_width' ) ) {
				?>
						<style type="text/css">
							@media only screen and (max-width: 48em) {
								#logo{
									max-width: <?php echo esc_attr( $maxwidth ) ?>px;
								}
							}
						</style>
						<?php
			}
		} );
    }
}