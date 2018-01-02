<?php

function butler_register_term_meta( $group, $fields, $terms ) {
	
	global $butler_registered_term_meta;
		
	if ( !is_array( $butler_registered_term_meta ) )
		$butler_registered_term_meta = array();
			
	/* we determine if the groups is in a function and call it if it is the case */
	if ( is_callable( $fields ) )
		$fields = call_user_func( $fields );
		
	$_fields = array();
		
	foreach ( $fields as $id => $field ) {
		
		$_fields[$id] = $field;
		$_fields[$id]['id'] = $id;
		$_fields[$id]['group'] = $group;
		$_fields[$id]['context'] = 'term-meta';
	
	}
	
	if ( $registered = butler_get( $group, $butler_registered_term_meta ) )
		$butler_registered_term_meta[$group] = array_merge( $registered, $_fields );
	else															
		$butler_registered_term_meta[$group] = $_fields;
	
	/* let third party define where the term meta are displayed */
	$terms = apply_filters( $group . '_term_meta_placement', $terms );
			
	butler_load( BUTLER_COMPONENTS_PATH . 'term-meta/class' );
		
	new butlerTermMeta( $group, $_fields, $terms );
	
}


function butler_get_term_meta( $field, $group_name, $term_id = null ) {

	if ( !$term_id )
		$term_id = is_array( get_queried_object() ) ? get_queried_object()->term_id : butler_get( 'tag_ID' );
				
	$group_data = get_option( $group_name . '_term_meta' );
				
	/* try to get the value saved in the db. Dont use butler_get for these check */
	if ( is_array( $group_data ) && isset( $group_data[$term_id] ) && isset( $group_data[$term_id][$field] ) )
		$return = butler_clean_data( $group_data[$term_id][$field] );
	
	/* return default if possible */
	else
		$return = butler_get_term_meta_default( $field, $group_name );
		
	return apply_filters( 'butler_term_meta_' . $group_name . '_' . $field, $return );
	
}


function butler_get_term_meta_default( $field, $group_name ) {
	
	global $butler_registered_term_meta;
		
	$group_data = butler_get( $group_name, $butler_registered_term_meta );
	
	if ( !$group_data || !$_field = butler_get( $field, $group_data ) )
		return false;
		
	$value = isset( $_field['default'] ) ? $_field['default'] : false;
	
	return butler_clean_data( $value );
	
}


function butler_set_term_meta( $field, $value, $group_name, $term_id ) {
			
	if ( !$field || $value === null || !$group_name || !$group_name )
		return false;
			
	$group_data = get_option( $group_name . '_term_meta' );
			
	$group_data[$term_id][$field] = $value;
	
	update_option( $group_name . '_term_meta', $group_data );
	
	return true;
	
}