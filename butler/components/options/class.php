<?php
/**
* @package   Butler Framework
* @author    ThemeButler http://themebutler.com
*/

class butlerOptionsMetaBox {
	
	var $group;
	
	var $pages;
	
	var $fields;
	
	var $args;
		
	function __construct( $group, $fields, $pages, $args ) {
			
		$this->group = $group;
		$this->fields = $fields;
		$this->pages = butler_maybe_array( $pages );
		$this->args = $args;
				
		add_action( 'admin_init', array( $this, 'add' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		
		/* add the necessary script for the post meta to work */
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_script' ) );
		
		/* fire save options function. Do not wrap in admin_init action */
		$this->save();

	}
	
	
	function enqueue_script( ) {
	
		wp_enqueue_script( 'postbox' );
		
	}
	
	
	function add( ) {
					
		foreach ( $this->pages as $page )						
			add_meta_box( $this->args['metabox_id'], $this->args['title'], array( $this, 'show' ), $page, $this->args['context'], $this->args['priority'] );

	}


	function show() {
		
		do_action( 'butler_options_meta_box_content', $this->group, $this->fields );			
		
	}
	
	
	function admin_notices() {
	
		static $once = false;
	
		if ( !butler_post( 'btr_submit_options' ) || $once )
			return;
			
		if ( !wp_verify_nonce( butler_post( 'btr_admin_options_nonce' ), 'btr_admin_options_nonce' ) )
			echo '<div id="message" class="error"><p>' . __( 'Settings could not be saved, please try again.', 'butler' ) . '</p></div>' . "\n";
		else
			echo '<div id="message" class="success updated"><p>' . __( 'Settings saved successfully!', 'butler' ) . '</p></div>' . "\n";
		
		$once = true;
				
	}
	
		
	function save() {
	
		static $once = false;
	
		if ( !butler_post( 'btr_submit_options' ) || $once )
			return;
		
		$group_names = butler_post( 'btr_options_group' ); 
					
		/* we save the options if the form contains options */
		if ( !$group_names )
			return;
						
		foreach ( butler_maybe_array( $group_names ) as $group_name ) {
		
			if ( !butler_post( $group_name ) )
				continue;
		
			foreach ( butler_post( $group_name ) as $option => $value )
				butler_set_option( $option, $value, $group_name );
				
		}
		
		$once = true;
				
		return true;
		
	}	
	
}