<?php

function butler_register_wp_customize_options( $group, $fields, $args = array() ) {
	
	global $butler_registered_wp_customizer;
		
	if ( !is_array( $butler_registered_wp_customizer ) )
		$butler_registered_wp_customizer = array();
			
	/* we determine if the groups is in a function and call it if it is the case */
	if ( is_callable( $fields ) )
		$fields = call_user_func( $fields );
		
	$defaults = array(
		'title' => null,
		'description' => null,
		'priority' => null
	);
		
	$args = array_merge( $defaults, $args );
	
	$_fields = array();
		
	foreach ( $fields as $id => $field ) {
		
		$_fields[$id] = $field;
		$_fields[$id]['id'] = $id;
		$_fields[$id]['group'] = $group;
		$_fields[$id]['context'] = 'wp-customize';
	
	}
	
	if ( $registered = butler_get( $group, $butler_registered_wp_customizer ) )
		$_fields = array_merge( $registered, $_fields );
	
	$butler_registered_wp_customizer[$group] = $_fields;	
		
	/* stop here if we are not on the customize page */	
	if ( !class_exists( 'WP_Customize_Manager' ) )
		return;
		
	butler_load( BUTLER_COMPONENTS_PATH . 'wp-customize/class' );
		
	new Butler_WP_Customize( $group, $_fields, $args );
	
	
}


function butler_get_theme_mod( $field, $group_name ) {
	
	$group_data = get_theme_mod( $group_name );
	
	/* try to get the value saved in the db. Dont use butler_get for this check */
	if ( is_array( $group_data ) && isset( $group_data[$field] ) )
		return butler_clean_data( $group_data[$field] );
	
	/* return default if possible */
	else
		return butler_get_theme_mod_default( $field, $group_name );
	
}


function butler_get_theme_mod_default( $field, $group_name ) {
	
	global $butler_registered_wp_customizer;
		
	$group_data = butler_get( $group_name, $butler_registered_wp_customizer );
	
	if ( !$group_data || !$_field = butler_get( $field, $group_data ) )
		return false;
		
	$value = isset( $_field['default'] ) ? $_field['default'] : false;
	
	return butler_clean_data( $value );
	
}
