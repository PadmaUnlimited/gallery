<?php

add_action( 'butler_options_meta_box', 'butler_do_options_meta_box' );

function butler_do_options_meta_box( $page ) {

	global $wp_meta_boxes;
	
	$has_sidebar = butler_multi_array_key_exists( 'side', $wp_meta_boxes );
	$columns = butler_multi_array_key_exists( 'column', $wp_meta_boxes );
	
	if ( $has_sidebar && $columns )
		$col_width = '1-3';
	elseif ( $has_sidebar && !$columns )
		$col_width = '2-3';
	elseif ( !$has_sidebar && $columns )
		$col_width = '1-2';
	else
		$col_width = '1-1';
					
	echo '<div class="metabox-holder btr-meta-boxes-grid">';
		
		echo '<div class="btr-width-' . $col_width . ' postbox-container">';
				do_meta_boxes( $page, 'normal', null );
		echo '</div>';
		
		if ( $columns ) :
			
			echo '<div class="btr-width-' . $col_width . ' postbox-container">';
				do_meta_boxes( $page, 'column', null );
			echo '</div>';
			
		endif;
		
		if ( $has_sidebar ) :
			
			echo '<div class="btr-width-1-3 postbox-container">';
				do_meta_boxes( $page, 'side', null );
			echo '</div>';
			
		endif;
				
	echo '</div>';
					
}


add_action( 'butler_before_options', 'butler_options_open_form' );

function butler_options_open_form( $page = false ) {
	
	echo '<form action="" method="post" class="btr-options-wrap" data-options-page="' . $page . '">';	
	
	/* no need to display metabox stuff if page isn't set */
	if ( $page ) {
	
		wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
		wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
	
	}
		
}


add_action( 'butler_after_options', 'butler_options_close_form' );

function butler_options_close_form() {
	
		echo '<input type="hidden" name="btr_admin_options_nonce" value="' . wp_create_nonce( 'btr_admin_options_nonce' ) . '"/>';
			
		echo '<p class="submit"><input type="submit" name="btr_submit_options" value="Save" class="button-primary btr-save-options"></p>';
		
	echo '</form>';	
		
}


add_action( 'butler_options_meta_box_content', 'butler_options', 10, 2 );
add_action( 'butler_options_group', 'butler_options', 10, 2 );

function butler_options( $group, $fields = false ) {
					
	butler_fields( $fields );
	
	echo '<input type="hidden" name="btr_options_group[]" value="' . $group . '">';

}