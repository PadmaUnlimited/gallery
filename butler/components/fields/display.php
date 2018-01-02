<?php
/**
* @package   Butler Framework
* @author    ThemeButler http://themebutler.com
*/

add_action( 'butler_fields', 'butler_do_fields', 10, 2 );

function butler_do_fields( $fields, $args ) {

	/* we use nonce for verification */
	if ( $args['nonce'] )
		echo '<input type="hidden" name="btr_fields_nonce" value="' . wp_create_nonce( $args['nonce'] ) . '" />';
			
	do_action( 'butler_before_fields', $fields );
	
	echo '<div class="btr-fields">';
	
		foreach ( $fields as $id => $field )
			do_action( 'butler_field', $field );
		
	echo '</div>';
	
	do_action( 'butler_after_fields', $fields );
				
}


add_action( 'butler_field', 'butler_do_field' );

function butler_do_field( $field, $i = 0 ) {

	static $activation = false;
	
	/* set default */
	$default = array(
		'name' => 'name="' . $field['group'] . '[' . $field['id'] . ']"',
		'value' => butler_get_input( $field ),	
		'default' => false,
		'checkbox-label' => 'Enable',
		'context' => false,
		'attr' => null,
		'raw' => ( $field['type'] == 'header' ? true : false )
	);
	
	$field = array_merge( $default, butler_parse_field_callback( $field ) );
	
	/* allow third party to short-circuit this function */				
	$pre = apply_filters( 'butler_pre_field_' . str_replace( '-', '_', $field['id'] ), false, $field );
	
	if ( $pre !== false )
		return $pre;
	
	/* set activation and jump straight to next field */				
	if ( $field['type'] == 'activation' ) {
	
		$activation = $field;
		return;
			
	}
	
	/* output field */
	do_action( 'butler_before_input', $field, $i );	
		
		/* add activation if set */
		if ( $activation )
			do_action( 'butler_input_' . $activation['type'], $activation );

		do_action( 'butler_input_' . $field['type'], $field, $i );
		
	do_action( 'butler_after_input', $field, $i );
		
	/* reset activation */
	$activation = false;
		
}


add_action( 'butler_before_input', 'butler_do_before_input' ); 

function butler_do_before_input( $field ) {
	
	/* stop here if raw output is set */
	if ( $field['raw'] )
		return;

	echo '<div class="btr-field-container ' . $field['context'] . '" data-btr-option-type="' . $field['type'] . '">';
	
		echo '<label>' . $field['label'] . '</label>';
		
		printf( '<div class="btr-field-wrap" %1$s >', ( $field['type'] == 'repeater' ? "data-option='" . json_encode( $field ) . "'" : '' ) );
	
}


add_action( 'butler_after_input', 'butler_do_after_input' ); 

function butler_do_after_input( $field ) {

	/* stop here if raw output is set */
	if ( $field['raw'] )
		return;

		
		
			if ( butler_get( 'description', $field ) ) :
				
				$description = butler_troncate( $field['description'] );
				
				echo '<div class="btr-field-description">';
					
					echo $description['main'];
					
					if ( !empty( $description['extended'] ) ) {
					
						echo '<a class="btr-read-more" href="#">' . $description['more_text'] . '</a>';
						echo '<div class="btr-extended-content">' . $description['extended'] . '</div>';
					
					}
				
				echo '</div>';
			
			endif;
			
		echo '</div>';
		
	echo '</div>';
	
}


add_action( 'butler_input_activation', 'butler_do_input_activation' ); 

function butler_do_input_activation( $field ) {

	$checked = $field['value'] ? ' checked="checked"' : null;
	
	echo '<input type="hidden" value="0" ' . $field['name'] . ' />';
	
	echo '<input class="activation" type="checkbox" ' . $field['name'] . ' value="1" ' . $checked . ' ' . $field['attr'] . ' />';
	
}


add_action( 'butler_input_repeater', 'butler_do_input_repeater', false, 2 ); 	

