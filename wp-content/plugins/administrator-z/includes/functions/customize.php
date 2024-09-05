<?php
function adminz_enable_zalo_support(){
    add_action( 'customize_register', function ($wp_customize) {
        $wp_customize->add_setting(
            'follow_zalo', array( 'default' => 'https://zalo.me/#' )
        );
        $wp_customize->add_control( 'follow_zalo', array(
            'label'   => __( 'Zalo', 'administrator-z' ),
            'section' => 'follow',
        ) );
        $wp_customize->add_setting(
            'follow_skype', array( 'default' => '#' )
        );
        $wp_customize->add_control( 'follow_skype', array(
            'label'   => __( 'Skype', 'administrator-z' ),
            'section' => 'follow',
        ) );
        $wp_customize->add_setting(
            'follow_whatsapp', array( 'default' => 'https://wa.me/#' )
        );
        $wp_customize->add_control( 'follow_whatsapp', array(
            'label'   => __( 'Whatsapp', 'administrator-z' ),
            'section' => 'follow',
        ) );
    } );
}