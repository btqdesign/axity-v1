<?php if (!defined ('ABSPATH')) die('No direct access allowed');


/**
 * WPBackItUp Encryption Class
 *
 * @link       http://www.wpbackitup.com
 * @since      1.13.0
 *
 * @package    WPBackItUp
 *
 */

//https://github.com/defuse/php-encryption
//require_once(WPBACKITUP__PLUGIN_PATH .'/includes/libraries/php-encryption-2.0.1/defuse-crypto.phar');
//use Defuse\Crypto\Exception as Ex;
//use Defuse\Crypto\File;
//use Defuse\Crypto\Key;
//use Defuse\Crypto\KeyProtectedByPassword;


class WPBackItUp_Encryption {

	private static $default_log = 'debug_encryption';
	private static $php_required='5.4.0';

	private $log_name;
	private $file_suffix='.crypt';
	private $passphrase;

	/**
	 * WPBackItUp_Encryption constructor.
	 *
	 * @param      $passphrase
	 * @param null $log_name
	 */
	function __construct( $passphrase, $log_name = null ) {


		try {

			$this->log_name = self::$default_log; //default log name
			if ( !empty($log_name ) ) $this->log_name = $log_name;

			//check version dependency
			if (! self::check_php_version()) {
				throw new Exception(sprintf( 'PHP Version less than %s. Current PHP version is: %s ',self::$php_required,PHP_VERSION));
			}

			//check encryption dependencies
			if (! self::check_dependencies()) {
				throw new Exception('One or more dependencies are missing.');
			}

			$this->passphrase = $passphrase;

		} catch ( Exception $e ) {
			WPBackItUp_Logger::log_error( $this->log_name, __METHOD__, 'Constructor Exception: ' . $e );
			throw $e;
		}
	}

	/**
	 * WPBackItUp_Encryption constructor.
	 *
	 */
	function __destruct() {

	}


	/**
	 *  Check if php version is adequate
	 */
	public static function check_php_version(){

		//check version dependency
		if (version_compare(PHP_VERSION, self::$php_required, '<')) {
			WPBackItUp_Logger::log_error( self::$default_log, __METHOD__,sprintf( 'PHP Version less than %s. Current PHP version is: %s ',self::$php_required,PHP_VERSION));
			return false;
		}

		return true;
	}

	/**
	 *  Check if all dependencies are installed
	 */
	public static function check_dependencies(){

		//mcrypt is just for testing...
		if (! extension_loaded('mcrypt')) {
			WPBackItUp_Logger::log_error( self::$default_log, __METHOD__, 'mcrypt is not installed');
			return false;
		}

		return true;
	}

	/**
	 *  Encrypt file
	 *  Encrypted files will use the .safe extension
	 *
	 * @param $source_file
	 *
	 * @return bool
	 */
	public function encrypt_file($source_file) {

		$target_file = $source_file . $this->file_suffix;//encryption suffix

		try {

			File::encryptFileWithPassword($source_file,$target_file, $this->passphrase);

			if ( ! file_exists( $target_file ) || filesize( $target_file ) <= 0 ){
				WPBackItUp_Logger::log_error( $this->log_name, __METHOD__, 'File was not encrypted : ' . $target_file );
				return false;
			}

			@unlink ($source_file); //remove original file

		} catch (Exception $ex) {
			WPBackItUp_Logger::log_error( $this->log_name, __METHOD__, 'File was not encrypted : ' . $ex );

			return false;
		}


		return true;
	}

	/**
	 * Decrypt file
	 * -- will remove .safe extension
	 *
	 * @param $source_file
	 *
	 * @return bool
	 */
	public function decrypt_file($source_file) {

		$target_file = str_replace($this->file_suffix,'',$source_file);//remove suffix

		try {
			File::decryptFileWithPassword($source_file, $target_file, $this->passphrase);

			if ( ! file_exists( $target_file ) || filesize( $target_file ) <= 0 ){
				WPBackItUp_Logger::log_error( $this->log_name, __METHOD__, 'File was not encrypted : ' . $target_file );
				return false;
			}

			@unlink ($source_file); //remove original file

		} catch (Exception $ex) {
			WPBackItUp_Logger::log_error( $this->log_name, __METHOD__, 'File was not decrypted : ' . $ex );

			return false;
		}

		return true;

	}


}