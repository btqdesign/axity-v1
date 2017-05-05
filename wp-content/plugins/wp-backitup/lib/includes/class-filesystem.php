<?php if (!defined ('ABSPATH')) die('No direct access allowed');

/**
 * WP BackItUp  - File System Class
 *
 * @package WP BackItUp
 * @author  Chris Simmons <chris.simmons@wpbackitup.com>
 * @link    http://www.wpbackitup.com
 *
 */

/*** Includes ***/

class WPBackItUp_FileSystem {

	const FILESYSTEM_LOG_NAME = 'debug_filesystem';
	private $log_name;

	function __construct($log_name=null) {
		try {

			$this->log_name = self::FILESYSTEM_LOG_NAME;//default log name
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
   		
   }

    public function create_dir($dir) {
	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Create Directory: ' .$dir);
		if( !is_dir($dir) ) {
			@mkdir($dir, 0755);
		}
	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Directory created: ' .$dir);
		return true;
	}

	/**
	 * Recursively delete a folder
	 *  -  does not stop on error but will return false if all files and folders could NOT be deleted
	 *
	 * @param $dir
	 * @param array $ignore
	 *
	 * @return bool false if all files and folders could NOT be deleted
	 */
	public function recursive_delete($dir, $ignore = array('') ){
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Recursively Delete: ' .$dir);
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Ignore:');
		WPBackItUp_Logger::log($this->log_name,$ignore);

		$success=true;
        if( is_dir($dir) ){
            //Make sure the folder is not in the ignore array
            if (!$this->delete_ignore($dir,$ignore)){
	            $dh = opendir($dir);
                if(false!== $dh) {
                    while( ($file = readdir($dh)) !== false ) {
                        if (!$this->delete_ignore($file,$ignore)) { //Check the file is not in the ignore array
                            if(!is_dir($dir .'/'. $file)) {
	                            //delete the file
                                if (unlink($dir .'/'. $file)){
	                                WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'File Deleted:' .$dir .'/'. $file);
                                } else {
	                                $success=false;
	                                WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'File NOT Deleted:' .$dir .'/'. $file);
                                }
                            } else {
                                //This is a dir so delete the files first
	                            $success=$this->recursive_delete($dir.'/'. $file, $ignore);
                            }
                        }
                    }
                } else{
	                WPBackItUp_Logger::log_error( $this->log_name, __METHOD__, 'Folder could not be opened:' . $dir );
	                $success=false;
                }
                //Remove the directory
                if (true===rmdir($dir)){
	                WPBackItUp_Logger::log_info( $this->log_name, __METHOD__, 'Folder Deleted:' . $dir );
                } else{
	                WPBackItUp_Logger::log_error( $this->log_name, __METHOD__, 'Folder could not be deleted:' . $dir );
	                $success=false;
                }

                closedir($dh);
            }
		}
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Recursive Delete Completed:'.var_export($success,true));
		return $success;
	}

    public function recursive_copy($dir, $target_path, $ignore = array('') ) {
	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Recursive copy FROM: ' .$dir);
	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Recursive Copy TO: '.$target_path);
	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'IGNORE:');
	    WPBackItUp_Logger::log($this->log_name,$ignore);

        if( is_dir($dir) ) { //If the directory exists
            //Exclude all the OTHER backup folders under wp-content
            //Will create the folders but NOT the contents
            if (!$this->ignore($dir,$ignore) && !$this->is_backup_folder($dir) ){
                if ($dh = opendir($dir) ) {
                    while(($file = readdir($dh)) !== false) { //While there are files in the directory
                        if (!$this->ignore($file,$ignore)) { //Check the file is not in the ignore array
                            if (!is_dir( $dir.$file ) ) {
                                try {
                                    $fsrc = fopen($dir .$file,'r');
                                    $fdest = fopen($target_path .$file,'w+');
                                    stream_copy_to_stream($fsrc,$fdest);
                                    fclose($fsrc);
                                    fclose($fdest);
                                } catch(Exception $e) {
	                                WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'File Copy Exception: ' .$e);
                                    return false;
                                }
                            } else { //If $file is a directory
                                $destdir = $target_path .$file; //Modify the destination dir
                                if(!is_dir($destdir)) { //Create the destdir if it doesn't exist
	                                WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Create Folder: ' .$destdir);
                                    try {
                                        @mkdir($destdir, 0755);
                                    } catch(Exception $e) {
	                                    WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Create Folder Exception: ' .$e);
                                        return false;
                                    }
                                }
                                $this->recursive_copy($dir .$file .'/', $target_path .$file .'/', $ignore);
                            }
                        }
                    }
                    closedir($dh);
                }
            }
        }

	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Completed');
        return true;
    }

    public function recursive_validate($source_path, $target_path, $ignore = array('') ) {

        $rtnVal=true;
        if( is_dir($source_path) ) { //If the directory exists
            if (!$this->ignore($source_path,$ignore)){
                if ($dh = opendir($source_path) ) {
                    while(($file = readdir($dh)) !== false) { //While there are files in the directory
                        if ( !$this->ignore($file,$ignore)) { //Check the file is not in the ignore array
                            if (!is_dir( $source_path.$file ) ) {
                                try {
                                    $source_file = $source_path .$file;
                                    $target_file = $target_path .$file;

                                    if (!file_exists($target_file))  {
	                                    WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Files DIFF - Target file doesnt exist:' . $target_file);
                                        $rtnVal=false;
                                        continue;
                                    }

                                    $source_file_size = filesize ($source_file);
                                    $target_file_size = filesize ($target_file);

                                    if ($source_file_size != $target_file_size){
	                                    WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Files DIFF Source:' . $source_file);
                                        WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Files DIFF Target:' . $target_file);
	                                    WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Files DIFF Size:' . $source_file_size .':' . $target_file_size);
                                        $rtnVal=false;
                                        continue;
                                    }

                                } catch(Exception $e) {
	                                WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Exception: ' .$e);
                                    return false;
                                }
                            } else { //If $file is a directory
                                $destdir = $target_path .$file; //Modify the destination dir
                                if(!is_dir($destdir)) {
	                                WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'DIFF Folder doesnt exist: ' .$destdir);
                                    $rtnVal= false;
                                }else{
                                    $dir_rtnVal=$this->recursive_validate($source_path .$file .'/', $target_path .$file .'/', $ignore);
                                    //Don't want to set to true as its the default on all calls
                                    if (!$dir_rtnVal) $rtnVal = false;
                                }
                            }
                        }
                    }
                    closedir($dh);
                }
            }
        }

        return $rtnVal;
    }

    private function ignore($file, $ignoreList){

        //Exclude these files and folders from the delete
        if (in_array(basename($file), $ignoreList) ||
            substr($file, 0, 1) == '.'   ||
            ($file == "." ) ||
            ($file == ".." ) ||
            ($file == "._" ) ||
            ($file == "cgi-bin" ))  {

            return true;
        }

        return false;
    }

	private function delete_ignore($file, $ignoreList){

		//Exclude these files and folders from the delete
		if (in_array(basename($file), $ignoreList) ||
		    //substr($file, 0, 1) == '.'   ||
		    ($file == "." ) ||
		    ($file == ".." ))
		    //($file == "._" )
		    //($file == "cgi-bin" ))
		{
			return true;
		}

		return false;
	}

    //Check for backup folders
    private function is_backup_folder($dir){
        if  (
            strpos(strtolower($dir),'/wp-content/backup')!== false ||
            strpos(strtolower($dir),'/wp-content/updraft')!== false ||
            strpos(strtolower($dir),'/wp-content/wp-clone')!== false ||
            strpos(strtolower($dir),'/wp-content/uploads/backwpup')!== false ||
            strpos(strtolower($dir),'/wp-content/uploads/backupwordpress')!== false
            ){

	            WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'SKIP Backup Folder: ' .$dir);
                return true;

            }else{
                return false;
            }

    }

	public function purge_FilesByDate($number_Files_Allowed,$path)
	{
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Purge files by date:' .$number_Files_Allowed .':'.$path);

		if (is_numeric($number_Files_Allowed) && $number_Files_Allowed> 0){
			$FileList = glob($path . "*.zip");

			//Sort by Date Time			
			usort($FileList, create_function('$a,$b', 'return filemtime($b) - filemtime($a);'));
			  	 
			$i = 1;
			foreach ($FileList as $key => $val)
			{
				WPBackItUp_Logger::log_info($this->log_name,__METHOD__,' File:'.$val);
				WPBackItUp_Logger::log_info($this->log_name,__METHOD__,' File Date Time:'.filemtime($val));

			  if($i <= $number_Files_Allowed)
			  {
			    $i++;
			    continue;
			  }
			  else{
                $log_file_path = str_replace('.zip','.log',$val);
                if (file_exists($val)) unlink($val);
                if (file_exists($log_file_path)) unlink($log_file_path);
				  WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Delete File:)' .$val);

			  }
			}
		}
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Completed.');
	}

    public function purge_files($path, $file_pattern, $days)
    {
	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Purge files days:' . $days);
	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Purge files path:' . $path);
	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Purge files extension:' . $file_pattern);

        //Check Parms
        if (empty($path) ||  empty($file_pattern) || !is_numeric($days)){
	        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Invalid Parm values');
            return false;
        }

        $FileList = glob($path . $file_pattern);

        //Sort by Date Time oldest first so can break when all old files are deleted
        usort($FileList, create_function('$a,$b', 'return filemtime($a) - filemtime($b);'));

        foreach ($FileList as $key => $file)
        {
	        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'File:'.$file);
	        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'File Date Time:'.filemtime($file));

            $current_date = new DateTime('now');
            $file_mod_date = new DateTime(date('Y-m-d',filemtime($file)));

            //PHP 5.3 only
            //$date_diff = $current_date->diff($file_mod_date);
            //$date_diff_days = $date_diff->days;

            $util = new WPBackItUp_Utility($this->log_name);
            $date_diff_days=$util->date_diff_days($file_mod_date,$current_date);

            if($date_diff_days>=$days){
                if (file_exists($file)) unlink($file);
	            WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Delete:' . $file);
            }
            else{
                break; //Exit for
            }
        }
	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Completed.');
        return true;
    }


