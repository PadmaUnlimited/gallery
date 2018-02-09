<?php
/**
* @package   PadmaRocket Framework
* @author    PadmaRocket http://Padmarocket.com
*/

/* we exit if uninstall not called from WordPress exit */
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

/* we remove the api data set in the db */
delete_option( 'padma_api_request_products' );

/* we remove the framework option group */
delete_option( 'padma_framework' );

/* we remove upgrade flags */
delete_option( 'padma_framework_upgrade' );