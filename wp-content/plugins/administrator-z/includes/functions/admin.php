<?php 
function adminz_admin_login_logo($image_url){
	add_filter( 'login_headerurl', function() use($image_url){
		echo '<style type="text/css"> h1 a {background-image: url(' . esc_url($image_url) . ') !important; background-size: contain !important;    width: 100%!important;}
			</style>';
    } );
}