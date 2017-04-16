<?php
/**
 *
 * @link              https://github.com/ucsf-ckm/iprestricted
 * @package           IP Restricted
 *
 * @wordpress-plugin
 * Plugin Name:       IP Restricted
 * Plugin URI:        https://github.com/ucsf-ckm/iprestricted
 * Description:       Wrap any block of content in a shortcode and restrict its visibility by IP address.
 * Version:           0.9.0
 * Author:            Stefan Topfstedt
 * Author URI:        https://github.com/stopfstedt
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * GitHub Plugin URI: https://github.com/ucsf-ckm/iprestricted
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( __DIR__ . '/functions.php' );

add_action( 'init', 'iprestricted_register_shortcode' );
