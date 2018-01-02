<?php
/**
* @package   Butler Framework
* @author    ThemeButler http://themebutler.com
*/

class butlerPostMetaMetaBox {
	
	var $group;
	
	var $fields = array();
	
	var $post_types = array();
	
	var $args = array();
		
	function __construct( $group, $fields, $post_types, $args ) {
		
		$this->post_types = $post_types;
		
		/* stop here if we are not editing a page type defined in butler_register_options() */
		if ( !add_action( 'current_screen', array( $this, 'init' ) ) )
			return;
			
		$this->group = $group;
		$this->fields = $fields;
		$this->args = $args;
		
	}
	
	
	function init() {
		
		if ( !butler_is_admin_post( $this->post_types ) )
			return false;
								
		add_action( 'add_meta_boxes', array( $this, 'add' ) );
		add_action( 'save_post', array( $this, 'save' ) );
		add_filter( 'attachment_fields_to_save', array( $this, 'save_attachment' ), 10, 2);
		
		return true;
		
	}
	
	
	function add( $post_type ) {
	
		/* make sure the option component is loaded */
		butler_load_components( 'options' );
		
		if ( butler_get( 'load-assets', $this->args ) !== false )
			butler_enqueue_options_assets( false, $this->fields );
					
		add_meta_box( $this->group, $this->args['title'], array( $this, 'show' ), $post_type, $this->args['context'], $this->args['priority'] );

	}


	function show( $post ) {
				
		butler_fields( $this->fields, array( 'nonce' => $post->ID ) );		
		
	}
	
	
	function save( $post_id ) {

		/* we verify the nonce before proceeding */
		if ( !isset( $_POST['btr_fields_nonce'] ) || !wp_verify_nonce( $_POST['btr_fields_nonce'], $post_id ) )
			return $post_id;

		/* we verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		/* we check permissions */
		if ( !current_user_can( 'edit_post', $post_id ) )
			return $post_id;
			
		
		
		if ( butler_get( $this->group, $_POST ) )
			update_post_meta( $post_id, $this->group, $_POST[$this->group] );
			
	}
		
		
	function save_attachment( $attachment ) {
				
		/* we verify the nonce before proceeding */
		if ( !isset( $attachment['ID'] ) || !isset( $_POST['btr_fields_nonce'] ) || !wp_verify_nonce( $_POST['btr_fields_nonce'], $attachment['ID'] ) )
			return $attachment;

		/* we verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $attachment;

		/* we check permissions */
		if ( !current_user_can( 'edit_post', $attachment['ID'] ) )
			return $attachment;
		
		if ( butler_get( $this->group, $_POST ) )
			update_post_meta( $attachment['ID'], $this->group, $_POST[$this->group] );
			
		return $attachment;
			
	}	
		
}