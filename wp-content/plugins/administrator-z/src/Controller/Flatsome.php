<?php
namespace Adminz\Controller;

final class Flatsome {
	private static $instance = null;
	public $option_group = 'group_adminz_flatsome';
	public $option_name = 'adminz_flatsome';

	public $settings = [];

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	function __construct() {
		add_filter( 'adminz_option_page_nav', [ $this, 'add_admin_nav' ], 10, 1 );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		$this->load_settings();
		$this->after_setup_theme();
	}

	function after_setup_theme(){
		// 
		remove_action( 'admin_notices', 'flatsome_status_check_admin_notice' );
		remove_action( 'admin_notices', 'flatsome_maintenance_admin_notice' );

		// 
		new \Adminz\Helper\Flatsome();

		// 
		foreach ( glob( ADMINZ_DIR . '/includes/shortcodes/flatsome-*.php' ) as $filename ) {
			require_once $filename;
		}

		// 
		if ( ( $this->settings['adminz_page_banner'] ?? "" ) == "on" ) {
			new \Adminz\Helper\FlatsomeAcfBanner;
		}

		// 
		if ( is_user_logged_in() ) {
			if ( ( $_GET['testhook'] ?? '' ) == 'flatsome' ) {
				$hooks = require_once ( ADMINZ_DIR . "includes/file/flatsome_hooks.php" );
				foreach ( $hooks as $hook ) {
					add_action( $hook, function () use ($hook) {
						echo do_shortcode( '[adminz_test content="' . $hook . '"]' );
					} );
				}
			}
		}

		// 
		if ( $hooks = ( $this->settings['adminz_flatsome_action_hook'] ?? "" ) ) {
			foreach ( $hooks as $hook => $shortcode ) {
				if ( $shortcode ) {
					add_action( $hook, function () use ($shortcode) {
						echo do_shortcode( $shortcode );
					} );
				}
			}
		}

		// 
		if ( ( $this->settings['adminz_flatsome_portfolio_custom'] ?? "" ) == "on" ) {
			$args = [ 
				'portfolio_name'        => $this->settings['adminz_flatsome_portfolio_name'] ?? "",
				'portfolio_category'    => $this->settings['adminz_flatsome_portfolio_category'] ?? "",
				'portfolio_tag'         => $this->settings['adminz_flatsome_portfolio_tag'] ?? "",
				'portfolio_product_tax' => $this->settings['adminz_flatsome_portfolio_product_tax'] ?? "",
			];
			new \Adminz\Helper\FlatsomePortfolio( $args );
		}

		// 
		if ( $post_type_support = ($this->settings['post_type_support'] ?? "") ) {
			foreach ( $post_type_support as $post_type ) {
				if($post_type){
					$xxx            = new \Adminz\Helper\FlatsomeUxBuilder;
					$xxx->post_type = $post_type;
					$xxx->post_type_content_support();
				}
			}
		}

		// 
		if ( $post_type_template = ( $this->settings['post_type_template'] ?? "" ) ) {
			foreach ( $post_type_template as $post_type => $template ) {
				if ( $template ) {
					$xxx                    = new \Adminz\Helper\FlatsomeUxBuilder;
					$xxx->post_type         = $post_type;
					$xxx->template_block_id = $template;
					$xxx->post_type_layout_support();
				}
			}
		}

		// 
		if ( $taxonomy_layout_support = ( $this->settings['taxonomy_layout_support'] ?? "" ) ) {
			foreach ( $taxonomy_layout_support as $tax => $template ) {
				if ( $template ) {
					$xxx                        = new \Adminz\Helper\FlatsomeUxBuilder;
					$xxx->taxonomy              = $tax;
					$xxx->tax_template_block_id = $template;
					$xxx->taxonomy_layout_support();
				}
			}
		}

		// 
		add_action( 'wp_enqueue_scripts', function(){
			wp_enqueue_style( 
				'adminz_flatsome_fix', 
				ADMINZ_DIR_URL . "includes/file/flatsome_css_fix.php", 
				[],
				ADMINZ_VERSION, 
				'all'
			);
		});

		// 
		if ( $pack = ( $this->settings['adminz_choose_stylesheet'] ?? "" )) {
			add_filter('body_class', function($class)use($pack){
				$class[] = $pack;
				$class[] = apply_filters( 'adminz_pack1_enable_sidebar', true ) ? 'enable_sidebar_pack1' : "";
				$class[] = apply_filters( 'adminz_pack2_enable_sidebar', true ) ? 'enable_sidebar_pack2' : "";
				return $class;
			});

			add_action( 'wp_enqueue_scripts', function () use ($pack) {
				wp_enqueue_style( 
					'adminz_flatsome_css_'.$pack, 
					ADMINZ_DIR_URL."assets/css/pack/$pack.css", 
					[],
					ADMINZ_VERSION, 
					'all'
				);
			} );
		}

		// 
		if ( ($this->settings['adminz_use_mce_button'] ?? "" ) == "on" ) {
			if ( is_admin() ) {
				new \Adminz\Helper\TinyMce;
			}
		}

		// 
		if ( ( $this->settings['adminz_flatsome_viewport_meta'] ?? "" ) == "on" ) {
			add_filter( 'flatsome_viewport_meta',function (){ return null;});
		}

		// 
		if ( ( $this->settings['adminz_flatsome_lightbox_close_btn_inside'] ?? "" ) == "on" ) {
			add_filter( 'flatsome_lightbox_close_btn_inside', function (){ return true;});
		}

		// 
		if ( ( $this->settings['adminz_enable_zalo_support'] ?? "" ) == "on" ) {
			adminz_enable_zalo_support();
		}

		// 
		if ( $pages = ($this->settings['page_for_transparent'] ?? "") ) {
			add_filter( 'body_class', function ($classes) use ($pages) {
				$classes[] = in_array( get_the_ID(), $pages ) ? 'page_for_transparent' : "";
				return $classes;
			} );
		}

		// 
		if ( $this->settings['adminz_mobile_verticalbox'] ?? "") {
			add_action('wp_enqueue_scripts', function(){
				wp_enqueue_style(
					'adminz_vertical_box', 
					ADMINZ_DIR_URL."/assets/css/flatsome/vertical-box.css", 
					[], 
					ADMINZ_VERSION, 
					'all'
				);
			});
		}
	}

