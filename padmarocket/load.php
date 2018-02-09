<?php
/**
* @package   PadmaRocket Framework
* @author    PadmaRocket http://Padmarocket.com
*/

add_action( 'padma_init', 'padma_define_constant', 1 );

function padma_define_constant() {
	
	/* PADMAROCKET_VERSION, PADMAROCKET_PATH, PADMAROCKET_URL are defined by the butler_load_component_once() function */
	define( 'PADMAROCKET_FUNCTIONS_PATH', PADMAROCKET_PATH . 'functions/' );
	define( 'PADMAROCKET_COMPONENTS_PATH', PADMAROCKET_PATH . 'components/' );
	define( 'PADMAROCKET_ADMIN_PATH', PADMAROCKET_PATH . 'admin/' );
	define( 'PADMAROCKET_ADMIN_CLASSES_PATH', PADMAROCKET_ADMIN_PATH . 'classes/' );
	define( 'PADMAROCKET_ADMIN_MODELS_PATH', PADMAROCKET_ADMIN_PATH . 'models/' );
	define( 'PADMAROCKET_ADMIN_PAGES_PATH', PADMAROCKET_ADMIN_PATH . 'pages/' );
	
	/* define url */
	define( 'PADMAROCKET_ADMIN_ASSETS_URL', PADMAROCKET_URL . 'admin/assets/' );
	define( 'PADMAROCKET_ADMIN_CSS_URL', PADMAROCKET_ADMIN_ASSETS_URL . 'css/' );
	define( 'PADMAROCKET_ADMIN_JS_URL', PADMAROCKET_ADMIN_ASSETS_URL . 'js/' );
	define( 'PADMAROCKET_ADMIN_IMAGES_URL', PADMAROCKET_ADMIN_ASSETS_URL . 'images/' );
	
	/* menu */
	define( 'PADMAROCKET_PARENT_MENU', 'pur-dashboard' );
	
}


add_action( 'padma_init', 'padma_do_maintenance', 2 );

function padma_do_maintenance() {

	require_once( PADMAROCKET_PATH . 'maintenance.php' );
	
	$option = get_option( 'padma_framework_upgrade' );
	
	if ( empty( $option ) )
		$option = array();	
	
	/* set the tasks and callback function */
	$tasks = array(
		'dashboard_data' => 'padma_framework_maintenance_dashboard_data',
		'admin_options_group_name' => 'padma_framework_maintenance_admin_options_group_name'
	);
	
	foreach ( $tasks as $task => $callback ) {
	
		if ( isset( $option[$task] ) && $option[$task] )
			continue;
			
		call_user_func( $callback );
			
		$option[$task] = true;
		
		update_option( 'padma_framework_upgrade', $option );
	
	}
	
}


add_action( 'padma_init', 'padma_load_components' );

function padma_load_components() {

	butler_load_components( array( 'options' ) );
	
	butler_load( PADMAROCKET_ADMIN_CLASSES_PATH . 'admin', 'PUR_Admin', true );
	
	require_once( PADMAROCKET_FUNCTIONS_PATH . 'utils.php' );

}


/* we register the framework assets to make it available for users */
add_action( 'admin_enqueue_scripts', 'register_Padmaroket_assets' );

function register_Padmaroket_assets(  ) {

	$global_css_path = version_compare( get_bloginfo( 'version' ), '3.8', '>=' ) ? 'global' : 'depreciate-global' ;
	
	/* css */
	wp_register_style( 'pur-global', PADMAROCKET_ADMIN_CSS_URL . $global_css_path . BUTLER_MIN_CSS . '.css', false, PADMAROCKET_VERSION );
	
}


do_action( 'padma_init' );