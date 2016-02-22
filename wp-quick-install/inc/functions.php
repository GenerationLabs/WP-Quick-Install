<?php

if ( ! function_exists( '_' ) ) {
	function _( $str ) {
		echo $str;
	}
}

function sanit( $str ) {
	return addcslashes( str_replace( array( ';', "\n" ), '', $str ), '\\' );
}



function check_before_upload(){

	$data = array();

	/*--------------------------*/
	/*	We verify if we can connect to DB or WP is not installed yet
	/*--------------------------*/

	// DB Test
	try {
		$db = new PDO('mysql:host='. $_db['dbhost'] .';dbname=' . $_db['dbname'] , $_db['uname'], $_db['pwd'] );
	}
	catch (Exception $e) {
		$data['db'] = "error etablishing connection";
	}

	// WordPress test
	if ( file_exists( $directory . 'wp-config.php' ) ) {
		$data['wp'] = "error directory";
	}

	// We send the response
	return $data;

}



function download_wp(){

	// Get WordPress language
	$language = substr( LANGUAGE, 0, 6 );

	// Get WordPress data
	$wp = json_decode( file_get_contents( WP_API_CORE . $language ) )->offers[0];

	/*--------------------------*/
	/*	We download the latest version of WordPress
	/*--------------------------*/

	if ( ! file_exists( WPQI_CACHE_CORE_PATH . 'wordpress-' . $wp->version . '-' . $language  . '.zip' ) ) {
		file_put_contents( WPQI_CACHE_CORE_PATH . 'wordpress-' . $wp->version . '-' . $language  . '.zip', file_get_contents( $wp->download ) );

		return true;
	}else{
		return false;
	}

}



function unzip_wp(){
	$directory = DIRECTORY;
	$language = LANGUAGE;
	// Get WordPress language
	$language = substr( $language, 0, 6 );

	// Get WordPress data
	$wp = json_decode( file_get_contents( WP_API_CORE . $language ) )->offers[0];

	/*--------------------------*/
	/*	We create the website folder with the files and the WordPress folder
	/*--------------------------*/

	// If we want to put WordPress in a subfolder we create it
	if ( ! empty( $directory ) ) {
		// Let's create the folder
		mkdir( $directory );

		// We set the good writing rights
		chmod( $directory , 0755 );
	}

	$zip = new ZipArchive;

	// We verify if we can use the archive
	if ( $zip->open( WPQI_CACHE_CORE_PATH . 'wordpress-' . $wp->version . '-' . $language  . '.zip' ) === true ) {

		// Let's unzip
		$zip->extractTo( '.' );
		$zip->close();

		// We scan the folder
		$files = scandir( 'wordpress' );

		// We remove the "." and ".." from the current folder and its parent
		$files = array_diff( $files, array( '.', '..' ) );

		// We move the files and folders
		foreach ( $files as $file ) {
			rename(  'wordpress/' . $file, $directory . '/' . $file );
		}

		rmdir( 'wordpress' ); // We remove WordPress folder
		unlink( $directory . '/license.txt' ); // We remove licence.txt
		unlink( $directory . '/readme.html' ); // We remove readme.html
		unlink( $directory . '/wp-content/plugins/hello.php' ); // We remove Hello Dolly plugin
		return true;
	}else{
	return false;
	}

}

