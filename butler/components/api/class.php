<?php
/**
* @package   Butler Framework
* @author    ThemeButler http://themebutler.com
*/

class butlerApi {

    var $version = "1.0.0";

    var $api_url;
    
    var $api_key;
    
    var $config;
        
    function __construct( $api_key = null, $config = array() ) {
    
        $this->api_url = "http://192.138.23.240/~devtbr/?tbr-api=php&version=" . $this->version;
        
        $this->api_key = $api_key;
        
        $this->config = $config;
        
    }
    
    
    function refresh( $method, $expiration = null, $params = array() ) {
    	
    	$request_id = $this->request_id( $method );
    	
    	$db_response = get_option( $request_id );
    	
    	if ( !$db_response )
    		return $this->request( $method, $expiration, $params );
    		
    	$new_expiration = $expiration != null ? $expiration : $db_response['expiration'];
    	   	
    	$db_response['expiration'] = 0;
    	
    	update_option( $request_id, $db_response );
    	
    	return $this->request( $method, $new_expiration, $params );
    	
    }
    
    
    function request_id( $method ) {
    
    	return 'btr_api_request_' . str_replace( '-', '_', $method );
    	
    }
    
            
    function request( $method, $expiration = null, $params = array() ) {
    
    	$request_id = $this->request_id( $method );
    	
    	/* we set a default expiration if it isn't passed with the request */	
    	if ( !isset( $expiration ) )
    		$expiration = 60*60*7;
        
        $db_response = get_option( $request_id );
                
        /* we diplay display the db response if it is set and if the expiration isn't passed */
        if ( $db_response && ( $db_response['last-update'] + $db_response['expiration'] ) > time() ) {
        	        	
        	$response = array(
        		'call' => 'database',
        		'response' => $db_response
        	);
        
        }
        /* we treat the server request */
	    else {
	    
	    	$host = $this->api_url["host"];
	    	$params["api-key"] = $this->api_key;
	    	$this->config['user-agent'] = 'butlerApi/' . $this->version . '\r\n';
	    	$this->config['body'] = $params;
	    	        
	    	$request = wp_remote_post( $this->api_url . "&method=" . $method, $this->config );
	    	$server_response = wp_remote_retrieve_body( $request );
	    	
	        if ( !$server_response || $request['response']['code'] != 200 ) {
	        	        	
	        	$code = isset( $request->errors['http_request_failed'] ) ? 1 : 2;
	        	
	        	/* we return it the db data if it exists */
	        	if ( !empty( $db_response ) )
	        		$response = array(
	        			'call' => 'database',
	        			'response' => $db_response,
	        			'code' => $code
	        		);
	        	/* if the first api calls fails, then we unfortunately have to display an error */ 
	        	elseif ( isset( $request->errors['http_request_failed'] ) )
	        		$response = array(
	        			'call' => 'server', 
	        			'response' => array( 'error' => $request->errors['http_request_failed'][0] ), 
	        			'code' => $code
	        		);
	        	else
		        	$response = array(
		        		'call' => 'server', 
		        		'response' => array( 'error' => 'Whoops, it look like there is problem. This is what we got:' . $server_response ),
		        		'code' => $code
		        	);
		        				
			} else {

				$serve_response = array(
					'last-update' => time(),
					'expiration' => $expiration,
					'params' => $params,
					'data' => @unserialize( $server_response )
				);
	        		        	
	        	/* we update the data in the db and set a new expiration */
	        	update_option( $request_id, $serve_response );
	        		
	        	$response = array(
	        		'call' => 'server', 
	        		'response' => $serve_response,
	        		'code' => wp_remote_retrieve_response_code( $request )
	        	);
	        		        	
	        }
	    
	    }
        
        return $response;
        
    }
    
    function raw_request( $method, $params = array() ) {
        
    	$host = $this->api_url["host"];
    	$params["api-key"] = $this->api_key;
    	$this->config['user-agent'] = 'butlerApi/' . $this->version . '\r\n';
    	$this->config['body'] = $params;
    	        
    	$request = wp_remote_post( $this->api_url . "&method=" . $method, $this->config );
        		    	
        return wp_remote_retrieve_body( $request );
        
    }

}