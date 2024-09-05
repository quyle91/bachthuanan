<?php
$wordpress_path = dirname( __FILE__ ) . '/../../../../';
require_once ( $wordpress_path . 'wp-load.php' );

if( !is_user_logged_in()){
	switch ( php_sapi_name() ) {
		case 'cli':
			# code...
			break;
		default:
			echo "The script was run from a webserver, or something else"; die;
			break;
	}
}


// maybe securiry problem?

if ( empty( $_GET ) ) {
	$default = ADMINZ_DIR_URL . 'cron/crawl.php';
	$text    = [ 
		'*note'            => '2024/09/07',
		'**note'           => 'Test before run cron',
		'post'             => add_query_arg(
			[ 
				'action' => 'run_adminz_import_from_post',
				'url'    => 'https://demos.flatsome.com/2015/10/13/velkommen-til-bloggen-min/',
			],
			$default
		),
		'category'         => add_query_arg(
			[ 
				'action' => 'run_adminz_import_from_category',
				'url'    => 'https://demos.flatsome.com/blog/',
			],
			$default
		),
		'product'          => add_query_arg(
			[ 
				'action' => 'run_adminz_import_from_product',
				'url'    => 'https://demos.flatsome.com/shop/shoes/all-star-canvas-hi-converse/',
			],
			$default
		),
		'product_category' => add_query_arg(
			[ 
				'action' => 'run_adminz_import_from_product_category',
				'url'    => 'https://demos.flatsome.com/product-category/men/',
			],
			$default
		),
	];
	echo "<pre>"; print_r( $text ); echo "</pre>"; die;
}


$action = '';
if ( isset( $_GET['action'] ) ) {
	$action = esc_attr( $_GET['action'] );
}

$url = '';
if ( isset( $_GET['url'] ) ) {
	$url = esc_attr( $_GET['url'] );
}

if ( $url and $action ) {


	$_Crawl = new \Adminz\Helper\Crawl( [ 
		'action' => $action,
		'url'    => $url,
	] );
	$_Crawl->set_return_type( 'json' );
	$return = $_Crawl->init();
	$return = array_filter(explode("\r\n", $return));
	if(!empty($return)){
		$log_file = dirname( __FILE__ ) . "/crawl.data";
		foreach ($return as $key => $line) {
			error_log( $line . ",\r\n", 3, $log_file );
			echo $line;
		}
	}
	
} else {
	echo 'Not enough url or action';
	die;
}