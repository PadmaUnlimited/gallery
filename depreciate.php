<?php

function hwr_gallery_depreciate_hooks() {
	
	$base = '.block-type-hwr-gallery .hwr-gallery';
	
	$double = '<span class="double-indent"></span>';
		
	return array(
		
		/* header */
		array(
			'id' => 'header-all-view',
			'name' => '<div class="element-header">All Views</div>',
			'selector' => '',
			'properties' => array('')
		),
		array(
			'id' => 'block-container',
			'name' => 'Block Container',
			'selector' => $base,
			'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'overflow', 'text-shadow')
		),
		array(
			'id' => 'block-before-',
			'name' => 'Before Block',
			'selector' => $base . ' .hwr-block-before',
			'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'overflow', 'text-shadow')
		),
		array(
			'id' => 'block-title',
			'name' => 'Block Title',
			'selector' => $base . ' .hwr-block-title',
			'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'text-shadow')
		),
		array(
			'id' => 'block-title-alt',
			'name' => 'Block Title Alt',
			'selector' => $base . ' .hwr-block-title span',
			'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'text-shadow')
		),
		array(
			'id' => 'block-content',
			'name' => 'Block Description',
			'selector' => $base . ' .hwr-block-content',
			'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'text-shadow')
		),
		array(
			'id' => 'block-footer',
			'name' => 'Block Footer',
			'selector' => $base . ' .hwr-block-footer',
			'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'text-shadow')
		),
		array(
			'id' => 'block-after',
			'name' => 'After Block',
			'selector' => $base . ' .hwr-block-after',
			'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'overflow', 'text-shadow')
		),
		array(
			'id' => 'items-container',
			'name' => 'Items Container',
			'selector' => $base . ' .hwr-album',
			'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'overflow', 'text-shadow')
		),
		array(
			'id' => 'readon-link',
			'name' => 'Readon Link',
			'selector' => $base . ' .readon-link a',
			'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'text-shadow'),
			'states' => array(
				'hover' => $base . ' .readon-link a:hover', 
				'active' => $base . ' .readon-link a:active'
			)
		),
		array(
			'id' => 'image-container',
			'name' => 'Image Container',
			'selector' => $base . ' .item, ' . $base . ' .slider-item',
			'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'padding', 'nudging', 'overflow', 'text-shadow')
		),
		array(
			'id' => 'image-wrap',
			'name' => 'Image',
			'selector' => $base . ' .image-wrap',
			'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'padding', 'nudging', 'overflow', 'text-shadow')
		),
		/* sub header */
		array(
			'id' => 'sub-header-grid',
			'name' => '<div class="element-sub-header">Grid Layout</div>',
			'selector' => '',
			'properties' => array('')
		),
		array(
			'id' => 'image-title',
			'name' => $double . 'Image Title',
			'selector' => $base . ' .image-title',
			'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'text-shadow')
		),
		array(
			'id' => 'image-title-count',
			'name' => $double . 'Image Title Count',
			'selector' => $base . ' .image-title .album-count',
			'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'text-shadow')
		),
		array(
			'id' => 'image-description',
			'name' => $double . 'Image Description',
			'selector' => $base . ' .image-description',
			'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'overflow', 'text-shadow')
		),
		/* sub header */
		array(
			'id' => 'sub-header-slider',
			'name' => '<div class="element-sub-header">Slider Layout</div>',
			'selector' => '',
			'properties' => array('')
		),
		array(
			'id' => 'pagination-container',
			'name' => $double . 'Pagination Container',
			'selector' => $base . ' .pager',
			'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'overflow', 'text-shadow')
		),
		array(
			'id' => 'pagination-thumb',
			'name' => $double . 'Pagination Thumbnails',
			'selector' => $base . ' .pager-item',
			'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'padding', 'nudging', 'overflow', 'text-shadow'),
			'states' => array(
				'hover' => $base . ' .pager-item:hover', 
				'active' => $base . ' .hwr-active-slide .pager-item,' . $base . ' .pager-item.hwr-active'
			)
		),
		/* sub header */
		array(
			'id' => 'sub-header-overlay',
			'name' => '<div class="element-sub-header">Overlay</div>',
			'selector' => '',
			'properties' => array('')
		),
		array(
			'id' => 'overlay-container',
			'name' => $double . 'Overlay Container',
			'selector' => $base . ' .overlay-wrap',
			'properties' => array('background', 'borders', 'rounded-corners', 'box-shadow')
		),
		array(
			'id' => 'overlay-title',
			'name' => $double . 'Overlay Title',
			'selector' => $base . ' .overlay-title',
			'properties' => array('fonts', 'padding', 'text-shadow')
		),
		array(
			'id' => 'overlay-caption',
			'name' => $double . 'Overlay Caption',
			'selector' => $base . ' .overlay-caption',
			'properties' => array('fonts', 'padding', 'text-shadow')
		),
		array(
			'id' => 'overlay-image',
			'name' => $double . 'Overlay Image',
			'selector' => $base . ' .overlay-image',
			'properties' => array('background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging')
		),
		/* header */
		array(
			'id' => 'header-album-view',
			'name' => '<div class="element-header">Album View</div>',
			'selector' => '',
			'properties' => array('')
		),
		array(
			'id' => 'album-content-wrap',
			'name' => 'Album Content',
			'selector' => $base . ' .album-content-wrap',
			'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'overflow', 'text-shadow')
		),
		array(
			'id' => 'album-title',
			'name' => 'Album Title',
			'selector' => $base . ' .album-title',
			'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'overflow', 'text-shadow')
		),
		array(
			'id' => 'album-description',
			'name' => 'Album Description',
			'selector' => $base . ' .album-description',
			'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'overflow', 'text-shadow')
		),
		/* header */
		array(
			'id' => 'header-media-view',
			'name' => '<div class="element-header">Media View</div>',
			'selector' => '',
			'properties' => array('')
		),
		array(
			'id' => 'media-image-title',
			'name' => 'Media Image Title',
			'selector' => $base . ' .media-view .image-title',
			'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'text-shadow')
		),
		array(
			'id' => 'media-image-description',
			'name' => 'Media Image Description',
			'selector' => $base . ' .media-view .image-description',
			'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'text-shadow')
		),
		array(
			'id' => 'image-nav-btn',
			'name' => 'Next &amp; Previous',
			'selector' => $base . ' .image-nav a',
			'properties' => array('fonts', 'background', 'borders', 'rounded-corners', 'box-shadow', 'margins', 'padding', 'nudging', 'text-shadow'),
			'states' => array(
				'hover' => $base . ' .image-nav a:hover', 
				'active' => $base . ' .image-nav a:active'
			)
		)
	);

}
	
