<?php
/**
* @package   Butler Framework
* @author    ThemeButler http://themebutler.com
*/

/* exit if uninstall not called from WordPress exit */
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

/* remove the versions registered in the db */
delete_option( 'butler_versions' );