<?php
/**
 * Adapter Pattern Class for HTTP GET request
 *
 * Uses either WordPress wp_remote_get() or Requests::get()
 *
 * https://www.geeksforgeeks.org/adapter-pattern/
 *
 */
namespace PackagistRequestor;

use Requests;

class HttpRequest {

	public $success;

	public $errors;

	public $body;

	public $status_code;

	static function get( $url, $args = array() ) {
		$args = array_merge( array(
			'headers' => array(),
		), $args );
		do {
			$request = new self();

			if ( function_exists( 'wp_remote_get' ) ) {
				if ( ! function_exists( 'is_wp_error' ) ) {
					break;
				}
				if ( ! function_exists( 'wp_remote_retrieve_body' ) ) {
					break;
				}
				if ( ! function_exists( 'wp_remote_retrieve_response_code' ) ) {
					break;
				}
				$response = wp_remote_get( $url, $args );
				$request->success = ! is_wp_error( $response );
				if ( ! $request->success ) {
					break;
				}
				$request->body = wp_remote_retrieve_body( $response );
				$request->status_code = wp_remote_retrieve_response_code( $response );
				break;
			}

			if ( class_exists( 'Requests' ) ) {
				$headers = $args[ 'headers' ];
				unset( $args[ 'headers' ] );
				$response             = Requests::get( $url, $headers, $args );
				$request->success     = $response->success;
				if ( ! $request->success ) {
					break;
				}
				$request->body        = $response->body;
				$request->status_code = $response->status_code;
				break;
			}

		} while ( false );
		return $request;
	}

}



