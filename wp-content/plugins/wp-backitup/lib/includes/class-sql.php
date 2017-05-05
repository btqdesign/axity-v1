<?php if (!defined ('ABSPATH')) die('No direct access allowed');

// Checking safe mode is on/off and set time limit
if( ini_get('safe_mode') ){
   @ini_set('max_execution_time', 0);
}else{
   @set_time_limit(0);
}

/**
 * WP BackItUp  - SQL Class
 *
 * @package WP BackItUp
 * @author  Chris Simmons <chris.simmons@wpbackitup.com>
 * @link    http://www.wpbackitup.com
 *
 */

class WPBackItUp_SQL {

	private $log_name;
    private $mysqli; //use getter

	function __construct($log_name=null) {
		try {
			$this->log_name = 'debug_sql';//default log name
			if (is_object($log_name)){
				//This is for the old logger
				$this->log_name = $log_name->getLogFileName();
			} else{
				if (is_string($log_name) && isset($log_name)){
					$this->log_name = $log_name;
				}
			}

		} catch(Exception $e) {
			error_log($e);
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Constructor Exception: ' .$e);
		}
   }

   function __destruct() {
       // Close the connection
	   if (is_object($this->mysqli)){
        $this->mysqli->close() ;
	   }
   }


	/**
	 * mysqldump database export
	 *
	 * @param $sql_data_file_name
	 * @param $table
	 * @param $offset
	 * @param $limit
	 * @param $create_table
	 * @param bool|false $with_mysqlpath
	 *
	 * @return bool
	 */
	public function mysqldump_export_data($sql_data_file_name,$table,$offset,$limit,$create_table,$with_mysqlpath=false) {
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin.');

		$db_name = DB_NAME;
		$db_user = DB_USER;
		$db_pass = DB_PASSWORD;
		$db_host = $this->get_hostonly(DB_HOST);
		$db_port = $this->get_portonly(DB_HOST);

		//This is to ensure that exec() is enabled on the server
		if($this->exec_enabled()) {
			try {
				$mysql_path='';
				if ($with_mysqlpath)  {
					$db = new WPBackItUp_DataAccess();
					$mysql_path = $db->get_mysql_path();
					if ($mysql_path===false) return false;
				}

				$process = $mysql_path .'mysqldump';
				$command = $process
				           . ' --host=' . $db_host;

				//Check for port
				if (false!==$db_port){
					$command .=' --port=' . $db_port;
				}

				$create_option =' --no-create-info';
				if ($create_table) {
					$create_option =' ';
				}

				$command .=
					' --where "1 LIMIT ' . $offset . ',' . $limit .'"'
					. $create_option
					. ' --insert-ignore'
					. ' --user=' . $db_user
					. ' --password=' . $db_pass
					. ' ' . $db_name
					. ' ' . $table
					. ' >> "' . $sql_data_file_name .'"';

				if (WPBACKITUP__DEBUG) {
					$masked_command = str_replace(array($db_user,$db_pass),'XXXXXX',$command);
					WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Execute command:' . $masked_command);
				}

				exec($command,$output,$rtn_var);
				WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Execute output:');
				WPBackItUp_Logger::log($this->log_name,$output);
				WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Return Value:' .$rtn_var);

				//0 is success
				if ($rtn_var>0){
					WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'EXPORT FAILED return Value:' .$rtn_var);
					return false;
				}

				//Did the export work
				clearstatcache();
				if (!file_exists($sql_data_file_name) || filesize($sql_data_file_name)<=0) {
					WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'EXPORT FAILED: Dump was empty or missing.');
					return false;
				}
			} catch(Exception $e) {
				WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'EXPORT FAILED Exception: ' .$e);
				return false;
			}
		}
		else
		{
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'EXPORT FAILED Exec() disabled.');
			return false;
		}

		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'SQL Dump SUCCESS.');
		return true;
	}

	/**
	 * Generate SQL statements to rename imported snapshot tables
	 *
	 * @param $sql_file
	 * @param $snapshot_prefix
	 *
	 * @return bool
	 */
	public function generate_rename_sql($sql_file,$snapshot_prefix) {
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Generate Rename SQL:'.$sql_file);

		global $wpdb;
		try{

			$db = new WPBackItUp_DataAccess();
			$table_prefix= $wpdb->base_prefix;

			// Script Header Information
			$sql_header  = '';
			$sql_header .= "-- ------------------------------------------------------\n";
			$sql_header .= "-- ------------------------------------------------------\n";
			$sql_header .= "--\n";
			$sql_header .= "-- WPBackItUp Database Export \n";
			$sql_header .= "--\n";
			$sql_header .= '-- Created: ' . date("Y/m/d") . ' on ' . date("h:i") . "\n";
			$sql_header .= "--\n";
			$sql_header .= "-- Database : " . DB_NAME . "\n";
			$sql_header .= "--\n";
			$sql_header .= "-- Database Prefix  : " . $table_prefix . "\n";
			$sql_header .= "-- Snapshot Prefix  : " . $snapshot_prefix . "\n";
			$sql_header .= "--\n";
			$sql_header .= "-- ------------------------------------------------------\n";
			$sql_header .= "-- ------------------------------------------------------\n";
			$sql_header .= 'SET AUTOCOMMIT = 0;' ."\n" ;
			$sql_header .= 'SET FOREIGN_KEY_CHECKS=0;' ."\n" ;
			$sql_header .= "\n";
			$sql_header .= '/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;' . "\n";
			$sql_header .= '/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;' . "\n";
			$sql_header .= '/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;' . "\n";
			if (@constant("DB_CHARSET")) {
				$sql_header .= '/*!40101 SET NAMES ' .DB_CHARSET .' */;' . "\n";
			}else{
				$sql_header .= '/*!40101 SET NAMES utf8 */;' . "\n";
			}

			//http://stackoverflow.com/questions/36374335/error-in-mysql-when-setting-default-value-for-date-or-datetime
			$sql_header .= '/*!40101 SET SESSION sql_mode = \'\' */;' . "\n";
			$sql_header .= "\n";

			//turn on foreign key checking amd commit
			$sql_footer  = "\nSET FOREIGN_KEY_CHECKS = 1;\n" ;
			$sql_footer .= "COMMIT;\n" ;
			$sql_footer .= "SET AUTOCOMMIT = 1;\n"  ;


			//get table with snapshot prefix only - remove _
			$tables = $db->get_tables(rtrim($snapshot_prefix, "_")); //only want to rename these

			//put options table on bottom
			foreach ($tables as $key=>$table){
				if (strpos($table, '_options') !== false) {
					unset( $tables[ $key ] );
					array_push( $tables, $table );
					break;
				}
			}
			

			$output_buffer='';
			$first_pass=true;
			foreach ($tables as $table){

				if ($first_pass){
					$output_buffer =$sql_header;
					$first_pass=false;
				}

				//strip the job id
				$table_no_prefix = str_replace( rtrim($snapshot_prefix, "_"), '',$table );

				//is x_ in the first position
				if (strpos( $table_no_prefix,'x_')===0){
					//NO prefix should be on this table
					$old_table= str_replace( 'x_', '',$table_no_prefix );
				}else{
					//add the current prefix
					$old_table= $table_prefix . ltrim($table_no_prefix,"_");
				}

				$output_buffer.=sprintf("\nDROP TABLE IF EXISTS `%s`; \n",$old_table);
				$output_buffer.=sprintf("RENAME TABLE `%s` TO `%s`;\n",$table, $old_table);
			}

			//if empty buffer then no tables were found with snapshot prefix
			if (empty($output_buffer  )){
				WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'No tables found for snapshot prefix: ' .$snapshot_prefix);
				return false;
			}

			//add the footer
			$output_buffer .=$sql_footer;

			//save sql to file to run at end of restore -  replaces existing
			if (false===file_put_contents($sql_file,$output_buffer)){
				WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Snapshot Rename SQL file could not be created: ' .$sql_file);
				return false;
			 }


			return true;



		}catch(Exception $e) {
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Exception: ' .$e);
			return false;
		}
	}


	/**
	 * Export a batch of rows to a SQL file
	 *
	 * @param $sql_data_file_name SQL File Name
	 * @param $table Table to be exported
	 * @param $offset Offset to start at
	 * @param $limit Number of rows to export
	 * @param $create_table drop and create table
	 *
	 * @return bool True on success/ False on error
	 */
	public function wpbackitup_export_data($job_id,$sql_data_file_name,$table,$offset,$limit,$create_table) {
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Export Database Table:'.$table);

		global $wpdb;
		try{

			//if file doesnt exists then write header needed
			//It is possible that the last export finished but the task was not updated to complete
			//This is why we try export 3 times before error - log warning
			if (file_exists($sql_data_file_name)){
				WPBackItUp_Logger::log_warning($this->log_name,__METHOD__,'SQL file already exists. This is not expected.');
			}

			$table_prefix= $wpdb->base_prefix;

			//If prefix exists in position 0
			if (strpos($table, $table_prefix) !== false) {
				$backup_prefix = $job_id.'_';
				$table_backup_prefix= str_replace( $table_prefix, $backup_prefix, $table );
			} else {
				//append snapshot prefix + x(no prefix)
				$backup_prefix = $job_id.'x_';
				$table_backup_prefix= $backup_prefix . $table;
			}

			$mysqli = $this->get_mysqli();

			if (false===$mysqli) {
				WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'No SQL Connection');
				return false;
			}

			$mysqli->set_charset('utf8');

			//Fetch the sql result set
			$sql = sprintf('SELECT * FROM %s LIMIT %s,%s',$table,$offset,$limit);
			$sql_result = $mysqli->query($sql);
			if (false===$sql_result) {
				WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Query Error:' .var_export( $sql_result,true ));
				return false;
			}

			//export the database even when no data because want to drop and add the table during the restore
			$num_rows = $sql_result->num_rows;
			WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'ROWS to export:' .$num_rows);

			// Get number of fields (columns) of each table
			$num_fields = $mysqli->field_count;


			//open the SQL file - replace if aleaady exists
			$handle = fopen($sql_data_file_name,'w');
			if (false===$handle) {
				WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'File could not be opened.');
				return false;
			}

			//if SQL file doesnt exist then
			//if (true===$write_header){
			// Script Header Information
			$sql_header  = '';
			$sql_header .= "-- ------------------------------------------------------\n";
			$sql_header .= "-- ------------------------------------------------------\n";
			$sql_header .= "--\n";
			$sql_header .= "-- WPBackItUp Database Export \n";
			$sql_header .= "--\n";
			$sql_header .= '-- Created: ' . date("Y/m/d") . ' on ' . date("h:i") . "\n";
			$sql_header .= "--\n";
			$sql_header .= "-- Database : " . DB_NAME . "\n";
			$sql_header .= "--\n";
			$sql_header .= "-- Backup   Table  : " . $table . "\n";
			$sql_header .= "-- Snapshot Table  : " . $table_backup_prefix . "\n";
			$sql_header .= "--\n";
			$sql_header .= "-- SQL    : " . $sql . "\n";
			$sql_header .= "-- Offset : " . $offset . "\n";
			$sql_header .= "-- Rows   : " . $num_rows . "\n";
			$sql_header .= "-- ------------------------------------------------------\n";
			$sql_header .= "-- ------------------------------------------------------\n";
			$sql_header .= 'SET AUTOCOMMIT = 0 ;' ."\n" ;
			$sql_header .= 'SET FOREIGN_KEY_CHECKS=0 ;' ."\n" ;
			$sql_header .= "\n";
			$sql_header .= '/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;' . "\n";
			$sql_header .= '/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;' . "\n";
			$sql_header .= '/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;' . "\n";
			if (@constant("DB_CHARSET")) {
				$sql_header .= '/*!40101 SET NAMES ' .DB_CHARSET .' */;' . "\n";
			}else{
				$sql_header .= '/*!40101 SET NAMES utf8 */;' . "\n";
			}

			//http://stackoverflow.com/questions/36374335/error-in-mysql-when-setting-default-value-for-date-or-datetime
			$sql_header .= '/*!40101 SET SESSION sql_mode = \'\' */;' . "\n";

			$sql_header .= "\n";
			fwrite($handle,$sql_header); //Write to file
		//}

			//Create Table SQL statement
			if ($create_table ) {
				$table_sql = "--\n";
				$table_sql .= '-- Table structure for table `' . $table_backup_prefix . '`' . "\n";
				$table_sql .= "--\n";
				$table_sql .= 'DROP TABLE  IF EXISTS `' . $table_backup_prefix . '`;' . "\n";

				// Get the table schema
				$schema = $mysqli->query( 'SHOW CREATE TABLE ' . $table );

				// Extract table schema
				$create_table_sql = $schema->fetch_row();
				mysqli_free_result( $schema );

				if (!is_array($create_table_sql) || count($create_table_sql)<1 ){
					WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Could not generate create SQL:' .$table);
					return false;
				}

				//pull create out of array
				$create_table_sql = $create_table_sql[1];
				$create_table_sql= str_replace( '`'. $table .'`', '`' .$table_backup_prefix .'`', $create_table_sql );//get rid of prefix
				$create_table_sql = str_replace('TYPE=', 'ENGINE=', $create_table_sql);	//replace type with engine if exists

				//Remove /PAGE_CHECKSUM from MyISAM if exists
				if (preg_match('/ENGINE=([^\s;]+)/', $create_table_sql, $eng_match)) {
					$engine = $eng_match[1];
					if ('myisam' == strtolower($engine)) {
						$create_table_sql = preg_replace('/PAGE_CHECKSUM=\d\s?/', '', $create_table_sql, 1);
					}
				}

				//Fix the zero date issue
				$create_table_sql = $this->fix_zero_date( $create_table_sql );

				// Append table schema
				$table_sql .= $create_table_sql . ";" . "\n\n";

				$table_sql.= "\n\n" ;
				fwrite($handle,$table_sql); //Write to file
			}


			$first_pass=true;
			$output_buffer  = ''; //reset the buffer

			$insert_sql     = "INSERT INTO `" .$table_backup_prefix."` VALUES \n" . "(";
			$integer_fields = $this->get_integer_fields( $sql_result->fetch_fields() );

			//write file header
			$output_buffer .= "--\n";
			$output_buffer .= '-- Data for table `' . $table . '`' . "\n";
			$output_buffer .= '-- Number of rows: ' . $sql_result->num_rows ."\n";
			$output_buffer .= "--\n";
			fwrite($handle,$output_buffer); //Write to file

			$total_rows=0;
			while($rowdata = $sql_result->fetch_row()) {
				$total_rows++;

				//only do this the first time
				if ($first_pass) {
					$output_buffer=$insert_sql;
					$first_pass=false;
				} else {
					if (strlen($output_buffer) > 500000) {
						$output_buffer.= ";\n\n" ; //end the insert

						//Fix the zero date issue
						$output_buffer = $this->fix_zero_date( $output_buffer );

						fwrite($handle,$output_buffer); //Write to file

						$output_buffer=$insert_sql;//add insert

					} else {
						$output_buffer .= ",\n (";//add comma and write last pass to file
					}
				}

				//build row insert sql
				for($j=0; $j<$num_fields; $j++){

					if (isset($rowdata[$j])) {
						//Is this an integer field
						if (in_array($j,$integer_fields)) {
							$output_buffer .=  $rowdata[ $j ];
						}else{
							// $data = str_replace($search, $replace, str_replace('\'', '\\\'', str_replace('\\', '\\\\', $rowdata[ $j ] )));
                            $data = $this->mysql_escape_mimic($rowdata[ $j ]);
							$output_buffer .= "'" . $data . "'";
						}

					} else {
						if (is_null($rowdata[$j])) {
							$output_buffer.= 'NULL';//Dont think this is working but not causing issues
						} else {
							$output_buffer.= "''";
						}
					}

					if ($j<($num_fields-1)) { $output_buffer.= ','; }
				}

				$output_buffer.= ")";
			}

			//If firstpass is still true then there was no data in the table
			if (!$first_pass) $output_buffer.= ";\n\n" ;

			mysqli_free_result($sql_result);

			//Fix the zero date issue
			$output_buffer = $this->fix_zero_date( $output_buffer );

			//turn on foreign key checking amd commit
			$output_buffer .= 'SET FOREIGN_KEY_CHECKS = 1 ; '  . "\n" ;
			$output_buffer .= 'COMMIT ; '  . "\n" ;
			$output_buffer .= 'SET AUTOCOMMIT = 1 ; ' . "\n"  ;
			fwrite($handle,$output_buffer);

			//close the file
			fclose($handle);

			clearstatcache();

			//Did the export work
			if (!file_exists($sql_data_file_name) || filesize($sql_data_file_name)<=0) {
				WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Failure: SQL Export file was empty or didnt exist.');
				return false;
			}

			WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'SQL Backup File Created:'.$sql_data_file_name);
			return true;//Success

		}catch(Exception $e) {
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Exception: ' .$e);
			return false;
		}
	}
	
    public function run_sql_exec($sql_file,$with_mysqlpath=false) {
	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'SQL Execute:' .$sql_file);

        //Is the backup sql file empty
        if (!file_exists($sql_file) || filesize($sql_file)<=0) {
	        WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Failure: SQL File was empty:' .$sql_file);
            return false;
        }

        //This is to ensure that exec() is enabled on the server
        if(exec('echo EXEC') != 'EXEC') {
	        WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Failure: Exec() disabled.');
            return false;
        }

        try {

            $mysql_path='';
            if ($with_mysqlpath)  {
	            $db = new WPBackItUp_DataAccess();
                $mysql_path = $db->get_mysql_path();
                if ($mysql_path===false) return false;
            }

            $db_name = DB_NAME;
            $db_user = DB_USER;
            $db_pass = DB_PASSWORD;
            $db_host = $this->get_hostonly(DB_HOST);
            $db_port = $this->get_portonly(DB_HOST);

            $process = $mysql_path .'mysql';
            $command = $process
                . ' --host=' . $db_host;

            //Check for port
            if (false!==$db_port){
                $command .=' --port=' . $db_port;
            }

            $command .=
                ' --user=' . $db_user
                . ' --password=' . $db_pass
                . ' --database=' . $db_name
                . ' --execute="SOURCE ' . $sql_file .'"';

            if (WPBACKITUP__DEBUG) {
	            $masked_command = str_replace(array($db_user,$db_pass),'XXXXXX',$command);
	            WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Execute command:' . $masked_command );
            }

            //$output = shell_exec($command);
            exec($command,$output,$rtn_var);
	        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Execute output:');
	        WPBackItUp_Logger::log($this->log_name,$output);
	        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Return Value:' .$rtn_var);

            //0 is success
            if ($rtn_var!=0){
	            WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'An Error has occurred RTNVAL: ' .$rtn_var);
                return false;
            }

        }catch(Exception $e) {
	        WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Exception: ' .$e);
            return false;
        }

        //Success
	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'SQL Executed successfully');
        return true;
    }

    function run_sql_manual($sql_file_path, $delimiter = ';')
    {
	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'SQL Execute:' .$sql_file_path);

	    // Assuming set time limit don't directly work for class file.
	    if(!ini_get('safe_mode')){
	    	@set_time_limit(0);
	    }

        //Is the backup sql file empty
        if (!file_exists($sql_file_path) || filesize($sql_file_path)<=0) {
	        WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Failure: SQL File was empty:' .$sql_file_path);
            return false;
        }

        try {
            if (is_file($sql_file_path) === true)
            {
                $sql_handle = fopen($sql_file_path, 'r');

                if (is_resource($sql_handle) === true)
                {
                    $query = array();

                    $mysqli = $this->get_mysqli();
	                if (false === $mysqli) {
		                WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'No SQL Connection');
		                return false;
	                }

                    $mysqli->set_charset('utf8');
//                  $mysqli->autocommit(FALSE);
//                  $mysqli->begin_transaction();

                    $error_count=0;
                    $total_query=0;
                    while (feof($sql_handle) === false)
                    {
                        $query[] = fgets($sql_handle);

                        if (preg_match('~' . preg_quote($delimiter, '~') . '\s*$~iS', end($query)) === 1)
                        {
                            $query = trim(implode('', $query));

	                        // Fix Default date for mySQl 5.7 strict mode
	                        $query = $this->fix_zero_date($query);

                            //Execute SQL statement
                            $total_query++;
                            if ($mysqli->query($query) === false) {
                                $error_count++;

	                            WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Total Queries Executed:' .$total_query);
	                            WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Query Errors:' .$error_count);
	                            WPBackItUp_Logger::log_info($this->log_name,__METHOD__,' SQL ERROR: ' . $query);

                                //$mysqli->rollback();
                                $mysqli->close();

                                fclose($sql_handle);
                                return false;
                            }
//                          else {
//                              WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'SUCCESS: ' . $query);
//                          }

                            while (ob_get_level() > 0)
                            {
                                ob_end_flush();
                            }

                            flush();
                        }

                        if (is_string($query) === true)
                        {
                            $query = array();
                        }
                    }

                    //$mysqli->commit();
                    $mysqli->close();

	                WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'SQL Executed successfully:' .$sql_file_path);
	                WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Total Queries Executed:' .$total_query);
	                WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Query Errors:' .$error_count);
                    return fclose($sql_handle);
                }
            }

        }catch(Exception $e) {
	        WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Exception: ' .$e);
            return false;
        }

	    WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'SQL File could not be opened:' .$sql_file_path);
        return false;
    }

	/**
	 * Fetch active connection or create a new one
	 *
	 * @return bool|mysqli
	 */
	public function get_mysqli() {
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Get SQL connection to database.');

		if (! function_exists('mysqli_connect')) {
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'mySQLi is not installed.');
			return false;
		}

		$db_name = DB_NAME; 
        $db_user = DB_USER;
        $db_pass = DB_PASSWORD; 
        $db_host = $this->get_hostonly(DB_HOST);
        $db_port = $this->get_portonly(DB_HOST);

		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Host:' . $db_host);
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Port:' . $db_port);

		//is the connection an object & responds to a ping
		if (is_object($this->mysqli)){
			if (true === $this->mysqli->ping()) {
				return $this->mysqli;
			}
		}

		//create a new connection
      	if (false===$db_port){
      		$mysqli = new mysqli($db_host , $db_user , $db_pass , $db_name);
      	}
        else {
			$mysqli = new mysqli($db_host , $db_user , $db_pass , $db_name,$db_port);
        }
		
		if ($mysqli->connect_errno) {
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Cannot connect to database.' . $mysqli->connect_error);
		   	return false;
		}

		$this->mysqli = $mysqli;
		return $this->mysqli;
    }

	private function get_hostonly($db_host) {
		//Check for port
		$host_array = explode(':',$db_host);
		if (is_array($host_array)){
			return $host_array[0];
		}
		return $db_host;
	}

	private function get_portonly($db_host) {
		//Check for port
		$host_array = explode(':',$db_host);
		if ( is_array($host_array) && isset($host_array[1]) ) {
			$port = trim($host_array[1]);
            if(!empty($port))
                return $port;
		}

		return false;
	}

    //Get SQL scalar value
    public function get_sql_scalar($sql){
        $value='';

	    $mysqli = $this->get_mysqli();
	    if (false === $mysqli) {
		    WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'No SQL Connection');
		    return false;
	    }

        if ($result = mysqli_query($mysqli, $sql)) {
            while ($row = mysqli_fetch_row($result)) {
                $value = $row[0];
            }
            mysqli_free_result($result);
        }
        return $value;
    }

    //Run SQL command
    public function run_sql_command($sql){
	    $mysqli = $this->get_mysqli();
	    if (false === $mysqli) {
		    WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'No SQL Connection');
		    return false;
	    }

        if(!mysqli_query($mysqli, $sql) ) {
	        WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Error:SQL Command Failed:' .$sql);
            return false;
        }

        return true;
    }


	// Checking exec is disabled or not
   function exec_enabled() {
	 	$disabled = explode(',', ini_get('disable_functions'));
 	    return !in_array('exec', $disabled);
	}

	/**
	 * Get array of fields that are integer type
	 *
     * http://php.net/manual/en/mysqli-result.fetch-fields.php
	 *
	 * @param $field_list
	 *
	 * @return array
	 */
	private function get_integer_fields( $field_list ) {

		//** Create an array of integer fields */
		//tinyint_   1
		//boolean_   1
		//smallint_  2
		//int_       3
		//float_     4
		//double     5
		//bigint_    8
		//mediumint_ 9
		//decimal_    246
		$integer_types  = array( 1, 2, 3, 4, 5, 8, 9, 16, 246 );
		$integer_fields = array();
		//$field_list     = $sql_result->fetch_fields();

		//array of columns that are integers - column position
		foreach ( $field_list as $key => $field ) {
			if ( in_array( $field->type, $integer_types ) ) {
				$integer_fields[] = $key;
			}
		}

		return $integer_fields;
		//End Integers */
	}

	/**
	 * Replace ZERO date, datetime, date fields with valid valyes
	 *
	 *  -- Default date for mySQl 5.7 strict mode
	 *
	 * http://stackoverflow.com/questions/36374335/error-in-mysql-when-setting-default-value-for-date-or-datetime
	 *
	 * @param $sql
	 *
	 * @return mixed
	 */
	private function fix_zero_date( $sql ) {

		// DatTime Range:   '1000-01-01 00:00:00' to '9999-12-31 23:59:59'.
		// Timestamp range: '1970-01-01 00:00:01' UTC to '2038-01-19 03:14:07'
		// Date Range:      '1000-01-01' to '9999-12-31'
		$sql = str_replace( "'0000-00-00 00:00:00'", "'1970-01-02 00:00:01'", $sql );
		$sql = str_replace( "'0000-00-00'", "'1970-01-02'", $sql );

		return $sql;
	}

	/**
     *  Escape MySQL String
     *
     * Custom method for escape mysql string since 5.7 drop mysql_real_escapse_string()
     * http://php.net/manual/en/function.mysql-real-escape-string.php
     *
     * @param $inp  string
     *
     * @return string
     */
    private function mysql_escape_mimic( $inp ) {
        if(is_array($inp))
            return array_map(__METHOD__, $inp);

        $search =  array("\x00", "\x0a", "\x0d", '\\', "\0", "\n", "\r", "'", '"', "\x1a");
        $replace = array("\0", "\n", "\r", '\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z');

        if(!empty($inp) && is_string($inp)) {
            return str_replace($search, $replace, $inp);
        }

        return $inp;
    }
}