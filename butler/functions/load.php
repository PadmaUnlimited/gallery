<?php

function butler_load( $files, $init = false, $init_once = false ) {
	
	static $loaded = array();
	
	static $initiated = array();	
			
	if ( !is_array( $files ) )
		$files = array( $files => $init );

	$classes_to_init = array();
					
	foreach ( $files as $file => $init ) {
				
		/* we set the default init argument if it is not set */
		if ( is_numeric( $file ) ) {
		
			$file = $init;
			$init = false;
		
		} 
		
		/* we don't require the file if it has already been loaded */
		if ( in_array( $file, $loaded ) )
			continue;
					
		/* we add the php extension if it is not already */
		$load = strpos( $file, '.php' ) ? $file : $file . '.php';
		
		if ( file_exists( $load ) )		
			require_once( $load );
			
		/* we add the file to the loaded array */
		$loaded[] = $file;
		
		/* we figure out what the class name is if init is true, otherwise we use the class provided */
		if ( $init )
			$classes_to_init[] = $init;
					
	}
	
	$objects = array();
	
	/* we are ready to initiated classes which require so */
	foreach ( $classes_to_init as $class ) {
		
		/* prevent init once class from loading multiple time */
		if ( isset($initiated[$class]) && $initiated[$class]  )
			continue;
		
		if ( method_exists( $class, 'init' ) ) {
		
			call_user_func( array( $class, 'init' ) );
			
			$initiated[$class] = $init_once;
			
		} else {
		
			if ( count( $files ) == 1 )
				$objects = new $class();
			else
				$objects[$class] = new $class();
				
			$initiated[$class] = $init_once;
				
		}
		
	}
	
	return $objects;

}


function butler_load_components( $components, array $custom = null ) {

	static $loaded = array();
	
	if ( $custom )
		$core = $custom;
	else
		$core = array(
			'options' => array(
				BUTLER_COMPONENTS_PATH . 'options/functions.php',
				BUTLER_COMPONENTS_PATH . 'fields/functions.php'
			),
			'wp-customize' => array(
				BUTLER_COMPONENTS_PATH . 'wp-customize/fields.php',
				BUTLER_COMPONENTS_PATH . 'wp-customize/functions.php'
			),
			'image-handler' => BUTLER_COMPONENTS_PATH . 'image-handler/functions.php',
			'post-meta' => BUTLER_COMPONENTS_PATH . 'post-meta/functions.php',
			'term-meta' => BUTLER_COMPONENTS_PATH . 'term-meta/functions.php',
			'post-types' => BUTLER_COMPONENTS_PATH . 'post-types/functions.php',
			'activation' => BUTLER_COMPONENTS_PATH . 'activation/functions.php',
			'updater' => BUTLER_COMPONENTS_PATH . 'updater/functions.php',
		);
	
	if ( !$components )
		$components = array_keys( $core );
		
	$return = array();
	
	foreach ( butler_maybe_array( $components ) as $component ) {
		
		if ( !isset( $core[$component] ) || in_array( $component, $loaded ) )
			continue;
			
		$loaded[] = $component;
		
		$return[$component] = butler_load( $core[$component] );	
														
	}
		
	return $return;

}