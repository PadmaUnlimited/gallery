<?php
/*
Plugin Name: Padma Gallery
Plugin URI: http://www.padmaunlimited.com/plugins/gallery
Description: Simple, Flexible & Powerful - The Ultimate Gallery System for Padma.
Version: 1.0.0
Author: Padma Unlimited team
Author URI: https://www.padmaunlimited.com
License: GNU GPL v2
*/

/*

PUR = PADMA UNLIMITED ROCKET

*/

define('PADMA_GALLERY_VERSION', '0.0.2');
define('PADMA_GALLERY_PATH', plugin_dir_path(__FILE__));
define('PADMA_GALLERY_URL', plugin_dir_url(__FILE__));

/* we call the Butler framework */
include(PADMA_GALLERY_PATH . 'butler/butler.php');

if ( version_compare( BUTLER_VERSION, '1.2.1', '<' ) )
	return;

/* we call the PadmaRocket framework */
butler_load(PADMA_GALLERY_PATH . 'padmarocket/padmarocket');

if ( version_compare( PADMAROCKET_VERSION, '1.1.1', '<' ) )
	return;

/* register block
***************************************************************/
add_action('after_setup_theme', 'padma_gallery_register');

function padma_gallery_register() {

	if ( !class_exists('Padma') )
		return;

	require_once 'block-styling.php';	
	require_once 'block-options.php';
	require_once 'gallery-display.php';
	require_once 'depreciate.php';

	$class = 'PadmaGalleryBlock';
	$block_type_url = substr(WP_PLUGIN_URL . '/' . str_replace(basename(__FILE__), '', plugin_basename(__FILE__)), 0, -1);		
	$class_file = __DIR__ . '/block.php';
	$icons = array(
			'path' => __DIR__,
			'url' => $block_type_url
		);

	return padma_register_block(
		$class,
		$block_type_url,
		$class_file,
		$icons
	);

}


/* include wp admin scripts
***************************************************************/
add_action('admin_enqueue_scripts', 'padma_gallery_wp_scripts');

function padma_gallery_wp_scripts($hook) {

	global $post;

	/* we register all our scripts */
	wp_register_script('padma-gallery-wp-admin-js', plugins_url('/admin/js/wp-admin.js', __FILE__), array('jquery'), PADMA_GALLERY_VERSION);
	wp_register_script('pur-wp-uploader-js', plugins_url('/admin/js/wp-updoader.js', __FILE__), array('jquery','media-upload','thickbox'), PADMA_GALLERY_VERSION);

	/* we register all our stylesheets */
	wp_register_style( 'padma-gallery-wp-admin-css', plugins_url('/admin/css/wp-admin.css', __FILE__), false, PADMA_GALLERY_VERSION );
	wp_register_style( 'padma-gallery-wp-gallery-css', plugins_url('/admin/css/wp-gallery.css', __FILE__), false, PADMA_GALLERY_VERSION );
	wp_register_style( 'pur-wp-attachment-css', plugins_url('/admin/css/wp-attachment.css', __FILE__), false, PADMA_GALLERY_VERSION );
	wp_register_style( 'pur-wp-uploader-css', plugins_url('/admin/css/wp-uploader.css', __FILE__), false, PADMA_GALLERY_VERSION );

	/* we enqueue the admin css */
	wp_enqueue_style('padma-gallery-wp-admin-css');

	if ( version_compare( get_bloginfo( 'version' ), '3.8', '<' ) )
		wp_enqueue_style( 'padma-gallery-depreciate-wp-admin-css', plugins_url('/admin/css/depreciate-wp-admin.css', __FILE__), false, PADMA_GALLERY_VERSION );

	/* we only enqueue the gallery js and css on our gallery plugin */
	if ( isset($post->post_type) && $post->post_type == 'padma_gallery' ) {

		wp_enqueue_style('padma-gallery-wp-gallery-css');
		wp_enqueue_script('padma-gallery-wp-admin-js');

	}

	/* we only enqueue uploader js and css on if it is open in an iframe */
	if ( isset($_GET['padma_media_editor']) && $_GET['padma_media_editor'] == true ) {

		wp_enqueue_style('pur-wp-uploader-css');
		wp_enqueue_script('pur-wp-uploader-js');

	}

	/* we only enqueue the gallery attachment css for the media custom field */
	if ( isset($post->post_type) && $post->post_type == 'attachment' )
		wp_enqueue_style('pur-wp-attachment-css');

}