function wp_config(){
	$directory = DIRECTORY;
	/*--------------------------*/
	/*	Let's create the wp-config file
	/*--------------------------*/

	// We retrieve each line as an array
	$config_file = file( $directory . 'wp-config-sample.php' );

	// Managing the security keys
	$secret_keys = explode( "\n", file_get_contents( 'https://api.wordpress.org/secret-key/1.1/salt/' ) );

	foreach ( $secret_keys as $k => $v ) {
		$secret_keys[$k] = substr( $v, 28, 64 );
	}

	// We change the data
	$key = 0;
	foreach ( $config_file as &$line ) {

		if ( '$table_prefix  =' == substr( $line, 0, 16 ) ) {
			$line = '$table_prefix  = \'' . sanit( WPPREFIX ) . "';\r\n";
			continue;
		}

		if ( ! preg_match( '/^define\(\'([A-Z_]+)\',([ ]+)/', $line, $match ) ) {
			continue;
		}

		$constant = $match[1];

		switch ( $constant ) {
			case 'WP_DEBUG'	   :

				// Debug mod
				if ( (int) WPCONFIGDEBUG == 1 ) {
					$line = "define('WP_DEBUG', 'true');\r\n";

					// Display error
					if ( (int) WPCONFIGDEBUGDISPLAY == 1 ) {

						$line .= "define('WP_DEBUG_DISPLAY', 'true');\r\n";
					}

					// To write error in a log files
					if ( (int) WPCONFIGDEBUGLOG == 1 ) {

						$line .= "define('WP_DEBUG_LOG', 'true');\r\n";
					}
				}

				// We add the extras constant
				if ( ! empty( UPLOADDIR ) ) {

					$line .= "define('UPLOADS', '" . sanit( UPLOADDIR ) . "');";
				}

				if ( (int) POSTREVISIONS >= 0 ) {

					$line .= "define('WP_POST_REVISIONS', " . (int) POSTREVISIONS . ");";
				}

				if ( (int) DISALLOWFILEEDIT == 1 ) {
					$line .= "define('DISALLOW_FILE_EDIT', true);";
				}

				if ( (int) AUTOSAVETIMEINTERVAL >= 60 ) {
					$line .= "define('AUTOSAVE_INTERVAL', " . (int) AUTOSAVETIMEINTERVAL . ");";
				}

				if ( ! empty( WPCOMAPIKEY ) ) {
					$line .= "\r\n\n " . "/** WordPress.com API Key */" . "\r\n";
					$line .= "define('WPCOM_API_KEY', '" . WPCOMAPIKEY . "');";
				}

				$line .= "define('WP_MEMORY_LIMIT', '96M');" . "\r\n";

				break;
			case 'DB_NAME'     :
				$line = "define('DB_NAME', '" . sanit( WPDBNAME ) . "');\r\n";
				break;
			case 'DB_USER'     :
				$line = "define('DB_USER', '" . sanit( WPUSER ) . "');\r\n";
				break;
			case 'DB_PASSWORD' :
				$line = "define('DB_PASSWORD', '" . sanit( WPPASS ) . "');\r\n";
				break;
			case 'DB_HOST'     :
				$line = "define('DB_HOST', '" . sanit( WPDBHOST ) . "');\r\n";
				break;
			case 'AUTH_KEY'         :
			case 'SECURE_AUTH_KEY'  :
			case 'LOGGED_IN_KEY'    :
			case 'NONCE_KEY'        :
			case 'AUTH_SALT'        :
			case 'SECURE_AUTH_SALT' :
			case 'LOGGED_IN_SALT'   :
			case 'NONCE_SALT'       :
				$line = "define('" . $constant . "', '" . $secret_keys[$key++] . "');\r\n";
				break;

			case 'WPLANG' :
				$line = "define('WPLANG', '" . sanit( LANGUAGE ) . "');\r\n";
				break;
		}
	}
	unset( $line );

	$handle = fopen( $directory . 'wp-config.php', 'w' );
	foreach ( $config_file as $line ) {
		fwrite( $handle, $line );
	}
	fclose( $handle );

	// We set the good rights to the wp-config file
	chmod( $directory . 'wp-config.php', 0666 );

	return true;
}


