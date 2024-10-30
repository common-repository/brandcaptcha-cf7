<?php
/*
Plugin Name: Contact Form 7 brandCAPTCHA extension
Plugin URI: https://github.com/PontaMedia/brandcaptcha-plugin-wordpress/tree/master/contact-form-7-brandcaptcha-extension.1.1
Description: Provides WP-brandCAPTCHA possibilities to the Contact Form 7 plugin. Requires both.
Version: 1.1.0
Author: Pontamedia 
Email: soporte@pontamedia.com
Author URI: http://www.pontamedia.com/
License: GPL2

Copyright (c) 2014 by PontaMedia

- Documentation and latest version https://github.com/PontaMedia/brandcaptcha-plugin-wordpress/tree/master/contact-form-7-brandcaptcha-extension.1.1
*/

// this is the 'driver' file that instantiates the objects and registers every hook

define('ALLOW_INCLUDE', true);

require_once('includes/CF7brandCAPTCHA.class.php');

define('ASD_PLUGIN_FILE', __FILE__ );

$cf7_brandcaptcha = new CF7brandCAPTCHA('cf7_brandcaptcha_options', 'cf7brandcapext');

register_activation_hook( __FILE__ , array($cf7_brandcaptcha, 'activate'));

?>