/* we add the meta boxes to post type(s) set
***************************************************************/
add_action('admin_head', 'padma_gallery_wp_head');

function padma_gallery_wp_head() {

	$options = wp_parse_args( wp_get_referer() );

	$output = '<script type="text/javascript">';

		$output .= 'padma_admin_url = "' .  admin_url() . '";';

		if ( isset($options['padma_action']) && $options['padma_action'] === 'done_editing' )
			$output .= 'self.parent.tb_remove();';

	$output .= '</script>';

	if ( isset($options['padma_action']) && $options['padma_action'] === 'done_editing')
		$output .= '<style type="text/css"">body { display:none; }</style>';

	echo $output;

}


/* create custom post type
***************************************************************/
add_action('init', 'padma_gallery_init');

function padma_gallery_init() {
	/* we register the post type */
    $args = array(
        'public' => true,
        'publicly_queryable' => true,
        'show_in_nav_menus' => true,
        'show_in_menu' => true,
        'labels' => array(
            'name' => _x('Albums', 'padma-gallery'),
            'singular_name' => _x('Album', 'padma-gallery'),
            'add_new' => _x('Add New Album', 'padma-gallery'),
            'add_new_item' => sprintf( __( 'Add New %s', 'padma-gallery' ), __( 'Album', 'padma-gallery' ) ),
            'edit_item' => sprintf( __( 'Edit %s', 'padma-gallery' ), __( 'Album', 'padma-gallery' ) ),
            'new_item' => sprintf( __( 'New %s', 'padma-gallery' ), __( 'Album', 'padma-gallery' ) ),
            'all_items' => sprintf( __( 'All %s', 'padma-gallery' ), __( 'Albums', 'padma-gallery' ) ),
            'view_item' => sprintf( __( 'View %s', 'padma-gallery' ), __( 'Album', 'padma-gallery' ) ),
            'search_items' => sprintf( __( 'Search %a', 'padma-gallery' ), __( 'Albums', 'padma-gallery' ) ),
            'not_found' =>  sprintf( __( 'No %s Found', 'padma-gallery' ), __( 'Album', 'padma-gallery' ) ),
            'not_found_in_trash' => sprintf( __( 'No %s Found In Trash', 'padma-gallery' ), __( 'Album', 'padma-gallery' ) ),
            'parent_item_colon' => '',
            'menu_name' => __( 'Gallery', 'padma-gallery' )
        ),
        'supports' => array(
            'title',
            'thumbnail'
        ),
        'has_archive' => true,
        'taxonomies' => array('gallery_categories', 'gallery_tags'),
        'menu_position' => 20,
        'rewrite' => array('slug' => 'albums') ,
        'menu_icon' => 'dashicons-format-gallery'
    );

    register_post_type('padma_gallery', $args);

    /* we create our own taxonomie for the categories */
    $labels = array(
        'name'                       => _x('Album Categories', 'padma_gallery'),
        'singular_name'              => _x('Album Category', 'padma_gallery'),
        'search_items'               => __('Search Album Categories', 'padma_gallery'),
        'popular_items'              => __('Popular Album Categories', 'padma_gallery'),
        'all_items'                  => __('All Album Categories', 'padma_gallery'),
        'parent_item'                => __('Parent Album Category', 'padma_gallery'),
        'edit_item'                  => __('Edit Album Category', 'padma_gallery'),
        'update_item'                => __('Update Album Category', 'padma_gallery'),
        'add_new_item'               => _x('Add New Album Category', 'padma_gallery'),
        'new_item_name'              => __('New Album Category', 'padma_gallery'),
        'separate_items_with_commas' => __('Separate Album Categories with commas', 'padma_gallery'),
        'add_or_remove_items'        => __('Add or remove Album Categories', 'padma_gallery'),
        'choose_from_most_used'      => __('Choose from most used Album Categories', 'padma_gallery')
    );
    $args = array(
        'labels'                     => $labels,
        'public'                     => true,
        'hierarchical'               => true,
        'show_ui'                    => true,
        'show_in_nav_menus'          => true,
        'query_var'                  => true,
        'rewrite' 					 => array('slug' => 'album-categories')
    );

    register_taxonomy( 'gallery_categories', 'padma_gallery', $args );

    /* we create our own taxonomie for the tags */
    $labels = array(
        'name'                       => _x( 'Album Tags', 'padma_gallery' ),
        'singular_name'              => _x( 'Album Tag', 'padma_gallery' ),
        'search_items'               => __( 'Search Album Tags', 'padma_gallery' ),
        'popular_items'              => __( 'Popular Album Tags', 'padma_gallery' ),
        'all_items'                  => __( 'All Album Tags', 'padma_gallery' ),
        'parent_item'                => __( 'Parent Album Tag', 'padma_gallery' ),
        'edit_item'                  => __( 'Edit Album Tag', 'padma_gallery' ),
        'update_item'                => __( 'Update Album Tag', 'padma_gallery' ),
        'add_new_item'               => _x( 'Add New Album Tag', 'padma_gallery' ),
        'new_item_name'              => __( 'New Album Tag', 'padma_gallery' ),
        'separate_items_with_commas' => __( 'Separate Album Tags with commas', 'padma_gallery' ),
        'add_or_remove_items'        => __( 'Add or remove Album Tags', 'padma_gallery' ),
        'choose_from_most_used'      => __( 'Choose from most used Album Tags', 'padma_gallery' )
    );
    $args = array(
        'labels'                     => $labels,
        'public'                     => true,
        'hierarchical'               => false,
        'show_ui'                    => true,
        'show_in_nav_menus'          => true,
        'query_var'                  => true,
        'rewrite' 					 => array('slug' => 'album-tags')
    );

    register_taxonomy( 'gallery_tags', 'padma_gallery', $args );

}


