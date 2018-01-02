<?php
/**
* @package   Butler Framework
* @author    ThemeButler http://themebutler.com
*/

function butler_render( $template, $args = array() ) {
			
	extract( $args );
	ob_start();
	
		include( $template );
    
    return ob_get_clean();

}


function butler_remove_folder( $folder ) {
	
	if ( !is_dir( $folder ) ) 
		return false;
				
	$items = scandir( $folder );
	unset( $items[0], $items[1] );
	
	foreach ( $items as $key => $item ) {
				
		$path = $folder . '/' . $item;
			
		if ( filetype( $folder . '/' . $item ) === 'dir' )
			butler_remove_folder( $path );
		else
			unlink( $path );
			
		unset( $items[$key] );
			
	}
		
	rmdir( $folder );
	
	return true;
	
}


function butler_url_to_path( $url ) {

	$site_url = site_url();

    if ( is_ssl() )
    	$site_url = preg_replace( '/^http:\/\//', 'https://', $site_url );
        	
    return str_replace( $site_url, untrailingslashit( ABSPATH ), $url );

}


function butler_version_control( $component, $version ) {

	if ( !$component || !$version )
		return false;

	$butler_versions = get_option( 'butler_versions' );
					
	if ( !isset( $butler_versions[$component] ) ) {
		
		$butler_versions[$component] = $version;
	
		update_option( 'butler_versions', $butler_versions );
	
	} elseif ( $butler_versions[$component] > $version ) {
	
		return false;
	
	}
	
	return true;
	
}


function butler_clean_data( $data ) {
	
	if ( is_numeric( $data ) ) {
		
		if ( floatval( $data ) == intval( $data ) )
			return (int) $data;
		else
			return (float) $data;
		
	} elseif ( $data === 'true' || $data === 'on' ) {
		
	 	return true;
		
	} elseif ( $data === 'false' ) {
		
	 	return false;
		
	} elseif ( $data === '' || $data === 'null' ) {
		
		return null;
		
	} else {

		$data = maybe_unserialize( $data );
		
		if ( !is_array( $data ) ) {
			return stripslashes( $data );
			
		} else {
			
			return array_map( 'maybe_unserialize', $data );
			
		}
		
	}
	
}


function butler_maybe_array( $element ) {
	
	if ( is_array( $element ) )
		return $element;
		
	return array( $element );

}


function butler_get( $name, $array = false, $default = null ) {
	
	if ( $array === false )
		$array = $_GET;
	
	if ( (is_string( $name ) || is_numeric( $name )) && !is_float( $name ) ) {

		if ( is_array( $array ) && isset( $array[$name] ) )
			return $array[$name];
		elseif ( is_object( $array ) && isset( $array->$name ) )
			return $array->$name;
		elseif ( is_string( $array ) )
			if ( $name == $array )
				return true;
			else
				return false;

	}
		
	return $default;	
		
}


function butler_post( $name, $data = null ) {

	if ( $data )
		$_POST = $data;
	
	return butler_get( $name, $_POST );
	
}

function butler_get_or_post( $name ) {
	
	if ( !butler_get( $name ) )
		return butler_post( $name );	
	
	return butler_get( $name );
	
}


function butler_get_post_types( $exclude = false, $ids = false ) {

	$return = array();
	$post_types = get_post_types( false, 'objects' );
		
	foreach ( $post_types as $post_type_id => $post_type ) {
		
		/* we make sure the post type is not an excluded post type. */
		if ( in_array( $post_type_id, array( 'revision', 'nav_menu_item' ) ) || in_array( $post_type_id, butler_maybe_array( $exclude ) ) ) 
			continue;
		
		if ( $ids )
			$return[] = $post_type_id;
		else
			$return[$post_type_id] = $post_type->labels->name;	
	
	}
	
	return $return;

}


function butler_get_post_types_for_field( $exclude = false, $ids = false ) {

	return array_merge( array( 'all' => 'All Post Type' ), butler_get_post_types( $exclude = false, $ids = false ) );

}


function butler_get_post_items( $post_type, $extra = array() ) {
		
	$args = array(
	    'posts_per_page' => -1,
	    'post_type' => $post_type,
	    'post_status' => 'publish',
	    'suppress_filters' => true
	);
	    
	$post_type_query = get_posts( $args );
	$items = $extra;
			
	foreach ( $post_type_query as $item )
		$items[$item->ID] = $item->post_title;
					
	return $items;

}


