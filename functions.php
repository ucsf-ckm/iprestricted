<?php

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
 * @return string The given content if client's IP is whitelisted and not blacklisted, or no white/blacklists were provided. Blank text in all other cases.
 */
function iprestricted_shortcode( $attrs = array(), $content = null ) {

	// exit early if shortcode has no content.
	if ( '' === trim( $content ) ) {
		return '';
	}

	$attrs = array_change_key_case( $attrs, CASE_LOWER );
	$attrs = shortcode_atts( [ 'whitelist' => '', 'blacklist' => '' ], $attrs );

	$whitelist = trim( $attrs['whitelist'] );
	$blacklist = trim( $attrs['blacklist'] );

	$has_whitelist = ( '' !== $whitelist );
	$has_blacklist = ( '' !== $blacklist );

	// no lists given means "no restrictions". return content.
	if ( ! $has_whitelist && ! $has_blacklist ) {
		return $content;
	}

	// get the request's IP address
	$client_ip = _iprestricted_get_client_ip();

	// if the client's IP cannot be determined then restrict content access.
	// better safe than sorry.
	if ( false === $client_ip ) {
		return '';
	}

	// convert the client IP to a long integer for further processing downstream.
	$client_ip = ip2long( $client_ip );

	// invalid client IP. restrict access.
	if ( false === $client_ip ) {
		return '';
	}

	$whitelisted = $has_whitelist ? _iprestricted_matches_list( $client_ip, $whitelist ) : true;
	$blacklisted = $has_blacklist ? _iprestricted_matches_list( $client_ip, $blacklist ) : false;

	// Return content ONLY if client IP is whitelisted and not blacklisted.
	if ( $whitelisted && ! $blacklisted ) {
		return $content;
	}

	// any other outcome of ip matching results in no content being shown.
	return '';
}

/**
 * Matches a given IP address against a given list of IP addresses.
 *
 * @param int $client_ip The client IP address.
 * @param string $list A comma separated list of IP addresses and IP ranges to match the client IP against.
 *
 * @return bool TRUE if a any match was found, FALSE otherwise.
 */
function _iprestricted_matches_list( $client_ip, $list = '' ) {

	$matches = false;
	$parts   = explode( ',', $list );
	while ( ! $matches && ! empty( $parts ) ) {
		$part = array_shift( $parts );

		if ( $part === '' ) {
			continue;
		}

		if ( strpos( $part, '-' ) ) {
			$range   = explode( '-', $part, 2 );
			$start   = trim( $range[0] );
			$end     = trim( $range[1] );
			$matches = _iprestricted_is_in_range( $client_ip, $start, $end );
		} else {
			$matches = _iprestricted_matches_ip( $client_ip, $part );
		}
	}

	return $matches;
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
 * @param string $start IP address at the start of the range.
 * @param string $end IP address at the end of the range.
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
 * @param string $ip IP address to compare to.
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
