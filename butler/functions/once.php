<?php

if ( !function_exists( 'butler_register_framework' ) ) {

	function butler_register_framework( $version, $slug, $path, $args = array() ) {

		static $loaded = array();

		$default = array(
			'load-file' => false,
			'load-once' => false
		);

		$args = array_merge( $default, (array) $args );
		$db_update = false;
		$component_cap = strtoupper( $slug );
		$db_option = get_option( 'butler_versions' );

		/* flag db_update if framework doesn't exist */
		if ( !isset( $db_option[$slug] ) ) :

			$db_update = true;

		/* flag db_update if any of the conditions below are not met */
		else :

			$conditions = array(
				'version' => !isset( $db_option[$slug]['version'] ),
				'new-version' => $version > $db_option[$slug]['version'],
				'downgrade' => $db_option[$slug]['path'] == $path && $version < $db_option[$slug]['version'],
				'realpath' => !is_dir( $db_option[$slug]['path'] )
			);

			foreach ( $conditions as $condition ) {

				if ( $condition === true ) {

					$db_update = true;
					break;

				}

			}

		endif;

		/* update the db if changes where made */
		if ( $db_update ) :

			$db_option[$slug] = array(
				'version' => $version,
				'path' => $path,
			);

			update_option( 'butler_versions', $db_option );

		/* return the path and version registered if nothing as changed and the component must only load once */
		elseif ( $args['load-once'] ) :

			$version = $db_option[$slug]['version'];
			$path = $db_option[$slug]['path'];

		endif;

		/* set the default constants */
		if ( !defined( $component_cap . '_VERSION' ) )
			define( $component_cap . '_VERSION', $version );

		if ( !defined( $component_cap . '_PATH' ) )
			define( $component_cap . '_PATH', $path );

		if ( !defined( $component_cap . '_URL' ) )
			define( $component_cap . '_URL', butler_path_to_url( $path ) );

		/* load the component if it isn't yet. Only load if the defined constant path is the same as the $path variable */
		if ( $args['load-file'] && !isset( $loaded[$slug] ) && constant( $component_cap . '_PATH' ) == $path ) :

			/* check if the file exist before loading */
			if ( file_exists( $path . $args['load-file'] ) ) :

				require_once( $path . $args['load-file'] );

				$loaded[$slug] = $path . $args['load-file'];

			endif;

		endif;

		/* display notice if the db has been updated but the latest version couldn't be loaded since it was already loaded */
		if ( $db_update && !isset( $loaded[$slug] ) ) :

			add_action( 'admin_notices', 'butler_framework_update_admin_notice' );

			/* remove the depreciate notice which can be due to some framework being updated and others only on the next load */
			remove_action( 'admin_notices', 'butler_display_depreciate_notice' );

		endif;

		return $loaded;

	}

}


if ( !function_exists( 'butler_framework_update_admin_notice' ) ) {

	function butler_framework_update_admin_notice() {

	    echo '<div id="message" class="updated"><p>Certain components have been updated <strong>successfully</strong> but will only take effect on the next page refresh.</p></div>';

	}

}


if ( !function_exists( 'butler_path_to_url' ) ) {

	function butler_path_to_url( $path ) {

	    // Stop here if it is already a url or data format.
		if ( preg_match( '#^(http|https|\/\/|data)#', $path ) == true )
			return $path;

		$path = str_replace( '\\', '/', $path );
		$abspath = str_replace( '\\', '/', ABSPATH );

	    return trailingslashit( site_url() ) . ltrim( str_replace( $abspath, '', $path ), '/' );

	}

}


/**
 * Depreciated. Register and load component. This function can't be in the depreciate.php file as it hasn't run yet
 *
 * @since 1.0.0
 * @deprecated 1.1.1
 */

if ( !function_exists( 'butler_load_component_once' ) ) {

	function butler_load_component_once( $version, $slug, $path, $load = false ) {

		$args = array(
			'load-file' => $load ? $load : 'load.php',
			'load-once' => true
		);

		butler_register_framework( $version, $slug, $path, $args );

	}

}