<?php
namespace Adminz\Controller;

final class Woocommerce {
	private static $instance = null;
	public $option_group = 'group_adminz_woocommerce';
	public $option_name = 'adminz_woocommerce';

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
		$this->plugin_loaded();
	}

	function plugin_loaded(){
		// tinh huyen xa
		if ( ( $this->settings['adminz_woocommerce_tinh_huyen_xa'] ?? "" ) == 'on' ) {
			new \Adminz\Helper\TinhHuyenXa();
		}

		// 
		if ( ( $this->settings['adminz_woocommerce_fix_notice_position'] ?? "" ) == 'on' ) {
			new \Adminz\Helper\WooMessage;
		}

		// 
		if ( ( $this->settings['adminz_woocommerce_discount_amount'] ?? "" ) == 'on' ) {
			$a = new \Adminz\Helper\WooOrdering();
			$a->setup_save_discount_data();
		}
		if ( !empty( $sort_ordering = ( $this->settings['sort_ordering'] ?? "" ) ) ) {
			$a = new \Adminz\Helper\WooOrdering();
			$a->setup_ordering( $sort_ordering );
		}

		// 
		if ( ( $this->settings['adminz_tooltip_products'] ?? "" ) == 'on' ) {
			new \Adminz\Helper\WooTooltip;
		}

		// 
		if ( ( $this->settings['variable_product_price_custom'] ?? "" ) == 'on' ) {
			$a = new \Adminz\Helper\WooVariation;
			$a->setup_hide_max_price();
		}

		// 
		if ( $text = ($this->settings['adminz_woocommerce_ajax_add_to_cart_text'] ?? "") ) {
			add_filter( 'body_class', function ($classes) {
				$classes[] = 'adminz_custom_add_to_cart_text';
				return $classes;
			}, 10, 1 );
			add_filter( 'woocommerce_product_add_to_cart_text', function () use ($text) {
				return $text;
			} );
			add_filter( 'woocommerce_product_single_add_to_cart_text', function () use ($text) {
				return $text;
			} );
			add_filter( 'woocommerce_product_text', function () use ($text) {
				return $text;
			} );
		}

		// 
		if ( $text = ($this->settings['adminz_woocommerce_empty_price_html'] ?? "") ) {
			add_action( 'woocommerce_single_product_summary', function () use ($text) {
				global $product;
				if(!$product->get_price()){
					echo do_shortcode( $text );
				}
			},21);
		}

		// 
		if ( ($this->settings['adminz_woocommerce_description_readmore'] ?? "") == 'on' ) {
			add_action( 'woocommerce_before_single_product', function () {
				// add class to compatity with adminz.js
				?>
				<script type="text/javascript">
					document.addEventListener('DOMContentLoaded',function(){
						document.querySelector('.woocommerce-Tabs-panel--description').classList.add('adminz_readmoreContent');
					});
				</script>
				<?php
			});
		}

		// Search-------------------
		add_filter( 'woocommerce_redirect_single_search_result', '__return_false' );
		add_filter( 'woocommerce_product_query_meta_query', function($meta_query){
			foreach ($_GET as $key => $value) {
				if(str_starts_with( $key, "meta_") and $value){
					$_key = str_replace('meta_', '', $key);
					if ( !isset( $meta_query['relation'] ) ) {
						$meta_query['relation'] = 'AND';
					}
					$meta_query[] = [ 
						'key'     => $_key,
						'compare' => 'EXISTS',
					];
					$meta_query[] = [ 
						'key'     => $_key,
						'compare' => 'IN',
						'value'   => $value,
					];
				}
			}
			return $meta_query;
		} );

		// 
		if ( is_user_logged_in() ) {
			if ( ( $_GET['testhook'] ?? '' ) == 'woocommerce' ) {
				$hooks = require_once ( ADMINZ_DIR . "includes/file/woocommerce_hooks.php" );
				foreach ( $hooks as $hook ) {
					add_action( $hook, function () use ($hook) {
						echo do_shortcode( '[adminz_test content="' . $hook . '"]' );
					} );
				}
			}
		}

		// 
		if ( $hooks = ( $this->settings['adminz_woocommerce_action_hook'] ?? "" ) ) {
			foreach ( $hooks as $hook => $shortcode ) {
				if ( $shortcode ) {
					add_action( $hook, function () use ($shortcode) {
						echo do_shortcode( $shortcode );
					} );
				}
			}
		}
	}

	function load_settings() {
		$this->settings = get_option( $this->option_name, [] );
	}

	function add_admin_nav( $nav ) {
		$nav[ $this->option_group ] = 'Woocommerce';
		return $nav;
	}

	function register_settings() {
		register_setting( $this->option_group, $this->option_name );

		// add section
		add_settings_section(
			'adminz_woocommerce_product_single',
			'Product single',
			function () {},
			$this->option_group
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Add to cart text',
			function () {
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'  => 'text',
						'name'  => $this->option_name . '[adminz_woocommerce_ajax_add_to_cart_text]',
						'value' => $this->settings['adminz_woocommerce_ajax_add_to_cart_text'] ?? "",
					],
					'copy'      => '[adminz_woocommerce_ajax_add_to_cart_text]',
				] );
			},
			$this->option_group,
			'adminz_woocommerce_product_single'
		);

		add_settings_field(
			wp_rand(),
			'Empty price html',
			function () {
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'        => 'text',
						'name'        => $this->option_name . '[adminz_woocommerce_empty_price_html]',
						'value'       => $this->settings['adminz_woocommerce_empty_price_html'] ?? "",
					],
					'copy'      => ['[adminz_woocommerce_empty_price_html]', '[button text="Call now!" icon="icon-phone" icon_pos="left"]'],
				] );
			},
			$this->option_group,
			'adminz_woocommerce_product_single'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Description readmore',
			function () {
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'    => 'checkbox',
						'name'    => $this->option_name . '[adminz_woocommerce_description_readmore]',
						'checked' => ( $this->settings['adminz_woocommerce_description_readmore'] ?? "" ) == "on"
					],
					'copy'      => '[adminz_woocommerce_description_readmore]',
				] );
			},
			$this->option_group,
			'adminz_woocommerce_product_single'
		);

		// field
		if( get_locale() == 'vi'){
		add_settings_field(
			wp_rand(),
			'Tỉnh/ huyện/ xã',
			function () {

				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'    => 'checkbox',
						'name'    => $this->option_name . '[adminz_woocommerce_tinh_huyen_xa]',
						'checked' => ( $this->settings['adminz_woocommerce_tinh_huyen_xa'] ?? "" ) == "on"
					],
					'copy'      => get_site_url()."/?do_import_tinh_huyen_xa",
					'note'	=> 'Tạo ra taxonomy tỉnh/ huyện/ xã'
				] );
			},
			$this->option_group,
			'adminz_woocommerce_product_single'
		);
		}

		// add section
		add_settings_section(
			'adminz_woocommerce_product_archive',
			'Product archive',
			function () {},
			$this->option_group
		);

		add_settings_field(
			wp_rand(),
			'Order by discount amount',
			function () {

				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'    => 'checkbox',
						'name'    => $this->option_name . '[adminz_woocommerce_discount_amount]',
						'checked' => ( $this->settings['adminz_woocommerce_discount_amount'] ?? "" ) == "on"
					],
					'note'      => 'Enable',
				] );
			},
			$this->option_group,
			'adminz_woocommerce_product_archive'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'List ordering',
			function () {
				$options = array(
					'menu_order' => __( 'Default sorting', 'woocommerce' ),
					'popularity' => __( 'Sort by popularity', 'woocommerce' ),
					'rating'     => __( 'Sort by average rating', 'woocommerce' ),
					'date'       => __( 'Sort by latest', 'woocommerce' ),
					'price'      => __( 'Sort by price: low to high', 'woocommerce' ),
					'price-desc' => __( 'Sort by price: high to low', 'woocommerce' ),
				);
				$options['__discount_amount'] = __( "Discount amount", 'woocommerce' );
				$current = $this->settings['sort_ordering']?? array_keys($options);
				?>
				<table class="adminz_table">
					<tbody class="adminz_repeater" data-primary-name="<?= esc_attr( $this->option_name ); ?>[woocommerce_sort_ordering]">
						<?php
							foreach ($current as $key => $value) {
								?>
								<tr>
									<td>
										<?php
										// custom note
										$note = [$value];
										if($value == '__discount_amount'){
											$note[]= "Re-save all product for apply new value";
										}
										// field
										echo adminz_form_field( [ 
											'field'     => 'select',
											'attribute' => [ 
												'name' => $this->option_name . '[sort_ordering][]'
											],
											'options'   => $options,
											'selected'  => $value,
											'note' => $note
										] );
										?>
									</td>
								</tr>
								<?php
							}
						?>
					</tbody>
				</table>
				<?php
			},
			$this->option_group,
			'adminz_woocommerce_product_archive'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Variation hide max price',
			function () {
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'    => 'checkbox',
						'name'    => $this->option_name . '[variable_product_price_custom]',
						'checked' => ( $this->settings['variable_product_price_custom'] ?? "" ) == "on"
					],
					'copy'      => '[variable_product_price_custom]',
				] );
			},
			$this->option_group,
			'adminz_woocommerce_product_archive'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Tooltip hover',
			function () {
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'    => 'checkbox',
						'name'    => $this->option_name . '[adminz_tooltip_products]',
						'checked' => ( $this->settings['adminz_tooltip_products'] ?? "" ) == "on"
					],
					'copy'      => '[adminz_tooltip_products]',
				] );
			},
			$this->option_group,
			'adminz_woocommerce_product_archive'
		);

		// add section
		add_settings_section(
			'adminz_woocommerce_other',
			'Other',
			function () {},
			$this->option_group
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Message notice position',
			function () {
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'    => 'checkbox',
						'name'    => $this->option_name . '[adminz_woocommerce_fix_notice_position]',
						'checked' => ( $this->settings['adminz_woocommerce_fix_notice_position'] ?? "" ) == "on"
					],
					'copy'      => '[adminz_woocommerce_fix_notice_position]',
				] );
			},
			$this->option_group,
			'adminz_woocommerce_other'
		);

		// field 
		add_settings_section(
			'adminz_woocommerce_hooks',
			'Woocommere template hooks',
			function () {},
			$this->option_group
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Test woocommerce hooks',
			function () {
				echo adminz_copy(add_query_arg( [ 'testhook' => 'woocommerce',], get_site_url() ));
			},
			$this->option_group,
			'adminz_woocommerce_hooks'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Use hooks',
			function () {
				?>
				<p>
					Use: Use: <?= adminz_copy( '[adminz_test]' ) ?>
				</p>
				<?php
				$flatsome_action_hooks = require ( ADMINZ_DIR . "includes/file/woocommerce_hooks.php" );
				foreach ( $flatsome_action_hooks as $hook ) {
					// field
					echo adminz_form_field( [ 
						'field'     => 'input',
						'attribute' => [ 
							'type'  => 'text',
							'name'  => $this->option_name . "[adminz_woocommerce_action_hook][$hook]",
							'value' => $this->settings['adminz_woocommerce_action_hook'][ $hook ] ?? "",
						],
						'before' => '<div class="adminz_grid_item_big"><span class="adminz_grid_item_label">'. $hook.'</span>',
						'after' => '</div>'
					] );
				}	
				?>
				<?php
			},
			$this->option_group,
			'adminz_woocommerce_hooks'
		);
	}
}