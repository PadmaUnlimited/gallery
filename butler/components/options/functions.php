<?php

function butler_register_options( $group, $options, $pages, $args = array() ) {
					
	global $butler_registered_options;
	
	if ( !is_array( $butler_registered_options ) )
		$butler_registered_options = array();
		
	/* we determine if the groups is in a function and call it if it is the case */
	if ( is_callable( $options ) )
		$options = call_user_func( $options );
		
	$defaults = array(
		'title' => __( 'Undefined', 'butler' ),
		'context' => 'normal',
		'priority' => 'high',
		'load_assets' => true,
		'metabox_id' => $group
	);
		
	$args = array_merge( $defaults, $args );
		
	$options = apply_filters( $group . '_registered_options', $options );
	
	if ( !is_array( $options ) )
		return false;
	
	$_fields = array();
		
	foreach ( $options as $id => $option ) {
		
		$_fields[$id] = $option;
		$_fields[$id]['id'] = $id;
		$_fields[$id]['group'] = $group;
		$_fields[$id]['context'] = 'option';
	
	}
	
	/* add the fields to the group if group already exist */
	if ( $registered = butler_get( $group, $butler_registered_options ) )
		$butler_registered_options[$group] = array_merge( $registered, $_fields );
	else
		$butler_registered_options[$group] = $_fields;
			
	/* stop here if we are not editing a page type defined in butler_register_options() */
	if ( !butler_is_admin_page( $pages ) )
		return;
	
	/* load the asset unless it is defined othewise */	
	butler_enqueue_options_assets( false, $_fields );
	
	/* load the class and display files */
	butler_load( array( 
		BUTLER_COMPONENTS_PATH . 'options/class',
		BUTLER_COMPONENTS_PATH . 'options/display'
	) );
	
	return new butlerOptionsMetaBox( $group, $_fields, $pages, $args );
		
}


function butler_enqueue_options_assets( $page, $options = array() ) {

	if ( $page && !butler_is_admin_page( $page ) )
		return;
		
	$hooks = array( 'admin_enqueue_scripts', 'customize_controls_enqueue_scripts' );
	
	if ( butler_in_multi_array( 'image', $options ) || butler_in_multi_array( 'gallery', $options ) )
		butler_add_multiple_actions( $hooks, 'butler_options_media_assets' );
		
	if ( butler_in_multi_array( 'repeater', $options ) )
		butler_add_multiple_actions( $hooks, 'butler_options_media_assets' );
		
	if ( butler_in_multi_array( 'select', $options ) || butler_in_multi_array( 'multiselect', $options ) )
		butler_add_multiple_actions( $hooks, 'butler_options_select_assets' );
		
	if ( butler_in_multi_array( 'slider', $options ) )
		butler_add_multiple_actions( $hooks, 'butler_options_slider_assets' );
		
	butler_add_multiple_actions( $hooks, 'butler_options_assets' );

}


function butler_options_assets() {
	
	wp_enqueue_style( 'btr-fields' );
	wp_enqueue_script( 'btr-fields' );
	
}


function butler_options_media_assets( ) {
	
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'btr-media' );
	wp_enqueue_media();
	wp_localize_script( 'btr-media', 'btrMedia', array( 'adminUrl' => admin_url() ) );
	
}


function butler_options_select_assets( ) {

	wp_enqueue_style( 'btr-select2' );
	wp_enqueue_script( 'btr-select2' );
	
}


function butler_options_slider_assets( ) {

	wp_enqueue_script( 'jquery-ui-slider' );
	
}


function butler_options_repeater_assets( ) {

	wp_enqueue_script( 'jquery-ui-sortable' );
	
}


function butler_get_option( $option, $group_name ) {

	global $current_site;
					
	if ( !is_multisite() )
		$group_data = get_option( $group_name . '_options' );
	else
		$group_data = get_blog_option( $current_site->blog_id, $group_name . '_options' );
			
	/* try to get the value saved in the db. Dont use butler_get for this check */
	if ( is_array( $group_data ) && isset( $group_data[$option] ) )
		$return = butler_clean_data( $group_data[$option] );
	
	/* return default if possible */
	else
		$return = butler_get_option_default( $option, $group_name );
		
	return apply_filters( 'butler_option_' . $group_name . '_' . $option, $return );
	
}


function butler_get_options_group( $group_name ) {

	global $butler_registered_options, $current_site;
					
	if ( !is_multisite() )
		$group_data = get_option( $group_name . '_options' );
	else
		$group_data = get_blog_option( $current_site->blog_id, $group_name . '_options' );
		
	/* we return default value if the group doesn't exist */
	if ( !$group_data ) {
	
	 	if ( isset( $butler_registered_options[$group_name] ) ) {
		
			$group_data = array();
			
			$options = $butler_registered_options[$group_name];
			
			foreach ( $options as $id => $option ) {
			
				$option = isset( $option['default'] ) ? $option['default'] : false;
							
				$group_data[$id] = $option;
				
			}
		
		} else {
			
			return false;
		
		}
		
	}

	return $group_data;
	
}


function butler_get_option_default( $option, $group_name ) {
	
	global $butler_registered_options;
		
	$group_data = butler_get( $group_name, $butler_registered_options );
	
	if ( !$group_data || !$_option = butler_get( $option, $group_data ) )
		return false;
		
	$value = isset( $_option['default'] ) ? $_option['default'] : false;
	
	return butler_clean_data( $value );
	
}


function butler_set_option( $option, $value = null, $group_name ) {
			
	if ( $value === null )
		return false;
	
	$group_data = get_option( $group_name . '_options' );
			
	$group_data[$option] = $value;
	
	update_option( $group_name . '_options', $group_data );
	
	return true;
	
}


function butler_options_meta_boxes( $page ) {

	do_action( 'butler_before_options', $page );
		
		do_action( 'butler_options_meta_box', $page );
		
	do_action( 'butler_after_options' );	
					
}


function butler_options_group( $group ) {

	global $butler_registered_options;
	
	/* we fetch the feilds or return if the group doesn't exists */
	if ( !$fields = butler_get( $group, $butler_registered_options ) )
		return;
	
	do_action( 'butler_before_options' );
	
		do_action( 'butler_options_group', $group, $fields );
		
	do_action( 'butler_after_options' );	
					
}