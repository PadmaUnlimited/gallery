<?php
/**
* @package   Butler Framework
* @author    ThemeButler http://themebutler.com
*/

include_once( trailingslashit( dirname( __FILE__ ) ) . '/functions/once.php' );

butler_register_framework( '1.2.1', 'butler', trailingslashit( dirname( __FILE__ ) ), array(
	'load-file' => 'load.php',
	'load-once' => true
) );