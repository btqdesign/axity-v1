<?php

namespace plainview\sdk_broadcast;

/**
	@brief			Collection of useful functions.

	@par			Versioning

	This base class contains the version of the SDK. Upon changing any part of the SDK, bump the version in here.

	@author			Edward Plainview		edward@plainview.se
	@copyright		GPL v3
**/
class base
{
	/**
		@brief		The instance of the base.
		@since		20130425
		@var		$instance
	**/
	protected static $instance = [];

	/**
		@brief		The version of this SDK file.
		@since		20130630
		@var		$sdk_version
	**/
	protected $sdk_version = 20170602;

	/**
		@brief		Constructor.
		@since		20130425
	**/
	public function __construct()
	{
		$classname = get_class( $this );
		self::$instance[ $classname ] = $this;
	}

	/**
		@brief		Insert an array into another.
		@details	Like array_splice but better, because it even inserts the new key.
		@param		array		$array		Array into which to insert the new array.
		@param		int			$position	Position into which to insert the new array.
		@param		array		$new_array	The new array which is to be inserted.
		@return		array					The complete array.
		@since		20130416
	**/
	public static function array_insert( $array, $position, $new_array )
	{
		$part1 = array_slice( $array, 0, $position, true );
		$part2 = array_slice( $array, $position, null, true );
		return $part1 + $new_array + $part2;
	}

	/**
		@brief		Sort an array of arrays using a specific key in the subarray as the sort key.
		@param		array		$array		An array of arrays.
		@param		string		$key		Key in subarray to use as sort key.
		@return		array					The array of arrays.
		@since		20130416
	**/
	public static function array_sort_subarrays( $array, $key )
	{
		// In order to be able to sort a bunch of objects, we have to extract the key and use it as a key in another array.
		// But we can't just use the key, since there could be duplicates, therefore we attach a random value.
		$sorted = array();

		$is_array = is_array( reset( $array ) );

		foreach( $array as $index => $item )
		{
			$item = (object) $item;
			do
			{
				$rand = rand(0, PHP_INT_MAX / 2);
				if ( is_int( $item->$key ) )
					$random_key = $rand + $item->$key;
				else
					$random_key = $item->$key . '-' . $rand;
			}
			while ( isset( $sorted[ $random_key ] ) );

			$sorted[ $random_key ] = array( 'key' => $index, 'value' => $item );
		}
		ksort( $sorted );

		// The array has been sorted, we want the original array again.
		$r = array();
		foreach( $sorted as $item )
		{
			$value = ( $is_array ? (array)$item[ 'value' ] : $item[ 'value' ] );
			$r[ $item['key'] ] = $item['value'];
		}

		return $r;
	}

	/**
		@brief		Rekey an array with the specified key/property of the array object.
		@param		$array		Array to rekey.
		@param		$key		Object key or array property to use as the new key.
		@return		array		Rearranged array.
		@since		20130416
	**/
	public static function array_rekey( $array, $key )
	{
		$r = [];
		foreach( $array as $value )
		{
			$object = (object)$value;
			$r[ $object->$key ] = $value;
		}
		return $r;
	}

	/**
		@brief		Check that the supplied text is plaintext.
		@details	Strips out HTML. Inspired by Drupal's check_plain.
		@param		string		$text		String to check for plaintext.
		@return		string					Cleaned up string.
		@since		20130416
	**/
	public static function check_plain( $text )
	{
		$text = strip_tags( $text );
		$text = stripslashes( $text );
		return $text;
	}

