<?php
/**
* @package   HeadwayRocket Framework
* @author    HeadwayRocket http://headwayrocket.com
*/

add_action( 'hwr_init', 'hwr_define_constant', 1 );

function hwr_define_constant() {
	
	/* HEADWAYROCKET_VERSION, HEADWAYROCKET_PATH, HEADWAYROCKET_URL are defined by the butler_load_component_once() function */
	define( 'HEADWAYROCKET_FUNCTIONS_PATH', HEADWAYROCKET_PATH . 'functions/' );
	define( 'HEADWAYROCKET_COMPONENTS_PATH', HEADWAYROCKET_PATH . 'components/' );
	define( 'HEADWAYROCKET_ADMIN_PATH', HEADWAYROCKET_PATH . 'admin/' );
	define( 'HEADWAYROCKET_ADMIN_CLASSES_PATH', HEADWAYROCKET_ADMIN_PATH . 'classes/' );
	define( 'HEADWAYROCKET_ADMIN_MODELS_PATH', HEADWAYROCKET_ADMIN_PATH . 'models/' );
	define( 'HEADWAYROCKET_ADMIN_PAGES_PATH', HEADWAYROCKET_ADMIN_PATH . 'pages/' );
	
	/* define url */
	define( 'HEADWAYROCKET_ADMIN_ASSETS_URL', HEADWAYROCKET_URL . 'admin/assets/' );
	define( 'HEADWAYROCKET_ADMIN_CSS_URL', HEADWAYROCKET_ADMIN_ASSETS_URL . 'css/' );
	define( 'HEADWAYROCKET_ADMIN_JS_URL', HEADWAYROCKET_ADMIN_ASSETS_URL . 'js/' );
	define( 'HEADWAYROCKET_ADMIN_IMAGES_URL', HEADWAYROCKET_ADMIN_ASSETS_URL . 'images/' );
	
	/* menu */
	define( 'HEADWAYROCKET_PARENT_MENU', 'hwr-dashboard' );
	
}


add_action( 'hwr_init', 'hwr_do_maintenance', 2 );

function hwr_do_maintenance() {

	require_once( HEADWAYROCKET_PATH . 'maintenance.php' );
	
	$option = get_option( 'hwr_framework_upgrade' );
	
	if ( empty( $option ) )
		$option = array();	
	
	/* set the tasks and callback function */
	$tasks = array(
		'dashboard_data' => 'hwr_framework_maintenance_dashboard_data',
		'admin_options_group_name' => 'hwr_framework_maintenance_admin_options_group_name'
	);
	
	foreach ( $tasks as $task => $callback ) {
	
		if ( isset( $option[$task] ) && $option[$task] )
			continue;
			
		call_user_func( $callback );
			
		$option[$task] = true;
		
		update_option( 'hwr_framework_upgrade', $option );
	
	}
	
}


add_action( 'hwr_init', 'hwr_load_components' );

function hwr_load_components() {

	butler_load_components( array( 'options' ) );
	
	butler_load( HEADWAYROCKET_ADMIN_CLASSES_PATH . 'admin', 'HwrAdmin', true );
	
	require_once( HEADWAYROCKET_FUNCTIONS_PATH . 'utils.php' );

}


/* we register the framework assets to make it available for users */
add_action( 'admin_enqueue_scripts', 'register_headwayroket_assets' );

function register_headwayroket_assets(  ) {

	$global_css_path = version_compare( get_bloginfo( 'version' ), '3.8', '>=' ) ? 'global' : 'depreciate-global' ;
	
	/* css */
	wp_register_style( 'hwr-global', HEADWAYROCKET_ADMIN_CSS_URL . $global_css_path . BUTLER_MIN_CSS . '.css', false, HEADWAYROCKET_VERSION );
	
}


do_action( 'hwr_init' );