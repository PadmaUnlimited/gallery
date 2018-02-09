<?php

$post_count = $this->query->post_count;
$count      = 1;
	
/* we build the output */
echo '<div class="pur-album">'; 
	
	while ( $this->query->have_posts() ) : $this->query->the_post();
		
		/* we get all the entries datas */
		$entries = $this->gallery_entry();
		
		/* we get the notices */
		$notice = $this->notices($entries);
		
		if ( empty($entries['album-images']) ) {
		
			echo $notice['no-image'];
		
		} else {
				
			$params = array(
				'entries' => $entries,
				'album-images' => array($entries['album-images'][0]),
				'image-count' => $post_count,
				'metas' => 'entries',
				'meta-count' => count($entries['album-images']),
				'count' => $count
			);
		
	    	/* grid output */
	    	echo $this->render('layouts/' . $this->set['layout'] . '.php', $params);	
		
		}
		
		$count++;
			
	endwhile;
	
echo '</div><!-- album close -->';