/* if no category is selected we set it to uncategorized */
add_action('publish_padma_gallery', 'padma_gallery_default_category');

function padma_gallery_default_category($padma_gallery_id) {

	if(!has_term('', 'gallery_categories', $padma_gallery_id)){

		wp_set_object_terms($padma_gallery_id, array('Uncategorized'), 'gallery_categories');
	}

}


/* we add our custom columns for the gallery post type */
add_filter( 'manage_edit-padma_gallery_columns', 'padma_gallery_set_columns' );

function padma_gallery_set_columns($columns) {

	$columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => __( 'Title' ),
		'images' => __( 'Images' ),
		'gallery-categories' => __( 'Album Categories' ),
		'gallery-tags' => __( 'Album Tags' ),
		'date' => __( 'Date' )
	);


    return $columns;

}


/* we create the output for our custom columns */
add_action( 'manage_padma_gallery_posts_custom_column', 'padma_gallery_fill_columns' );

function padma_gallery_fill_columns($column) {

	global $post;

    switch($column) {

    	case 'images' :

    		$album_imgs = get_post_meta( $post->ID, 'padma_gallery_image', true );

			if ( !empty($album_imgs) ) {

				$album_img = array_slice($album_imgs, 0, 4);

			    foreach ( $album_img as $i => $album_img_id ) {

			    	$get_album_img = get_post( $album_img_id );
			    	$album_img_src = wp_get_attachment_image_src( $album_img_id, 'full' );
			    	$album_img_src = padma_resize_image($album_img_src[0], 40, 40, true);

			    	echo '<div class="thumbnail-wrap"><div class="thumbnail"><img src="' . $album_img_src . '" /></div></div>';

			    }

			    $nbr_images = count($album_imgs) . ' images';

			} else {

				$album_img = array();

				$nbr_images = 'no image';

			}

			for ($i = 1; $i <= 4 - count($album_img); $i++)
			    echo '<div class="thumbnail-wrap"><div class="thumbnail"><img src="' .  plugins_url('/admin/images/no-image.png', __FILE__) . '" ></div></div>';

			echo '<a class="nbr-images" href="' . get_site_url() .'/wp-admin/post.php?post=' . $post->ID . '&action=edit">' . $nbr_images . '</span>';

        break;

        case 'featured-image' :

            if ( has_post_thumbnail($post->ID) )
                echo get_the_post_thumbnail($post->ID, array(30, 30));

            else
                echo 'No Featured Image';

        break;

        case 'gallery-categories' :

        	$taxonomy_type = 'gallery_categories';
        	$empty         = 'No Album Category';

			echo padma_gallery_taxonomy_columns_output($taxonomy_type, $empty);

        break;

        case 'gallery-tags':

        	$taxonomy_type = 'gallery_tags';
        	$empty         = 'No Album Tag';

        	echo padma_gallery_taxonomy_columns_output($taxonomy_type, $empty);

        break;

    }
}