function butler_do_input_repeater( $field, $i ) {
		
	$groups = $field['value'];
					
	if ( $i != 0 || empty( $field['value'] ) )
		$groups = range( 0, 0 );
	
	/* this is used to set the number of repeat by default */
	else if ( is_numeric( $groups ) )
		$groups = range( 0, ( $groups - 1 ) );
	
	foreach ( $groups as $group_id => $group ) {
			
		echo '<div class="btr-repeater-group-' . $i . '">';
			
			echo '<div class="btr-repeater-fields-wrap">';
	
				foreach ( $field['fields'] as $id => $repeater ) {
					
					/* no support for activation field at the moment */
					if ( $repeater['type'] == 'activation' )
						continue;
					
					$repeater['id'] = $id;
					$repeater['group'] = $field['group']. '[' . $field['id'] . '][' . $i . ']';
					$repeater['raw'] = true;
									
					echo '<div class="btr-repeater-field" data-btr-option-type="' . $repeater['type'] . '">';
																				
						/* we return the saved value if it is set */
						if ( is_array( $field['value'] ) && isset( $field['value'][$i][$repeater['id']] ) )
							$repeater['value'] = $field['value'][$i][$repeater['id']];
							
						else
							$repeater['value'] = isset( $repeater['default'] ) ? $repeater['default'] : false ;
							
						if ( $i === 0 && isset( $repeater['label'] ) )
							echo '<label>' . $repeater['label'] . '</label>';
																				
						echo butler_do_field( $repeater, $i );
						
					echo '</div>';
					
				}
				
				echo '<div class="btr-toolbar btr-repeater-toolbar">';
				
					echo '<a href="#" class="dashicons dashicons-menu"></a>';
					echo '<a href="#" class="dashicons dashicons-post-trash"></a>';
					
				echo '</div>';
		
			echo '</div>';
		
		echo '</div>';
		
		$i++;
		
	}
	
	echo '<a href="#" class="btr-repeat button button-small">Add New</a>';
	
}


add_action( 'butler_input_text', 'butler_do_input_text' );

function butler_do_input_text( $field ) {

	echo '<input class="btr-field"  type="text" ' . $field['name'] . ' value="' . $field['value'] . '">';
	
}


add_action( 'butler_input_slider', 'butler_do_input_slider' );

function butler_do_input_slider( $field ) {

	$defaults = array(
		'slider-min' => 0,
		'slider-max' => 100,
		'slider-interval' => 1,
		'unit' => null,
	);
	
	$field	= array_merge( $defaults, $field );
	
	echo '<div class="btr-field" slider_min="' . $field['slider-min'] . '" slider_max="' . $field['slider-max'] . '" slider_interval="' . $field['slider-interval'] . '">';
		
		echo '<input class="btr-slider-hidden" type="hidden" value="' . $field['value'] . '" ' . $field['name'] . ' ' . $field['attr'] . '/>';
		
	echo '</div>';
		
	echo '<span class="btr-slider-value">' . $field['value'] . '</span>';
	
	if ( $field['unit'] )
		echo '<span class="btr-slider-unit">' . $field['unit'] . '</span>';

}


add_action( 'butler_input_text', 'butler_do_input_textarea' );

function butler_do_input_textarea( $field ) {
			
	echo '<textarea class="btr-field" ' . $field['name'] . '>' . $field['value'] . '</textarea>';
	
}


add_action( 'butler_input_text', 'butler_do_input_radio' );	

function butler_do_input_radio( $field ) {
	
	$field['default'] = isset( $checkbox['default'] ) ? $checkbox['default'] : key( $field['radios'] );
	
	echo '<fieldset>';
			
		$i = 0; foreach ( $field['radios'] as $id => $radio ) {
							
			$checked = $id == $field['value'] ? ' checked="checked"' : null;
							
			echo '<label class="radio-label" for="' . $id . '-' . $i . '">';
			
				echo '<input class="btr-field" type="radio" ' . $field['name'] . ' value="' . $id . '" ' . $checked . '/>';
				
			echo $radio . '</label>';
			
			$i++;
			
		}
				
	echo '</fieldset>';	
	
}


add_action( 'butler_input_imageradio', 'butler_do_input_imageradio' );

function butler_do_input_imageradio( $field ) {
		
	$field['default'] = isset( $checkbox['default'] ) ? $checkbox['default'] : key( $field['radios'] );
	
	echo '<fieldset>';
			
		$i = 0; foreach ( $field['radios'] as $id => $radio ) {
							
			$checked = $id == $field['value'] ? ' checked="checked"' : null;
			
			$has_image = @getimagesize( $radio ) ? 'has-image' : false;					
							
			echo '<label class="' . $has_image . '">';
			
				if ( $has_image )
					echo '<img src="' . $radio . '" />';
			
				echo '<input class="btr-field" type="radio" ' . $field['name'] . ' value="' . $id . '" ' . $checked . ' ' . $field['attr'] . '/>';
				
				if ( !$has_image )
					echo $radio; 
				
			echo '</label>';
			
			$i++;
			
		}
				
	echo '</fieldset>';	
	
}


add_action( 'butler_input_checkbox', 'butler_do_input_checkbox' );

function butler_do_input_checkbox( $field ) {
				
	$checked = $field['value'] ? ' checked="checked"' : null;
			
	echo '<input type="hidden" value="0" ' . $field['name'] . ' />';
	
	echo '<input class="btr-field" type="checkbox" ' . $field['name'] . ' value="1" ' . $checked . ' />';
	
	echo '<span class="checkbox-label">' . $field['checkbox-label'] . '</span>';
	
}


add_action( 'butler_input_multicheckbox', 'butler_do_input_multicheckbox' );

