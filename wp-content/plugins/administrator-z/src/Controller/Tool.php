<?php
namespace Adminz\Controller;

final class Tool {
	private static $instance = null;
	public $option_group = 'group_adminz_tool';
	public $option_name = 'adminz_tool';

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

		// crawl
		add_action( 'wp_ajax_check_adminz_import_from_post', [$this, 'adminz_crawl']);
		add_action( 'wp_ajax_run_adminz_import_from_post', [$this, 'adminz_crawl']);
		add_action( 'wp_ajax_check_adminz_import_from_category', [$this, 'adminz_crawl']);
		add_action( 'wp_ajax_run_adminz_import_from_category', [$this, 'adminz_crawl']);
		add_action( 'wp_ajax_check_adminz_import_from_product', [$this, 'adminz_crawl']);
		add_action( 'wp_ajax_run_adminz_import_from_product', [$this, 'adminz_crawl']);
		add_action( 'wp_ajax_check_adminz_import_from_product_category', [$this, 'adminz_crawl']);
		add_action( 'wp_ajax_run_adminz_import_from_product_category', [$this, 'adminz_crawl']);

		// ajax
		add_action( 'wp_ajax_adminz_replace_image', [ $this, 'adminz_replace_image' ] );
		
		// zip download
		add_action( 'wp_ajax_adminz_zip_download', [$this, 'adminz_zip_download'] );
	}

	function adminz_crawl() {
		if ( !wp_verify_nonce( $_POST['nonce'], 'adminz_js' ) ) exit;
		ob_start();

		// move all to helper
		$Crawl = new \Adminz\Helper\Crawl($_POST);
		echo $Crawl->init();

		$return = ob_get_clean();

		if ( !$return ) {
			wp_send_json_error( 'Error' );
			wp_die();
		}

		wp_send_json_success( $return );
		wp_die();
	}

	function adminz_replace_image() {
		if ( !wp_verify_nonce( $_POST['nonce'], 'adminz_js' ) ) exit;
		$return = false;

		ob_start();

		foreach ( $_FILES as $key => $file ) {
			echo adminz_replace_media($file);
		}

		$return = ob_get_clean();

		if ( !$return ) {
			wp_send_json_error( 'Error' );
			wp_die();
		}

		wp_send_json_success( $return );
		wp_die();
	}

	function adminz_zip_download(){
		if ( !current_user_can( 'manage_options' ) ) {
			wp_die( 'Unauthorized user' );
		}

		$folder_path = isset( $_POST['folder_path'] ) ? sanitize_text_field( $_POST['folder_path'] ) : '';

		if ( !$folder_path ) {
			wp_die( 'No folder path provided' );
		}

		$abs_path = ABSPATH . $folder_path;

		if ( !file_exists( $abs_path ) || !is_dir( $abs_path ) ) {
			wp_die( 'The specified folder does not exist' );
		}

		$folder_name = basename( $folder_path ); // Lấy tên folder từ path
		$upload_dir  = wp_upload_dir();
		$timestamp   = time();
		$zip_file    = $upload_dir['path'] . "/$folder_name-$timestamp.zip"; // Sử dụng tên thư mục làm tiền tố

		// Create a zip file
		$zip = new \ZipArchive();
		if ( $zip->open( $zip_file, \ZipArchive::CREATE ) !== true ) {
			wp_die( 'Cannot open zip file' );
		}

		// Thêm một folder vào file zip
		$zip->addEmptyDir( $folder_name );

		$files = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $abs_path ), \RecursiveIteratorIterator::LEAVES_ONLY );

		foreach ( $files as $file ) {
			if ( !$file->isDir() ) {
				$file_path     = $file->getRealPath();
				$relative_path = substr( $file_path, strlen( $abs_path ) + 1 );
				// Thay đổi relative path để có folder bên trong
				$zip->addFile( $file_path, $folder_name . '/' . $relative_path );
			}
		}

		$zip->close();

		// Force download the zip file
		header( 'Content-Type: application/zip' );
		header( 'Content-Disposition: attachment; filename="' . $folder_name . '-' . $timestamp . '.zip"' ); // Sử dụng tên thư mục làm tiền tố
		header( 'Content-Length: ' . filesize( $zip_file ) );
		readfile( $zip_file );

		// Delete the zip file after download
		unlink( $zip_file );

		exit;
	}

	function load_settings() {
		$this->settings = get_option( $this->option_name, [] );
	}

	function add_admin_nav( $nav ) {
		$nav[ $this->option_group ] = 'Tools';
		return $nav;
	}

	function register_settings() {
		register_setting( $this->option_group, $this->option_name );

		// add section
		add_settings_section(
			'adminz_tool_crawl_tools',
			'Crawl tools',
			function () {
				
			},
			$this->option_group
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Post',
			function () {
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'        => 'text',
						'name'        => $this->option_name . '[adminz_import_from_post]',
						'value'       => $this->settings['adminz_import_from_post'] ?? "",
					],
					'before'    => '',
					'after'     => '',
				] );
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'          => 'button',
						'class'         => [ 'adminz_field', 'button', 'adminz_fetch' ],
						'data-response' => '.adminz_response1',
						'data-action'   => 'check_adminz_import_from_post',
						'value'         => 'Check',
					],
					'before'    => '',
					'after'     => '',
				] );
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'          => 'button',
						'class'         => [ 'adminz_field', 'button button-primary', 'adminz_fetch' ],
						'data-response' => '.adminz_response1',
						'data-action'   => 'run_adminz_import_from_post',
						'value'         => 'Run',
					],
					'before'    => '',
					'after'     => '',
					'copy'      => 'https://demos.flatsome.com/2015/10/13/velkommen-til-bloggen-min/',

				] );
				echo '<div class="adminz_response adminz_response1"></div>';
			},
			$this->option_group,
			'adminz_tool_crawl_tools'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Category',
			function () {
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'        => 'text',
						'name'        => $this->option_name . '[adminz_import_from_category]',
						'value'       => $this->settings['adminz_import_from_category'] ?? "",
					],
					'before'    => '',
					'after'     => '',
				] );
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'          => 'button',
						'class'         => [ 'adminz_field', 'button', 'adminz_fetch' ],
						'data-response' => '.adminz_response2',
						'data-action'   => 'check_adminz_import_from_category',
						'value'         => 'Check',
					],
					'before'    => '',
					'after'     => '',
				] );
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'          => 'button',
						'class'         => [ 'adminz_field', 'button button-primary', 'adminz_fetch' ],
						'data-response' => '.adminz_response2',
						'data-action'   => 'run_adminz_import_from_category',
						'value'         => 'Run',
					],
					'before'    => '',
					'after'     => '',
					'copy'      => 'https://demos.flatsome.com/blog/',

				] );

				echo '<div class="adminz_response adminz_response2"></div>';
			},
			$this->option_group,
			'adminz_tool_crawl_tools'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Product',
			function () {
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'        => 'text',
						'name'        => $this->option_name . '[adminz_import_from_product]',
						'value'       => $this->settings['adminz_import_from_product'] ?? "",
					],
					'before'    => '',
					'after'     => '',

				] );
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'          => 'button',
						'class'         => [ 'adminz_field', 'button', 'adminz_fetch' ],
						'data-response' => '.adminz_response3',
						'data-action'   => 'check_adminz_import_from_product',
						'value'         => 'Check',
					],
					'before'    => '',
					'after'     => '',
				] );
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'          => 'button',
						'class'         => [ 'adminz_field', 'button button-primary', 'adminz_fetch' ],
						'data-response' => '.adminz_response3',
						'data-action'   => 'run_adminz_import_from_product',
						'value'         => 'Run',
					],
					'before'    => '',
					'after'     => '',
					'copy'      => 'https://demos.flatsome.com/shop/clothing/hoodies/ship-your-idea-2/',

				] );

				echo '<div class="adminz_response adminz_response3"></div>';
			},
			$this->option_group,
			'adminz_tool_crawl_tools'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Product category',
			function () {
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'        => 'text',
						'name'        => $this->option_name . '[adminz_import_from_product_category]',
						'value'       => $this->settings['adminz_import_from_product_category'] ?? "",
					],
					'before'    => '',
					'after'     => '',
				] );
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'          => 'button',
						'class'         => [ 'adminz_field', 'button', 'adminz_fetch' ],
						'data-response' => '.adminz_response4',
						'data-action'   => 'check_adminz_import_from_product_category',
						'value'         => 'Check',
					],
					'before'    => '',
					'after'     => '',
				] );
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'          => 'button',
						'class'         => [ 'adminz_field', 'button button-primary', 'adminz_fetch' ],
						'data-response' => '.adminz_response4',
						'data-action'   => 'run_adminz_import_from_product_category',
						'value'         => 'Run',
					],
					'before'    => '',
					'after'     => '',
					'copy'      => 'https://demos.flatsome.com/product-category/clothing/',

				] );
				
				echo '<div class="adminz_response adminz_response4"></div>';
			},
			$this->option_group,
			'adminz_tool_crawl_tools'
		);

		// add section
		add_settings_section(
			'adminz_tool_css_selector',
			'Css Selector',
			function () {},
			$this->option_group
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Post single',
			function () {

				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'        => 'text',
						'name'        => $this->option_name . '[adminz_import_post_title]',
						'placeholder' => 'Title wrapper',
						'value'       => $this->settings['adminz_import_post_title'] ?? "",
					],
					'copy' => '.article-inner .entry-header .entry-title',
				] );

				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'        => 'text',
						'name'        => $this->option_name . '[adminz_import_post_thumbnail]',
						'placeholder' => 'Thumbnail image',
						'value'       => $this->settings['adminz_import_post_thumbnail'] ?? "",
					],
					'copy' => '.article-inner .entry-header .entry-image img',
				] );

				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'        => 'text',
						'name'        => $this->option_name . '[adminz_import_post_category]',
						'placeholder' => 'Categories ',
						'value'       => $this->settings['adminz_import_post_category'] ?? "",
					],
					'copy'      => '.entry-header .entry-category a',
				] );

				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'        => 'text',
						'name'        => $this->option_name . '[adminz_import_post_content]',
						'placeholder' => 'Content wrapper',
						'value'       => $this->settings['adminz_import_post_content'] ?? "",
					],
					'copy' => '.article-inner .entry-content',
				] );
			},

			$this->option_group,
			'adminz_tool_css_selector'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Category/ blog',
			function () {

				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'        => 'text',
						'name'        => $this->option_name . '[adminz_import_category_post_item]',
						'placeholder' => 'Post item wrapper',
						'value'       => $this->settings['adminz_import_category_post_item'] ?? "",
					],
					'copy'      => '#post-list article',
				] );

				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'        => 'text',
						'name'        => $this->option_name . '[adminz_import_category_post_item_link]',
						'placeholder' => 'Post item link',
						'value'       => $this->settings['adminz_import_category_post_item_link'] ?? "",
					],
					'copy'      => '.more-link',
					'before'    => '<p>↳',
					'after'     => '</p>'
				] );
			},
			$this->option_group,
			'adminz_tool_css_selector'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Product single',
			function () {

				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'        => 'text',
						'name'        => $this->option_name . '[adminz_import_product_title]',
						'placeholder' => 'Title wrapper',
						'value'       => $this->settings['adminz_import_product_title'] ?? "",
					],
					'copy'      => ['.product-info>.product-title', 'adminz_import_product_title'],
				] );

				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'        => 'text',
						'name'        => $this->option_name . '[adminz_import_product_category]',
						'placeholder' => 'Product categories',
						'value'       => $this->settings['adminz_import_product_category'] ?? "",
					],
					'copy'      => [ '.summary  .posted_in a' ],
				] );

				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'        => 'text',
						'name'        => $this->option_name . '[adminz_import_product_prices]',
						'placeholder' => 'Prices',
						'value'       => $this->settings['adminz_import_product_prices'] ?? "",
					],
					'copy'      => ['.product-info .price-wrapper .woocommerce-Price-amount', 'adminz_import_product_prices'],
				] );

				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'        => 'text',
						'name'        => $this->option_name . '[adminz_import_product_thumbnail]',
						'placeholder' => 'Gallery wrapper',
						'value'       => $this->settings['adminz_import_product_thumbnail'] ?? "",
					],
					'copy'      => ['.woocommerce-product-gallery__image', 'adminz_import_product_thumbnail'],
				] );

				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'        => 'text',
						'name'        => $this->option_name . '[adminz_import_product_content]',
						'placeholder' => 'Product content',
						'value'       => $this->settings['adminz_import_product_content'] ?? "",
					],
					'copy'      => ['.woocommerce-Tabs-panel--description', 'adminz_import_product_content'],
				] );

				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'        => 'text',
						'name'        => $this->option_name . '[adminz_import_product_short_description]',
						'placeholder' => 'Short description',
						'value'       => $this->settings['adminz_import_product_short_description'] ?? "",
					],
					'copy'      => [ '.product-short-description', 'adminz_import_product_short_description' ],
				] );
			},
			$this->option_group,
			'adminz_tool_css_selector'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Product list',
			function () {
				
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'        => 'text',
						'name'        => $this->option_name . '[adminz_import_category_product_item]',
						'placeholder' => 'Item wrapper',
						'value'       => $this->settings['adminz_import_category_product_item'] ?? "",
					],
					'copy'      => '.products .product',
				] );

				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'        => 'text',
						'name'        => $this->option_name . '[adminz_import_category_product_item_link]',
						'placeholder' => 'Item wrapper link',
						'value'       => $this->settings['adminz_import_category_product_item_link'] ?? "",
					],
					'copy'      => '.box-image a',
					'before'    => '<p>↳',
					'after'     => '</p>',
				] );
			},
			$this->option_group,
			'adminz_tool_css_selector'
		);

		// add section
		add_settings_section(
			'adminz_tool_setup',
			'Setup crawl',
			function () {},
			$this->option_group
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Content Fix',
			function () {
				
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'        => 'text',
						'name'        => $this->option_name . '[adminz_import_content_remove_attrs]',
						'value'       => $this->settings['adminz_import_content_remove_attrs'] ?? "a",
					],
					'note'      => 'Remove Attributes for Tags',
				] );

				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'  => 'text',
						'name'  => $this->option_name . '[adminz_import_content_remove_tags]',
						'value' => $this->settings['adminz_import_content_remove_tags'] ?? "iframe,script,video,audio",
					],
					'note'      => 'Remove HTML Tags',
				] );

				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'  => 'number',
						'min'	=> '0',
						'name'  => $this->option_name . '[adminz_import_content_remove_first]',
						'value' => $this->settings['adminz_import_content_remove_first'] ?? "0",
					],
					'note'      => 'Removes the number of elements from the First',
				] );

				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'  => 'number',
						'min'	=> '0',
						'name'  => $this->option_name . '[adminz_import_content_remove_end]',
						'value' => $this->settings['adminz_import_content_remove_end'] ?? "0",
					],
					'note'      => 'Removes the number of elements from the End',
				] );
			},
			$this->option_group,
			'adminz_tool_setup'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Search and replace content',
			function () {
				// field
				$default = "January\r\nFebruary\r\nMarch\r\nApril\r\nMay\r\nJune\r\nJuly\r\nAugust\r\nSeptember\r\nOctober\r\nNovember\r\nDecember";
				echo adminz_form_field( [ 
					'field'     => 'textarea',
					'attribute' => [ 
						'name'        => $this->option_name . '[adminz_import_content_replace_from]',
						'placeholder' => $default,
					],
					'value'     => $this->settings['adminz_import_content_replace_from'] ?? $default,
					'note'	=> 'search'
				] );
				// field
				$default = "1\r\n2\r\n3\r\n4\r\n5\r\n6\r\n7\r\n8\r\n9\r\n10\r\n11\r\n12";
				echo adminz_form_field( [ 
					'field'     => 'textarea',
					'attribute' => [ 
						'name'        => $this->option_name . '[adminz_import_content_replace_to]',
						'placeholder' => $default,
					],
					'value'     => $this->settings['adminz_import_content_replace_to'] ?? $default,
					'note'	=> 'replace'
				] );
			},
			$this->option_group,
			'adminz_tool_setup'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Woocommerce',
			function () {
				// field
				$checked = ($this->settings['adminz_import_product_include_image_content_to_gallery'] ?? "") == "on";
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'    => 'checkbox',
						'name'    => $this->option_name . '[adminz_import_product_include_image_content_to_gallery]',
						'checked' => $checked
					],
					'label'     => "Include entry content images to gallery",
				] );
				// field
				$checked = ($this->settings['adminz_import_product_include_image_variations_to_gallery'] ?? "") == "on";
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'    => 'checkbox',
						'name'    => $this->option_name . '[adminz_import_product_include_image_variations_to_gallery]',
						'checked' => $checked,
					],
					'label'     => "Include variations images to gallery",
				] );
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type' => 'number',
						'name'        => $this->option_name . '[adminz_import_content_product_decimal_seprator]',
						'value'     => $this->settings['adminz_import_content_product_decimal_seprator'] ?? "2",
					],
					'note'      => 'Product price remove decimal separator from END',
				] );
			},
			$this->option_group,
			'adminz_tool_setup'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Cron',
			function () {
				echo "<code>" . ADMINZ_DIR_URL . 'cron/crawl.php' . "</code>";
			},
			$this->option_group,
			'adminz_tool_setup'
		);

		// add section
		add_settings_section(
			'adminz_tool_file',
			'File tools',
			function () {},
			$this->option_group
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Zip downloader',
			function () {
				?>
				<div class="wrap">
					<div class="form">
						<input type="text" id="folder-path" name="folder-path" class="regular-text"
						placeholder="e.g., plugins/contact-form-7" />
						<button type="button" id="zip-download-button" class="button button-primary">
							Download Zip
						</button>
						<span id="zip-download-status"></span>
					</div>
					<div id="suggestions">
						<ul>
							<li class="theme">
								<strong> Themes: </strong>
								<?php
									$theme_dir = WP_CONTENT_DIR . '/themes';
									foreach ( glob( $theme_dir . '/*', GLOB_ONLYDIR ) as $theme_path ) {
										$theme_name = basename( $theme_path );
										$theme      = wp_get_theme( $theme_name );
										echo '<button type=button class="button button-small theme-suggestion" data-path="wp-content/themes/' . esc_attr( $theme_name ) . '">' . esc_html( $theme->get( 'Name' ) ) . '</button> ';
									}
								?>
							</li>
							<li class="plugin">
								<strong> Plugins: </strong>
								<?php
									$plugin_dir = WP_CONTENT_DIR . '/plugins';
									foreach ( glob( $plugin_dir . '/*', GLOB_ONLYDIR ) as $plugin_path ) {
										$plugin_name = basename( $plugin_path );
										$plugin_file = $plugin_name;
										$plugin_data = get_plugins( '/' . $plugin_file );
										if ( !empty( $plugin_data ) ) {
											$plugin_name_display = esc_html( $plugin_data[ key( $plugin_data ) ]['Name'] );
											echo '<button type=button class="button button-small plugin-suggestion" data-path="wp-content/plugins/' . esc_attr( $plugin_file ) . '">' . $plugin_name_display . '</button> ';
										}
									}
									?>
							</li>
						</ul>
					</div>
					<script type="text/javascript">
						jQuery(document).ready(function ($) {

							$('#suggestions').on('click', '.theme-suggestion, .plugin-suggestion', function (e) {
								e.preventDefault();
								$('#folder-path').val($(this).data('path'));
							});

							$('#zip-download-button').on('click', function () {
								$('#zip-download-status').text('Processing...');

								var folderPath = $('#folder-path').val();

								if (!folderPath) {
									$('#zip-download-status').text('Please enter a valid folder path.');
									return;
								}

								$.ajax({
									url: ajaxurl,
									type: 'POST',
									data: {
										action: 'adminz_zip_download',
										folder_path: folderPath
									},
									xhrFields: {
										responseType: 'blob'
									},
									success: function (blob, status, xhr) {
										var link = document.createElement('a');
										link.href = window.URL.createObjectURL(blob);
										var contentDisposition = xhr.getResponseHeader('Content-Disposition');
										var filename = 'download.zip'; // Default filename
										if (contentDisposition) {
											var matches = /filename="([^"]*)"/.exec(contentDisposition);
											if (matches != null && matches[1]) filename = matches[1];
										}
										link.download = filename;
										document.body.appendChild(link);
										link.click();
										document.body.removeChild(link);
										$('#zip-download-status').text('Download complete.');
									},
									error: function () {
										$('#zip-download-status').text('An error occurred.');
									}
								});
							});
						});
					</script>
				</div>
				<?php
			},
			$this->option_group,
			'adminz_tool_file'
		);

		// add section
		add_settings_section(
			'adminz_tool_image',
			'Image tools',
			function () {},
			$this->option_group
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Replace Image',
			function () {
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'        => 'file',
						'class' => ['adminz_field', 'adminz_upload_image'],
						'data-action' => 'adminz_replace_image',
						'data-response' => '.adminz_response5',
						'accept' => "image/*",
					],
					'note' => "Please use same image name!"
				] );
				?>
				<div class="adminz_response adminz_response5"></div>
				<?php
			},
			$this->option_group,
			'adminz_tool_image'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Move Media to Subdomain',
			function () {
				?>
				<div>	        				
					<p><small>1. Create subdomain</small></p>
					<p><small>2. Create ssl verification for subdomain</small></p>
					<p><small>3. Go to file manager subdomain, create: index.php and type <code> echo esc_attr($_SERVER["DOCUMENT_ROOT"]); </code></small></p>
					<p><small>4. Go to subdomain url in browse, Copy dir path</small></p>
					<p><small>5. Go to <code>wp-admin/options.php</code>. Search <code>upload_url_path / upload_path</code>, put dir path into <code>Store uploads in this folder</code>, put subdomain into <code>Full URL path to files</code></small></p>
					<p><small>6. Test Media Upload and image in front end</small></p>							
					<p><small>7. Move all folder and files in wp-content/uploads into subdomain in file manager </small></p>
					<p><small>8. Use <code>better search and replace</code> to replace Old link to new link: <code>maindomain/wp-content/uploads/</code> -&gt; <code>subdomain/</code> </small></p>
					<p><small>9. Useful links: </small></p>
					<p><small>https://webmtp.com/toi-uu-hoa-website-wordpress-tren-pagespeed-insights/</small></p>
					<p><small>link: https://wordpressvn.com/t/huong-dan-toi-uu-flatsome-tang-diem-toi-da-google-insight/2848</small></p>
					<p><small>link: https://aaron.kr/content/code/move-wordpress-media-uploads-to-a-subdomain/</small></p>
				</div>
				<?php
			},
			$this->option_group,
			'adminz_tool_image'
		);
	}
}