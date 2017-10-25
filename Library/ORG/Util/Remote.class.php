<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author:
// +----------------------------------------------------------------------

class Remote {
	
	// Default curl options
	public static $default_options = array (
			CURLOPT_CONNECTTIMEOUT => 120,
			CURLOPT_TIMEOUT => 120 
	);
	
	/**
	 * Returns the output of a remote URL.
	 * Any [curl option](http://php.net/curl_setopt)
	 * may be used.
	 *
	 * // Do a simple GET request
	 * $data = Remote::get($url);
	 *
	 * // Do a POST request
	 * $data = Remote::get($url, array(
	 * CURLOPT_POST => TRUE,
	 * CURLOPT_POSTFIELDS => http_build_query($array),
	 * ));
	 *
	 * @param
	 *        	string remote URL
	 * @param
	 *        	array curl options
	 * @return string
	 * @throws Exception
	 */
	static public function get($url, array $options = NULL) {
		if ($options === NULL) {
			// Use default options
			$options = Remote::$default_options;
		} else {
			// Add default options
			$options = $options + Remote::$default_options;
		}
		
		// The transfer must always be returned
		$options [CURLOPT_RETURNTRANSFER] = TRUE;
		
		// Open a new remote connection
		$remote = curl_init ( $url );
		
		// Set connection options
		if (! curl_setopt_array ( $remote, $options )) {
			throw_exception ( 'Failed to set CURL options, check CURL documentation: :url' );
		}
		
		if (array_key_exists ( CURLOPT_POST, $options )) {
			curl_setopt ( $remote, CURLOPT_HTTPHEADER, array (
					'Expect:' 
			) );
		}
		
		// Get the response
		$response = curl_exec ( $remote );
		
		// Get the response information
		$code = curl_getinfo ( $remote, CURLINFO_HTTP_CODE );
		if ($code and $code < 200 or $code > 299) {
			$error = $response;
		} elseif ($response === FALSE) {
			$error = curl_error ( $remote );
		}
		
		// Close the connection
		curl_close ( $remote );
		
		if (isset ( $error )) {
			throw_exception ( 'Error fetching remote :url [ status :code ] :error' . ':url' . $url . ':code' . $code . ':error' . $error );
		}
		
		return $response;
	}
	
	/**
	 * Returns the status code (200, 500, etc) for a URL.
	 *
	 * $status = Remote::status($url);
	 *
	 * @param
	 *        	string URL to check
	 * @return integer
	 */
	static public function status($url) {
		// Get the hostname and path
		$url = parse_url ( $url );
		
		if (empty ( $url ['path'] )) {
			// Request the root document
			$url ['path'] = '/';
		}
		
		// Open a remote connection
		$port = isset ( $url ['port'] ) ? $url ['port'] : 80;
		$remote = fsockopen ( $url ['host'], $port, $errno, $errstr, 5 );
		
		if (! is_resource ( $remote ))
			return FALSE;
			
			// Set CRLF
		$CRLF = "\r\n";
		
		// Send request
		fwrite ( $remote, 'HEAD ' . $url ['path'] . ' HTTP/1.0' . $CRLF );
		fwrite ( $remote, 'Host: ' . $url ['host'] . $CRLF );
		fwrite ( $remote, 'Connection: close' . $CRLF );
		
		// Send one more CRLF to terminate the headers
		fwrite ( $remote, $CRLF );
		
		// Remote is offline
		$response = FALSE;
		
		while ( ! feof ( $remote ) ) {
			// Get the line
			$line = trim ( fgets ( $remote, 512 ) );
			
			if ($line !== '' and preg_match ( '#^HTTP/1\.[01] (\d{3})#', $line, $matches )) {
				// Response code found
				$response = ( int ) $matches [1];
				break;
			}
		}
		
		// Close the connection
		fclose ( $remote );
		
		return $response;
	}
} // End remote
?>