	/**
		@brief		Build the complete current URL.
		@param		array		$SERVER		Optional _SERVER array to use, instead of the normal _SERVER array.
		@return		string		The complete URL, with http / https, port, etc.
		@since		20130604
	**/
	public static function current_url( $SERVER = null )
	{
		if ( $SERVER === null )
			$SERVER = $_SERVER;

		// Unable to current_url if we're running as a CLI.
		if ( ! isset( $SERVER[ 'SERVER_PORT' ] ) )
			return '';

		$ssl = false;
		if ( isset( $SERVER[ 'HTTPS' ] ) )
		{
			$value = self::strtolower( $SERVER[ 'HTTPS' ] );
			$ssl = (
				( $value != '' )
				&&
				( $value != 'off' )
			);
		}

		$port = $SERVER[ 'SERVER_PORT' ];
		if ( $ssl && $port == 443 )
			$port = '';
		if ( ! $ssl && $port == 80 )
			$port = '';
		if ( $port != '' )
			$port = ':' . $port;

		$url = $SERVER[ 'REQUEST_URI' ];

		return sprintf( '%s://%s%s%s',
			$ssl ? 'https' : 'http',
			$SERVER[ 'HTTP_HOST' ],
			$port,
			$url
		);
	}

	/**
		@brief		Creates a form2 object.
		@return		\\plainview\\sdk_broadcast\\form2\\form		A new form object.
		@since		2015-12-25 17:21:03
	**/
	public function form()
	{
		if ( ! class_exists( '\\plainview\\sdk_broadcast\\form2\\form' ) )
			require_once( dirname( __FILE__ ) . '/form2/form.php' );

		if ( ! class_exists( '\\plainview\\sdk_broadcast\\wordpress\\form2\\form' ) )
			require_once( 'form2.php' );

		$form = new \plainview\sdk_broadcast\wordpress\form2\form();
		return $form;
	}

	/**
		@brief		Backwards compatibility alias for form.
		@see		form()
		@since		20130509
	**/
	public function form2()
	{
		return $this->form();
	}

	/**
		@brief		Convenience function to wrap a string in h1 tags.
		@param		string		$string		The string to wrap.
		@return		string					The wrapped string.
		@see		open_close_tag()
		@since		20130416
	**/
	public static function h1( $string )
	{
		return self::open_close_tag( $string, 'h1' );
	}

	/**
		@brief		Convenience function to wrap a string in h2 tags.
		@param		string		$string		The string to wrap.
		@return		string					The wrapped string.
		@see		open_close_tag()
		@since		20130416
	**/
	public static function h2( $string )
	{
		return self::open_close_tag( $string, 'h2' );
	}

	/**
		@brief		Convenience function to wrap a string in h3 tags.
		@param		string		$string		The string to wrap.
		@return		string					The wrapped string.
		@see		open_close_tag()
		@since		20130416
	**/
	public static function h3( $string )
	{
		return self::open_close_tag( $string, 'h3' );
	}

	/**
		@brief		Returns a hash value of a string. The standard hash type is sha512 (64 chars).

		@param		string		$string			String to hash.
		@param		string		$type			Hash to use. Default is sha512.
		@return									Hashed string.
		@since		20130416
	**/
	public static function hash( $string, $type = 'sha512' )
	{
		return hash($type, $string);
	}

	/**
		@brief		Return the human-readable byte amount.
		@details	From http://www.php.net/manual/en/function.filesize.php#106569
		@since		2014-06-11 14:13:28
	**/
	public static function human_bytes( $bytes, $decimals = 2 )
	{
		$prefix = [
			'',
			'k',
			'M',
			'G',
			'T',
			'P',
		];
		$factor = floor( ( strlen( $bytes ) - 1 ) / 3 );
		return sprintf( "%.{$decimals}f %sB",
			$bytes / pow( 1024, $factor ),
			$prefix[ $factor ]
		);
	}

