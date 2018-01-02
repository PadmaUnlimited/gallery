<?php

class butlerAdminUpdate {

    /* The slug of the product. This will be provided to you by the Headway team. */
	private $slug;

	/* The path to the product. If a theme, use get_option('stylesheet'), plugins use plugin_basename(__FILE__) */
	private $path;

	/* Name of product */
	private $name;

	/* Either 'theme', 'plugin', 'widget' or 'tool' */
	private $type;

	/* Current version */
	private $current_version;

	/* Determines whether or not the transient will be modified to allow upgrades. We recommend that child themes are notify only. **/
	private $notify_only;

	/* The URL to the product info */
	private $product_url;

	/* Transient ID. This will automatically be set */
    private $transient_id;

    
    public function __construct( $args ) {
    
        /* we set the class private variables */
        $this->slug = $args['slug'];
        $this->path = $args['path'];
        $this->name = $args['name'];
        $this->type = $args['type'];
        $this->current_version = $args['current_version'];
        $this->product_url = '';
        
        if ( !is_admin() && !is_super_admin() )
			return;

		$this->transient_id = 'btr-update-check-' . $this->type . '-' . $this->slug;
		
		$themes_or_plugins = $this->type == 'theme' ? 'themes' : 'plugins';
			
		add_action( 'load-update-core.php', array( $this, 'clear_transient' ) );
		add_filter( 'site_transient_update_' . $themes_or_plugins, array( $this, 'intercept_transient' ) );
		add_filter( 'transient_update_' . $themes_or_plugins, array( $this, 'intercept_transient' ) );
		
		if ( $this->type != 'theme' )
			add_filter( 'plugins_api', array( $this, 'plugin_information' ), 10, 3 );

    }
	
	
	public function retrieve_update_info() {

		$update_info = get_transient( $this->transient_id );		
		
		/* we query themeButler if the transient is expired */
		if ( empty( $update_info ) ) {
						
	        $api = new butlerApi( '189514ec97134faf6cad4e1f3648de1e' );
	        
	        $request = $api->raw_request( 'get_update', array( 'slug' => $this->slug, 'type' => $this->type ) );
	        	        	        	        	        	        
	        /* we store the result for 30 minutes if the request failed */
			if ( !$request || !is_serialized( $request ) )
				return $this->set_temporary_transient();
				
			$update_info = maybe_unserialize( $request );
	
			/* we set transient for 24 hours */
			set_transient( $this->transient_id, $update_info, 60 * 60 * 24 );
		
		}
		
		/* we don't go any further if the item is up to date */
		if ( butler_get( 'new_version', $update_info ) && version_compare( $this->current_version, butler_get( 'new_version', $update_info ), '>=' ) )
			return false;
			
		return $update_info;
          		
	}	
	
	
	public function intercept_transient( $value ) {

		$update_info = $this->retrieve_update_info();
				
		if ( !$update_info )
			return $value;

		/* we only allow the update if the license is valid or if it is a free product. If a little clever user removes this condition, he won't be able to download since we don't return the download link if the licence isn't valid (ha, who is the clever one now) */
		if ( butler_get( 'is_free', $update_info ) === true || butler_get( 'license_status', $update_info ) === 'active' ) {

			if ( $this->type == 'theme' ) {

				$obj = array();
				$obj['slug'] = $this->slug;
				$obj['url'] = $this->product_url;
				$obj['package'] = $update_info['download_url'];
				$obj['tested'] = $update_info['tested'];
				$obj['requires'] = $update_info['requires']; 
				$obj['new_version'] = $update_info['new_version'];
				$obj['changelog_url'] = $update_info['changelog_url'];
				$value->response[$this->path] = $obj;
				

			} else {

				$obj = new stdClass();
				$obj->slug = $this->slug;
				$obj->url = $this->product_url;
				$obj->package = $update_info['download_url'];
				$obj->tested = $update_info['tested'];
				$obj->requires = $update_info['requires']; 
				$obj->new_version = $update_info['new_version'];
				$obj->changelog_url = $update_info['changelog_url'];
	            $value->response[$this->path] = $obj;
			
			}

		}
		
		return $value;

	}
		
	
	public function plugin_information( $false, $action, $args ) {
		
		/* we don't do anything if it is not about this plugin */
		if ( !isset( $args->slug ) || $args->slug != $this->slug )
			return $false;
		
		$update_info = $this->retrieve_update_info();
			
		$value = new stdClass();
		$value->new_version = $update_info['new_version'];
		$value->tested = $update_info['tested'];
		$value->requires = $update_info['requires'];
		//$value->compatibility['3.7']['2.0.0'] = array('90', false, false);
		$value->slug = $this->slug;
		
		/* we only get the changelog in the plugin details lightbox */
		if ( butler_get( 'plugin' ) == $this->slug ) {
		
			$request = wp_remote_get( $update_info['changelog_url'] );
			$server_response = wp_remote_retrieve_body( $request );
			
			if ( is_wp_error( $request ) || !$server_response || $request['response']['code'] != 200 )
				$server_response = '<div id="message" class="updated"><p>Oops, it seems like the changelog is not available from our server at the moment, please try later!</p></div>';
			
			$value->sections = array(  
	        	'changelog' => $server_response 
	      	);
	      	
	    }
		
		return $value;

	}


	public function set_temporary_transient() {

		set_transient( $this->transient_id, array( 'new_version' => $this->current_version ), 60 * 30 );
		
		return false;

	}


	public function clear_transient() {

		delete_transient( $this->transient_id );

	}

}