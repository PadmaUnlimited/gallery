<?php
/**
* @package   HeadwayRocket Api
* @author    HeadwayRocket http://headwayrocket.com
*/

class HwrApi {

    var $version = "1.0.1";

    var $api_url;
            
    function __construct() {
    
        $this->api_url = "http://headwayrocket.com/api/headwayrocket/" . $this->version . "/api.php?output=php";
                        
    }
          
    
    function request_id( $method ) {
    
    	return 'hwr_api_request_' . str_replace( '-', '_', $method );
    	
    }
    
            
    function request( $method, $expiration = null, $refresh = false, $params = array() ) {
    	
    	if ( !$expiration )
    		$expiration = 60*60*7;
    	
    	$request_id = $this->request_id( $method );
    	        
        $db_response = get_option( $request_id );
        
        $default_response = array(
        	'last-update' => 'Unavailable',
        	'expiration' => $expiration,
        	'params' => $params,
        	'data' => '',
        	'error' => false,
        	'code' => ''
        );
                
        if ( !$refresh && $db_response && ($db_response['last-update'] + $db_response['expiration']) > time() ) {
        	
        	return $db_response;
        
        } else {
	    	    	        
	    	$request = wp_remote_post( $this->api_url . "&method=" . $method, array( 'method' => 'POST',	'body' => $params ) );
	    		    		    		    		    		    		    	
	        if ( !isset( $request['body'] ) || !is_serialized( $request['body'] ) || $request['response']['code'] != 200 ) {
	        		        	
	        	$code = isset( $request->errors['http_request_failed'] ) ? 1 : 2;
	        		        	
	        	if ( !empty( $db_response ) )
	        		return array_merge( $default_response, array( 'code' => $code, 'data' => $db_response['data'] ) );
	        	else
	        		return array_merge( $default_response, array( 'error' => true, 'code' => $code ) );
		        				
			} else {
														
				$serve_response = array(
					'last-update' => time(),
					'expiration' => $expiration,
					'params' => $params,
					'data' => @unserialize( wp_remote_retrieve_body( $request ) )
				);
				
				$return = array_merge( $default_response, $serve_response );
				
				update_option( $request_id, $return );
	        	
	        	return $return;
	        			        		        	
	        }
	    
	    }
        
        return false;
        
    }

}