/* we build the taxonomy culomns ouput */
function padma_gallery_taxonomy_columns_output($taxonomy_type, $empty) {

	global $post;
	global $typenow;

	$terms = get_the_terms($post->ID, $taxonomy_type);

	if ( $terms ) {
		$out = array();

		foreach ( $terms as $term ) {

			$out[] = '<a href="edit.php?post_type=' . $typenow . '&' . $taxonomy_type .'=' . $term->slug . '">' . esc_html(sanitize_term_field('name', $term->name, $term->term_id, $taxonomy_type, 'display')) . '</a>';

		}

		$return = join( ', ', $out );

	} else {

		$return = $empty;

	}

	return $return;
}


/* we register the meta boxes */
add_action('init', 'padma_gallery_meta_box');

function padma_gallery_meta_box() {

	global $post;

	$gallery_meta_boxes = array();

	$gallery_meta_boxes[] = array(
		'id' => 'display_padma_gallery',
		'title' => 'Album',
		'pages' => array('padma_gallery'),
		'context' => 'normal',
		'priority' => 'high',
		'fields' => array(
			array(
				'name' => '',
				'desc' => '',
				'id' => 'padma_gallery_count',
				'type' => 'thumbnail-count',
				'std' => 'no image'
			),
			array(
				'name' => '',
				'desc' => 'Add images which will be used for your album.',
				'id' => 'padma_gallery_image',
				'type' => 'gallery',
				'std' => ''
			)
		)
	);

	$gallery_meta_boxes[] = array(
		'id' => 'display_padma_gallery_options',
		'title' => 'Album block options',
		'pages' => array('padma_gallery'),
		'context' => 'normal',
		'priority' => 'high',
		'fields' => array(
			array(
				'name' => 'Album Description',
				'desc' => 'Enter your custom Readon Link here. Leave it empty if you want to keep the WordPress Readon Link default behaviour.',
				'id' => 'padma_gallery_description',
				'type' => 'wysiwyg',
				'std' => ''
			),
			array(
				'name' => 'Album Caption',
				'desc' => 'Enter your album caption which will be used if you choose to display the albums as thumbnails.',
				'id' => 'padma_gallery_caption',
				'type' => 'text',
				'std' => ''
			),
			array(
				'name' => 'Custom Readon Link',
				'desc' => 'Enter your custom Readon Link here. Leave it empty if you want to keep the WordPress Readon Link default behaviour.',
				'id' => 'padma_gallery_readon_link',
				'type' => 'text',
				'std' => ''
			)
		)
	);

	foreach ( $gallery_meta_boxes as $gallery_meta_box ) {

		$my_box = new PadmaGalleryMetaBox($gallery_meta_box);

	}

}


