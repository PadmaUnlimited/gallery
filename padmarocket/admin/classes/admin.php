<?php
/**
* @package   PadmaRocket Framework
* @author    PadmaRocket http://Padmarocket.com
*/

class PUR_Admin {

	var $name;
	
	var $token;
					
	var $page_token;

	function __construct() {
					
		$this->token = 'pur';
		$this->name = 'Padmarocket';
		
		$this->register_options();
										
		if ( butler_get_option( 'admin_bar_display_menu', 'padma_framework' ) )
			//add_action( 'admin_bar_menu', array( $this, 'add_admin_pur' ), 76 );

		/* add js global var to head */
		add_action( 'padma_visual_editor_head', array( &$this, 'padma_wp_head_script' ) );
		
		/* we prevent all the admin stuff from loading in the frontend */
		if ( !is_admin() )
			return;
						
		//add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		add_action( 'admin_head', array( &$this, 'padma_wp_head_script' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_assets') );
				
	}
	
	
	function enqueue_assets() {
		
		wp_enqueue_style( 'pur-global' );
		
		/* we load the butler ui css for the dashboard */
		if ( butler_get( 'page' ) == $this->page_token ) {
			
			wp_enqueue_style( 'btr-uikit' );
			wp_enqueue_style( 'btr-ui' );
			wp_enqueue_style( 'pur-page', PADMAROCKET_ADMIN_CSS_URL . 'pages' . BUTLER_MIN_CSS . '.css', false, PADMAROCKET_VERSION );
			wp_enqueue_script( 'btr-uikit' );
			
		}
		
	}
		
	function register_options() {
	
		$options = array(
			'admin_bar_display_menu' => array(
				'label' => 'Admin Bar Menu',
				'type' => 'checkbox',
				'default' => true,
				'checkbox-label' => 'Show PadmaRocket Menu',
				'description' => 'Set whether you would like to show the PadmaRocket menu in the top admin bar.',
			)
		);
		
		butler_register_options( 'padma_framework' , $options, PADMAROCKET_PARENT_MENU, array( 'title' => 'Menu' ) );
	
	}
	
	function menu_position( $position ) {
	
		global $menu;
			
		if ( array_key_exists( $position, $menu ) )
			return $this->menu_position( $position + 1 );
				
		return $position;
		
	}
	
	
	function admin_menu() {
						
		add_menu_page( $this->name, $this->name, 'manage_options', PADMAROCKET_PARENT_MENU, array( $this, 'display' ), 'dashicons-pur-icon-rocket', $this->menu_position( 50 ) );
		
		$hook = add_submenu_page( PADMAROCKET_PARENT_MENU, 'Dashboard', 'Dashboard', 'manage_options', PADMAROCKET_PARENT_MENU, array( $this, 'display' ) );
		
		add_action( 'load-' . $hook, array( $this, 'admin_load' ) );
		
		/* explode $hook before the end function for php 5.4 strict standards */		
		$hook = explode( '_', $hook );
		$this->page_token = end( $hook );
	
	}
	
	
	function admin_load() {
	
		$this->model = butler_load( array( PADMAROCKET_ADMIN_MODELS_PATH . 'admin-dashboard' => 'PUR_AdminModel' ) );
	
	}


	function add_admin_pur() {
	
		global $wp_admin_bar;
		
		$wp_admin_bar->add_menu( array(
			'id' => 'Padmarocket', 
			'title' => 'PadmaRocket', 
			'href' => add_query_arg( array( 'page' => PADMAROCKET_PARENT_MENU ), admin_url( 'admin.php' ) )
		) );
		
	}
		
		
	function padma_wp_head_script() {
	
		echo '<script type="text/javascript">';
			echo 'var pur = { assetsLoaded: [], PadmaVersion: "' . PADMA_VERSION . '" };';
			echo 'padma_is_admin = "' .  is_admin() . '";';
			echo 'padma_admin_url = "' .  admin_url() . '";';
			echo 'padma_loader_grey = "' . PADMAROCKET_ADMIN_IMAGES_URL . 'loader-grey.gif' . '";';
		echo '</script>';
					
	}
	
	
	function display() {
		
		require_once( PADMAROCKET_ADMIN_PAGES_PATH . 'admin-dashboard.php' );
											
	}
	
	/**
	 * Deprecated. Used to get the framework parent menu token.
	 *
	 * @since 1.0.0
	 * @deprecated 1.1.0
	 */
	static function parent_menu() {
	
		return PADMAROCKET_PARENT_MENU;
	
	}
			
}