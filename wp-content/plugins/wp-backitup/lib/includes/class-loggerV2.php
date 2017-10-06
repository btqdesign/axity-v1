<?php if (!defined ('ABSPATH')) die('No direct access allowed (logger)');

if( !class_exists( 'Logger' ) ) {
    require_once( WPBACKITUP__PLUGIN_PATH .'/vendor/KLogger/Logger.php' );
}

if( !class_exists( 'LogLevel' ) ) {
    require_once( WPBACKITUP__PLUGIN_PATH .'/vendor/KLogger/LogLevel.php' );
}

/**
 * WP BackItUp  - Logger System Class V2
 *
 * @package WP BackItUp
 * @author  Chris Simmons <chris.simmons@wpbackitup.com>
 * @link    http://www.wpbackitup.com
 *
 */

class WPBackItUp_LoggerV2 {

    /**
     * logger
     * @var mixed
     */
	private static $logger;

	/**
	 *  Write messages to the log
	 *
	 * @param      $log_name Log Name
	 * @param      $message  Log Message (Array or object)
	 */
	public static function log($log_name,$message) {
		try {
            if(self::is_logging() === true) {
                $logger = self::getLogger($log_name);
                $logger->log(LogLevel::DEBUG, $message);
            }
		}catch(Exception $e) {
			error_log( $e );
		}
	}

	/**
	 *  Write informational messages to the log
	 *
	 * @param $log_name Log Name
	 * @param $function Name of calling function(__METHOD__)
	 * @param $message Log Message (Array or object)
	 * @param null $additional_message  (string)
	 */
	public static function log_info($log_name, $function, $message, $additional_message = null ) {

		try {
            if(self::is_logging() === true) {
                $logger = self::getLogger($log_name);
                $logger->log_info($function, $message, $additional_message);
            }

		}catch(Exception $e) {
			error_log( $e );
		}
	}

	/**
	 *  Write error messages to the log
	 *
	 * @param $log_name Log Name
	 * @param $function Name of calling function(__METHOD__)
	 * @param $message Log Message (Array or object)
	 * @param null $additional_message  (string)
	 */
	public static function log_error($log_name, $function,$message,$additional_message=null) {

		try {
            if(self::is_logging() === true) {
                $logger = self::getLogger($log_name);
                $logger->log_error($function, $message, $additional_message);
            }
		}catch(Exception $e) {
			error_log( $e );
		}
	}

	/**
	 *  Write warning messages to the log
	 *
	 * @param $log_name Log Name
	 * @param $function Name of calling function(__METHOD__)
	 * @param $message Log Message (Array or object)
	 * @param null $additional_message  (string)
	 */
	public static function log_warning($log_name, $function,$message,$additional_message=null) {

		try {
            if(self::is_logging() === true) {
                $logger = self::getLogger($log_name);
                $logger->log_warning($function, $message, $additional_message);
            }

		} catch(Exception $e) {
			error_log( $e );
		}
	}

