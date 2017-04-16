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

add_action( 'init', 'iprestricted_register_shortcode' );

/**
 * Registers the <code>iprestricted</code> shortcode.
 */
function iprestricted_register_shortcode() {
	add_shortcode( 'iprestricted', 'iprestricted_shortcode' );
}

/**
 * Processes the <code>iprestricted</code> shortcode.
 *
 * @param array $attrs The shortcode attributes.
 * @param string|null $content The short code content.
 *
 * @return string The given content if client's IP is whitelisted, or blank text otherwise.
 */
function iprestricted_shortcode( $attrs = array(), $content = null ) {

	// exit early if shortcode has no content.
	if ( '' === trim( $content ) ) {
		return '';
	}

	$attrs = array_change_key_case( $attrs, CASE_LOWER );
	$attrs = shortcode_atts( [ 'whitelist' => '' ], $attrs );

	$whitelist = trim( $attrs[ 'whitelist' ] );

	if ( '' === $whitelist ) {
		return '';
	}

	// get the request's IP address
	$client_ip = _iprestricted_get_client_ip();

	if ( false === $client_ip ) {
		return '';
	}

	// convert the client IP to a long integer for further processing downstream.
	$client_ip = ip2long( $client_ip );
	if ( false === $client_ip ) {
		return '';
	}

	// process given whitelist of IP addresses and/or IP ranges.
	$whitelisted = false;
	$parts       = explode( ',', $whitelist );
	while ( ! $whitelisted && ! empty( $parts ) ) {
		$part = array_shift( $parts );

		if ( strpos( $part, '-' ) ) {
			$range       = explode( '-', $part, 2 );
			$start       = trim( $range[0] );
			$end         = trim( $range[1] );
			$whitelisted = _iprestricted_is_in_range( $client_ip, $start, $end );
		} else {
			$whitelisted = _iprestricted_matches_ip( $client_ip, $part );
		}
	}

	// Return content ONLY if client IP got whitelisted.
	// Otherwise, return a blank string.
	if ( $whitelisted ) {
		return $content;
	}

	return '';
}


/**
 * Determines the client's IP address.
 *
 * @return bool|string The client's IP address, or FALSE if none could be determined.
 */
function _iprestricted_get_client_ip() {
	$vars      = array( 'REMOTE_ADDR', 'HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP' );
	$client_ip = false;

	foreach ( $vars as $var ) {
		if ( isset( $_SERVER[ $var ] ) ) {
			$client_ip = $_SERVER[ $var ];
			break;
		}
	}

	return $client_ip;
}


/**
 * Checks if a given IP address is within a given IP range.
 *
 * @param int $client_ip An integer representation of the client's IP address.
 * @param string $start Ipv4 address at the start of the range.
 * @param string $end Ipv4 address at the end of the range.
 *
 * @return bool TRUE if client IP is within range, FALSE otherwise.
 */
function _iprestricted_is_in_range( $client_ip, $start, $end ) {
	$start = ip2long( $start );

	if ( false === $start ) {
		return false;
	}

	$end = ip2long( $end );

	if ( false === $end ) {
		return false;
	}

	return ( $client_ip >= $start && $client_ip <= $end );
}

/**
 * Checks if two given IP addresses match.
 *
 * @param int $client_ip An integer representation of the client's IP address.
 * @param string $ip IPv4 address to compare to.
 *
 * @return bool TRUE if both IP addresses match, FALSE otherwise.
 */
function _iprestricted_matches_ip( $client_ip, $ip ) {
	$ip = ip2long( $ip );

	if ( false === $ip ) {
		return false;
	}

	return ( $ip === $client_ip );
}
