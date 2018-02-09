<?php

function padma_framework_maintenance_dashboard_data() {
	
	/* we reset the dashboard data in version 1.1.0 cause the data format as changed */	
	delete_option( 'padma_api_request_products' );

}


function padma_framework_maintenance_admin_options_group_name() {
	
	/* we update the options db group name with _options suffix since we updated the butler framework to 1.2.0 */
	if ( $option = get_option( 'padma_framework' ) ) {
	
		update_option( 'padma_framework_options', $option );
		
		delete_option( 'padma_framework' );
		
	}

}