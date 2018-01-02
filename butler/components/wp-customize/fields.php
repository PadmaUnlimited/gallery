<?php
/**
* @package   Butler Framework
* @author    ThemeButler http://themebutler.com
*/

if ( class_exists( 'WP_Customize_Control' ) ) :

	class Butler_WP_Customize_Control extends WP_Customize_Control {
		
		var $butler_field;
		
		function __construct() {
			
			$args = func_get_args();
									
			call_user_func_array( array( 'parent', '__construct' ), $args );
			
			$field = array(
				'attr' => $this->get_link(),
			);
			
			$this->butler_field = array_merge( $field, end( $args ) );
			
		}
		
		
		function render_content() {
			
			$field = $this->butler_field;
						
			butler_field( $field );
			
		}
				 			
	}
	
endif;