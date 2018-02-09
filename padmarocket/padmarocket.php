<?php
/**
* @package   PadmaRocket Framework
*/

if ( !defined( 'BUTLER_VERSION' ) )
	return;

$version = '1.2.0';

butler_register_framework( $version, 'Padmarocket', trailingslashit( dirname( __FILE__ ) ), array( 
	'load-file' => 'load.php',
	'load-once' => true
) );