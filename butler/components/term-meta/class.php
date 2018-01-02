<?php
/**
* @package   Butler Framework
* @author    ThemeButler http://themebutler.com
*/

class butlerTermMeta {
	
	var $group;
	
	var $fields;
	
	var $terms;
	
	function __construct( $group, $fields, $terms ) {
		
		$this->terms = $terms;
		
		/* stop here if we are not editing a page type defined in butler_register_options() */
		if ( !add_action( 'current_screen', array( $this, 'init' ) ) )
			return;
			
		$this->group = $group;
		$this->fields = $fields;
		
	}
	
	
	function init() {
		
		if ( !butler_is_admin_term( $this->terms ) )
			return false;
												
		/* only load assets and display on term edit to avoid having this loading on the term summary page */
		if ( butler_get( 'tag_ID' ) ) :
						
			butler_load_components( 'options' );
			
			butler_enqueue_options_assets( false, $this->fields );
					
			add_action( butler_get( 'taxonomy' ). '_edit_form', array( $this, 'display' ) );
		
		endif;
		
		add_action( 'edit_term', array( $this, 'save' ) );
		add_action( 'delete_term', array( $this, 'delete' ) );
				
	}
	
	
	function display( $tag ) {
	
		butler_fields( $this->fields, array( 'nonce' => $tag->term_id ) );
	
	}
	
	
	function save( $term_id ) {
			
		/* we verify the nonce before proceeding */
		if ( !isset( $_POST['btr_fields_nonce'] ) || !wp_verify_nonce( $_POST['btr_fields_nonce'], $term_id ) )
			return $term_id;
		
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
			return $term_id;
			 		
		/* we check permissions */
		if ( !current_user_can( 'unfiltered_html' ) )
			return $term_id;
			
		if ( $fields = butler_post( $this->group ) )
			foreach ( $fields as $field => $value )
				butler_set_term_meta( $field, $value, $this->group, $term_id );
				
		return true;

	}
	
	
	function delete( $term_id ) {
				
		$group = get_option( $this->group );
		
		/* only update the group which contain data for this term */
		if ( !isset( $group[$term_id] ) )
			return;
		
		/* unset the options from the group data before we updating the it */
		unset( $group[$term_id] );
				
		/* we finally update the group without the option removed */
		update_option( $this->group, $group );
		
			
	}
	
	
}