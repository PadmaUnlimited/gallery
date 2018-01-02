<?php
/**
* @package   Butler Framework
* @author    ThemeButler http://themebutler.com
*/

class butlerAdminPostTypes {
	
	static $taxonomies = array();
	static $taxonomies_default = array();
	
	static function init( $args ) {
	
		if ( !is_admin() )
			return;
		
		/* we register the taxonomies before the post type so that it can be added to post type default agrs */
		if ( isset( $args['register_taxonomies'] ) && $args['register_taxonomies'] )
			self::register_taxonomy( $args );
		
		self::register_custom_post_type( $args );
		
	}
	
	
	static function register_custom_post_type( $args ) {
		
		$default = array(
		    'public' => true,
		    'publicly_queryable' => true,
		    'show_in_nav_menus' => true,
		    'show_in_menu' => true,
		    'labels' => array(
		        'name' => _x( $args['plurial_name'], $args['id'] ),
		        'singular_name' => _x( $args['singular_name'], $args['id'] ),
		        'add_new' => _x( 'Add New', $args['id'] ),
		        'add_new_item' => sprintf( __( 'Add New %s', $args['id'] ), __( $args['singular_name'], $args['id'] ) ),
		        'edit_item' => sprintf( __( 'Edit %s', $args['id'] ), __( $args['singular_name'], $args['id'] ) ),
		        'new_item' => sprintf( __( 'New %s', $args['id'] ), __( $args['singular_name'], $args['id'] ) ),
		        'all_items' => sprintf( __( 'All %s', $args['id'] ), __( $args['plurial_name'], $args['id'] ) ),
		        'view_item' => sprintf( __( 'View %s', $args['id'] ), __( $args['singular_name'], $args['id'] ) ),
		        'search_items' => sprintf( __( 'Search %a', $args['id'] ), __( $args['plurial_name'], $args['id'] ) ),
		        'not_found' =>  sprintf( __( 'No %s Found', $args['id'] ), __( $args['singular_name'], $args['id'] ) ),
		        'not_found_in_trash' => sprintf( __( 'No %s Found In Trash', $args['id'] ), __( $args['singular_name'], $args['id'] ) ),
		        'parent_item_colon' => '',
		        'menu_name' => __( $args['menu_name'], $args['id'] )
		    ),
		    'taxonomies' => self::$taxonomies,
		    'rewrite' => array( 'slug' => strtolower( $args['menu_name'] ) ),
		    'has_archive' => true,
		    'menu_position' => 20,
		);
	
		register_post_type( $args['id'], array_merge( $default, $args ) );
			
	}
	
	
	static function register_taxonomy( $args ) {
					
		/* we reset the $taxonomies */
		self::$taxonomies = array();
		self::$taxonomies_default = array();
	
		foreach ( $args['register_taxonomies'] as $tax ) {
		
			$singular = $args['singular_name'] . ' ' . $tax['singular_name'];
			$plurial = $args['plurial_name'] . ' ' . $tax['plurial_name'];
		
			$default = array(
				'id' => $args['id'] . '_' . strtolower( $tax['plurial_name'] ),
			    'labels' => array(  
			        'name' => _x( $plurial, $args['id'] ),
			        'menu_name' => _x( $tax['plurial_name'], $args['id'] ),
			        'singular_name' => _x( $singular, $args['id'] ),  
			        'search_items' => __( 'Search ' . $plurial, $args['id'] ),  
			        'popular_items' => __( 'Popular ' . $plurial, $args['id'] ),  
			        'all_items' => __( 'All ' . $plurial, $args['id'] ),  
			        'parent_item' => __( 'Parent ' . $singular, $args['id'] ),  
			        'edit_item' => __( 'Edit ' . $singular, $args['id'] ),  
			        'update_item' => __( 'Update ' . $singular, $args['id'] ),  
			        'add_new_item' => _x( 'Add New ' . $singular, $args['id'] ),  
			        'new_item_name' => __( 'New ' . $singular, $args['id'] ),  
			        'separate_items_with_commas' => __( 'Separate ' .$plurial . ' with commas', $args['id'] ),  
			        'add_or_remove_items' => __( 'Add or remove ' . $plurial, $args['id'] ),  
			        'choose_from_most_used' => __( 'Choose from most used ' . $plurial, $args['id'] )  
			    ),  
			    'public' => true,  
			    'hierarchical' => true,  
			    'show_ui' => true,  
			    'show_in_nav_menus' => true,  
			    'query_var' => true,
			    'rewrite' => array( 'slug' => strtolower( $args['menu_name'] ) . '-' . strtolower( $tax['plurial_name'] ) )
			);
			
			$merged = array_merge( $default, $tax );
			
			register_taxonomy( $merged['id'], $args['id'], $merged );
			
			self::$taxonomies[] = $merged['id'];
			
			if ( $merged['hierarchical'] ) {
				
				/* we add the unvategorized term */
				wp_insert_term( 'Uncategorized', $merged['id'] );
				
				self::$taxonomies_default[$merged['id']] = $merged['id'];
				
				/* we force to have at least one category selected */
				add_action( 'publish_' . $args['id'], array( __CLASS__, 'default_category' ) );
				
			}
		
		}
	
	}
		
		
	static function default_category( $id ) {
	
		$post = get_post( $id );
		$taxonomies = get_object_taxonomies( $post->post_type );
		$default = self::$taxonomies_default;
				
		foreach ( $taxonomies as $taxonomie )
			if ( isset( $default[$taxonomie] ) )
				if( !has_term( '', array_search( $default[$taxonomie], $default ), $id ) )
					wp_set_object_terms( $id, array( 'Uncategorized' ), $default[$taxonomie] );
		
	}
	
}