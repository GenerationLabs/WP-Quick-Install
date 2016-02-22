<?php
/*
Script Name: WP Quick Install - PHP ONLY VERSION
Author: Jonathan Buttigieg
Contributors: Julio Potier, Brandon Knudsen
Script URI: http://wp-quick-install.com
Version: 1.5.0
Licence: GPLv3
Last Update: 21 Feb 2016
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



/*

Start of Install:

 */




$__check = check_before_upload();


if ( $__check['db'] == "error etablishing connection" ) {
	die('Error Establishing a Database Connection.');
}

if ( $__check['wp'] == "error directory" ) {
	die('WordPress seems to be Already Installed.');
}


if(download_wp()){
	if(unzip_wp()){
		if(wp_config()){
			if(install_wp()){
				if(install_theme()){
					if(install_plugins()){
						if(success()){


							echo 'success';


						}else{die('failed on success');}
					}else{die('failed on install_plugins');}
				}else{die('failed on install_theme');}
			}else{die('failed on install_wp');}
		}else{die('failed on wp_config')}
	}else{die('failed on upzip_wp');}
}else{die('failed on download_wp');}
