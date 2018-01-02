<?php
/**
* @package   headwayrocket
* @author    ThemeButler http://themebulter.com
*/

class HwrAdminModel {

	var $url;
	
	var $components = array();
	
	var $plugins = array();
	
	var $active_components = array();
	
	var $installed_components = array();
	
	var $last_update = null;
	
	var $components_request = array();
	
	var $refresh = false;


	function __construct() {
				
		butler_load( HEADWAYROCKET_COMPONENTS_PATH . 'api/class' );
		
		$this->url = butler_get( 'page' ) ? admin_url( 'admin.php?page=' . butler_get( 'page' ) ) : '';
		
		$this->refresh = butler_get( 'hwr-action' ) == 'refresh' ? true : false;
		
		$this->get_components();

		$this->plugins = get_plugins();	
		
		$this->actions();	
							
		add_action( 'admin_notices', array( &$this, 'admin_notices' ) );
		
	}
		
	
	function actions() {
	
		$response = false;
		
		$action = butler_get( 'hwr-action' );
				
		if ( $action == 'activate-component' )
			$response = $this->activate_component( butler_get( 'slug' ), true );
			
		if ( $action == 'deactivate-component' )
			$response = $this->deactivate_component( butler_get( 'slug' ), true );
		
		if ( $action == 'download-component' && butler_get( 'slug' ) )
			$response = $this->download_component( butler_get( 'slug' ), true);

		return $response;
		
	}

	
	function get_components() {
	
		$api = new HwrApi();
		
		$api_request = $api->request( 'products', 60*60*7, $this->refresh );
						
		$this->components_request = $api_request;
		
		if ( (isset( $api_request['error'] ) && $api_request['error'] || isset( $api_request['code'] ) && $api_request['code']) && !butler_get( 'hwr-notice' ) ) {
			
			wp_redirect( add_query_arg( array( 'hwr-notice' => 'request-error', 'error-code' => $api_request['code'] ) , $this->url ) );
			
			exit;
			
		}
		
		if ( isset( $api_request['data']['items'] ) ) {
										
			$this->components = $api_request['data']['items'];
						
			return $this->components;
			
		}
		
		return false;
				
	}
	
	
	function get_last_update() {
		
		$last_update = $this->components_request['last-update'];
		
		if ( is_numeric( $last_update ) )
			return date( "d M y - h:i A (e)", $last_update );
		
		return $last_update;
	
	}
	
	
	function get_filtered_components( $filter ) {
	
		if ( !$filter || $filter == 'all')
			return $this->components;
		
		$components = array();
		$filter = $filter . '_components';
		
		foreach ( $this->components as $component )
			if ( isset( $this->{$filter}[$component['slug']] ) && $this->{$filter}[$component['slug']] )
				$components[] = $component;
								
		return $components;
					
	}
	
	
	function get_installed_components() {
		
		$components = array();
		
		foreach ( $this->components as $component )
			if ( $this->is_install( $component['slug'] ) )
				$components[] = $component;
				
		return $components;
					
	}
	
	
	function get_active_components() {
		
		$components = array();
		
		foreach ( $this->components as $component )
			if ( $this->is_active( $component['slug'] ) )
				$components[] = $component;
				
		return $components;
					
	}
	
	
	function is_install( $slug ) {
		
		$return = false;
		
		if ( !isset( $this->installed_components[$slug] ) ) {
	
			if ( array_key_exists( $this->components[$slug]['filepath'], $this->plugins ) )
				$return = true;
		
			$this->installed_components[$slug] = $return;
			
		} else {
		
			$return = $this->installed_components[$slug];
		
		}
		
		return $return;
		
	}
	
	
	function is_active( $slug ) {
	
		$return = false;
	
		if ( !isset( $this->active_components[$slug] ) ) {
	
			$return =  is_plugin_active( $this->components[$slug]['filepath'] );
		
			$this->active_components[$slug] = $return;
			
		} else {
		
			$return = $this->active_components[$slug];
		
		}
		
		return $return;
					
	}

	
	function admin_notices() {
		
		$notice = '';
		
		$action = butler_get( 'hwr-notice' );
		
		if ( $action == 'request-error' )
			if ( butler_get( 'error-code' ) == 1 )
				$notice .= '<div id="message" class="error"><p>' . sprintf( __( 'Whoops! It would seem that you are not connected to the internet. Please check your internet connection and try again.', 'headwayrocket' ), butler_get( 'name' ) ) . '</p></div>' . "\n";
			else
				$notice .= '<div id="message" class="error"><p>' . sprintf( __( 'Whoops! Our update server seems to be offline. It’s likely that we’re doing maintainance on the server, so please try again later.', 'headwayrocket' ), butler_get( 'name' ) ) . '</p></div>' . "\n";

		if ( $action == 'component-activated' )
			$notice = '<div id="message" class="success updated fade"><p>' . sprintf( __( '<strong>%s</strong> has been activated successfully.', 'headwayrocket' ),  butler_get( 'name' ) ) . '</p></div>' . "\n";
		
		if ( $action == 'component-activation-error' )
			$notice = '<div id="message" class="error"><p>' . sprintf( __( 'There was an error activating <strong>%s</strong>, please try again.', 'headwayrocket' ), butler_get( 'activated-component' ) ) . '</p></div>' . "\n";
		
		if ( $action == 'component-deactivated' )
			$notice = '<div id="message" class="success updated fade"><p>' . sprintf( __( '<strong>%s</strong> has been deactivated successfully.', 'headwayrocket' ),  butler_get( 'name' ) ) . '</p></div>' . "\n";
						
		if ( $action == 'component-deactivation-error' )
			$notice = '<div id="message" class="error"><p>' . sprintf( __( 'There was an error deactivating <strong>%s</strong>, please try again.', 'headwayrocket' ), butler_get( 'activated-component' ) ) . '</p></div>' . "\n";
		
		if ( $action == 'component-downloaded' )
			$notice = '<div id="message" class="success updated fade"><p>' . sprintf( __( '<strong>%s</strong> has been downloaded successfully.', 'headwayrocket' ),  butler_get( 'name' ) ) . '</p></div>' . "\n";
			
		if ( $action == 'download-component-error' )
			$notice .= '<div id="message" class="error"><p>' . sprintf( __( 'There was an error downloading <strong>%s</strong>, please try again.', 'headwayrocket' ), butler_get( 'name' ) ) . '</p></div>' . "\n";
				
		echo $notice;
		
	}
	
	
	function get_action_button( $slug, $class = 'button-secondary' ) {
	
		$action = $text = $url = $target = '';
	
		if ( $this->is_active( $slug ) ) {
		
			$action = 'deactivate-component';
						
			$text = 'Deactivate';
		
		} elseif ( $this->is_install( $slug ) ) {
		
			$action = 'activate-component';
			
			$text = 'Activate';
		
		} elseif ( $this->components[$slug]['price'] == 0 ) {
		
			$action = 'download-component';
						
			$text = 'Download';
		
		} else {
			
			$url = $this->components[$slug]['buy_url'];
			
			$text = 'Buy';
			
			$class = 'button-primary';
			
			$target = 'target="_blank"';
		
		}
		
		if ( empty( $url ) )
			$url = add_query_arg(
				array(
					'hwr-action' => $action,
					'slug' => $this->components[$slug]['slug'],
				),
				$this->url
			);
		
		return '<a class="' . $class . '" ' . ($action ? 'data-action="' . $action . '"' : "") . ' href="' . $url . '" title="' . $text . ' ' . $this->components[$slug]['name'] . '" ' . $target . '>' . $text . '</a>';
	
	}
	
	
	function get_badge( $slug ) {
	
		$badge = $text = '';
	
		if ( $this->is_active( $slug ) ) {
		
			$badge = 'green';
						
			$text = 'Activated';
		
		} elseif ( $this->is_install( $slug ) ) {
		
			$badge = 'orange';
			
			$text = 'Installed';
		
		} elseif ( $this->components[$slug]['price'] === 0 ) {
		
			$badge = '';
						
			$text = 'Free';
		
		} else {
			
			$badge = '';
						
			$text = '$' . $this->components[$slug]['price'];
		
		}
		
		return '<span class="btr-badge-' . $badge . '">' . $text . '</span>';
		
	}
	
	
	function deactivate_component( $slug, $redirect = false ) {
	
		$deactivated = false;
	
		deactivate_plugins( $this->components[$slug]['filepath'] );
		
		$deactivated = true;
		
		if ( $redirect ) {
		
			if ( $deactivated ) {
				
				wp_redirect( add_query_arg( array( 'hwr-notice' => 'component-deactivated', 'name' => $this->components[$slug]['name'] ) , $this->url ) );
				
				exit;
				
			} else {
			
				wp_redirect( add_query_arg( array( 'hwr-notice' => 'component-deactivation-error', 'name' => $this->components[$slug]['name'] ) , $this->url ) );
				
				exit;
			
			}
						
		} else {
			
			if ( $deactivated )
				return true;
			else
				return false;
			
		}
	
	}
	
	
	function activate_component( $slug, $redirect = false, $from_download = false ) {
	
		$activated = false;
		
		activate_plugin( $this->components[$slug]['filepath'] );
		
		$activated = true;
		
		if ( $redirect ) {
		
			if ( $activated ) {
				
				$notice = $from_download ? 'component-downloaded-activated' : 'component-activated';
				
				wp_redirect( add_query_arg( array( 'hwr-notice' => $notice, 'name' => $this->components[$slug]['name'] ) , $this->url ) );
				
			} else {
			
				$notice = $from_download ? 'component-download-activation-error' : 'component-activation-error';
			
				wp_redirect( add_query_arg( array( 'hwr-notice' => $notice, 'name' => $this->components[$slug]['name'] ) , $this->url ) );
				
				exit;
			
			}
						
		} else {
			
			if ( $activated )
				return true;
			else
				return false;
			
		}
			
	}
	
	
	function download_component( $slug, $redirect = false ) {
					
		$component = $this->components[$slug];
		
		/* we make sure the component isn't install*/
		if ( !$this->is_install( $slug ) ) {

			$url = wp_nonce_url(
				add_query_arg(
					array(
						'hwr-action' => 'download-component',
						'slug' => $this->components[$slug]['slug'],
					),
					$this->url
				),
				'download-component'
			);
			
			$fields = array( sanitize_key( 'download-component' ) );
	
			if ( false === ($creds = request_filesystem_credentials( $url, '', false, false, $fields )) )
				return false;
	
			if ( !WP_Filesystem( $creds ) ) {
			
				request_filesystem_credentials( $url, '', true, false, $fields );
				
				return false;
			}
			
			$remote_file = download_url( $component['download_url'] );
			
			if ( is_wp_error( $remote_file ) ) {
			
				$downloaded = false;
			
			} else {
			 	
				$unzip = unzip_file( $remote_file, WP_PLUGIN_DIR );
	
				if ( is_wp_error( $unzip ) )
					$downloaded = false;
				else
					$downloaded = true;
					
				unlink( $remote_file );
					
			}
		
		} else  {
		
			$downloaded = true;
		
		}
				
		if ( $downloaded ) {
			
			if ( $redirect ) {
			
				wp_redirect( add_query_arg( array( 'hwr-notice' => 'component-downloaded', 'name' => $this->components[$slug]['name']) , $this->url ) );
				
				exit;
			
			}	
							
			return true;
					
		} else {
		
			if ( $redirect ) {
			
				wp_redirect( add_query_arg( array( 'hwr-notice' => 'download-component-error', 'name' => $this->components[$slug]['name'] ) , $this->url ) );
				
				exit;
			
			}				
		
			return false;
			
		}
			
	}
	
}