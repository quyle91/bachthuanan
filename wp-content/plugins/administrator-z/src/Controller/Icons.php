<?php
namespace Adminz\Controller;

final class Icons {
	private static $instance = null;
	public $option_group = 'group_adminz_icons';
	public $option_name = 'adminz_icons';

	public $settings = [], $icons = [];

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
	}

	function load_settings() {
		// $this->settings = get_option( $this->option_name, [] );
		// icons
		foreach ( glob( ADMINZ_DIR . '/assets/icons/*.svg' ) as $path ) {
			$this->icons[] = str_replace( '.svg', '', basename( $path ) );
		}
	}

    function get_icon_url($icon){
        return ADMINZ_DIR_URL . 'assets/icons/' . $icon;
    }

	function add_admin_nav( $nav ) {
		$nav[ $this->option_group ] = 'Icons';
		return $nav;
	}

	function register_settings() {
		register_setting( $this->option_group, $this->option_name );

		// add section
		add_settings_section(
			'adminz_icons',
			'Icons supported',
			function () {},
			$this->option_group
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Shortcode',
			function () {
				?>
                    <small class="adminz_click_to_copy" data-text='[adminz_icon icon="clock" max_width="16px" class="footer_icon"]'>
						[adminz_icon icon="clock" max_width="16px" class="footer_icon"]
					</small>
			    <?php
			},
			$this->option_group,
			'adminz_icons'
		);

		// field 
		add_settings_field(
			wp_rand(),
			'Icons',
			function () {
				foreach ($this->icons as $key => $icon) {
                    ?>
                    <div 
						class="adminz_click_to_copy"
						data-text="<?= esc_attr($icon) ;?>"
						style="
							display: inline-flex; 
							flex-direction: column; 
							width: 30px; 
							border: 1px solid gray; 
							margin: 2px; 
							padding: 5px;
						">
                        <img  src="<?php echo esc_url($this->get_icon_url($icon)) ?>.svg" alt="">
                    </div>
                    <?php
                }
			},
			$this->option_group,
			'adminz_icons'
		);

	}

	function get_icon_html( $icon = 'info-circle', $attr = [] ) {
		$iconurl = str_starts_with( $icon, 'http' ) ? $icon : ADMINZ_DIR . 'assets/icons/' . $icon . '.svg';

		// Normalize attributes
		$convert_attr = array_merge( [ 
			'class' => [ 'adminz_svg' ],
			'alt'   => [ 'adminz' ],
			'style' => [ 'fill' => 'currentColor' ],
		], array_map( function ($value) {
			return is_array( $value ) ? $value : explode( ',', $value );
		}, $attr ) );

		// Build attribute string
		$attr_item = '';
		foreach ( $convert_attr as $key => $value ) {
			$attr_item .= $key . '="' . implode( ' ', array_map( function ($v, $k) {
				return is_int( $k ) ? $v : "$k:$v;";
			}, $value, array_keys( $value ) ) ) . '" ';
		}

		// Return HTML
		if ( pathinfo( $iconurl, PATHINFO_EXTENSION ) !== 'svg' ) {
			return '<img ' . trim( $attr_item ) . ' src="' . esc_url( $iconurl ) . '"/>';
		}

		$response = @file_get_contents( $iconurl );
		return $this->cleansvg( $response, $attr_item );
	}

	public function cleansvg( $response, $attr_item ) {
		$return = "";
		// Tìm thẻ <svg>
		preg_match( '/<svg[^>]*>(.*?)<\/svg>/is', $response, $matches );
		if ( isset( $matches[0] ) ) {
			$response = $matches[0];
			$return   = str_replace(
				'<svg',
				'<svg ' . $attr_item,
				$response
			);
			$return   = preg_replace( '/<!--(.*)-->/', '', $return );
			// $return = preg_replace('/width="[^"]+"/i', '', $return);
			// $return = preg_replace('/height="[^"]+"/i', '', $return);
		}
		return $return;
	}
}