function install_wp(){
	global $posts;
	$directory = DIRECTORY;
	/*--------------------------*/
	/*	Let's install WordPress database
	/*--------------------------*/

	define( 'WP_INSTALLING', true );

	/** Load WordPress Bootstrap */
	require_once( $directory . 'wp-load.php' );

	/** Load WordPress Administration Upgrade API */
	require_once( $directory . 'wp-admin/includes/upgrade.php' );

	/** Load wpdb */
	require_once( $directory . 'wp-includes/wp-db.php' );

	// WordPress installation
	wp_install( TITLE, USERLOGIN, USEREMAIL, (int) SEO, '', USERPASSWORD );

	// We update the options with the right siteurl et homeurl value
	$protocol = ! is_ssl() ? 'http://' : 'https://';
	$url = trim( $protocol.SITEURL, '/' );

	update_option( 'siteurl', $url );
	update_option( 'home', $url );

	/*--------------------------*/
	/*	We remove the default content
	/*--------------------------*/

	if ( WPDEFAULTCONTENT == '1' ) {
		wp_delete_post( 1, true ); // We remove the article "Hello World"
		wp_delete_post( 2, true ); // We remove the "Exemple page"
	}

	/*--------------------------*/
	/*	We update permalinks
	/*--------------------------*/
	if ( ! empty( PERMALINK ) ) {
		update_option( 'permalink_structure', PERMALINK );
	}

	/*--------------------------*/
	/*	We update the media settings
	/*--------------------------*/


	 update_option( 'uploads_use_yearmonth_folders', (int) YEARMONTHFOLDERS );

	/*--------------------------*/
	/*	We add the pages we found in the data.ini file
	/*--------------------------*/



		// We verify if we have at least one page
		if ( count( $posts ) >= 1 ) {

			foreach ( $posts as $postdata ) {



				foreach($postdata as $key => $value){

					// We retrieve the page title
					if ( 'title' == $key ) {
						$post['title'] = $value;
					}

					// We retrieve the status (publish, draft, etc...)
					if ( 'status' == $key ) {
						$post['status'] = $value;
					}

					// On retrieve the post type (post, page or custom post types ...)
					if ( 'type' == $key ) {
						$post['type'] = $value;
					}

					// We retrieve the content
					if ( 'content' == $key ) {
						$post['content'] = $value;
					}

					// We retrieve the slug
					if ( 'slug' == $key ) {
						$post['slug'] = $value;
					}

					// We retrieve the title of the parent
					if ( 'parent' == $key ) {
						$post['parent'] = $value;
					}
				}

				if ( isset( $post['title'] ) && !empty( $post['title'] ) ) {

					$parent = get_page_by_title( trim( $post['parent'] ) );
					$parent = $parent ? $parent->ID : 0;

					// Let's create the page
					$args = array(
						'post_title' 		=> trim( $post['title'] ),
						'post_name'			=> $post['slug'],
						'post_content'		=> trim( $post['content'] ),
						'post_status' 		=> $post['status'],
						'post_type' 		=> $post['type'],
						'post_parent'		=> $parent,
						'post_author'		=> 1,
						'post_date' 		=> date('Y-m-d H:i:s'),
						'post_date_gmt' 	=> gmdate('Y-m-d H:i:s'),
						'comment_status' 	=> 'closed',
						'ping_status'		=> 'closed'
					);
					wp_insert_post( $args );

				}

			}
		}

		return true;

}


function install_theme(){
	$directory = DIRECTORY;
	/** Load WordPress Bootstrap */
	require_once( $directory . 'wp-load.php' );

	/** Load WordPress Administration Upgrade API */
	require_once( $directory . 'wp-admin/includes/upgrade.php' );

	/*--------------------------*/
	/*	We install the new theme
	/*--------------------------*/

	// We verify if theme.zip exists
	if ( file_exists( 'theme.zip' ) ) {

		$zip = new ZipArchive;

		// We verify we can use it
		if ( $zip->open( 'theme.zip' ) === true ) {

			// We retrieve the name of the folder
			$stat = $zip->statIndex( 0 );
			$theme_name = str_replace('/', '' , $stat['name']);

			// We unzip the archive in the themes folder
			$zip->extractTo( $directory . 'wp-content/themes/' );
			$zip->close();

			// Let's activate the theme
			// Note : The theme is automatically activated if the user asked to remove the default theme
			if ( ACTIVATEDTHEME == 1 || DELETEDEFAULTTHEMES == 1 ) {
				switch_theme( $theme_name, $theme_name );
			}

			// Let's remove the Tweenty family
			if ( DELETEDEFAULTTHEMES == 1 ) {
				delete_theme( 'twentysixteen' );
				delete_theme( 'twentyfithteen' );
				delete_theme( 'twentyfourteen' );
				delete_theme( 'twentythirteen' );
				delete_theme( 'twentytwelve' );
				delete_theme( 'twentyeleven' );
				delete_theme( 'twentyten' );
			}

			// We delete the _MACOSX folder (bug with a Mac)
			delete_theme( '__MACOSX' );

		}
	}
	return true;
}

