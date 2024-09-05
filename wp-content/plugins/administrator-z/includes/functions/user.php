<?php
function adminz_user_image_auto_excerpt(){
	if ( is_admin() ) {
		add_action( 'add_attachment', function ($post_ID) {
			if ( wp_attachment_is_image( $post_ID ) ) {
				$my_image_title = get_post( $post_ID )->post_title;
				$my_image_meta  = array(
					'ID'           => $post_ID,			// Specify the image (ID) to be updated
					'post_title'   => $my_image_title,		// Set image Title to sanitized title
					'post_excerpt' => $my_image_title,		// Set image Caption (Excerpt) to sanitized title
					'post_content' => $my_image_title,		// Set image Description (Content) to sanitized title
				);
				update_post_meta( $post_ID, '_wp_attachment_image_alt', $my_image_title );
				wp_update_post( $my_image_meta );
			}
		} );
	}
}

function adminz_user_admin_notice($notice){    
    add_action( 'admin_notices', function() use($notice){
		if ( !$notice ) return;
        echo <<<HTML
            <div class="notice is-dismissible">
                <p>
                    <strong> Notice:</strong> 
                    {$notice} 
                </p>
            </div>
        HTML;
	});
}

function adminz_user_reset_password( $__username, $__useremail, $__password){
	// make sure user functions exists
    require_once( ABSPATH."wp-includes/pluggable.php"); 
    if(!$__username){
        die('__username is required!');
    }
    if(!$__useremail){
        die('__useremail is required!');
    }
    if(!$__password){
        die('__password is required!');
    }

    $id_username_exists = username_exists( $__username);
    $id_email_exists = email_exists( $__useremail);
    $user_id = false;
    if ( $id_username_exists ) {
        $user_id = $id_username_exists;
    }
    if ( $id_email_exists ) {
        $user_id = $id_email_exists;
    }
    if($user_id){
        wp_set_password( $__password, $user_id );
        $user_login = get_user_by('id', $user_id)->data->user_login;
        echo '<pre>'; print_r('Password is updated!'); echo '</pre>';
        echo '<pre>'; print_r($user_login); echo '</pre>';
        echo '<pre>'; print_r($__password); echo '</pre>';
        die;
    }else{
    $inserted = wp_insert_user(
        array (
            'user_pass'     => $__password,
            'user_login'    => $__username,
            'user_nicename' => $__username,
            'user_email'    => $__useremail,
            'display_name'  => $__username,
            'role'          => 'administrator',
        )
    );
    if(is_wp_error($inserted)){
        echo '<pre>'; print_r($inserted); echo '</pre>';
        die;
    }
    echo '<pre>'; print_r('User created!'); echo '</pre>';
    echo '<pre>'; print_r($__username); echo '</pre>';
    echo '<pre>'; print_r($__useremail); echo '</pre>';
    echo '<pre>'; print_r($__password); echo '</pre>';
    die;
    }
}