//	/**
//     * Purge the backups that exceed the retained number setting
//     *
//     * @param $path
//     * @param $pattern
//     * @param $retention_limit
//     *
//     * @return bool
//     */
//    public function purge_folders($path, $pattern, $retention_limit)
//    {
//	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Purge folders retained number:' . $retention_limit);
//	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Purge folder path:' . $path);
//	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Purge pattern:' . $pattern);
//
//        //Check Parms
//        if (empty($path) ||  empty($pattern) || !is_numeric($retention_limit)){
//	        WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Invalid Parm values');
//            return false;
//        }
//
//        $folder_list = glob($path . $pattern, GLOB_ONLYDIR);
//
//        //Sort by Date Time so newest is on top of array
//	    usort($folder_list, create_function('$a,$b', 'return filemtime($a)>filemtime($b);'));
//
//
//        $backup_count=0;
//        foreach (array_reverse($folder_list) as $key => $folder)
//        {
//	        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Folder:'.$folder);
//	        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Folder Date Time:'. date("F d Y H:i:s", filemtime($folder)));
//
//            ++$backup_count;
//            if($backup_count>$retention_limit){
//                if (file_exists($folder)) {
//                    $this->recursive_delete($folder);
//                }
//            }
//        }
//	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'End');
//        return true;
//    }

	public function delete_files($file_list)
	{
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin');
		try {
			$rtn_val = true;
			foreach ($file_list as $key => $file)
			{
				if (file_exists($file)){
					
					//if any delete fails keep on going but return false
					if (false===unlink($file)){
						WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'File was NOT Deleted:' . $file);
						$rtn_val=false;
					}

					WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Deleted:' . $file);
				} else {
					//not an error
					WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'File was not found:' . $file);
				}
			}

			WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'End');
			return $rtn_val;

		} catch(Exception $e) {
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Exception:' . $e);
			return false;
		}
	}


	function get_file_handle($path,$newFile=false) {
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Path:' . $path);

        try {

            if ($newFile && file_exists($path)){
                if (unlink($path)){
	                WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Deleted:' . $path);
                }
                else{
	                WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'File could not be deleted:');
	                WPBackItUp_Logger::log_info($this->log_name,__METHOD__,var_export(error_get_last(),true));
                }
            }

            $fh= fopen($path, 'w');
            if (false===$fh){
	            WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'File could not be opened:');
	            WPBackItUp_Logger::log_info($this->log_name,__METHOD__,var_export(error_get_last(),true));
                return false;
            }

            return $fh;

        } catch(Exception $e) {
	        WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Exception:' . $e);
            return false;
        }
    }

	/**
	 * Copy single file
	 * @param $from_file
	 * @param $to_file
	 *
	 * @return bool
	 */
	function copy_file($from_file,$to_file) {
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'FROM Path:' . $from_file);
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'TO Path:' . $to_file);

		try {
			if (file_exists($from_file)){
				if (copy($from_file,$to_file)){
					WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'File copied successfully.');
					return true;
				}
				else{
					WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'File could not be copied:');
					WPBackItUp_Logger::error($this->log_name,__METHOD__,var_export(error_get_last(),true));
					return false;
				}
			}
			else{
				WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'FROM File doesnt exist');
				return false;
			}

		} catch(Exception $e) {
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Exception:' . $e);
			return false;
		}
	}

	/**
	 * Rename single file
	 * @param $from_file
	 * @param $to_file_name
	 *
	 * @return bool
	 */
	function rename_file($from_file,$to_file_name) {
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'FROM Path:' . $from_file);
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'TO Path:' . $to_file_name);

		try {
			if (file_exists($from_file)){
				if (rename($from_file,$to_file_name)){
					WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'File renamed successfully.');
					return true;
				}
				else{
					WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'File could not be renamed:');
					WPBackItUp_Logger::log_error($this->log_name,__METHOD__,var_export(error_get_last(),true));
					return false;
				}
			}
			else{
				WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'FROM File doesnt exist');
				return false;
			}

		} catch(Exception $e) {
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Exception:' . $e);
			return false;
		}
	}

	/**
	 * Make sure that htaccess/web.config files exist in folder
	 * If folder doesnt exist then create it.
	 * @param $path
	 */
	function secure_folder($path){
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin');

		$path = rtrim($path,"/");

		if( !is_dir($path) ) {
			@mkdir($path, 0755);
			WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Folder Created:' .$path);
		}

		if (!is_file($path.'/index.html')) @file_put_contents($path.'/index.html',"<html><body><a href=\"http://www.wpbackitup.com\">WPBackItUp - The simplest way to backup WordPress</a></body></html>");
		if (!is_file($path.'/.htaccess')) @file_put_contents($path.'/.htaccess','deny from all');
		if (!is_file($path.'/web.config')) @file_put_contents($path.'/web.config', "<configuration>\n<system.webServer>\n<authorization>\n<deny users=\"*\" />\n</authorization>\n</system.webServer>\n</configuration>\n");
		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Secure files exist or were created.');


		WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'End');
	}


	public function get_recursive_file_list($pattern) {
		return $this->glob_recursive($pattern);
	}

	private function glob_recursive($pattern, $flags = 0) {

		//The order here is important because the folders must be in the list before the files.
		$files = glob($pattern, $flags); //everything in the root

        //Get the folders and append all the files in the folder
		foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR) as $dir)
		{
            //Get the contents of the folder
            $current_folder = $this->glob_recursive($dir.'/'.basename($pattern), $flags);

            if (is_array($current_folder)){
			    $files = array_merge($files,$current_folder );
            }
		}

		return $files;
	}


	/**
	 * Fetch an array of files from the path provided.
	 * An optional list of pipe delimited extensions may be provided to filter
	 * results by extension.  If none is provided all files in path will be returned.
	 *
	 * @param $path Path to folder where files reside.
	 * @param null $extensions Pipe delimited list of extensions ex. txt|sql
	 *
	 * @return array Array of files
	 */
	function get_fileonly_list($path,$extensions=null){
		//WPBackItUp_Logger::log_info($this->log_name,__METHOD__,"Begin:" .$path);
		//WPBackItUp_Logger::log_info($this->log_name,__METHOD__,"Pattern:" .$extensions);//txt|sql'

		//Remove trailing slashes
		$path = rtrim($path,"\\");
		$path = rtrim($path,"/");
		//WPBackItUp_Logger::log_info($this->log_name,__METHOD__,"Path:" .$path);

		$all_files = glob($path .'/*.*');
		//WPBackItUp_Logger::log_info($this->log_name,__METHOD__,"All Files:" .var_export($all_files,true));

		//If a file pattern is passed then filter
		if(isset($extensions)){
			$regex = sprintf('~\.(%s)$~',$extensions);
			$filtered_files = preg_grep($regex, $all_files);
		}else{
			$filtered_files =array_filter($all_files, 'is_file');
		}

		//WPBackItUp_Logger::log_info($this->log_name,__METHOD__,"Filtered Files:" .var_export($filtered_files,true));
		return $filtered_files;
	}

	/**
	 *  Append to output file.  From file will be read in chunks to reduce chance for memory exceptions
	 *
	 * @param string $from_filepath
	 * @param string $to_filepath
	 *
	 * @return bool
	 */
	function append_file_chunked($from_filepath , $to_filepath ) {

		try {

			// make sure from file exists
			if(!is_file($from_filepath)) {
				BackItUp_LoggerV2::log_error($this->log_name,__METHOD__,sprintf("Input file error, file does not exist: %s", $from_filepath));
				return false;
			}

			//Open output file
			$toFile_handle = @fopen( $to_filepath, 'a');
			if (false===$toFile_handle) {
				WPBackItUp_Logger::log_error($this->log_name,__METHOD__,sprintf("Output file error, file could not be created: %s", $toFile_handle));
				return false;
			}

			$chunkSize = 5242880;//5 MB
			$offset=0;
			$partNumber = 0;
			$fileSize = filesize($from_filepath);
			if ($fromFile_handle = @fopen($from_filepath, 'r')) {
				do  {
					++$partNumber;
					echo $partNumber;
					// Seek to the correct position on the file pointer
					fseek( $fromFile_handle, $offset );

					// Read from the file handle until EOF, uploading each chunk
					if ( $data = fread( $fromFile_handle, $chunkSize ) ) {

						if (false ===  fwrite($toFile_handle, $data)) return false;
						$offset += $chunkSize;  //Get next offset
					} else{
						//should never get here
						WPBackItUp_Logger::log_error($this->log_name,__METHOD__,sprintf("Unable to read file-offset : %s-%s", $from_filepath,$offset));
						@fclose( $fromFile_handle);
						return false;
					}

				} while ($offset<$fileSize);

				@fclose( $toFile_handle);
				@fclose( $fromFile_handle);

				return true;

			} else {
				WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Read file error');
				return false;
			}


		} catch (Exception $e) {
			WPBackItUp_Logger::log_error($this->log_name,__METHOD__,'Exception:' . $e->getMessage());
			return false;
		}

	}

	/**
	 * Fetch an array of files from the path provided + file size in KB
	 *
	 * An optional list of pipe delimited extensions may be provided to filter
	 * results by extension.  If none is provided all files in path will be returned.
	 *
	 * @param $path Path to folder where files reside.
	 * @param null $extensions Pipe delimited list of extensions ex. txt|sql
	 *
	 * @return array Array of files
	 */
	function get_fileonly_list_with_filesize($path,$extensions=null){
		
		$file_list = $this->get_fileonly_list($path,$extensions);

		//get file sizes and add to array
		if (is_array($file_list) && count($file_list)>0){
			$files_container = array();
			foreach($file_list as $file){
				$files_container[$file] = ceil(filesize($file) /1024); //round up to nearest KB;
			}

			$file_list = $files_container;
		}

		return $file_list;
	}

    /**
     *
     * An optional list of pipe delimited extensions may be provided to filter
     * results by extension.  If none is provided all files in path will be returned.
     *
     * @param $file name.
     *
     * @return single file time
     */
    function get_filetime_with_filename($file){

        //get file sizes
        if(!empty($file)){
            $file_time = date("F d,Y H:i:s",filemtime($file));
        }
        
        return $file_time;
    }
    
	/**
	 * Human readable file size
	 *
	 * @param $kilo_bytes
	 *
	 * @return string
	 */
	static function format_file_size_kb($kilo_bytes) {

		$bytes = ceil($kilo_bytes * 1024);

	return self::format_file_size($bytes);
	}
	/**
	 * Human readable file size
	 * @param $bytes
	 *
	 * @return string
	 */
	static function format_file_size($bytes)
	{
		try {
			if ($bytes >= 1073741824)
			{
				$bytes = number_format($bytes / 1073741824, 2) . ' GB';
			}
			elseif ($bytes >= 1048576)
			{
				$bytes = number_format($bytes / 1048576, 2) . ' MB';
			}
			elseif ($bytes >= 1024)
			{
				$bytes = number_format($bytes / 1024, 2) . ' KB';
			}
			elseif ($bytes > 1)
			{
				$bytes = $bytes . ' bytes';
			}
			elseif ($bytes == 1)
			{
				$bytes = $bytes . ' byte';
			}
			else
			{
				$bytes = '0 bytes';
			}

			return $bytes;

		} catch(Exception $e) {
			WPBackItUp_Logger::log_error(self::FILESYSTEM_LOG_NAME,__METHOD__,'Exception:' . $e);
			return '0 bytes';
		}

	}



 }

