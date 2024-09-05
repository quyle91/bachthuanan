<?php
namespace Adminz\Controller;

final class QuickContact {
	private static $instance = null;
	public $option_group = 'group_adminz_contactgroup';
	public $option_name = 'adminz_contactgroup';

    public $settings = [], $nav_asigned = [], $menus = [], $styles = [];

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

	function init() {
		if(is_admin()){
			return;
		}

		if(empty($this->nav_asigned)){
			return;
		}
		
		foreach ($this->nav_asigned as $style => $menu) {
			if($menu){
				// get menu data
				$menu_name = str_replace('adminz_', '', $menu);
				foreach ($this->menus as $key => $value) {
					if(str_replace(' ', '', $value['name']) == $menu_name){
						$menu_data = $value['items'];
						$style = str_replace('callback_','',$style);
						add_action('wp_footer', function() use($style, $menu_data){
							wp_enqueue_style(
								'adminz_quick_contact_style_'.$style,
								ADMINZ_DIR_URL . "assets/css/quick-contact/" . str_replace( 'callback_', '', $style ).".css", 
								null,
								ADMINZ_VERSION, 
								'all'
							);
							echo call_user_func('adminz_quick_contact_'.$style, $menu_data, $this->settings['settings']);
						});
					}
				}
			}
		}		
	}

	function load_settings() {
		$this->settings = get_option( $this->option_name, [] );
		// styles 
		$this->styles   = [ 
			'callback_style1'  => array(
				'callback'    => 'callback_style1',
				'title'       => '[1] Fixed Right',
				'css'         => [ ADMINZ_DIR_URL . 'assets/css/style1.css', 'all' ],
				'js'          => [],
				'description' => '',
			),
			'callback_style2'  => array(
				'callback'    => 'callback_style2',
				'title'       => '[2] Left Expanding Group',
				'css'         => [ ADMINZ_DIR_URL . 'assets/css/style2.css', 'all' ],
				'js'          => [ ADMINZ_DIR_URL . 'assets/js/style2.js' ],
				'description' => 'add class <code>right</code> to right style',
			),
			'callback_style3'  => array(
				'callback'    => 'callback_style3',
				'title'       => '[3] Left zoom',
				'css'         => [ ADMINZ_DIR_URL . 'assets/css/style3.css', 'all' ],
				'js'          => [],
				'description' => '',
			),
			'callback_style4'  => array(
				'callback'    => 'callback_style4',
				'title'       => '[4] Left Expand',
				'css'         => [ ADMINZ_DIR_URL . 'assets/css/style4.css', 'all' ],
				'js'          => [],
				'description' => 'Allow shortcode into title attribute. To auto show, put <code>show_desktop</code> into classes',
			),
			'callback_style5'  => array(
				'callback'    => 'callback_style5',
				'title'       => '[5] Fixed Bottom Mobile',
				'css'         => [ ADMINZ_DIR_URL . 'assets/css/style5.css', '(max-width: 768px)' ],
				'js'          => [],
				'description' => '',
			),
			'callback_style6'  => array(
				'callback'    => 'callback_style6',
				'title'       => '[6] Left Expand Horizontal',
				'css'         => [ ADMINZ_DIR_URL . 'assets/css/style6.css', 'all' ],
				'js'          => [],
				'description' => 'Round button Horizontal and tooltip, put <code>active</code> into classes to show tooltip or <code>zeffect1</code> for effect animation',
			),
			'callback_style10' => array(
				'callback'    => 'callback_style10',
				'title'       => '[7] Fixed Simple right',
				'css'         => [ ADMINZ_DIR_URL . 'assets/css/style10.css', 'all' ],
				'js'          => [],
				'description' => 'Simple fixed',
			),
		];

		// menu
		$settings    = $this->settings['settings'] ?? [];
		$custom_menu = $settings['custom_menu'] ?? [];
		// old data
		if ( isset( $settings['custom_nav'] ) ) {
			$custom_nav = (array) json_decode( $settings['custom_nav'] );
			$tmp        = [];
			foreach ( $custom_nav as $key => $value ) {
				$tmp[] = [ 
					'name'  => $value[0],
					'items' => $value[1],
				];
			}
			$custom_menu = $tmp;
		}
		$this->menus = $custom_menu;

		// nav assigned
		$this->nav_asigned = $this->settings['nav_asigned'] ?? [];
	}

	function add_admin_nav( $nav ) {
		$nav[ $this->option_group ] = 'Quick Contact';
		return $nav;
	}

