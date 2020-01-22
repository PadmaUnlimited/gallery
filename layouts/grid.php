<?php

global $post;

$entries = $params['entries'];
$featured_img = $entries['featured-image'][0];

/* we store all images height in an array */
$image_height = array();

foreach ( $entries['album-images'] as $images )
	$image_height[] = $images['height'];

$auto_thumb = $this->set['img-crop-method'] == 'auto-thumb' ? true : false;

$manual_crop = $this->set['img-crop-method'] == 'crop' ? $this->set['img-crop-height'] : '';

/* image output */
foreach ( $params['album-images'] as $i => $this_image ) {

	/* we get the gallery effects */
	$effects = $this->gallery_effect($post->ID, $this_image['title']);

	$meta_count = '';
	$i          = $i + 1;

	/* we modify $i if the layout is set to albums */
	if ( $this->view() == 'albums' )
		$i = $params['count'];

	$meta_count = $this->view() != 'album' && $this->set['img-enable-title-count'] ? $params['meta-count'] : '';

	/* we get the image metas html */
	$image_metas_html = $this->display_metas($params['metas'], $meta_count);

	$alt_class = $i % 2 == 0 ? 'even' : 'odd';

	$last_class = $i % $this->columns == 0 ? ' last' : '';

	/* we build the ouptput */
	echo $this->display_row( $i, $params['image-count'] );

	echo '<div class="item ' . $alt_class . $last_class . '">';

		echo $image_metas_html['title-above-image'];

		/* if there is a featured image, we display it as the first thumb in albums view */
		if ( $this->view() == 'albums' && !empty($featured_img['id']) )
			echo $this->display_image($this_image, $image_metas_html['overlay'], $this->set['img-enable-crop-height'], $auto_thumb, min($image_height), $manual_crop, null, $featured_img['url'] );

		else
			echo $this->display_image($this_image, $image_metas_html['overlay'], $this->set['img-enable-crop-height'], $auto_thumb, min($image_height), $manual_crop );

		if ( $this->view() == 'albums' && $this->set['enable-lightbox'] && $this->set['lightbox-enable-loop'] ) {

			foreach ( array_slice($entries['album-images'], 1) as $album_images ) {

				$effects = $this->gallery_effect($post->ID, $album_images['title']);

				echo '<a href="' . $album_images['url'] . '" ' . $effects['lightbox'] . $this->display_tags('link', 'title', $album_images['title'], true) . ' ></a>';

			}

		}
		echo $image_metas_html['title-below-image'];

		echo $image_metas_html['description'];

	echo '</div><!-- item close -->';

}

/* we close the last row */
if ( $i == $params['image-count'] && $params['image-count'] > $this->columns )
	echo '</div><!-- row close -->';