/* we create the meta boxes */
class PadmaGalleryMetaBox {

	protected $_gallery_meta_box;


	function __construct($gallery_meta_box) {

		if ( !is_admin() )
			return;

		$this->_gallery_meta_box = $gallery_meta_box;

		add_action('admin_menu', array(&$this, 'add'));
		add_action('save_post', array(&$this, 'save'));
		add_filter( 'attachment_fields_to_edit', array(&$this, 'attachment_fields_edit'), 10, 2);
		add_filter( 'attachment_fields_to_save', array(&$this, 'attachment_fields_save'), 10, 2);

	}


	/* we add the meta boxes to post type(s) set */
	function add() {

		$this->_gallery_meta_box['context'] = empty($this->_gallery_meta_box['context']) ? 'normal' : $this->_gallery_meta_box['context'];

		$this->_gallery_meta_box['priority'] = empty($this->_gallery_meta_box['priority']) ? 'high' : $this->_gallery_meta_box['priority'];

		foreach ( $this->_gallery_meta_box['pages'] as $page ) {

			add_meta_box($this->_gallery_meta_box['id'], $this->_gallery_meta_box['title'], array(&$this, 'show'), $page, $this->_gallery_meta_box['context'], $this->_gallery_meta_box['priority']);

		}

	}


	/* we built the metabox content */
	function show() {

		global $post;

		/* we use nonce for verification */
		$output = '<input type="hidden" name="meta_box_nonce" value="' . wp_create_nonce(basename(__FILE__)) . '" />';

		$output .= '<div class="padma-gallery-options">';

		foreach ( $this->_gallery_meta_box['fields'] as $field ) {

			/* we get current post meta data */
			$meta = get_post_meta($post->ID, $field['id'], true);

			$output .= $field['name'] ? '<label for="' . $field['id'] . '">' . $field['name'] . '</label>' : '';

			switch ($field['type']) {

				case 'text':
					$value = $meta ? $meta : $field['std'];

					$output .= '<input type="text" name="' . $field['id'] . '" id="' . $field['id'] . '" value="' . $value . '" size="30" />';
					$output .= '<span class="pur-field-description">' . $field['desc'] . '</span>';

				break;

				case 'thumbnail-count':

					$value = $meta != 0 ? $meta : $field['std'];

					$output .= '<input type="hidden" name="' . $field['id'] . '" id="' . $field['id'] . '" value="' . $meta . '" size="30" />';

					$output .= '<span class="pur-thumbnail-count"><strong>Images: </strong><span>' . $value . '</span></span>';

				break;

				case 'wysiwyg':

					$meta = html_entity_decode($meta, ENT_QUOTES, 'UTF-8');
					$value = $meta ? $meta : $field['std'];
			        $settings = array(
			        		'wpautop' => false,
			        		'media_buttons' => false,
			        	);

			       	ob_start();
			       		wp_editor( $value, $field['id'], $settings );
			       		$output .= ob_get_contents();
			       	ob_end_clean();

			       	$output .= '<span class="pur-field-description">' . $field['desc'] . '</span>';

				break;

				case 'gallery':

					$thumbnail_count = get_post_meta($post->ID, 'padma_gallery_count', true);

					$src = '';

					$thumbnail_option = array(
					    'width' => get_option('thumbnail_size_w'),
					    'height' => get_option('thumbnail_size_h'),
					    'crop' => get_option('thumbnail_crop'),
					);

					$output .= '<input class="pur-upload-image button button-primary" type="button" value="Add image" /><span class="drag-notice">Drad and drop to re-order</span>';

					$output .= '<div data-thumb-w="' . $thumbnail_option['width'] . '" data-thumb-h="' . $thumbnail_option['height'] . '" class="pur-thumbnails ui-sortable">';

					$output .= '<input type="hidden" name="' . $field['id'] . '" value="" />';

					if ( $meta ) {

						foreach ( $meta as $attachment => $id ) {

							if ( $id ) {

								$img = wp_get_attachment_image_src( $id, 'full' );
								$img_src = $img[0];
								$img_w = $img[1];
								$img_h = $img[2];
								$thumb_crop = $thumbnail_option['crop'] == 1 ? true : false;

								if ( $img_src ) {

									$img_src = padma_resize_image($img_src, $thumbnail_option['width'], $thumbnail_option['height'], $thumb_crop);

									$output .= '<div class="pur-thumbnail" style="width: ' . $thumbnail_option['width'] . 'px; height: ' . $thumbnail_option['height'] . 'px">';

										$output .= '<input class="pur-image-value" type="hidden" name="' . $field['id'] . '[]" value="' . $id .'" />';
										$output .= '<div class="pur-image-wrap">';

											$output .= '<img src="' . $img_src . '" />';

										$output .= '</div>';

										$output .= '<div class="pur-thumbnail-toolbar">';
											$output .= '
												<ul>
													<li><a href="#" class="pur-drag pur-btn">Drag</a></li>
													<li><a href="#" class="pur-edit pur-btn">Edit</a></li>
													<li><a href="#" class="pur-remove pur-btn">Remove</a></li>
												</ul>';
										$output .= '</div>';

									$output .= '</div>';

								}

							}

						}

					}

					$output .= '<div class="pur-no-thumbnail">
									<p>You do not have any image selected!</p>
									<p><a href="#" class="pur-upload-image browser button button-hero">Add image</a></p>
								</div>';

					$output .= '</div>';

					$output .= '<span class="pur-field-description">' . $field['desc'] . '</span>';

				break;

			}

		}

		$output .= '</div>';

		echo $output;

	}


