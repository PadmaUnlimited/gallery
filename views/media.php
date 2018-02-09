<?php

global $post;

$attachment = true;

if ( $this->is_visual_editor() ) {

	$post = $this->query->posts[0];
	$attachment = false;
	
}

/* we make sure it doesn't return an error on the media post if there isn't any album */
if ( !isset($post->ID) )
	return;
	
$attachment_id = $post->ID;

$album_count = '';

$uploaded_to = isset($_GET['uploaded_to']) ? $_GET['uploaded_to'] : '';

$entries = $this->gallery_entry($attachment_id, $attachment);
	
if ( !empty($uploaded_to) ) {
	
	$album_entries = $this->gallery_entry($uploaded_to);
	$album_count = !empty($uploaded_to) ? count($album_entries['album-images']) : '';
	
}

/* we create the image output */
echo '<div class="pur-album clear">'; 
	
	/* grid params */
	$params = array(
		'entries' => $entries,
		'album-images' => array($entries['album-images'][0]),
		'image-count' => 1,
		'metas' => 'this_image',
		'meta-count' => ''
	);
	
	echo $this->render('layouts/grid.php', $params);
		
echo '</div><!-- album close -->';

/* we create the image navigation */
if ( $this->set['img-nav'] && !empty($uploaded_to) ) {
	
	$entries = $this->gallery_entry($uploaded_to);
		
	/* we prepare only the album_images according to the $uploaded_to */
	foreach ( $entries['album-images'] as $key => $value ) {
	
		if( $attachment_id == $value['id'] )
			$this_image_key = $key;
		
	}
			
	/* we make sure the pagination is not returning an error */
	if ( !isset($this_image_key) )
		return;

	echo '<div class="image-nav clear">';
	
		if ( $this_image_key != 0 ) {
			
			$previous = $entries['album-images'][$this_image_key - 1]['id'];
		
			echo '<a class="previous" href="' . add_query_arg(array('uploaded_to' => $uploaded_to), get_attachment_link($previous)) . '">' . $this->set['img-nav-previous-text'] . '</a>';
			
		}
		
		if ( $this_image_key != end(array_keys($entries['album-images'])) ) {
		
			$next = $entries['album-images'][$this_image_key + 1]['id'];
		
			echo '<a class="next" href="' . add_query_arg(array('uploaded_to' => $uploaded_to), get_attachment_link($next)) . '">' . $this->set['img-nav-next-text'] . '</a>';
			
		}
		
	echo '</div><!-- image navigation close -->';
	
}

/* we display just a sample in the VE to allow user styling pagination * */
elseif ( $this->set['img-nav'] && $this->is_visual_editor() ) {

	echo '<div class="image-nav clear">';
		
		echo '<a class="previous" href="">' . $this->set['img-nav-previous-text'] . '</a>';
		echo '<a class="next" href="">' . $this->set['img-nav-next-text'] . '</a>';
		
	echo '</div><!-- image navigation close -->';
}