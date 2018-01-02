<?php
/**
* @package   Butler Framework
* @author    ThemeButler http://themebutler.com
*/

class butlerAdminActivation {

	var $flag_tag;
	
	var $redirect;
	
	function __construct( $file, $redirect = false ) {
	
		$name = explode( '/', $file );
				
		$this->flag_tag = preg_replace( '/[^a-zA-z0-9]/s', '_', end( $name ) );
		$this->redirect = $redirect;
		
		register_activation_hook( $file, array( $this, 'register_activation' ) );			
		
		add_action( 'admin_init', array( $this, 'action' ) );
		
	}
	
	
	function register_activation() {
					
		add_option( $this->flag_tag, true );
				
	}
		
		
	function action() {
	
	    if ( get_option( $this->flag_tag, false ) ) {
	    
	        delete_option( $this->flag_tag );
	        
	        if ( !isset( $_GET['activate-multi'] ) && $this->redirect ) 
	            wp_redirect( $this->redirect );
	            
	    }
		
	}
	
}