    /**
     *  Write system information to the log
     *
     * @param $log_name
     *
     */
    public static function log_sysinfo($log_name) {
        global $wpdb;

        try{
                if(self::is_logging() === true) {
                    $wpbackitup_license = new WPBackItUp_License();

                    // get the logger
                    $logger = self::getLogger($log_name);

                    $logger->log(LogLevel::DEBUG, "\n**SYSTEM INFO**");

                    $logger->log(LogLevel::DEBUG, "\n--WPBackItUp Info--");

                    $logger->log(LogLevel::DEBUG, "WPBACKITUP License Active: " . ($wpbackitup_license->is_license_active() ? 'true' : 'false'));
                    $prefix = 'WPBACKITUP';
                    foreach (get_defined_constants() as $key => $value) {
                        if (substr($key, 0, strlen($prefix)) == $prefix) {
                            $logger->log(LogLevel::DEBUG, $key . ':' . $value);
                        }
                    }

                    $logger->log(LogLevel::DEBUG, "\n--Site Info--");
                    $logger->log(LogLevel::DEBUG, 'Site URL:' . site_url());
                    $logger->log(LogLevel::DEBUG, 'Home URL:' . home_url());
                    $logger->log(LogLevel::DEBUG, 'Multisite:' . (is_multisite() ? 'Yes' : 'No'));

                    $logger->log(LogLevel::DEBUG, "\n--Wordpress Info--");
                    $logger->log(LogLevel::DEBUG, "Wordpress Version:" . get_bloginfo('version'));
                    $logger->log(LogLevel::DEBUG, 'Language:' . (defined('WPLANG') && WPLANG ? WPLANG : 'en_US'));
                    $logger->log(LogLevel::DEBUG, 'DB_HOST:' . DB_HOST);
                    $logger->log(LogLevel::DEBUG, 'Table Prefix:' . 'Length: ' . strlen($wpdb->prefix) . '   Status: ' . (strlen($wpdb->prefix) > 16 ? 'ERROR: Too long' : 'Acceptable'));
                    $logger->log(LogLevel::DEBUG, 'WP_DEBUG:' . (defined('WP_DEBUG') ? WP_DEBUG ? 'Enabled' : 'Disabled' : 'Not set'));
                    $logger->log(LogLevel::DEBUG, 'Memory Limit:' . WP_MEMORY_LIMIT);

                    $logger->log(LogLevel::DEBUG, "\n--WordPress Active Plugins--");
                    // Check if get_plugins() function exists. This is required on the front end of the
                    // site, since it is in a file that is normally only loaded in the admin.
                    if (!function_exists('get_plugins')) {
                        require_once ABSPATH . 'wp-admin/includes/plugin.php';
                    }

                    $plugins = get_plugins();
                    $active_plugins = get_option('active_plugins', array());
                    foreach ($plugins as $plugin_path => $plugin) {
                        if (!in_array($plugin_path, $active_plugins)) continue;

                        $logger->log(LogLevel::DEBUG, $plugin['Name'] . ': ' . $plugin['Version']);
                    }

                    // WordPress inactive plugins
                    $logger->log(LogLevel::DEBUG, "\n" . '--WordPress Inactive Plugins--');

                    foreach ($plugins as $plugin_path => $plugin) {
                        if (in_array($plugin_path, $active_plugins))
                            continue;

                        $logger->log(LogLevel::DEBUG, $plugin['Name'] . ': ' . $plugin['Version']);
                    }

                    $logger->log(LogLevel::DEBUG, "\n--Server Info--");
                    $logger->log(LogLevel::DEBUG, 'PHP Version:' . PHP_VERSION);
                    $logger->log(LogLevel::DEBUG, 'Webserver Info:' . $_SERVER['SERVER_SOFTWARE']);
                    $logger->log(LogLevel::DEBUG, 'MySQL Version:' . $wpdb->db_version());

                    $logger->log(LogLevel::DEBUG, "\n--PHP Info--");
                    $logger->log(LogLevel::DEBUG, "PHP Info:" . phpversion());
                    $logger->log(LogLevel::DEBUG, "Operating System:" . php_uname());

                    if (@ini_get('safe_mode') || strtolower(@ini_get('safe_mode')) == 'on') {
                        $logger->log(LogLevel::DEBUG, "PHP Safe Mode: On");
                    } else {
                        $logger->log(LogLevel::DEBUG, "PHP Safe Mode: Off");
                    }

                    if (@ini_get('sql.safe_mode') || strtolower(@ini_get('sql.safe_mode')) == 'on') {
                        $logger->log(LogLevel::DEBUG, "SQL Safe Mode: On");
                    } else {
                        $logger->log(LogLevel::DEBUG, "SQL Safe Mode: Off");
                    }
                    $logger->log(LogLevel::DEBUG, "Script Max Execution Time:" . ini_get('max_execution_time'));
                    $logger->log(LogLevel::DEBUG, 'Memory Limit:' . ini_get('memory_limit'));
                    $logger->log(LogLevel::DEBUG, 'Upload Max Size:' . ini_get('upload_max_filesize'));
                    $logger->log(LogLevel::DEBUG, 'Post Max Size:' . ini_get('post_max_size'));
                    $logger->log(LogLevel::DEBUG, 'Upload Max Filesize:' . ini_get('upload_max_filesize'));
                    $logger->log(LogLevel::DEBUG, 'Max Input Vars:' . ini_get('max_input_vars'));
                    $logger->log(LogLevel::DEBUG, 'Display Errors:' . (ini_get('display_errors') ? 'On (' . ini_get('display_errors') . ')' : 'N/A'));
                    $logger->log(LogLevel::DEBUG, 'Curl Installed:' . (function_exists('curl_version') ? 'True' : 'False'));

                    $logger->log(LogLevel::DEBUG, "\n**END SYSTEM INFO**");
                }
        } catch(Exception $e) {
            error_log($e);
        }
    }

