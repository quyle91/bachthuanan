<?php
namespace Adminz\Controller;

final class Enqueue {
	private static $instance = null;
	public $option_group = 'group_adminz_enqueue';
	public $option_name = 'adminz_enqueue';

	public $fonts_uploaded = [];
	public $fonts_supported = [];
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
		add_action( 'init', [ $this, 'init' ] );
	}

	function init(){

		// 
		if(($this->settings['remove_upload_filters']?? '') == 'on'){
			if(!defined('ALLOW_UNFILTERED_UPLOADS')){
				define( 'ALLOW_UNFILTERED_UPLOADS', true );
			}
		}

		// 
		if( $this->settings['adminz_fonts_uploaded'] ?? ""){
			adminz_enqueue_font_uploaded($this->settings['adminz_fonts_uploaded']);
		}

		// 
		if( $this->settings['adminz_supported_font'] ?? ""){
			adminz_enqueue_font_supported( $this->settings['adminz_supported_font']);
		}

		if ( $this->settings['adminz_custom_css_fonts'] ?? "" ) {
			adminz_enqueue_css( $this->settings['adminz_custom_css_fonts'] );
		}

		if ( $this->settings['adminz_custom_js'] ?? "" ) {
			adminz_enqueue_js( $this->settings['adminz_custom_js'] );
		}
	}

	function load_settings(){
		$this->settings = get_option( $this->option_name,[] );

		// font uploaded
		$fonts_uploaded = $this->settings['adminz_fonts_uploaded'] ?? [];
		// old version
		$fonts_uploaded       = adminz_maybeJson( $fonts_uploaded ) ?? [];
		$this->fonts_uploaded = $fonts_uploaded;

		// font supported
		$fonts_supported       = $this->settings['adminz_supported_font'] ?? [];
		$this->fonts_supported = $fonts_supported;
	}

	function add_admin_nav( $nav ) {
		$nav[$this->option_group] = 'Enqueue';
		return $nav;
	}

	function register_settings() {
		register_setting( $this->option_group, $this->option_name );

		// add section
		add_settings_section(
			'adminz_custom_font',
			'Custom font',
			function () {},
			$this->option_group
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Remove Upload filters',
			function () {
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type' => 'checkbox',
						'name' => $this->option_name . '[remove_upload_filters]',
						'checked' => ($this->settings['remove_upload_filters'] ?? "") == "on"
					],
					'note'      => 'Check it to allow upload your fonts file. Dont forget to disable it later.',
					'label'     => "remove_upload_filters",
				] );
			},
			$this->option_group,
			'adminz_custom_font'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Fonts uploaded',
			function () {
				$fonts_uploaded = $this->fonts_uploaded;

				// default
				if(empty($fonts_uploaded)){
					$fonts_uploaded = [
						[
							'',
							'',
							'',
							'',
							'',
						]
					];
				}
				?>
				<table class="adminz_table">
					<tbody class="adminz_repeater" data-primary-name="<?= esc_attr( $this->option_name ); ?>[adminz_fonts_uploaded]">
						<?php
						foreach ($fonts_uploaded as $key => $value) {
							?>
							<tr>
								<td>
									<?php
										// field
										echo adminz_form_field( [ 
											'field'     => 'input',
											'attribute' => [ 
												'type'    => 'text',
												'name'    => $this->option_name . '[adminz_fonts_uploaded]['.$key.'][0]',
												'placeholder' => 'src',
												'value'   => $value[0] ?? "",
											],
											'before' => '',
											'after' => '',
										] );
									?>
								</td>
								<td>
									<?php
										// field
										echo adminz_form_field( [ 
											'field'     => 'input',
											'attribute' => [ 
												'type'    => 'text',
												'name'    => $this->option_name . '[adminz_fonts_uploaded]['.$key.'][1]',
												'placeholder' => 'font-family',
												'value'   => $value[1] ?? "",
											],
											'before' => '',
											'after' => '',
										] );
									?>
								</td>
								<td>
									<?php
										// field
										echo adminz_form_field( [ 
											'field'     => 'input',
											'attribute' => [ 
												'type'    => 'text',
												'name'    => $this->option_name . '[adminz_fonts_uploaded]['.$key.'][2]',
												'placeholder' => 'font-weight',
												'value'   => $value[2] ?? "",
											],
											'before' => '',
											'after' => '',
										] );
									?>
								</td>
								<td>
									<?php
										// field
										echo adminz_form_field( [ 
											'field'     => 'input',
											'attribute' => [ 
												'type'    => 'text',
												'name'    => $this->option_name . '[adminz_fonts_uploaded]['.$key.'][3]',
												'placeholder' => 'font-style',
												'value'   => $value[3] ?? "",
											],
											'before' => '',
											'after' => '',
										] );
									?>
								</td>
								<td>
									<?php
										// field
										echo adminz_form_field( [ 
											'field'     => 'input',
											'attribute' => [ 
												'type'    => 'text',
												'name'    => $this->option_name . '[adminz_fonts_uploaded]['.$key.'][4]',
												'placeholder' => 'font-stretch',
												'value'   => $value[4] ?? "",
											],
											'before' => '',
											'after' => '',
										] );
									?>
								</td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
				<small>
					Paste font file url here
				</small>
				<?php
				// echo "<pre>"; print_r($this->fonts_uploaded); echo "</pre>";
			},
			$this->option_group,
			'adminz_custom_font'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Fonts supported',
			function () {
				$fonts = [
					'lato' => 'Lato Vietnamese',
					'fontawesome' => 'font awesome 6.5.2-web',
				];
				foreach ($fonts as $value => $name) {
					// field
					echo adminz_form_field( [ 
						'field'     => 'input',
						'attribute' => [ 
							'type'    => 'checkbox',
							'name'    => $this->option_name . '[adminz_supported_font][]',
							'checked' => in_array( $value, $this->settings['adminz_supported_font'] ?? [] ),
							'value'   => $value,
						],
						'label'     => $name,
						'before' => '<div class="adminz_grid_item">',
						'after' => '</div>',
					] );
				}
				// echo "<pre>"; print_r($this->settings); echo "</pre>";
			},
			$this->option_group,
			'adminz_custom_font'
		);

		// add section
		add_settings_section(
			'adminz_enqueue_libary',
			'Custom code',
			function () {},
			$this->option_group
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Custom Css',
			function () {
				echo adminz_form_field( [ 
					'field'     => 'textarea',
					'attribute' => [ 
						'name' => $this->option_name . '[adminz_custom_css_fonts]'
						// 'placeholder' => "x",
					],
					'value'     => $this->settings['adminz_custom_css_fonts'] ?? "",
					'copy'      => 'adminz_custom_css_fonts',
				] );
			},
			$this->option_group,
			'adminz_enqueue_libary'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Custom Javascript',
			function () {
				echo adminz_form_field( [ 
					'field'     => 'textarea',
					'attribute' => [ 
						'name' => $this->option_name . '[adminz_custom_js]'
						// 'placeholder' => "x",
					],
					'value'     => $this->settings['adminz_custom_js'] ?? "",
					'copy'      => 'adminz_custom_js',
				] );
			},
			$this->option_group,
			'adminz_enqueue_libary'
		);

	}
}