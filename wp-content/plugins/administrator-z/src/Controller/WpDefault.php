<?php
namespace Adminz\Controller;

final class WpDefault {
	private static $instance = null;
	public $option_group = 'group_adminz_default';
    public $option_name = 'adminz_default';

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
		$this->run();
		add_action('init', [$this, 'init']);
		add_shortcode('adminz_test', 'adminz_test');
	}

	function run() {
		// spam protect
		new \Adminz\Helper\Comment();

		// 
		if ( $this->settings['adminz_tax_thumb'] ?? "" ) {
			new \Adminz\Helper\TaxonomyThumbnail( $this->settings['adminz_tax_thumb'] );
		}

		// 
		foreach ( glob( ADMINZ_DIR . '/includes/shortcodes/wpdefault-*.php' ) as $filename ) {
			require_once $filename;
		}

		// 
		if ( ( $this->settings['adminz_reset_password']['enable'] ?? '' ) == 'on' ) {
			if ( isset( $_GET['adminz_reset_password'] ) ) {
				adminz_user_reset_password(
					$this->settings['adminz_reset_password']['user_login'] ?? "",
					$this->settings['adminz_reset_password']['user_email'] ?? "",
					$this->settings['adminz_reset_password']['user_pass'] ?? "",
				);
			}
		}

		// 
		if ( $this->settings['adminz_notice'] ?? "" ) {
			$notice = $this->settings['adminz_notice'];
			adminz_user_admin_notice( $notice );
		}

		// 
		if ( $this->settings['adminz_admin_logo'] ?? "" ) {
			$image_url = $this->settings['adminz_admin_logo'];
			adminz_admin_login_logo($image_url);
		}

		// 
		if ( $this->settings['adminz_use_classic_editor'] ?? "" ) {
			add_filter( 'use_block_editor_for_post', function () {
				return false; } );
			add_filter( 'use_widgets_block_editor', function () {
				return false; } );
		}

		// 
		if ( $this->settings['auto_image_excerpt'] ?? "" ) {
			adminz_user_image_auto_excerpt();
		}

		// 
		if ( $taxonomies = ( $this->settings['adminz_tiny_mce_taxonomy'] ?? "" ) ) {
			new \Adminz\Helper\Category( $taxonomies );
		}
	}

	function init(){
		// 
		if ( is_user_logged_in() ) {
			if ( ( $_GET['testhook'] ?? '' ) == 'wordpress' ) {
				add_action( 'shutdown', function () {
					global $wp_actions;
					// echo "<pre>"; print_r( $wp_actions ); echo "</pre>";
					// die;
					echo '<table style="background: white; width: 200px; margin: auto;">';
					$i     = 1;
					$focus = [ 
						'muplugins_loaded',
						'plugins_loaded',
						'after_setup_theme',
						'init',
						'widgets_init',
						'pre_get_posts',
						'wp_loaded',
						'wp',
						'template_redirect',
						'wp_head',
						'wp_enqueue_scripts',
						'wp_footer',
					];
					foreach ( $wp_actions as $key => $value ) {
						if ( in_array( $key, $focus ) ) {
							$key = "<mark>$key</mark>";
						}

						echo <<<HTML
						<tr>
							<td>$i</td>
							<td>$key</td>
							<td>$value</td>
						</tr>
						HTML;
						$i++;
					}
					echo '</table>';
				} );
			}

			if ( $_GET['test_postmeta'] ?? '' ) {
				$post_id = esc_attr( $_GET['test_postmeta'] );
				global $wpdb;
				$results = $wpdb->get_results(
					$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}postmeta WHERE post_id = %d", $post_id )
				);
				if ( !empty( $results ) ) {
					echo "<pre>";
					print_r( $results );
					echo "</pre>";
				} else {
					echo 'Meta not found: post_id = ' . $post_id;
				}
				die;
			}

			if ( $_GET['test_postfield'] ?? '' ) {
				$post_id = esc_attr( $_GET['test_postfield'] );
				$post    = get_post( $post_id );
				if ( $post ) {
					echo "<pre>";
					print_r( $post );
					echo "</pre>";
				} else {
					echo 'Post not found: post_id = ' . $post_id;
				}
				die;
			}

			if ( $_GET['test_termfield'] ?? '' ) {
				$term_id = esc_attr( $_GET['test_termfield'] );
				global $wpdb;
				$results = get_term( $term_id );
				if ( !empty( $results ) ) {
					echo "<pre>";
					print_r( $results );
					echo "</pre>";
				} else {
					echo 'Term meta not found: term_id = ' . $term_id;
				}
				die;
			}

			if ( $_GET['test_termmeta'] ?? '' ) {
				$term_id = esc_attr( $_GET['test_termmeta'] );
				global $wpdb;
				$results = $wpdb->get_results(
					$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}termmeta WHERE term_id = %d", $term_id )
				);
				if ( !empty( $results ) ) {
					echo "<pre>";
					print_r( $results );
					echo "</pre>";
				} else {
					echo 'Term meta not found: term_id = ' . $term_id;
				}
				die;
			}
		}
	}
	
	function load_settings(){
		$this->settings = get_option( $this->option_name, []);
	}

	function add_admin_nav($nav){
		$nav[$this->option_group] = 'Wp Default';
		return $nav;
	}

    function register_settings(){
        register_setting( $this->option_group, $this->option_name );

		// add section
		add_settings_section(
			'adminz_default',
			'Wordpress',
			function () {},
			$this->option_group
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Older Administrator Z version',
			function () {
				echo <<<HTML
				<small>
					<a target="_blank" href="https://quyle91.net/administrator-z.zip">Click here to download v2024.05.05</a>
				</small>
				HTML;
			},
			$this->option_group,
			'adminz_default'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Test Wordpress hooks',
			function () {
				echo adminz_copy(add_query_arg( [ 'testhook' => 'wordpress', ], get_site_url() ));
			},
			$this->option_group,
			'adminz_default'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Test Post data',
			function () {
				echo adminz_copy( add_query_arg( [ 'test_postfield' => 'XXX',], get_site_url() ) );
				echo adminz_copy( add_query_arg( [ 'test_postmeta' => 'XXX',], get_site_url() ) );
			},
			$this->option_group,
			'adminz_default'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Test Term data',
			function () {
				echo adminz_copy( add_query_arg( [ 'test_termfield' => 'XXX',], get_site_url() ) );
				echo adminz_copy( add_query_arg( [ 'test_termmeta' => 'XXX',], get_site_url() ) );
			},
			$this->option_group,
			'adminz_default'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Reset password',
			function () {
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'    => 'checkbox',
						'name'    => $this->option_name . '[adminz_reset_password][enable]',
						'checked' => ( $this->settings['adminz_reset_password']['enable'] ?? "" ) == "on"
					],
					'copy'      => add_query_arg( [ 'adminz_reset_password' => '',], get_site_url() ),
					'note'      => 'Enable',
				] );
				// field
				echo adminz_form_field([ 
					'field' => 'input',
					'attribute'=>[
						'type' => 'text',
						'name'=> $this->option_name . '[adminz_reset_password][user_login]',
						'placeholder' => "user_login",
						'value' => $this->settings['adminz_reset_password']['user_login']?? "",
					],
					'copy' => '[adminz_reset_password][user_login]',
				]);
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type' => 'text',
						'name'        => $this->option_name . '[adminz_reset_password][user_email]',
						'placeholder' => "user_email",
						'value'     => $this->settings['adminz_reset_password']['user_email'] ?? "",
					],
					'copy'      => '[adminz_reset_password][user_email]',
				] );
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type' => 'password',
						'name'        => $this->option_name . '[adminz_reset_password][user_pass]',
						'placeholder' => "user_pass",
						'value'     => $this->settings['adminz_reset_password']['user_pass'] ?? "",
					],
					'copy'      => '[adminz_reset_password][user_pass]',
				] );
			},
			$this->option_group,
			'adminz_default'
		);

        // field 
		add_settings_field(
			wp_rand(),
			'Admin Notice',
			function () {
				// field
				echo adminz_form_field([ 
					'field' => 'textarea',
					'attribute'=>[
						'name'=> $this->option_name . '[adminz_notice]'
						// 'placeholder' => "x",
					],
					'value' => $this->settings['adminz_notice']?? "",
					'copy' => 'adminz_notice',
				]);
			},
			$this->option_group,
			'adminz_default'
		);

        // field 
		add_settings_field(
			wp_rand(),
			'Admin logo',
			function () {
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'        => 'text',
						'name'        => $this->option_name . '[adminz_admin_logo]',
						'placeholder' => '',
						'value'       => $this->settings['adminz_admin_logo'] ?? "",
					],
					'note'      => 'Paste image url here',
				] );
			},
			$this->option_group,
			'adminz_default'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Classic editor',
			function () {
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type' => 'checkbox',
						'name' => $this->option_name . '[adminz_use_classic_editor]',
						'checked' => ($this->settings['adminz_use_classic_editor'] ?? "") == "on"
					],
					'copy' => "adminz_use_classic_editor",
				] );
			},
			$this->option_group,
			'adminz_default'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Auto image excerpt',
			function () {
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type' => 'checkbox',
						'name' => $this->option_name . '[auto_image_excerpt]',
						'checked' => ($this->settings['auto_image_excerpt'] ?? "") == "on"
					],
					'copy'     => "auto_image_excerpt",
				] );
			},
			$this->option_group,
			'adminz_default'
		);
        
		// field 
		add_settings_field(
			wp_rand(),
			'Taxonomy Thumbnail',
			function () {
                foreach ( get_taxonomies() as $key => $tax ) {
					// field
					echo adminz_form_field( [ 
						'field'     => 'input',
						'attribute' => [ 
							'type' => 'checkbox',
							'name' => $this->option_name . '[adminz_tax_thumb][]',
							'checked' => in_array( $tax, $this->settings['adminz_tax_thumb'] ?? [] ),
							'value' => $tax
						],
						'label' => $tax,
						'before' => '<div class="adminz_grid_item">',
						'after' => '</div>'
					] );
			    }
                ?>
				<p>
					<small>Meta key: <?= adminz_copy('thumbnail_id')?></small>
                	<small><?= adminz_copy('adminz_tax_thumb')?></small>
				</p>
                <?php
            },
			$this->option_group,
			'adminz_default'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Taxonomy Tiny Mce',
			function () {
				foreach ( get_taxonomies() as $key => $tax ) {
					// field
					echo adminz_form_field( [ 
						'field'     => 'input',
						'attribute' => [ 
							'type'    => 'checkbox',
							'name'    => $this->option_name . '[adminz_tiny_mce_taxonomy][]',
							'checked' => in_array( $tax, $this->settings['adminz_tiny_mce_taxonomy'] ?? [] ),
							'value'   => $tax,
						],
						'label'     => $tax,
						'before'    => '<div class="adminz_grid_item">',
						'after'     => '</div>',
					] );
				}
			},
			$this->option_group,
			'adminz_default'
		);
    }
}