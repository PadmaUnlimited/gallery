<?php

$entries 	  = $params['entries'];
$album_images = $entries['album-images'];

/* slider output */  
$i = $params['count'];

/* we store all images height in an array */
$image_height = array();

foreach ( $album_images as $images )
	$image_height[] = $images['height'];
	
$manual_crop = $this->set['slider-height'] == 'crop' ? $this->set['slider-crop-height'] : '';

$animate_height = $this->set['slider-height'] == 'animate' && $this->set['slider-direction'] != 'vertical' ? true : false;

/* we build the ouptput */
echo '<div class="slider-wrap">';

	echo '<div id="slider-' . $this->block['id'] . '-' . $i . '" class="slider-item flexslider clear">';
	
		if ( $this->is_visual_editor() )
			echo $this->notice['slider-disabled'];
	
		if ( $this->is_visual_editor() == false )
			echo '<div class="slider-loading"></div>';
														
		echo '<ul class="slides">';
		
		/* we only load the right number of images in the VE, improve speed */
		$slider_images = $this->is_visual_editor() ? array_slice($album_images, 0, 3) : $album_images;
		
		foreach ( $slider_images as $images ) { 
			
			/* we get the image metas html */
			$image_metas_html = $this->display_metas($images);
			
			echo '<li>';
															
				echo $this->display_image($images, $image_metas_html['overlay'], true, false, min($image_height), $manual_crop, $animate_height);
				
			echo '</li>';
											
		}
		
		echo '</ul>';
			
	echo '</div><!-- item close -->';
	
echo '</div>';
	

/* pager output */
if ( $this->set['slider-pager'] ) {

	echo '<div class="pager-wrap" data-thumb-count="' . count($album_images) . '">';

		if ( $this->set['slider-enable-pager-thumbs'] ) {
			
			/* we get the block dimensions */
			$block_dim = $this->dimensions(count($album_images));
					
			$pager_force_center = $block_dim['pager-force-center'] ? 'margin-right: auto !important; margin-left: auto !important;' : '';
		
			if ( $this->set['slider-pager-show-all'] ) {
				
				/* we get the block dimensions */
				echo '<div class="thumbs-item pager" id="pager-' . $this->block['id'] . '-' . $i . '">';
				
					foreach ( $album_images as $i => $this_thumb ) {
					
						$last = ($i + 1) == count($album_images) ? 'last' : '';
							
						echo '<div style="max-width: ' . $block_dim['pager-col-width-pct'] . '%; margin-right: ' . $block_dim['pager-thumb-spacing'] * 2 . '%;" class="pager-item ' . $last . '">';
					
							echo $this->display_pager_image($this_thumb, count($album_images));
						
						echo '</div>';
					
					}
								
				echo '</div>';
					
			} else {
			
				/* we only load the right number of thumbnails in the VE, improve speed */
				$slider_pager_thumbs = $this->is_visual_editor() ? array_slice($album_images, 0, $this->set['slider-thumb-count']) : $album_images;
							
					echo '<div style="max-width: ' . $block_dim['pager-width'] . '; ' . $pager_force_center . '" id="carousel-' . $this->block['id'] . '-' . $i . '" class="carousel-item pager flexslider">';
						
						echo '<ul class="slides">';
						
							foreach ( $slider_pager_thumbs as $i => $this_thumb ) {
							
								$i = $i + 1;
								
								echo '<li>';
									
									echo '<div class="pager-item">';
									
										echo $this->display_pager_image($this_thumb);
										
									echo '</div>';
							
								echo '</li>';
								
							}
						
						echo '</ul>';
				
					echo '</div>';
					
			}
		
		} else {				
			
			echo '<ul id="pager-' . $this->block['id'] . '-' . $i . '" class="nav-item pager">';
			
				foreach ( $params['album-images'] as $i => $value ) {
					
					$i = $i + 1;
					
					echo '<li>' . $i . '</li>';
					
				}
						
			echo '</ul>';
		
		}
	
	echo '</div><!-- pager close -->';
	
} 