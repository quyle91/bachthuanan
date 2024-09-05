<?php 
$___                     = new \Adminz\Helper\FlatsomeELement;
$___->shortcode_name     = 'adminz_googlemap';
$___->shortcode_title    = 'Google map iframe';
$___->shortcode_icon     = 'text';
$___->options            = [ 
	'address' => array(
		'type'    => 'textfield',
		'heading' => 'Address or latlong',
		'default' => '21.028232792016798, 105.83566338846242',
	),
	'height'  => array(
		'type'    => 'textfield',
		'heading' => 'Height',
		'default' => '300px',
	),
	'hl'      => array(
		'type'    => 'textfield',
		'heading' => 'Language',
		'default' => 'vn',
	),
];
$___->shortcode_callback = function ($atts, $content=null){
    extract(shortcode_atts(array(
        'address'=> '21.028232792016798, 105.83566338846242',
        'height'=>'300px',
        'hl'=>"vn"
    ), $atts));

	if ( isset( $_POST['ux_builder_action'] ) ) {
		return adminz_preview_text();
	}

    ob_start(); 
    ?>
    <iframe 
        style="
            margin-bottom: -7px;
            border: none; 
            height: <?php echo esc_attr($height) ?>; 
            width: 100%" 
        src = "https://maps.google.com/maps?q=<?php echo esc_attr($address); ?>&hl=<?php echo esc_attr($hl) ?>;z=14&amp;output=embed">
    </iframe>
    <?php
    return ob_get_clean();
};
$___->general_element();