    /**
     * Write memory information to the log.
     *
     * @param $log_name
     *
     */
    private static function log_memory_info($log_name){
        try{
            if(self::is_logging() === true) {
                $memory_usage = memory_get_usage();
                $memory_peak_usage = memory_get_peak_usage();
                $memory_limit = ini_get('memory_limit');

                // get the logger
                $logger = self::getLogger($log_name);

                $logger->log(LogLevel::DEBUG, "\n**MEMORY USAGE INFO**");
                $logger->log(LogLevel::DEBUG, 'Memory in use: ' . $memory_usage . ' (' . $memory_usage / 1024 / 1024 . ' Mb)');
                $logger->log(LogLevel::DEBUG, 'Peak usage: ' . $memory_peak_usage . ' (' . $memory_peak_usage / 1024 / 1024 . ' Mb)');
                $logger->log(LogLevel::DEBUG, 'Memory limit: ' . $memory_limit . ' (' . $memory_limit / 1024 / 1024 . ' Mb)');
                $logger->log(LogLevel::DEBUG, "\n**END MEMORY USAGE INFO**");
            }
        } catch(Exception $e) {
            error_log($e);
        }
    }


	/**
	 *  Get Logger instance
	 *
	 * @param $log_name
     * @param $log_level
	 *
	 * @return mixed
	 */
	private static function getLogger($log_name, $log_level = LogLevel::DEBUG) {
        $path = WPBACKITUP__PLUGIN_PATH .'/logs';

        $options = array (
            'extension'      => 'log',
            'dateFormat'     => 'Y-m-d G:i:s.u',
            'filename'       => $log_name,
            'flushFrequency' => false,
            'prefix'         => 'log_',
            'logFormat'      => false,
            'appendContext'  => true,
        );

		try{

			if (! isset( self::$logger[$log_name])) {
				self::$logger[$log_name] = $logger = new Logger($path, $log_level, $options);
			}

			return self::$logger[$log_name];

		}catch(Exception $e) {
			error_log( $e );
		}
	}

    /**
     *  Check if logging is enabled or not
     *
     * @return boolean
     */
    private static function is_logging(){
	    $logging = WPBackItUp_Utility::get_option('logging',0);
	    return $logging == 1? true: false;
    }


	/**                             PUBLIC METHODS                              	**/

	/**
	 *  close Log file name
	 *
	 * @param $log_name
	 *
	 * @return mixed
	 */
	public static function close($log_name) {
		try{
            if(self::is_logging() === true) {
                $logger = self::getLogger($log_name);
                $logger->close();
                self::$logger[$log_name] = null;
                unset(self::$logger[$log_name]);
            }

		}catch(Exception $e) {
			error_log( $e );
		}
	}


	/**
	 *  Get Log file name
	 *
	 * @param $log_name
	 *
	 * @return mixed
	 */
	public static function getLogFileName($log_name) {
		try{
            if(self::is_logging() === true) {
                $logger = self::getLogger($log_name);
                return $logger->getLogFileName();
            }

		}catch(Exception $e) {
			error_log( $e );
		}
	}

	/**
	 *  Get Logger instance
	 *
	 * @param $log_name
	 *
	 * @return mixed
	 */
	public static function getLogFilePath($log_name) {
		try{
            if(self::is_logging() === true) {
                $logger = self::getLogger($log_name);
                return $logger->getLogFilePath();
            }

		}catch(Exception $e) {
			error_log( $e );
		}
	}
}