function butler_get_int( $string ) {

	preg_match( "/([0-9]+[\.,]?)+/", $string, $matches );
	
	if ( !isset( $matches[0] ) ) 
		return false;
	
	return $matches[0];
	
}


function butler_troncate( $content, $tag = '<!--plus-->', $more_text = 'More...' ) {
		
	if ( preg_match( '/' . $tag . '/', $content, $matches ) ) {
	
		list( $main, $extended ) = explode( $matches[0], $content, 2 );
		
	} else {
	
		$main = $content;
		$extended = '';
		$more_text = '';
		
	}

	/* we scandirtrip leading and trailing whitespace */
	$main = preg_replace( '/^[\s]*(.*)[\s]*$/', '\\1', $main );
	$extended = preg_replace( '/^[\s]*(.*)[\s]*$/', '\\1', $extended );
	$more_text = preg_replace( '/^[\s]*(.*)[\s]*$/', '\\1', $more_text );

	return array(
		'main' => $main,
		'extended' => $extended,
		'more_text' => $more_text
	);
		
}


function butler_register_version_flag( $version ) {

	if ( version_compare( $version, BUTLER_VERSION, '>=' ) )
		return true;
		
	add_action( 'admin_notices', 'butler_display_depreciate_notice' );
	
	return false;
	
}


function butler_count_recursive( $array, $depth = false, $count_parent = true ) { 

	if ( !is_array( $array ) ) 
		return 0;
		
	if ( $depth === 1 )
		return count( $array );
	
	if ( !is_numeric( $depth ) )
		return count( $array, COUNT_RECURSIVE );
	
	$count = $count_parent ? count( $array ) : 0; 
	
	foreach ( $array as $_array ) 
		 if ( is_array( $_array ) )
		 	$count += butler_count_recursive( $_array, $depth - 1, $count_parent );
		 else
		 	$count += 1;
	
	return $count; 
	
}
  

function butler_in_multi_array( $value, $array ) { 

	$array = butler_maybe_array( $array );

	if ( in_array( $value, $array, true ) )
		return true;
	
	foreach ( $array as $item )
		if ( is_array( $item ) && butler_in_multi_array( $value , $item ) )
	    	return true;

	return false;
	
}


function butler_multi_array_key_exists( $key, $array ) { 

	$array = butler_maybe_array( $array );

	if ( array_key_exists( $key, $array ) )
		return true;
	
	foreach ( $array as $item )
		if ( is_array( $item ) && butler_multi_array_key_exists( $key , $item ) )
	    	return true;

	return false;
	
}
 
 
function butler_is_admin_page( $pages ) { 

	if ( !is_admin() )
		return false;
	
	$pages = butler_maybe_array( $pages );
	
	foreach ( $pages as $page )
		if ( butler_get( 'page' ) == $page )
			return true;

	return false;
	
}


function butler_is_admin_post( $screens ) { 
	
	if ( !is_admin() )
		return false;
		
	global $current_screen;

	if ( !is_object( $current_screen ) || $current_screen->base != 'post' )
		return false;
	
	$screens = butler_maybe_array( $screens );
	
	if ( in_array( 'all', $screens ) )
		return true;
	
	if ( isset( $current_screen->id ) && in_array( $current_screen->id, $screens ) )
		return true;

	return false;
	
}


function butler_is_admin_term( $screens ) {

	if ( !is_admin() )
		return false;
		
	global $current_screen;

	if ( !is_object( $current_screen ) || $current_screen->base != 'edit-tags' )
		return false;
	
	$screens = butler_maybe_array( $screens );
	
	if ( in_array( 'all', $screens ) )
		return true;
	
	if ( isset( $current_screen->taxonomy ) && in_array( $current_screen->taxonomy, $screens ) )
		return true;

	return false;
	
}
 
 
function butler_admin_menu_position( $position ) {

	global $menu;
			
	if ( array_key_exists( $position, $menu ) )
		return butler_admin_menu_position( $position + 1 );
			
	return $position;
	
}


function butler_add_multiple_actions( $hooks, $function_to_add, $priority = 10, $accepted_args = null ) {

	foreach ( butler_maybe_array( $hooks ) as $hook )
		add_action( $hook, $function_to_add, $priority, $accepted_args );
	
}