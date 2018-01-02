<?php

function butler_register_activation_hook( $file, $page ) {

	butler_load( BUTLER_PATH . 'components/activation/class' );

	new butlerAdminActivation( $file, $page );
	
}