	/**
		@brief		Output the unix time as a human-readable string.
		@details

		In order to aid in translation, use the various text_* keys in the options array.

		See the source below for a list of keys to include.

		@param		int			$current		Current timestamp.
		@param		int			$reference		Reference timestamp, if not now.
		@param		array		$options		Options.
		@since		20130809
	**/
	public static function human_time( $current, $reference = null, $options = [] )
	{
		$options = \plainview\sdk_broadcast\base::merge_objects( [
			'time' => date( 'U' ),
			'text_day' => '1 day',
			'text_days' => '%d days',
			'text_hour' => '1 hour',
			'text_hours' => '%d hours',
			'text_minute' => '1 minute',
			'text_minutes' => '%d minutes',
			'text_second' => '1 second',
			'text_seconds' => '%d seconds',
		], $options );
		if ( $reference === null )
			$reference = $options->time;
		if( ! is_int( $current ) )
			$current = strtotime( $current );
		$difference = abs( $reference - $current );
		$seconds = round( $difference, 0 );
		$minutes = round( $difference / 60, 0 );
		$hours = round( $difference / ( 60 * 60 ), 0 );
		$days = round( $difference / ( 60 * 60 * 24 ), 0 );

		if ( $days > 0 )
			if ( $days == 1 )
				return $options->text_day;
			else
				return sprintf( $options->text_days, $days );

		if ( $hours > 0 )
			if ( $hours == 1 )
				return $options->text_hour;
			else
				return sprintf( $options->text_hours, $hours );

		if ( $minutes > 0 )
			if ( $minutes == 1 )
				return $options->text_minute;
			else
				return sprintf( $options->text_minutes, $minutes );

		if ( $seconds == 1 )
			return $options->text_second;
		else
			return sprintf( $options->text_seconds, $seconds );
	}

	/**
		@brief		Wrap the human time in a span with the computer time as the hover title.
		@param		int			The current time (Y-m-d H:i:s)  to convert to human-readable time.
		@return		string		A HTML string containing a span with a title, and the human-readable date within.
		@since		20130809
	**/
	public function human_time_span( $current, $options = [] )
	{
		return sprintf( '<span title="%s">%s</span>', $current, $this->human_time( $current, null, $options ) );
	}


	/**
		@brief		Implode an array in an HTML-friendly way.
		@details	Used to implode arrays using HTML tags before, between and after the array. Good for lists.
		@param		string		$prefix		li
		@param		string		$suffix		/li
		@param		array		$array		The array of strings to implode.
		@return		string					The imploded string.
		@since		20130416
	**/
	public static function implode_html( $array, $prefix = '<li>', $suffix = '</li>' )
	{
		return $prefix . implode( $suffix . $prefix, $array ) . $suffix;
	}

	/**
		@brief		Return the instance of this object class.
		@return		base		The instance of this object class.
		@since		20130425
	**/
	public static function instance()
	{
		$classname = get_called_class();
		if ( ! isset( self::$instance[ $classname ] ) )
			return false;
		return self::$instance[ $classname ];
	}

	/**
		@brief		Check en e-mail address for validity.
		@param		string		$address		Address to check.
		@param		boolean		$check_mx		Check for a valid MX?
		@return		boolean		True, if the e-mail address is valid.
		@since		20130416
	**/
	public static function is_email( $address, $check_mx = true )
	{
		if ( $address == '' )
			return false;

		if ( filter_var( $address, FILTER_VALIDATE_EMAIL ) != $address )
			return false;

		// If no need to check the MX, and we've gotten this far, then it's ok.
		if ( $check_mx == false )
			return true;

		// Check the DNS record.
		$host = preg_replace( '/.*@/', '', $address );
		if ( ! checkdnsrr( $host, 'MX' ) )
			return false;

		return true;
	}

	/**
		@brief		Check if an IP is private.
		@param		string		$ip		IP address to check.
		@return		bool				True, if the IP is private.
		@since		20130825
	**/
	public static function is_private_ip( $ip )
	{
		$private_addresses = [
			'10.0.0.0|10.255.255.255',
			'172.16.0.0|172.31.255.255',
			'192.168.0.0|192.168.255.255',
			'169.254.0.0|169.254.255.255',
			'127.0.0.0|127.255.255.255'
		];

		$long_ip = ip2long( $ip );
		if( $long_ip != -1 )
		{
			foreach( $private_addresses as $private_address )
			{
				list( $start, $end ) = explode( '|', $private_address );

				 if( $long_ip >= ip2long( $start ) && $long_ip <= ip2long( $end ) )
					return true;
			}
		}
		return false;
	}

