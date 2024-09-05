<?php 
function adminz_get_icon( $icon = 'info-circle', $attr = [] ){
    global $adminz;
    return $adminz['Icons']->get_icon_html($icon, $attr);
}

function adminz_get_list_icons(){
	$options = [ '' => '--Select--' ];
	foreach ( adminz_get_settings( 'Icons', 'icons' ) as $icon ) {
		$options[ str_replace( ".svg", "", $icon ) ] = $icon;
	}

    return $options;
}