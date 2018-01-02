<?php

function butler_register_post_type( $args ) {

	butler_load( BUTLER_PATH . 'components/post-types/class' );
	
	butlerAdminPostTypes::init( $args );

}