function butler_do_input_multicheckbox( $field ) {
			
	echo '<fieldset>';
		
		$i = 0; foreach ( $field['checkboxes'] as $id => $label ) {
											
			$checked = is_array( $field['value'] ) && isset( $field['value'][$id] ) && $field['value'][$id] == true ? ' checked="checked"' : null;
			
			echo '<label class="checkbox-label" for="' . $id . '-' . $i . '">';
			
				echo '<input type="hidden" value="0" name="' . $field['group'] . '[' . $field['id'] .'][' . $id .']" />';
			
				echo '<input class="btr-field" type="checkbox" name="' . $field['group'] . '[' . $field['id'] .'][' . $id .']" value="1" ' . $checked . ' />';
									
			echo $label . '</label>';
			
			$i++;
			
		}
				
	echo '</fieldset>';		
	
}


add_action( 'butler_input_select', 'butler_do_input_select' );

function butler_do_input_select( $field ) {
	
	echo '<select class="btr-field" ' . $field['name'] . '>';
			
		foreach ( $field['options'] as $value => $label ) {
			
			$selected = $value == $field['value'] ? ' selected="selected"' : null;
	
			echo '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
	
		}
				
	echo '</select>';	
	
}


add_action( 'butler_input_multiselect', 'butler_do_input_multiselect' );

function butler_do_input_multiselect( $field ) {

	echo '<input type="hidden" value="0" ' . $field['name'] . ' />';
	
	echo '<select class="btr-field" name="' . $field['group'] . '[' . $field['id'] .'][]" multiple="multiple">';
			
		foreach ( $field['options'] as $value => $label ) {
			
			$selected = is_array( $field['value'] ) && in_array( $value, $field['value'] ) ? ' selected="selected"' : null;
	
			echo '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
	
		}
				
	echo '</select>';	
	
}


add_action( 'butler_input_image', 'butler_do_input_image' );

function butler_do_input_image( $field ) {
		
	$img = wp_get_attachment_image_src( $field['value'], 'thumbnail' );

	echo '<div class="btr-image-wrapper' . ( empty( $img ) ? ' hide' : '' ) . '">';
	
		echo '<input class="image-id ' . $field['id'] . '"  type="hidden" ' . $field['name'] . ' value="' . $field['value'] . '" data-multiple="false">';
	
		echo '<img class="btr-field" src="' . $img[0] . '">';
		
		echo '<div class="btr-toolbar">';
			
			echo '<a href="#" class="dashicons dashicons-edit"></a>';
			echo '<a href="#" class="dashicons dashicons-post-trash"></a>';
			
		echo '</div>';
		
	echo '</div>';		
	
	echo '<a href="#" class="btr-upload-image button button-small' . ( $img ? ' hide' : '' ) . '">Add Image</a>';
	
}


add_action( 'butler_input_gallery', 'butler_do_input_gallery' );

function butler_do_input_gallery( $field ) {

	$images = butler_maybe_array( $field['value'] );
	
	$images = array_merge( $images, array( 'tmpl' ) );
	
	echo '<a href="#" class="btr-upload-image button button-small">Add Image</a><span class="drag-notice">Drad and drop to re-order</span>';
	
	echo '<input class="gallery-id ' . $field['id'] . '"  type="hidden" ' . $field['name'] . ' value=""  data-multiple="true">';
	
	echo '<div class="btr-gallery-wrapper">';
		
		foreach ( $images as $id ) {
			
			/* we prevent the image from loading if it doesn't have an id */
			if ( !$id )
				continue;
			
			$img = wp_get_attachment_image_src( $id, 'thumbnail' );
			
			echo '<div class="btr-image-wrapper' . ( $id == 'tmpl' ? '-tmpl' : '' ) . '">';
			
				echo '<input class="image-id" type="hidden" ' . ($id == 'tmpl' ? 'data-' : '') . 'name="' . $field['group'] . '[' . $field['id'] . '][]" value="' . ( $id == 'tmpl' ? '' : $id ) .'" />';
				
				echo '<img class="btr-field" src="' . $img[0] . '">';
				
				echo '<div class="btr-toolbar">';
					
					echo '<a href="#" class="dashicons dashicons-menu"></a>';
					echo '<a href="#" class="dashicons dashicons-edit"></a>';
					echo '<a href="#" class="dashicons dashicons-post-trash"></a>';
					
				echo '</div>';
				
			echo '</div>';
			
		}
					
	echo '</div>';
					
}


add_action( 'butler_input_header', 'butler_do_input_header' );

function butler_do_input_header( $field ) {
		
	$default = array(
		'markup' => 'h4',
	);
	
	$field = array_merge( $default, $field );

	echo '<' . $field['markup'] . ' class="' . $field['type'] . ' ' . $field['id'] . '">' . $field['label'] . '</' .  $field['markup'] . '>';
	
}