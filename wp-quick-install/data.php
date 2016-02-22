<?php
////////
// Enter below the installation folder :
////////

define('DIRECTORY', 'demo');


////////
// Enter below the code language :
////////

define('LANGUAGE', 'en_US');


////////
// Enter below the Site Title :
////////

define('TITLE', 'New Project');


// SITE URL: without http://
define('SITEURL', 'example.com');

////////
// Enter below your database connection detail :
////////

define('WPDBNAME', '');
define('WPDBHOST', 'localhost');
define('WPPREFIX', 'wp_');
define('WPUSER', 'root');
define('WPPASS', 'root');
define('WPDEFAULTCONTENT', 1);


////////
// Enter below the admin username and password :
////////

define('USERLOGIN', 'admin');
define('USERPASSWORD', 'demo');
define('USEREMAIL', 'demo@example.com');


////////
// Enable SEO ?  :
// 1 = Yes, 0 = No
////////

define('SEO', 1);


////////
// Activate Theme after WordPress installation? :
// 1 = Yes, 0 = No
////////

define('ACTIVATEDTHEME', 1);


////////
// Delete Twenty Themes ?  :
// 1 = Yes, 0 = No
////////

define('DELETEDEFAULTTHEMES', 1);


////////
// List all plugin you want to install from Wordpress site below  (sperated by comma):
////////

define('PLUGINNAMES', 'wordpress-seo');



////////
// Install extensions which are on the "wp-quick-install" "plugins" folder :
// 1 = Yes, 0 = No
////////

define('PREMIUMPLUGINS', 0);


////////
// Activate plugins after WordPress Installation :
// 1 = Yes, 0 = No
////////

define('ACTIVATEPLUGINS', 1);


////////
// Permalink Structure :
////////

define('PERMALINK', '%postname%');

////////
// Medias :
////////

define('UPLOADDIR', 'img');
define('YEARMONTHFOLDERS', 0);

////////
// Constant to add to wp-config.php  :
// 1 = Yes, 0 = No
////////

define('POSTREVISIONS', 3);
define('DISALLOWFILEEDIT', 0);
define('AUTOSAVETIMEINTERVAL', 7200);
define('WPCONFIGDEBUG', 0);
define('WPCONFIGDEBUGDISPLAY', 0);
define('WPCONFIGDEBUGLOG', 0);
define('WPCOMAPIKEY', '');


////////
/* Post to automatically add after WordPress installation :
///  title = Title
///  status = Status (publish, draft, etc...). Default : draft
///  type = Post Type. Default : post
///  content = Content (HTML allowed)
///  slug = Slug
///  parent = Parent page Title
*///////
/// Examples:
/*//
$posts[0] = title::Legal - status::publish - content::Lorem ipsum dolor sit amet - type::page;
$posts[1] = title::Contact - status::publish - content::Lorem ipsum dolor sit amet - type::page - parent::Legal;

*/


$posts[0]['title'] = 'Home';
$posts[0]['status'] = 'publish';
$posts[0]['type'] = 'page';
$posts[0]['content'] = 'Welcome to '.TITLE;
$posts[0]['slug'] = 'home';
//$posts[0]['parent'] = '';

$posts[1]['title'] = 'Blog';
$posts[1]['status'] = 'publish';
$posts[1]['type'] = 'page';
$posts[1]['content'] = 'Blog Page';
$posts[1]['slug'] = 'blog';
//$posts[0]['parent'] = '';
