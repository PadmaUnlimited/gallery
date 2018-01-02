<?php


function butler_resize_image( $args ) {
	
	if ( !isset( $args['url'] ) || !isset( $args['width'] ) ) 
		return false;
	
	$defaults = array( 
		'height' => null,
		'crop' => true,
		'folder' => null,
		'array' => false,
	);
	
	$args = wp_parse_args( $args, $defaults );
	
	require_once( BUTLER_COMPONENTS_PATH . 'image-handler/class.php' );
	
	$instance = new butlerImageHandler();
	
	return $instance->get_resized( $args['url'], $args['width'], $args['height'], $args['crop'], $args['folder'], $args['array'] );
		
}


function butler_removed_resized_image_dir( $folder = null, $all = false ) {
				
	require_once( BUTLER_COMPONENTS_PATH . 'image-handler/class.php' );
	
	$instance = new butlerImageHandler();
	
	return $instance->delete( $folder, $all );
		
}


function butler_get_attachment_details( $post_id, $size = 'full' ) {
	
	$id = get_post_thumbnail_id( $post_id );
	$post = get_post( $id );
	$src = wp_get_attachment_image_src( $id, $size );
	
	$obj = new stdClass();
	$obj->id = $id;
	$obj->url = $src[0];
	$obj->width = $src[1];
	$obj->height = $src[2];
	$obj->alt = trim( strip_tags( get_post_meta( $id, '_wp_attachment_image_alt', true ) ) );
	$obj->title = $post->post_title;
	$obj->caption = $post->post_excerpt;
	$obj->description = $post->post_content;
	
	return $obj;

}