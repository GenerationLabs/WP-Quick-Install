<?php
/*
Script Name: WP Quick Install
Author: Jonathan Buttigieg
Contributors: Julio Potier
Script URI: http://wp-quick-install.com
Version: 1.4.1
Licence: GPLv3
Last Update: 08 jan 15
*/

@set_time_limit( 0 );

define( 'WP_API_CORE'				, 'http://api.wordpress.org/core/version-check/1.7/?locale=' );
define( 'WPQI_CACHE_PATH'			, 'cache/' );
define( 'WPQI_CACHE_CORE_PATH'		, WPQI_CACHE_PATH . 'core/' );
define( 'WPQI_CACHE_PLUGINS_PATH'	, WPQI_CACHE_PATH . 'plugins/' );

require( 'data.php' );

require( 'inc/functions.php' );



// Create cache directories
if ( ! is_dir( WPQI_CACHE_PATH ) ) {
	mkdir( WPQI_CACHE_PATH );
}
if ( ! is_dir( WPQI_CACHE_CORE_PATH ) ) {
	mkdir( WPQI_CACHE_CORE_PATH );
}
if ( ! is_dir( WPQI_CACHE_PLUGINS_PATH ) ) {
	mkdir( WPQI_CACHE_PLUGINS_PATH );
}