	function load_settings() {
		$this->settings = get_option( $this->option_name, [] );
	}

	function add_admin_nav( $nav ) {
		$nav[ $this->option_group ] = 'Flatsome';
		return $nav;
	}

	function register_settings() {
		register_setting( $this->option_group, $this->option_name );

		// add section
		add_settings_section(
			'adminz_flatsome_config',
			'Flatsome config',
			function () {},
			$this->option_group
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Css pack',
			function () {
				$options = [ '' => '-- Select --' ];
				foreach ( glob( ADMINZ_DIR . 'assets/css/pack/*.css' ) as $filename ) {
					$_key           = str_replace( ".css", "", basename( $filename ) );
					$_value         = basename( $filename );
					$options[ $_key ] = $_value;
				}
				echo adminz_form_field( [ 
					'field'     => 'select',
					'attribute' => [ 
						'name' => $this->option_name . "[adminz_choose_stylesheet]"
					],
					'options'   => $options,
					'selected'  => $this->settings['adminz_choose_stylesheet'] ?? "",
				] );
			},
			$this->option_group,
			'adminz_flatsome_config'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Tiny MCE editor',
			function () {
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'    => 'checkbox',
						'name'    => $this->option_name . '[adminz_use_mce_button]',
						'checked' => ( $this->settings['adminz_use_mce_button'] ?? "" ) == "on"
					],
					'label'     => "adminz_use_mce_button",
				] );
			},
			$this->option_group,
			'adminz_flatsome_config'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'ACF Page banner',
			function () {
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'    => 'checkbox',
						'name'    => $this->option_name . '[adminz_page_banner]',
						'checked' => ( $this->settings['adminz_page_banner'] ?? "" ) == "on"
					],
					'label'     => "adminz_page_banner",
				] );
			},
			$this->option_group,
			'adminz_flatsome_config'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Disable Meta viewport',
			function () {
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'    => 'checkbox',
						'name'    => $this->option_name . '[adminz_flatsome_viewport_meta]',
						'checked' => ( $this->settings['adminz_flatsome_viewport_meta'] ?? "" ) == "on"
					],
					'label'     => "adminz_flatsome_viewport_meta",
				] );
			},
			$this->option_group,
			'adminz_flatsome_config'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Lightbox close button inside',
			function () {
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'    => 'checkbox',
						'name'    => $this->option_name . '[adminz_flatsome_lightbox_close_btn_inside]',
						'checked' => ( $this->settings['adminz_flatsome_lightbox_close_btn_inside'] ?? "" ) == "on"
					],
					'label'     => "adminz_flatsome_lightbox_close_btn_inside",
				] );
			},
			$this->option_group,
			'adminz_flatsome_config'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Enable Zalo, Skype, Whatsapp icon support',
			function () {
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'    => 'checkbox',
						'name'    => $this->option_name . '[adminz_enable_zalo_support]',
						'checked' => ( $this->settings['adminz_enable_zalo_support'] ?? "" ) == "on"
					],
					'note'      => "Add new builder with zalo follow icon",
				] );
			},
			$this->option_group,
			'adminz_flatsome_config'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Hide Header main on scroll - Desktop',
			function () {
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'    => 'checkbox',
						'name'    => $this->option_name . '[adminz_hide_headermain_on_scroll]',
						'checked' => ( $this->settings['adminz_hide_headermain_on_scroll'] ?? "" ) == "on"
					],
					'note'      => "Fix sticky header bottom fixed scroll.",
				] );
			},
			$this->option_group,
			'adminz_flatsome_config'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Mobile vertical box',
			function () {
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'    => 'checkbox',
						'name'    => $this->option_name . '[adminz_mobile_verticalbox]',
						'checked' => ( $this->settings['adminz_mobile_verticalbox'] ?? "" ) == "on"
					],
					'note'      => "Fix mobile layout to vertical box",
				] );
			},
			$this->option_group,
			'adminz_flatsome_config'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Set a page to transparent header - Desktop',
			function () {
				foreach ( get_pages() as $key => $page ) {
					// field
					echo adminz_form_field( [ 
						'field'     => 'input',
						'attribute' => [ 
							'type'    => 'checkbox',
							'name'    => $this->option_name . '[page_for_transparent][]',
							'checked' => in_array( $page->ID, ( $this->settings['page_for_transparent'] ?? [] ) ),
							'value'   => $page->ID,
						],
						'label'     => $page->post_title,
						'before'    => '<div class="adminz_grid_item">',
						'after'     => '</div>',
					] );
				}
				?>
					<p>
						<small>
							Replace default functionality of flatsome.
						</small>
					</p>
				<?php
			},
			$this->option_group,
			'adminz_flatsome_config'
		);

		// add section
		add_settings_section(
			'adminz_flatsome_portfolio',
			'Portfolio',
			function () {},
			$this->option_group
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Enable',
			function () {
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'    => 'checkbox',
						'name'    => $this->option_name . '[adminz_flatsome_portfolio_custom]',
						'checked' => ($this->settings['adminz_flatsome_portfolio_custom'] ?? "" ) == "on",
						// 'value'   => $post_type,
					],
					'copy' => 'adminz_flatsome_portfolio_custom'
				] );
			},
			$this->option_group,
			'adminz_flatsome_portfolio'
		);
		

		// field 
		add_settings_field(
			wp_rand(),
			'Portfolio rename',
			function () {
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'  => 'text',
						'name'  => $this->option_name . '[adminz_flatsome_portfolio_name]',
						'value' => $this->settings['adminz_flatsome_portfolio_name'] ?? "",
					],
					'note'      => 'First you can try with Customize->Portfolio->Custom portfolio page <a href="https://www.youtube.com/watch?v=3cl6XCUjOPI">Link</a>',
					'copy'      => 'adminz_flatsome_portfolio_name'
				] );
			},
			$this->option_group,
			'adminz_flatsome_portfolio'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Portfolio Categories rename',
			function () {
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'  => 'text',
						'name'  => $this->option_name . '[adminz_flatsome_portfolio_category]',
						'value' => $this->settings['adminz_flatsome_portfolio_category'] ?? "",
					],
					'copy'      => 'adminz_flatsome_portfolio_category'
				] );
			},
			$this->option_group,
			'adminz_flatsome_portfolio'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Portfolio Tags rename',
			function () {
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'  => 'text',
						'name'  => $this->option_name . '[adminz_flatsome_portfolio_tag]',
						'value' => $this->settings['adminz_flatsome_portfolio_tag'] ?? "",
					],
					'copy'      => 'adminz_flatsome_portfolio_tag'
				] );
			},
			$this->option_group,
			'adminz_flatsome_portfolio'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Sync portfolio with product',
			function () {
				$options = [ '' => '-- Select --' ];
				foreach ( get_object_taxonomies( 'product', 'objects' ) as $key => $value ) {
					$options[ $key ] = $value->label;
				}
				// field
				echo adminz_form_field( [ 
					'field'     => 'select',
					'attribute' => [ 
						'name' => $this->option_name . "[adminz_flatsome_portfolio_product_tax]"
					],
					'options'   => $options,
					'selected'  => $this->settings['adminz_flatsome_portfolio_product_tax'] ?? "",
					'copy'      => 'adminz_flatsome_portfolio_product_tax'
				] );
			},
			$this->option_group,
			'adminz_flatsome_portfolio'
		);

		// add section
		add_settings_section(
			'adminz_flatsome_ux_build',
			'UX builder',
			function () {},
			$this->option_group
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Post type content',
			function () {
				foreach ( get_post_types() as $key => $post_type) {
					echo adminz_form_field( [ 
						'field'     => 'input',
						'attribute' => [ 
							'type'    => 'checkbox',
							'name'    => $this->option_name . '[post_type_support][]',
							'checked' => in_array($post_type, ( $this->settings['post_type_support'] ?? [] )),
							'value'	=> $post_type,
						],
						'label'     => $post_type,
						'before' => '<div class="adminz_grid_item">',
						'after' => '</div>'
					] );
				}
				?>
				<p>
					<small>
						Looking for: Remove the post's default <strong>sidebar</strong>? | 
						Let's create a <strong>block</strong> valued: <?= adminz_copy('[adminz_post_field post_field="post_content"][/adminz_post_field]') ?> | 
						Then set that block to the post type layout in <strong>Uxbuilder Layout Support</strong><br>
					</small>
				</p>
				<?php 
			},
			$this->option_group,
			'adminz_flatsome_ux_build'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Post type layout',
			function () {
				// blocks
				$blocks_arr = [];
				$args       = [ 
					'post_type'      => 'blocks',
					'post_status'    => 'publish',
					'posts_per_page' => -1
				];
				$the_query = new \WP_Query( $args );
				if ( $the_query->have_posts() ) :
					while ( $the_query->have_posts() ) :
						$the_query->the_post();
						$blocks_arr["block_id_".get_the_ID()] = "Block: ".get_the_title();
					endwhile;
				endif;
				wp_reset_postdata();

				foreach ( get_post_types() as $key => $post_type ) {
					// terms
					$terms = [];
					$taxonomies = get_object_taxonomies( $post_type );
					if(!empty($taxonomies) and is_array($taxonomies)){
						foreach ($taxonomies as $index => $_tax) {
							$_value = "taxonomy_" . $_tax;
							$_name = "Terms of: $_tax";
							$terms[$_value] = $_name;
						}
					}
					$options = ['' => '-- Select --'] + $blocks_arr + $terms;

					// field
					echo adminz_form_field( [ 
						'field'     => 'select',
						'attribute' => [ 
							'name' => $this->option_name . "[post_type_template][$post_type]"
						],
						'options'   => $options,
						'selected'  => $this->settings['post_type_template'][ $post_type ] ?? "",
						'before'    => '<div class="adminz_grid_item"><span class="adminz_grid_item_label">'. $post_type.'</span>',
						'after'     => '</div>',
					] );
				}
			},
			$this->option_group,
			'adminz_flatsome_ux_build'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Taxonomy layout',
			function () {
				// blocks
				$blocks_arr = [];
				$args       = [ 
					'post_type'      => 'blocks',
					'post_status'    => 'publish',
					'posts_per_page' => -1
				];
				$the_query  = new \WP_Query( $args );
				if ( $the_query->have_posts() ) :
					while ( $the_query->have_posts() ) :
						$the_query->the_post();
						$blocks_arr[ "block_id_" . get_the_ID()] = "Block: " . get_the_title();
					endwhile;
				endif;
				wp_reset_postdata();

				foreach ( get_taxonomies() as $key => $taxonomy) {
					$options = [ '' => '-- Select --' ] + $blocks_arr;
					// field
					echo adminz_form_field( [ 
						'field'     => 'select',
						'attribute' => [ 
							'name' => $this->option_name . "[taxonomy_layout_support][$taxonomy]"
						],
						'options'   => $options,
						'selected'  => $this->settings['taxonomy_layout_support'][ $taxonomy ] ?? "",
						'before'    => '<div class="adminz_grid_item"><span class="adminz_grid_item_label">'.$taxonomy.'</span>',
						'after'     => '</div>',
					] );
				}
				?>
				<p>
					<small>
						Looking for: posts grid?. Use element: <strong>Taxonomy Posts</strong>
					</small>
				</p>
				<?php
			},
			$this->option_group,
			'adminz_flatsome_ux_build'
		);

		// add section
		add_settings_section(
			'adminz_flatsome_hooks',
			'Flatsome template hooks',
			function () { },
			$this->option_group
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Test flatsome hooks',
			function () {
				echo adminz_copy(add_query_arg( [ 'testhook' => 'flatsome',], get_site_url() ));
			},
			$this->option_group,
			'adminz_flatsome_hooks'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Use hooks',
			function () {
				?>
				<p>
					Use: <?= adminz_copy('[adminz_test]')?>
				</p>
				<?php
				$flatsome_action_hooks = require(ADMINZ_DIR."includes/file/flatsome_hooks.php");
				foreach ($flatsome_action_hooks as $hook) {
					// field
					echo adminz_form_field( [ 
						'field'     => 'input',
						'attribute' => [ 
							'type'        => 'text',
							'name'        => $this->option_name . "[adminz_flatsome_action_hook][$hook]",
							'value'       => $this->settings['adminz_flatsome_action_hook'][$hook] ?? "",
						],
						'before'    => '<div class="adminz_grid_item_big"><span class="adminz_grid_item_label">' . $hook . '</span>',
						'after' => '</div>'
					] );
				}
			},
			$this->option_group,
			'adminz_flatsome_hooks'
		);

		// add section
		add_settings_section(
			'adminz_flatsome_css',
			'Miscellaneous',
			function () {},
			$this->option_group
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Cheatsheet',
			function () {
				?>
				<table class="form-table">
	        	<?php
					$classcheatsheet = require ( ADMINZ_DIR . 'includes/file/flatsome_css_classes.php' );
					foreach ( $classcheatsheet as $key => $value ) {
						?>
						<tr valign="top">
							<th><?php echo esc_attr( $key ); ?></th>
							<td>
								<?php foreach ( $value as $classes ) {
										foreach ( $classes as $class ) {
											echo "<small class='adminz_click_to_copy' data-text='$class'>$class</small>";
										}
									} ?>
							</td>
						</tr>
						<?php
					}
					?>
				</table>
				<?php
			},
			$this->option_group,
			'adminz_flatsome_css'
		);
	}
}