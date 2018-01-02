<?php

function butler_register_post_meta( $group, $fields, $post_types, $args = array() ) {

	global $butler_registered_post_meta;
	
	if ( !is_array( $butler_registered_post_meta ) )
		$butler_registered_post_meta = array();
	
	$defaults = array(
		'title' => __( 'Undefined', 'butler' ),
		'context' => 'normal',
		'priority' => 'high',
		'load-assets' => true
	);
		
	$args = array_merge( $defaults, $args );
	
	$_fields = array();
		
	foreach ( $fields as $id => $field ) {
		
		$_fields[$id] = $field;
		$_fields[$id]['id'] = $id;
		$_fields[$id]['group'] = $group;
		$_fields[$id]['context'] = 'post-meta';
	
	}
	
	if ( $registered = butler_get( $group, $butler_registered_post_meta ) )
		$_fields = array_merge( $registered, $_fields );
	
	$butler_registered_post_meta[$group] = $_fields;
	
	/* let third party define where the post meta are displayed */
	$post_types = apply_filters( $group . '_post_meta_placement', $post_types );
	
	butler_load( BUTLER_COMPONENTS_PATH . 'post-meta/class' );
		
	new butlerPostMetaMetaBox( $group, $_fields, $post_types, $args );
		

}


function butler_get_post_meta( $field, $group_name, $post_id = null ) {
		
	if ( !$post_id ) 
		$post_id = is_object( get_post() ) ? get_the_ID() : butler_get( 'post' );
		
	$group_data = butler_maybe_array( get_post_meta( $post_id, $group_name ) );
	
	reset( $group_data );
	$group_data = current( $group_data );
						
	/* try to get the value saved in the db. Dont use butler_get for this check */
	if ( isset( $group_data[$field] ) )
		$return = butler_clean_data( $group_data[$field] );
	
	/* return default if possible */
	else
		$return = butler_get_post_meta_default( $field, $group_name );
		
	return apply_filters( 'butler_post_meta_' . $group_name . '_' . $field, $return );
	
}


function butler_get_post_meta_default( $field, $group_name ) {
	
	global $butler_registered_post_meta;
		
	$group_data = butler_get( $group_name, $butler_registered_post_meta );
	
	if ( !$group_data || !$_field = butler_get( $field, $group_data ) )
		return false;
		
	$value = isset( $_field['default'] ) ? $_field['default'] : false;
	
	return butler_clean_data( $value );
	
}