	/**
		@brief		Creates a mail object.
		@return		\\plainview\\sdk_broadcast\\mail\\mail		A new PHPmailer object.
		@since		20130430
	**/
	public static function mail()
	{
		$mail = new \plainview\sdk_broadcast\mail\mail();
		$mail->CharSet = 'UTF-8';
		return $mail;
	}

	/**
		@brief		Merge two objects.
		@details	The objects can even be arrays, since they're automatically converted into objects.
		@param		mixed		$base		An array or object into which to append the new properties.
		@param		mixed		$new		New properties to append to $base.
		@return		object					The expanded $base object.
		@since		20130416
	**/
	public static function merge_objects( $base, $new )
	{
		$base = clone (object)$base;
		foreach( (array)$new as $key => $value )
			$base->$key = $value;
		return $base;
	}

	/**
		@brief		Returns the number corrected into the min and max values.
		@param		int		$number		Number to adjust.
		@param		int		$min		Minimum value.
		@param		int		$max		Maximum value.
		@return		int					The corrected $number.
		@since		20130416
	**/
	public static function minmax( $number, $min, $max )
	{
		$number = min( $max, $number );
		$number = max( $min, $number );
		return $number;
	}

	/**
		@brief		Tries to figure out the mime type of this filename.
		@param		string		$filename		The complete file path.
		@return		string
		@since		20130416
	**/
	public static function mime_type( $filename )
	{
		// Try to use file.
		if ( is_executable( '/usr/bin/file' ) )
		{
			exec( "file -bi '$filename'", $r );
			$r = reset( $r );
			$r = preg_replace( '/;.*/', '', $r );
			return $r;
		}

		// Try to use the finfo class.
		if ( class_exists( 'finfo' ) )
		{
			$fi = new finfo( FILEINFO_MIME, '/usr/share/file/magic' );
			$r = $fi->buffer(file_get_contents( $filename ));
			return $r;
		}

		// Last resort: mime_content_type which rarely works properly.
		if ( function_exists( 'mime_content_type' ) )
		{
			$r = mime_content_type ( $filename );
			return $r;
		}

		// Nope. Return a general value.
		return 'application/octet-stream';
	}

	/**
		@brief		Enclose in string in an HTML element tag.
		@details	Parameter order: enclose THIS in THIS
		@param		string		$string		String to wrap.
		@param		string		$tag		HTML element tag: h1, h2, h3, p, etc...
		@return		string					The wrapped string.
		@since		20130416
	**/
	public static function open_close_tag( $string, $tag )
	{
		return sprintf( '<%s>%s</%s>', $tag, $string, $tag );
	}

	/**
		@brief		Recursively removes a directory.
		@details	Assumes that all files in the directory, and the dir itself, are writeable.
		@param		string		$directory		Directory to remove.
		@since		20130416
	**/
	public static function rmdir( $directory )
	{
		$directory = rtrim( $directory, '/' );
		if ( $directory == '.' || $directory == '..' )
			return;
		if ( is_file( $directory ) )
			unlink ( $directory );
		else
		{
			$files = glob( $directory . '/*' );
			foreach( $files as $file )
				self::rmdir( $file );
			rmdir( $directory );
		}
	}

	/**
		@brief		Returns a stack trace as a string.
		@return		string			A stacktrace.
		@since		20130416
	**/
	public static function stacktrace()
	{
		$e = new \Exception;
		return var_export( $e->getTraceAsString(), true );
	}

