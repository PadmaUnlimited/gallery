<?php
/**
* @package   Butler Framework
* @author    ThemeButler http://themebutler.com
*/

add_action( 'butler_init', 'butler_define_constant' );

function butler_define_constant() {
	
	/* BUTLER_VERSION, BUTLER_PATH, BUTLER_URL are defined by the butler_load_component_once() function */
	
	/* maybe already defined constant */
	defined( 'BUTLER_MIN_CSS' ) or define( 'BUTLER_MIN_CSS', WP_DEBUG ? '' : '.min' );
	defined( 'BUTLER_MIN_JS' ) or define( 'BUTLER_MIN_JS', WP_DEBUG ? '' : '.min' );
	
	/* paths */
	define( 'BUTLER_FUNCTIONS_PATH', BUTLER_PATH . 'functions/' );
	define( 'BUTLER_COMPONENTS_PATH', BUTLER_PATH . 'components/' );
	define( 'BUTLER_CLASSES_PATH', BUTLER_PATH . 'classes/' );
	
	
	/* urls */
	define( 'BUTLER_ASSETS_URL', BUTLER_URL . 'assets/' );
	define( 'BUTLER_CSS_URL', BUTLER_ASSETS_URL . 'css/' );
	define( 'BUTLER_JS_URL', BUTLER_ASSETS_URL . 'js/' );
	define( 'BUTLER_IMAGES_URL', BUTLER_ASSETS_URL . 'images/' );
}


add_action( 'butler_init', 'butler_load_core_components' );

function butler_load_core_components() {
	
	require_once( BUTLER_FUNCTIONS_PATH . 'utils.php' );
	require_once( BUTLER_FUNCTIONS_PATH . 'load.php' );
	require_once( BUTLER_PATH . 'depreciate.php' );

}


/* we register the framework assets to make it available for users */
add_action( 'admin_enqueue_scripts', 'register_butler_assets' );
add_action( 'customize_controls_enqueue_scripts', 'register_butler_assets' );

function register_butler_assets( $dependencies = null ) {
	
	/* css */
	wp_register_style( 'btr-ui', BUTLER_CSS_URL . 'ui' . BUTLER_MIN_CSS . '.css', false, BUTLER_VERSION );
	wp_register_style( 'btr-uikit', BUTLER_CSS_URL . 'uikit' . BUTLER_MIN_CSS . '.css', false, BUTLER_VERSION );
	wp_register_style( 'btr-fields', BUTLER_CSS_URL . 'fields' . BUTLER_MIN_CSS . '.css', false, BUTLER_VERSION );
	wp_register_style( 'btr-select2', BUTLER_CSS_URL . 'select2' . BUTLER_MIN_CSS . '.css', false, BUTLER_VERSION );
	wp_register_style( 'btr-wp-customize-options', BUTLER_CSS_URL . 'wp-customize-options' . BUTLER_MIN_CSS . '.css', false, BUTLER_VERSION );
	
	/* js */
	wp_register_script( 'btr-fields', BUTLER_JS_URL . 'fields' . BUTLER_MIN_JS . '.js', array( 'jquery' ), BUTLER_VERSION );
	wp_register_script( 'btr-media', BUTLER_JS_URL . 'media' . BUTLER_MIN_JS . '.js', array( 'jquery' ), BUTLER_VERSION );
	wp_register_script( 'btr-select2', BUTLER_JS_URL . 'select2' . BUTLER_MIN_JS . '.js', array( 'jquery' ), BUTLER_VERSION );
	wp_register_script( 'btr-uikit', BUTLER_JS_URL . 'uikit' . BUTLER_MIN_JS . '.js', array( 'jquery' ), BUTLER_VERSION );
	wp_register_script( 'btr-wp-customize-options', BUTLER_JS_URL . 'wp-customize-options' . BUTLER_MIN_JS . '.js', array( 'jquery' ), BUTLER_VERSION );
	
}


do_action( 'butler_init' );