	/* we make sure the metabox content can be modified and saved */
	function save($post_id) {

		/* we verify the nonce before proceeding. */
		if ( !isset($_POST['meta_box_nonce']) || !wp_verify_nonce($_POST['meta_box_nonce'], basename(__FILE__)) )
			return $post_id;

		/* we verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything */
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
			return $post_id;

		/* we check permissions */
		if ( 'page' == $_POST['post_type'] ) {

			if ( !current_user_can('edit_page', $post_id) )
				return $post_id;

		} elseif ( !current_user_can('edit_post', $post_id) ) {

			return $post_id;

		}

		foreach ( $this->_gallery_meta_box['fields'] as $field ) {

			$name = $field['id'];

			$old = get_post_meta($post_id, $name, true);
			$new = $_POST[$field['id']];

			if ( $field['type'] == 'wysiwyg' )
				$new = wpautop($new);

			if ( $new && $new != $old )
				update_post_meta($post_id, $name, $new);

			elseif ( '' == $new && $old )
				delete_post_meta($post_id, $name, $old);

		}

	}


	/* we add a custom field to an attachment */
	function attachment_fields_edit($form_fields, $post) {

	    $form_fields['pur-custom-link']['label'] = __( 'Custom Link', 'padma_gallery' );
	    $form_fields['pur-custom-link']['value'] = get_post_meta($post->ID, '_padma_custom_link', true);
	    $form_fields['pur-custom-link']['helps'] = __( 'Added by PadmaRocket Gallery Block', 'padma_gallery' );

	    return $form_fields;
	}


	/* we make sure the attachment custom field can be modified and saved */
	function attachment_fields_save($post, $attachment) {

	    if ( isset($attachment['pur-custom-link']) )
	        update_post_meta($post['ID'], '_padma_custom_link', $attachment['pur-custom-link']);

	    return $post;

	}

}