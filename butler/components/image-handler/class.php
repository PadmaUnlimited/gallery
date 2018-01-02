<?php

class butlerImageHandler {

	private $url;
	
	private $width;
	
	private $height;
	
	private $crop;
	
	private $folder = 'miscellaneous';
	
	private $array;
	
	private $upload_dir;
	
	private $upload_url;
	
	private $img_path;
	
	private $basename;
	
	private $ext;
			
	private $destfilename;
	
	private $query;
	
	private $db_image;
	
	private $db_group;
	
	public $db_prefix = 'butler_resized_images_';
	
	public function __construct() {
		
		$upload_url = wp_upload_dir();
		$this->upload_dir = $upload_url['basedir'] . '/butler/';
		$this->upload_url = $upload_url['baseurl'] . '/butler/';
		$this->db_groups = get_option( $this->db_prefix . 'groups' );
		
		if ( !$this->db_groups )
			$this->db_groups = array();
			
		if ( is_ssl() )
			$this->upload_url = preg_replace( '/^http:\/\//', 'https://', $upload_url );
			
	}
	
	
	private function process() {
			
		$this->img_path = butler_url_to_path( $this->url );
		$info = pathinfo( $this->img_path );	

		if ( !file_exists( $this->img_path ) ) 
			return false;
							
		$this->basename = $info['basename'];
		$this->ext = $info['extension'];
		
		return $this->set_image_dimensions();
			
	}
		
	
	private function image_exist() {
		
		if ( !$this->db_image )
			$this->db_image = array();
			
		if ( !array_key_exists( $this->query, $this->db_image ) )
			return false;
			
		$image = $this->array ? $this->db_image[$this->query]['url'] : $this->db_image[$this->query];
		
		if ( !file_exists( butler_url_to_path( $image ) ) )
			return false;
						
		return true;
			
	}
	
	
	private function set_image_dimensions() {
	
		list( $originial_width, $originial_height ) = @getimagesize( $this->img_path );
		
		$dimensions = image_resize_dimensions( $originial_width, $originial_height, $this->width, $this->height, $this->crop );
		
		if ( !$dimensions ) 
			return false;
		
		$this->width = $dimensions[4];
		$this->height = $dimensions[5];
		
		return $this->prepare_resize();
	
	}
	
	
	private function prepare_resize() {
	
		$upload_url = wp_upload_dir();
		$upload_dir = $this->upload_dir . $this->folder . '/';
		$upload_url = $this->upload_url . $this->folder . '/';
		$suffix = "{$this->width}x{$this->height}";
		$filname = str_replace( '.' . $this->ext, '', $this->basename );
		$this->destfilename = "{$upload_dir}{$filname}-{$suffix}.{$this->ext}";
		$new_url = str_replace( $upload_dir, $upload_url, $this->destfilename );
		
		return $this->resize( $new_url );
		
	}
	
	
	private function resize( $new_url ) {
												
		$editor = wp_get_image_editor( $this->img_path );
		
		if ( is_wp_error( $editor ) || is_wp_error( $editor->resize( $this->width, $this->height, $this->crop ) ) )
			return false;
		
		$resized_file = $editor->save( $this->destfilename );
		
		if ( !is_wp_error( $resized_file ) )
			$this->url = $new_url;
		else
			return false;
		
		return true;
	
	}
	
	
	private function add_db_group() {
				
		if ( array_key_exists( $this->folder, $this->db_groups ) )	
			return true;
		
		$this->db_groups[$this->folder] = true;
		
		update_option( $this->db_prefix . 'groups', $this->db_groups );
			
	}
	
	
	private function add_db_image( $resized_image ) {
				
		if ( !$this->db_image )
			$this->db_image = array();
			
		$this->db_image[$this->query] = $resized_image;
		
		update_option( $this->db_prefix . $this->folder, $this->db_image );
			
	}
	
	
	private function remove_db_group( $group, $all = false ) {
	
		if ( $all ) {
			
			foreach ( $this->db_groups as $folder => $status )
				delete_option( $this->db_prefix . $folder );
				
			delete_option( $this->db_prefix . 'groups' );
			
			return;
		
		}
			
		if ( array_key_exists( $group, $this->db_groups ) )
			unset( $this->db_groups[$group] );
		
		if ( empty( $this->db_groups ) )
			delete_option( $this->db_prefix . 'groups' );
		else
			update_option( $this->db_prefix . 'groups', $this->db_groups );
		
		delete_option( $this->db_prefix . $group );
			
	}
	
	
	public function delete( $folder, $all ) {

		if ( $all ) {
			
			butler_remove_folder( $this->upload_dir );
			$this->remove_db_group( false, true );
		
		} elseif ( $folder === null ) {
		
			butler_remove_folder( $this->upload_dir . $this->folder );
			$this->remove_db_group( $this->folder );
		
		} else {
		
			butler_remove_folder( $this->upload_dir . $folder );
			$this->remove_db_group( $folder );
			
		}	
	
	}
	
		
	public function get_resized( $url, $width, $height, $crop, $folder, $array ) {
	
		$this->url = $url;
		$this->width = $width;
		$this->height = $height;
		$this->crop = $crop;
		$this->array = $array;
		$this->query = $url.$width.$height.$crop.$folder.$array;
		$this->folder = !empty($folder) ? $folder : $this->folder;
		$this->db_image = get_option( $this->db_prefix . $this->folder );
				
		if ( $this->image_exist() )
			return $this->db_image[$this->query];
		
		$this->process();
					
		if ( $this->array )
			$return = array(
				'url' => $this->url,
				'width' => $this->width,
				'height' => ( $this->height ? $this->height : 'auto' )
			);
		else
			$return = $this->url;
		
		$this->add_db_group();	
		$this->add_db_image( $return );
		
		return $return;
		
	}

}