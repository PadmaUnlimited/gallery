<?php

class PadmaRocketBlockMaintenance {

	var $option = array();
	
	var $block_type;
	
	var $option_group;
		
	function __construct( $block_type ) {
			
		$this->block_type = $block_type;
		
		$this->option_group = str_replace( '-', '_', $block_type ) . '_upgrade';
		
		$this->option = get_option( $this->option_group );
			
	}
	
	
	function elements_mapping( $elements ) {
	
		$default_skin = defined( 'padma_DEFAULT_SKIN' ) ? padma_DEFAULT_SKIN : 'base';
		$active_skin = PadmaOption::get( 'current-skin', 'general', $default_skin );
		$task = 'elements_mapping';
		
		if ( empty( $this->option[$task] ) )
			$this->option[$task] = array();
			
		if ( !isset( $this->option[$task][$active_skin] ) ) {
					
			if ( version_compare( PADMA_VERSION, '3.4.5', '>=' ) ) {
				
			    /* we get all the blocks for this type */
			    $blocks = PadmaBlocksData::get_blocks_by_type( $this->block_type );
			    
			    if ( $blocks ) {
				    
				    $block_instance_elements = array();
				    
				    $all_elements = PadmaElementsData::get_all_elements();
				    
				    /* we build the array with all the elements registered with the ID */
				    foreach ( $blocks as $block_id => $layout )
				    	foreach ( $elements as $element )
				    		$block_instance_elements[] = 'block-' . $this->block_type . '-' . $element['id'] . '-' . $block_id;
				    	    		
					/* we loop trough all the elements registered with the ID */
					foreach ( $block_instance_elements as $element ) {
						
						if ( !isset( $all_elements[$element] ) )
							continue;
					    	    
					    $instance_id = end( explode( '-', $element ) );
					    
					    $element_with_no_instance = str_replace( '-' . $instance_id, '', $element );
				
					    $instance_to_register = $element_with_no_instance . '-block-' . $instance_id;
					    
					    /* we map the element properties to the correct Padma instances */
					    if ( isset( $all_elements[$element]['properties'] ) )
						    foreach ( $all_elements[$element]['properties'] as $property => $property_value )
						        PadmaElementsData::set_special_element_property( 'blocks', $element_with_no_instance, 'instance', $instance_to_register, $property, $property_value );
						        
						        
						
						/* we map or overwrite the properties if it was set for a layout since the "edit for current layout" was previously only styling the instance rather that all the block for the layout */
						if ( isset( $all_elements[$element]['special-element-layout'] ) )
						    foreach ( $all_elements[$element]['special-element-layout'] as $layout => $properties )
						    	foreach ( $properties as $property => $property_value )
						        	PadmaElementsData::set_special_element_property( 'blocks', $element_with_no_instance, 'instance', $instance_to_register, $property, $property_value );
						
					}
					
				}
			    
			    /* we have to make the task an array again to overwrite the previous update. this only applies to the features block but does affect others */
			    $this->option[$task] = array();
			    
			    /* we finally set a flag in the db to say that the job is done */
			    $this->option[$task][$active_skin] = true;
			    
			    update_option( $this->option_group, $this->option );
			
			}
			
		}
	
	}
	
	
	function merge_default_elements( $defaults_elements ) {
	
		/* merge default elements */
		$task = 'merge_default_elements';
			
		if ( version_compare( PADMA_VERSION, '3.6', '>=' ) && !isset( $this->option[$task] ) ) {
					
			PadmaElementsData::merge_default_design_data( $defaults_elements, $this->block_type );
			
			/* we finally set a flag in the db to say that the job is done */
			$this->option[$task] = true;
			
			update_option( $this->option_group, $this->option );
			
		}
	
	}

}