<?php

$count = 1;

/* we build the output */
while ( $this->query->have_posts() ) : $this->query->the_post();

	/* we get all the entries datas */
	$entries = $this->gallery_entry();

	/* we get the notices */
	$notice = $this->notices($entries);

	echo '<div class="pur-album">';

		if ( !empty($this->set['album-show-title']) || !empty($this->set['album-show-description']) )
			echo '<div class="album-content-wrap">';

	    	/* we set title html */
	    	if ( $this->set['album-show-title'] )
	    		echo '<' . $this->set['album-title-type'] . ' class="album-title">' . $entries['title']  . '</' . $this->set['album-title-type'] . '>';

	    	/* we set description html */
	    	if ( $this->set['album-show-description'] )
	    		echo '<div class="album-description">' . $this->fetch_content($entries['description']) . '</div>';


	    if ( !empty($this->set['album-show-title']) || !empty($this->set['album-show-description']) )
	    	echo '</div>';

    	/* if there isn't any image, we display a notice */
    	if ( empty($entries['album-images']) ) {

    		echo $notice['no-image'];

    	} else {


	    	/* grid params */
	    	if ( $this->set['layout'] == 'grid' )
	    		$params = array(
					'entries' => $entries,
					'album-images' => $entries['album-images'],
					'image-count' => count($entries['album-images']),
					'metas' => 'this_image'
				);

	    	/* slider params */
	    	if ( $this->set['layout'] == 'slider' )
	    		$params = array(
					'entries' => $entries,
					'album-images' => $entries['album-images'],
					'metas' => 'this_image',
					'count' => $count
				);

	    	echo $this->render('layouts/' . $this->set['layout'] . '.php', $params);

	    }

	echo '</div><!-- album close -->';

	$count++;

endwhile;