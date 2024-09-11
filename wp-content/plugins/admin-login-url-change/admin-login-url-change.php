<?php
/**
 * Plugin Name:       Admin login URL Change
 * Plugin URI:        https://wordpress.org/plugins/admin-login-url-change/
 * Description:       Allows you to Change your WordPress WebSite Login URL.
 * Version:           1.0.8
 * Requires at least: 4.7
 * Tested up to: 6.5
 * Requires PHP:      5.3
 * Author:            jahidcse
 * Author URI:        https://profiles.wordpress.org/jahidcse/


 */

/**
* OOP Class WP_Login_Change
*/

class WP_Login_Change {

public function __construct() {

$file_data = get_file_data( __FILE__, array( 'Version' => 'Version' ) );

$this->plugin                           = new stdClass;
$this->plugin->name                     = 'admin-login-url-change';
$this->plugin->displayName              = 'Admin login URL Change';
$this->plugin->version                  = $file_data['Version'];
$this->plugin->folder                   = plugin_dir_path( __FILE__ );
$this->plugin->url                      = plugin_dir_url( __FILE__ );

/**
* Hooks
*/

add_action('admin_menu', array($this,'admin_login_url_change_add_page'));
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array($this,'admin_login_url_change_page_settings'));
add_action('admin_enqueue_scripts', array($this,'admin_login_url_change_css'));
add_action('login_head', array($this,'admin_login_url_change_redirect_error_page'));
add_action('init', array($this,'admin_login_url_change_redirect_success_page'));
add_action('wp_logout', array($this,'admin_login_url_change_redirect_login_page'));
add_action('wp_login_failed', array($this,'admin_login_url_change_redirect_failed_login_page'));

}

/**
* Admin Menu
*/

function admin_login_url_change_add_page() {
     add_submenu_page( 'options-general.php', $this->plugin->displayName, $this->plugin->displayName, 'manage_options', $this->plugin->name, array( &$this, 'settingsPanel' ) );
}

/**
* Activated Plugin Setting
*/

function admin_login_url_change_activated( $plugin ) {
  if ( plugin_basename( __FILE__ ) == $plugin ) {
    wp_redirect( admin_url( 'options-general.php?page='.$this->plugin->name ) );
    die();
  }
}


/**
* Plugin Setting Page Linked
*/

function admin_login_url_change_page_settings( $links ) {
  $link = sprintf( "<a href='%s' style='color:#2271b1;'>%s</a>", admin_url( 'options-general.php?page='.$this->plugin->name ), __( 'Settings', 'admin-login-url-change' ) );
  array_push( $links, $link );

  return $links;
}

/**
* Setting Page and data store
*/

function settingsPanel() {

if ( ! current_user_can( 'manage_options' ) ) {
  wp_die( esc_html__( 'Sorry, you are not allowed to access this page.', 'admin-login-url-change' ) );
}

  if(isset($_REQUEST['but_submit'])){
    // Check if a nonce is valid.
    if (  !isset( $_POST['jh_login_url_nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jh_login_url_nonce'] ) ), 'jh_login_url_nonce_action' ) ) {
      return;
    }
    
    update_option( 'jh_new_login_url', sanitize_text_field($_REQUEST['jh_new_login_url']) );
    $this->message = esc_html__( 'Settings Saved.', 'admin-login-url-change' );
  }
  $this->admin_login_url_info = array(
    'jh_new_login_url' => esc_html( wp_unslash( get_option( 'jh_new_login_url' ) ) ),
  );
  include_once $this->plugin->folder.'/view/settings.php';

}


/**
* Admin Include CSS
*/

function admin_login_url_change_css(){
  wp_enqueue_style( 'admin_login_url_change_css', plugins_url('/assets/css/style.css', __FILE__), false, $this->plugin->version);
}



/**
* Redirect Error Page
*/

function admin_login_url_change_redirect_error_page(){

  $jh_new_login = wp_unslash(get_option( 'jh_new_login_url' ));
  if(!empty($jh_new_login)){
    if(strpos($_SERVER['REQUEST_URI'], $jh_new_login) === false){
      wp_safe_redirect( home_url( '404' ), 302 );
      exit(); 
    } 
  }
}

/**
* Redirect Success Page
*/

function admin_login_url_change_redirect_success_page(){
  $jh_new_login = wp_unslash(get_option( 'jh_new_login_url' ));
  if(!empty($jh_new_login)){
    $jh_wp_admin_login_current_url_path=wp_parse_url($_SERVER['REQUEST_URI']);

    if($jh_wp_admin_login_current_url_path["path"] == '/'.$jh_new_login){
      wp_safe_redirect(home_url("wp-login.php?$jh_new_login&redirect=false"));
      exit(); 
    }
  }
}

/**
* Redirect Login Page
*/

function admin_login_url_change_redirect_login_page() {
  $jh_new_login = wp_unslash(get_option( 'jh_new_login_url' ));
  if(!empty($jh_new_login)){
    wp_safe_redirect(home_url("wp-login.php?$jh_new_login&redirect=false"));
    exit();
  }
}

/**
* Redirect Login Page for Login Failed
*/

function admin_login_url_change_redirect_failed_login_page($username) {
  $jh_new_login = wp_unslash(get_option( 'jh_new_login_url' ));
  if(!empty($jh_new_login)){
    wp_safe_redirect(home_url("wp-login.php?$jh_new_login&redirect=false"));
    exit();
  }
}


}

$WP_Login_Change = new WP_Login_Change();