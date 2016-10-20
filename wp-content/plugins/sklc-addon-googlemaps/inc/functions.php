<?php

	/**
	 * Enqueue Scripts
	 */

	function sklc_gmaps_scripts() {

		wp_enqueue_script( 'sklc-gmaps-api', '//maps.google.com/maps/api/js?sensor=false' );
		wp_enqueue_script( 'sklc-gmaps-js', SKLC_GMAPS_URL . 'js/main.js' );

		wp_enqueue_style( 'sklc-gmaps-css', SKLC_GMAPS_URL . 'css/main.css' );

	} add_action( 'wp_enqueue_scripts', 'sklc_gmaps_scripts' );

	function sklc_gmaps_textdomain() {
		load_plugin_textdomain( 'sklc_gmaps_string', false, SKLC_GMAPS_DIRNAME . '/lang/' );
	} add_action('init', 'sklc_gmaps_textdomain');

	/**
	 * Get coordinated from address
	 *
	 * Thanks to Pippin Williamson for this one https://pippinsplugins.com/
	 */

	function sklc_address_to_coordinates( $address, $force_refresh = false ) {

		$address_hash = md5( $address );

		$coordinates = get_transient( $address_hash );

		if ( $force_refresh || $coordinates === false ) {

			$args       = array( 'address' => urlencode( $address ), 'sensor' => 'false' );

			// wp_remote_get isn't working with url without protocol declared
			$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
			$url        = esc_url_raw( add_query_arg( $args, $protocol.'maps.googleapis.com/maps/api/geocode/json' ) );
			$response 	= wp_remote_get( $url );

			if( is_wp_error( $response ) )
				return;

			$data = wp_remote_retrieve_body( $response );

			if( is_wp_error( $data ) )
				return;

			if ( $response['response']['code'] == 200 ) {

				$data = json_decode( $data );

				if ( $data->status === 'OK' ) {

					$coordinates = $data->results[0]->geometry->location;

					$cache_value['lat'] 	= $coordinates->lat;
					$cache_value['lng'] 	= $coordinates->lng;
					$cache_value['address'] = (string) $data->results[0]->formatted_address;

					set_transient($address_hash, $cache_value, 3600*24*30);
					$data = $cache_value;

				} elseif ( $data->status === 'ZERO_RESULTS' ) {
					return 'zero_results';
				} elseif( $data->status === 'INVALID_REQUEST' ) {
					return 'invalid_request';
				} else {
					return 'error';
				}

			} else {
				return 'no_load';
			}

		} else {

			// return cached results
			$data = $coordinates;

		}

		return $data;
	}