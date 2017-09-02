<?php
   /*
   Plugin Name: Sol Carousel Slider
   Plugin URI: http://carouselsider.com
   Description: a plugin to create MyPlugin and spread joy
   Version: 1.0
   Author: Mr. Awesome
   Author URI: http://wordpress.com
   License: GPL2
   Text Domain: sol-carousel-slider
   Domain Path: /languages/
   */

   
 /**
 * Exit if accessed directly
 */
if (!defined('ABSPATH')) {
    exit;
}

define('SOLCAROUSELSLIDER_URL', plugins_url() . '/sol-carousel-slider');   
define('SOLCAROUSELSLIDER_DIR', plugin_dir_path(__FILE__));   
include_once( 'includes/slider.php' );
include_once( 'includes/slide.php' );
include_once( 'includes/slide-function.php' );
?>
