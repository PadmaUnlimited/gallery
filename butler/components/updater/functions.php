<?php


function butler_register_updater( $args ) {

	butler_load( array(
		BUTLER_PATH . 'components/api/class.php',
		BUTLER_PATH . 'components/updater/class.php'
	) );
	
	return new butlerAdminUpdate( $args );

}