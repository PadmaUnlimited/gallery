<?php
/**
* @package   Butler Framework
* @author    ThemeButler http://themebutler.com
*/

class Butler_WP_Customize {
	
	var $group;
	
	var $fields = array();
	
	var $butler_fields = array( 'activation' ,'imageradio', 'slider' );
			
	function __construct( $group, $fields, $args ) {
		
		$this->group = $group;
		$this->fields = $fields;
		$this->args = $args;
		
		/* make sure the option component is loaded */
		butler_load_components( 'options' );
		
		butler_enqueue_options_assets( false, $fields );
						
		add_action( 'customize_register', array( $this, 'add' ) );
		
	}
	
	
	function enqueue_assets( ) {
		
		wp_enqueue_style( 'btr-fields' );
		wp_enqueue_script( 'btr-fields' );
		
	}
	
	
	function add( $wp_customize ) {
		
		$this->add_section( $wp_customize );
		
		$defaults = array(
			'db_type' => 'theme_mod',
			'capability' => 'edit_theme_options',
			'section' => $this->group,
			'transport' => 'postMessage',
		);
	
		$i = 0; foreach ( $this->fields as $field ) {
		
			$field = array_merge( $defaults, $field );
			
			$class = $this->get_class_name( $field['type'] );
			
			$wp_customize->add_setting(
				$this->group . '[' . $field['id'] . ']', 
				array(
					'default' => butler_get( 'default', $field ),
					'type' => $field['db_type'], 
					'capability' => $field['capability'],
					'transport' => $field['transport']
				)
			);
						
			$wp_customize->add_control(
				new $class(
					$wp_customize,
					$this->group . '[' . $field['id'] . ']', 
					array(
						'label' => $field['label'], 
						'section' => $field['section'],
						'priority' => 10 + $i //make sure the fields are ordered as the array is. very important to keep as wp customize would mess up the ordering
					),
					$field
				)
			);
			
			$i++;
										
		}
		
		if ( $wp_customize->is_preview() && !is_admin() )
		    add_action( 'wp_footer', array( $this, 'customize_preview' ), 21);

	}
	
	
	function add_section( $wp_customize ) {
		
		if ( $section = $wp_customize->get_section( $this->group ) ) {
			
			if ( $this->args['title'] )
				$section->title = $this->args['title'];
			if ( $this->args['priority'] )	
				$section->priority = $this->args['priority'];
			
		} else {
			
			$wp_customize->add_section( 
				$this->group,
				array(
				    'title' => $this->args['title'] ? $this->args['title'] : __( 'Undefined', 'butler' ),
				    'priority' => $this->args['priority'] ? $this->args['priority'] : 30,
				)
			);
			
		}
		
	}
	
	
	function get_class_name( $type ) {
		
		if ( in_array( $type, $this->butler_fields ) )
			return 'Butler_WP_Customize_Control';
			
		if ( $type === 'image')
			return 'WP_Customize_Image_Control';
			
		if ( $type === 'color' )
			return 'WP_Customize_Color_Control';
			
		if ( $type === 'upload' )
			return 'WP_Customize_Upload_Control';
		
		return 'WP_Customize_Control';	
			
		
	}
	
		
	function customize_preview() {
	
		if ( !butler_multi_array_key_exists( 'js-callback', $this->fields ) )
			return;
	    
	    echo '<script type="text/javascript">' . "\n";
	    	
	    	echo '( function( $ ) {' . "\n";
	    	
	  	    	foreach ( $this->fields as $id => $field ) {
		    	
		    		if ( !$callback = butler_get( 'js-callback', $field ) )
		    			continue;
		    			           
		            echo "\t" . 'wp.customize( "' . $this->group . '[' . $field['id'] . ']", function( value ) {'. "\n";
		                		                
		                echo "\t\t" . 'value.bind( function( to ) {' . "\n";
		                   
		                	echo "\t\t\t" . $callback . "\n";
		                			                
		                echo "\t\t" . '});' . "\n";
		                
		            echo "\t" . '});' . "\n";
			        
			    }
	   		
	   		echo '} )( jQuery )';
	   		
	    echo '</script>';

    
	} 
	
}