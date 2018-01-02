<?php
/**
* @package   HeadwayRocket Framework
* @author    HeadwayRocket http://headwayrocket.com
*/

class HwrAdmin {

	var $name;
	
	var $token;
					
	var $page_token;

	function __construct() {
					
		$this->token = 'hwr';
		$this->name = 'Headwayrocket';
		
		$this->register_options();
										
		if ( butler_get_option( 'admin_bar_display_menu', 'hwr_framework' ) )
			add_action( 'admin_bar_menu', array( $this, 'add_admin_hwr' ), 76 );

		/* add js global var to head */
		add_action( 'headway_visual_editor_head', array( &$this, 'hwr_wp_head_script' ) );
		
		/* we prevent all the admin stuff from loading in the frontend */
		if ( !is_admin() )
			return;
						
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		add_action( 'admin_head', array( &$this, 'hwr_wp_head_script' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_assets') );
				
	}
	
	
	function enqueue_assets() {
		
		wp_enqueue_style( 'hwr-global' );
		
		/* we load the butler ui css for the dashboard */
		if ( butler_get( 'page' ) == $this->page_token ) {
			
			wp_enqueue_style( 'btr-uikit' );
			wp_enqueue_style( 'btr-ui' );
			wp_enqueue_style( 'hwr-page', HEADWAYROCKET_ADMIN_CSS_URL . 'pages' . BUTLER_MIN_CSS . '.css', false, HEADWAYROCKET_VERSION );
			wp_enqueue_script( 'btr-uikit' );
			
		}
		
	}
		
	function register_options() {
	
		$options = array(
			'admin_bar_display_menu' => array(
				'label' => 'Admin Bar Menu',
				'type' => 'checkbox',
				'default' => true,
				'checkbox-label' => 'Show HeadwayRocket Menu',
				'description' => 'Set whether you would like to show the HeadwayRocket menu in the top admin bar.',
			)
		);
		
		butler_register_options( 'hwr_framework' , $options, HEADWAYROCKET_PARENT_MENU, array( 'title' => 'Menu' ) );
	
	}
	
	function menu_position( $position ) {
	
		global $menu;
			
		if ( array_key_exists( $position, $menu ) )
			return $this->menu_position( $position + 1 );
				
		return $position;
		
	}
	
	
	function admin_menu() {
						
		add_menu_page( $this->name, $this->name, 'manage_options', HEADWAYROCKET_PARENT_MENU, array( $this, 'display' ), 'dashicons-hwr-icon-rocket', $this->menu_position( 50 ) );
		
		$hook = add_submenu_page( HEADWAYROCKET_PARENT_MENU, 'Dashboard', 'Dashboard', 'manage_options', HEADWAYROCKET_PARENT_MENU, array( $this, 'display' ) );
		
		add_action( 'load-' . $hook, array( $this, 'admin_load' ) );
		
		/* explode $hook before the end function for php 5.4 strict standards */		
		$hook = explode( '_', $hook );
		$this->page_token = end( $hook );
	
	}
	
	
	function admin_load() {
	
		$this->model = butler_load( array( HEADWAYROCKET_ADMIN_MODELS_PATH . 'admin-dashboard' => 'HwrAdminModel' ) );
	
	}


	function add_admin_hwr() {
	
		global $wp_admin_bar;
		
		$wp_admin_bar->add_menu( array(
			'id' => 'headwayrocket', 
			'title' => 'HeadwayRocket', 
			'href' => add_query_arg( array( 'page' => HEADWAYROCKET_PARENT_MENU ), admin_url( 'admin.php' ) )
		) );
		
	}
		
		
	function hwr_wp_head_script() {
	
		echo '<script type="text/javascript">';
			echo 'var hwr = { assetsLoaded: [], headwayVersion: "' . HEADWAY_VERSION . '" };';
			echo 'hwr_is_admin = "' .  is_admin() . '";';
			echo 'hwr_admin_url = "' .  admin_url() . '";';
			echo 'hwr_loader_grey = "' . HEADWAYROCKET_ADMIN_IMAGES_URL . 'loader-grey.gif' . '";';
		echo '</script>';
					
	}
	
	
	function display() {
		
		require_once( HEADWAYROCKET_ADMIN_PAGES_PATH . 'admin-dashboard.php' );
											
	}
	
	/**
	 * Deprecated. Used to get the framework parent menu token.
	 *
	 * @since 1.0.0
	 * @deprecated 1.1.0
	 */
	static function parent_menu() {
	
		return HEADWAYROCKET_PARENT_MENU;
	
	}
			
}