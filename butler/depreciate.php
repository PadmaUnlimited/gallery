<?php
/**
 * Depreciated. Register metabox.
 *
 * @since 1.0.0
 * @Depreciated 1.2.0
 */
function butler_register_metabox( $args, $pages ) {

	add_action( 'admin_notices', 'butler_display_depreciate_notice' );
	
}

/**
 * Depreciated. Display options group.
 *
 * @since 1.0.0
 * @Depreciated 1.2.0
 */
function butler_do_options_group( $group = null, $groups = array() ) {

	return butler_options_group( $group, $groups );
	
}

/**
 * Depreciated. Display options groups.
 *
 * @since 1.0.0
 * @Depreciated 1.2.0
 */
function butler_do_options_groups( $groups = null ) {

	add_action( 'admin_notices', 'butler_display_depreciate_notice' );
	
}

/**
 * Depreciated. Load butler components.
 *
 * @since 1.0.0
 * @Depreciated 1.1.0
 */
add_action( 'butler_load_dependencies', 'butler_depreciate_load_dependencies' );

function butler_depreciate_load_dependencies( $args ) {

	if ( is_array( $args ) ) :
	
		$return = array();
		
		foreach ( $args as $key => $value )
			$return[] = is_numeric( $key ) ? $value : $key;
	
	else : 
	
		$return = $args;
	
	endif;

	/* we don't add the depreciate alert as it is only used internaly */
	butler_load_components( $return );

}

/**
 * Depreciated. Register component and load once.
 *
 * @since 1.1.0
 * @Depreciated 1.1.0
 */
add_action( 'butler_register', 'butler_depreciate_load_component_once' );

function butler_depreciate_load_component_once( $args ) {

	/* we don't add the depreciate alert as it is only used internaly */
	butler_load_component_once( $args['version'], $args['id'], $args['path'] );

}

/**
 * Depreciated. Register admin options.
 *
 * @since 1.1.0
 * @Depreciated 1.1.0
 */
add_action( 'butler_register_options', 'butler_depreciate_register_options' );

function butler_depreciate_register_options( $args ) {

	add_action( 'admin_notices', 'butler_display_depreciate_notice' );

}

/**
 * Depreciated. Display tabs.
 *
 * @since 1.1.0
 * @Depreciated 1.1.0
 */
add_action( 'butler_admin_tabs', 'butler_depreciate_admin_tabs' );

function butler_depreciate_admin_tabs( $args ) {
 
	add_action( 'admin_notices', 'butler_display_depreciate_notice' );

}

/**
 * Depreciated. Redirect on component activation.
 *
 * @since 1.1.0
 * @Depreciated 1.1.0
 */
add_action( 'butler_admin_activation', 'butler_depreciate_load_component_once', 10, 2 );

function butler_depreciate_admin_activation( $file, $redirect ) {
 
	add_action( 'admin_notices', 'butler_display_depreciate_notice' );

}

/**
 * Depreciated. Register admin meta boxes.
 *
 * @since 1.1.0
 * @Depreciated 1.1.0
 */
add_action( 'butler_admin_meta_box', 'butler_depreciate_admin_meta_box' );

function butler_depreciate_admin_meta_box( $args ) {
 
	add_action( 'admin_notices', 'butler_display_depreciate_notice' );

}

/**
 * Depreciated. Display admin options.
 *
 * @since 1.1.0
 * @Depreciated 1.1.0
 */
add_action( 'butler_admin_display_options', 'butler_depreciate_display_options' );

function butler_depreciate_display_options( $args ) {
 
	add_action( 'admin_notices', 'butler_display_depreciate_notice' );

}

/**
 * Depreciated. Display admin options save button and close options form.
 *
 * @since 1.1.0
 * @Depreciated 1.1.0
 */
add_action( 'butler_admin_display_save_options', 'butler_depreciate_admin_display_save_options' );

function butler_depreciate_admin_display_save_options( $args ) {
 
	add_action( 'admin_notices', 'butler_display_depreciate_notice' );

}

/**
 * Depreciated. Display admin options save message.
 *
 * @since 1.1.0
 * @Depreciated 1.1.0
 */
add_action( 'butler_admin_display_save_options_message', 'butler_depreciate_display_save_options_message' );

function butler_depreciate_display_save_options_message( $args ) {
 
	add_action( 'admin_notices', 'butler_display_depreciate_notice' );

}

/**
 * Depreciated. Save admin options.
 *
 * @since 1.1.0
 * @Depreciated 1.1.0
 */
add_action( 'butler_admin_save_options', 'butler_depreciate_admin_save_options' );

function butler_depreciate_admin_save_options( $args ) {
 
	add_action( 'admin_notices', 'butler_display_depreciate_notice' );

}

/**
 * Depreciated. Register admin post type.
 *
 * @since 1.1.0
 * @Depreciated 1.1.0
 */
add_action( 'butler_admin_post_type', 'butler_depreciate_admin_post_type' );

function butler_depreciate_admin_post_type( $args ) {
 
	add_action( 'admin_notices', 'butler_display_depreciate_notice' );

}

/**
 * Depreciated. This declare the old butlerFramework class which used to be a check to know if the framework was already loaded.
 *
 * @since 1.1.0
 * @Depreciated 1.1.0
 */
class butlerFramework{}


/**
 * Displays the butler framework depreciate notice.
 *
 * @since 1.1.0
 */
function butler_display_depreciate_notice() {

	if ( get_option( 'butler_hide_depreciate_notice' ) )
		return;
		
	if ( butler_get( 'butler_hide_depreciate_notice' ) ) {
	
		update_option( 'butler_hide_depreciate_notice', true );
		return;	
		
	}
	
	echo '<div id="message" class="error">';
		echo '<p>';
			
			printf( 'Certain components still use an older version of the Butler Framework. Butler version %1$s is currently loaded which might prevent the other components from working properly. Please upgrade all other components which are using the Butler Framework to the latest versions! <a class="button secondary" href="%2$s" style="margin-left: 5px;">Hide Notice</a>', BUTLER_VERSION, add_query_arg( array( 'butler_hide_depreciate_notice' => true ), admin_url() ) );
		
		echo '</p>';
	echo '</div>';

}