	function register_settings() {
		register_setting( $this->option_group, $this->option_name );

		// field 
		add_settings_field(
			wp_rand(),
			'Menu Creator',
			function () {
				$custom_menu = $this->menus;
				
				// default
				if(empty($custom_menu)){
					$custom_menu = [
						[
							"name" => 'Menu contact',
							"items" => [
								[
									'#',
									'Hotline',
									'call',
									'_blank',
									'',
									'',
									''
								]
							]
						]
					];
				}
				?>
				<table class="adminz_table">
					<tbody class="adminz_repeater" data-primary-name="<?= esc_attr($this->option_name); ?>[settings][custom_menu]">
						<?php
							foreach ( $custom_menu as $menu_key => $nav ) {
								$name  = $nav['name'] ?? "";
								$items = $nav['items'] ?? [];
								?>
								<tr> 
									<td>
										<?php
											// field
											echo adminz_form_field( [ 
												'field'     => 'input',
												'attribute' => [ 
													'type'        => 'text',
													'placeholder' => 'url',
													'name'        => $this->option_name . "[settings][custom_menu][$menu_key][name]",
													'value'       => $name ?? "",
												],
												'before'    => '',
												'after'     => '',
											] );
										?>
									</td>
									<td>
										<div class="adminz_repeater" data-primary-name="<?= esc_attr($this->option_name); ?>[settings][custom_menu][<?= esc_attr($menu_key); ?>][items]">
											<?php 
												foreach ($items as $item_key => $item) {
													?>
													<div>
														<?php
															// field
															echo adminz_form_field( [ 
																'field'     => 'input',
																'attribute' => [ 
																	'type'        => 'text',
																	'placeholder' => 'url',
																	'name'        => $this->option_name . "[settings][custom_menu][$menu_key][items][$item_key][0]",
																	'value'       => $item[0] ?? "",
																],
																'before'    => '',
																'after'     => '',
															] );
															// field
															echo adminz_form_field( [ 
																'field'     => 'input',
																'attribute' => [ 
																	'type'        => 'text',
																	'placeholder' => 'Navigation Label',
																	'name'        => $this->option_name . "[settings][custom_menu][$menu_key][items][$item_key][1]",
																	'value'       => $item[1] ?? "",
																],
																'before'    => '',
																'after'     => '',
															] );
															// field
															echo adminz_form_field( [ 
																'field'     => 'input',
																'attribute' => [ 
																	'type'        => 'text',
																	'placeholder' => 'Icon code',
																	'name'        => $this->option_name . "[settings][custom_menu][$menu_key][items][$item_key][2]",
																	'value'       => $item[2] ?? "",
																],
																'before'    => '',
																'after'     => '',
															] );
															// field
															echo adminz_form_field( [ 
																'field'     => 'input',
																'attribute' => [ 
																	'type'        => 'text',
																	'placeholder' => '_blank',
																	'name'        => $this->option_name . "[settings][custom_menu][$menu_key][items][$item_key][3]",
																	'value'       => $item[3] ?? "",
																],
																'before'    => '',
																'after'     => '',
															] );
															// field
															echo adminz_form_field( [ 
																'field'     => 'input',
																'attribute' => [ 
																	'type'        => 'text',
																	'placeholder' => 'CSS Classes',
																	'name'        => $this->option_name . "[settings][custom_menu][$menu_key][items][$item_key][4]",
																	'value'       => $item[4] ?? "",
																],
																'before'    => '',
																'after'     => '',
															] );
															// field
															echo adminz_form_field( [ 
																'field'     => 'input',
																'attribute' => [ 
																	'type'        => 'text',
																	'placeholder' => 'Color code',
																	'name'        => $this->option_name . "[settings][custom_menu][$menu_key][items][$item_key][5]",
																	'value'       => $item[5] ?? "",
																],
																'before'    => '',
																'after'     => '',
															] );
															// field
															echo adminz_form_field( [ 
																'field'     => 'input',
																'attribute' => [ 
																	'type'        => 'text',
																	'placeholder' => 'Description',
																	'name'        => $this->option_name . "[settings][custom_menu][$menu_key][items][$item_key][6]",
																	'value'       => $item[6] ?? "",
																],
																'before'    => '',
																'after'     => '',
															] );
														?>
													</div>
													<?php
												}
											?>
										</div>
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
			'adminz_contactgroup_menu'
		);

		// add section
		add_settings_section(
			'adminz_contactgroup_menu',
			'Menu',
			function () {},
			$this->option_group
		);
        // field 
		add_settings_field(
			wp_rand(),
			'Menu Asign',
			function () {
				// prepare data
				$nav_asigned = $this->nav_asigned;
				$menus = [''=>'- Not assigned -'];
				foreach ($this->menus as $key => $menu) {
					$_name = 'adminz_' . str_replace( ' ', '', $menu['name'] );
					$menus[$_name] =  $menu['name'];
				}
				
				foreach ($this->styles as $key => $value) {
					// field
					$select_name = $value['callback'];
					echo adminz_form_field( [ 
						'field'     => 'select',
						'attribute' => [ 
							'type'  => 'text',
							'name'  => $this->option_name. "[nav_asigned][$select_name]",
						],
						'options' => $menus,
						'note' => $value['title'],
						'selected' => $nav_asigned[$key] ?? false
					] );
				}
			},
			$this->option_group,
			'adminz_contactgroup_menu'
		);

		// add section
		add_settings_section(
			'adminz_contactgroup_config',
			'Config',
			function () {},
			$this->option_group
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Group title',
			function () {
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'  => 'text',
						'name'  => $this->option_name . '[settings][contactgroup_title]',
						'value' => $this->settings['settings']['contactgroup_title'] ?? "",
					],
				] );
			},
			$this->option_group,
			'adminz_contactgroup_config'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Classes',
			function () {
				// field
				echo adminz_form_field( [ 
					'field'     => 'input',
					'attribute' => [ 
						'type'  => 'text',
						'name'  => $this->option_name . '[settings][contactgroup_classes]',
						'value' => $this->settings['settings']['contactgroup_classes'] ?? "",
					],
					'note' => adminz_copy('left') . " " . adminz_copy('right'),
				] );
			},
			$this->option_group,
			'adminz_contactgroup_config'
		);
	}
}