function install_plugins(){
	$directory = DIRECTORY;
	/*--------------------------*/
	/*	Let's retrieve the plugin folder
	/*--------------------------*/

	if ( ! empty( PLUGINNAMES ) ) {

		$plugins     = explode( ",", PLUGINNAMES );
		$plugins     = array_map( 'trim' , $plugins );
		$plugins_dir = $directory . 'wp-content/plugins/';

		foreach ( $plugins as $plugin ) {

			// We retrieve the plugin XML file to get the link to downlad it
			 $plugin_repo = file_get_contents( "http://api.wordpress.org/plugins/info/1.0/$plugin.json" );

			 if ( $plugin_repo && $plugin = json_decode( $plugin_repo ) ) {

				$plugin_path = WPQI_CACHE_PLUGINS_PATH . $plugin->slug . '-' . $plugin->version . '.zip';

				if ( ! file_exists( $plugin_path ) ) {
					// We download the lastest version
					if ( $download_link = file_get_contents( $plugin->download_link ) ) {
						file_put_contents( $plugin_path, $download_link );
					}							}

				// We unzip it
				$zip = new ZipArchive;
				if ( $zip->open( $plugin_path ) === true ) {
					$zip->extractTo( $plugins_dir );
					$zip->close();
				}
			 }
		}
	}

	if ( PREMIUMPLUGINS == 1 ) {

		// We scan the folder
		$plugins = scandir( 'plugins' );

		// We remove the "." and ".." corresponding to the current and parent folder
		$plugins = array_diff( $plugins, array( '.', '..' ) );

		// We move the archives and we unzip
		foreach ( $plugins as $plugin ) {

			// We verify if we have to retrive somes plugins via the WP Quick Install "plugins" folder
			if ( preg_match( '#(.*).zip$#', $plugin ) == 1 ) {

				$zip = new ZipArchive;

				// We verify we can use the archive
				if ( $zip->open( 'plugins/' . $plugin ) === true ) {

					// We unzip the archive in the plugin folder
					$zip->extractTo( $plugins_dir );
					$zip->close();

				}
			}
		}
	}

	/*--------------------------*/
	/*	We activate extensions
	/*--------------------------*/

	if ( ACTIVATEPLUGINS == 1 ) {

		/** Load WordPress Bootstrap */
		require_once( $directory . 'wp-load.php' );

		/** Load WordPress Plugin API */
		require_once( $directory . 'wp-admin/includes/plugin.php');

		// Activation
		activate_plugins( array_keys( get_plugins() ) );
	}
	return true;
}

function success(){

	/*--------------------------*/
	/*	If we have a success we add the link to the admin and the website
	/*--------------------------*/

	/** Load WordPress Bootstrap */
	require_once( $directory . 'wp-load.php' );

	/** Load WordPress Administration Upgrade API */
	require_once( $directory . 'wp-admin/includes/upgrade.php' );

	/*--------------------------*/
	/*	We update permalinks
	/*--------------------------*/
	if ( ! empty( PERMALINK ) ) {
		file_put_contents( $directory . '.htaccess' , null );
		flush_rewrite_rules();
	}

	return true;

}
