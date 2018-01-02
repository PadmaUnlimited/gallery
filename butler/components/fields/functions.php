<?php

function butler_fields( $fields, $args = array() ) {

	butler_load( BUTLER_COMPONENTS_PATH . 'fields/display' );

	$defaults = array(
		'nonce' => false
	);
	
	$args = array_merge( $defaults, $args );
				
	do_action( 'butler_fields', $fields, $args );
				
}


function butler_field( $field ) {
		
	butler_load( BUTLER_COMPONENTS_PATH . 'fields/display' );
	
	do_action( 'butler_field', $field );
	
}


function butler_get_input( $field ) {

	if ( !$context = butler_get( 'context', $field ) )
		return false;
		
	switch ( $context ) {
		
		case 'option':
			return butler_get_option( $field['id'], $field['group'] );
		break;
		
	    case 'post-meta':
	        return butler_get_post_meta( $field['id'], $field['group'] );
	    break;
	    
	    case 'term-meta':
	        return butler_get_term_meta( $field['id'], $field['group'] );
	    break;
	   
	    case 'wp-customize':
	        return butler_get_theme_mod( $field['id'], $field['group'] );
	    break;
	    
	    default:
	    	return false;    
	
	}

}


function butler_parse_field_callback( $field ) {
	
	if ( !butler_multi_array_key_exists( 'callback_function', $field ) )
		return $field;
		
	foreach ( $field as $key => $value ) {
	
		if ( !$callback = butler_get( 'callback_function', $value ) )
			continue;
				
		/* is it a function */
		if ( $function = butler_get( 'function', $callback ) )			
			$field[$key] = call_user_func( $function, ( $args = butler_get( 'args', $callback ) ? $args : '' ) );
		else
			continue;
		
	}
	
	return $field;
	
}


add_action( 'admin_head', 'butler_fields_wp_head' );

function butler_fields_wp_head() {

	$fields = wp_parse_args( wp_get_referer() );
	
	if ( isset( $fields['btr_action'] ) && $fields['btr_action'] === 'done_editing_media' ) {
	
		echo '<script type="text/javascript">self.parent.tb_remove();</script>';
		echo '<style type="text/css">body { display:none; }</style>';
	}
						
}


add_action( 'admin_enqueue_scripts', 'butler_fields_assets' );

function butler_fields_assets() {

	if ( butler_get( 'btr_media_editor' ) == true ) {
		
		wp_enqueue_style( 'btr-media-editor', BUTLER_CSS_URL . 'media-editor' . BUTLER_MIN_CSS . '.css', false, BUTLER_VERSION ); 
		wp_enqueue_script( 'btr-media-editor', BUTLER_JS_URL . 'media-editor' . BUTLER_MIN_JS . '.js', array( 'jquery' ), BUTLER_VERSION );
		wp_localize_script( 'btr-media-editor', 'btrMediaEditor', array( 'loaderUrl' => BUTLER_IMAGES_URL . 'btr-loader-split-infinite.gif' ) );
					
	}
	
}


add_action( 'wp_ajax_butler_admin_fields_repeater', 'butler_admin_fields_repeater' );

function butler_admin_fields_repeater() {

	/* we call the butler options since it wasn't globally loaded */
	butler_load_components( 'options' );
	
	butler_load( BUTLER_COMPONENTS_PATH . 'fields/display' );
	
	$field = $_POST['option'];
	
	/* set to fields to be outputed as raw */
	$field['raw'] = true;
		
	echo butler_do_field( $field, $_POST['count'] );
	
	exit;

}