	/**
		@brief		Converts a string to an array of e-mail addresses.
		@param		string		$string		A multiline text-area.
		@param		bool		$mx			Check each e-mail address for valid MX?
		@return		array					An array of valid e-mail addresses. If no valid e-mail addresses are found, then the returned array is empty.
		@since		20130425
	**/
	public static function string_to_emails( $string, $mx = true )
	{
		$string = str_replace( array( "\r", "\n", "\t", ';', ',', ' ' ), "\n", $string );
		$lines = array_filter( explode( "\n", $string ) );
		$r = array();
		foreach( $lines as $line )
			if ( self::is_email( $line, $mx ) )
				$r[ $line ] = $line;
		ksort( $r );
		return $r;
	}

	/**
		@brief		Strips the array of slashes. Used for $_POSTS.
		@param		array		$post		The array to strip. If null then $_POST is used.
		@return		array					Posts with slashes stripped.
		@since		20130416
	**/
	public static function strip_post_slashes( $post = null )
	{
		if ( $post === null )
			$post = $_POST;
		foreach( $post as $key => $value )
			if ( ! is_array( $value ) && strlen( $value ) > 1 )
				$post[ $key ] = stripslashes( $value );

		return $post;
	}

	/**
		@brief		Multibyte strtolower.
		@param		string		$string			String to lowercase.
		@return									Lowercased string.
		@since		20130416
	**/
	public static function strtolower( $string )
	{
		if ( function_exists( 'mb_strtolower' ) )
			return mb_strtolower( $string );
		else
		return strtolower( $string );
	}

	/**
		@brief		Multibyte strtoupper.
		@param		string		$string			String to uppercase.
		@return									Uppercased string.
		@since		20130416
	**/
	public static function strtoupper( $string )
	{
		if ( function_exists( 'mb_strtoupper' ) )
			return mb_strtoupper( $string );
		else
		return strtoupper( $string );
	}

	/**
		@brief		Create a new temporary directory in the system's temp dir, and return the name.
		@param		string		$subdirectory		Prefix of subdir.
		@return		string							Complete path to new directory.
		@since		20130515
	**/
	public static function temp_directory( $prefix = null )
	{
		if ( $prefix === null )
			$prefix = self::uuid( 8 );
		$prefix .= self::uuid( 8 );
		$r = sys_get_temp_dir() . '/' . $prefix;
		mkdir( $r );
		if ( ! is_readable( $r ) )
			return false;
		return $r;
	}

	/**
		@brief		Return the name to a temporary file name, optionally in a specific temp directory.
		@param		string		$prefix		Prefix of temporary file.
		@param		string		$temp_dir	Optional temporary directory in which to create the temp file.
		@return		string					Complete filename to a temporary file.
		@since		20130515
	**/
	public static function temp_file( $prefix = null, $temp_dir = null )
	{
		if ( $prefix === null )
			$prefix = 'plainview_sdk_base_';
		if ( $temp_dir !== null )
			$temp_dir = self::temp_directory( $temp_dir );
		else
			$temp_dir = sys_get_temp_dir();

		return tempnam( $temp_dir, $prefix );
	}

	/**
		@brief		Explode a text area to an array, cleaning the
		@details	Used to clean and filter textareas.
		@since		2015-04-15 22:29:14
	**/
	public static function textarea_to_array( $string )
	{
		$s = str_replace( "\r", '', $string );
		$lines = explode( "\n", $s );
		$lines = array_filter( $lines );
		return $lines;
	}

	/**
		@brief		Produce a random uuid.
		@param		int			$length		Length of ID to return.
		@return		string					An x-character long random ID.
		@since		20130506
	**/
	public static function uuid( $length = 64 )
	{
		$r = 'u';
		while( strlen( $r ) < $length )
			$r .= self::hash( microtime() . rand( 0, PHP_INT_MAX ) );
		return substr( $r, 0, $length );
	}

	/**
		@brief
		@since		2017-02-21 07:43:53
	**/
	public static function wpautop( $string )
	{
		if ( ! function_exists( 'wpautop' ) )
			require_once( 'wpautop.php' );

		return wpautop( $string );
	}
}
