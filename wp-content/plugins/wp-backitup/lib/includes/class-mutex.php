<?php

class WPBackItUp_Mutex {

	private $log_name= 'debug_mutex';
	private $writeablePath = '';
	private $lockName = '';
	private $fileHandle = null;

    public function __construct($lockName, $writeablePath = null){
        $this->lockName = preg_replace('/[^a-zA-Z0-9]/', '', $lockName);

	    if($writeablePath == null){
            $this->writeablePath = $this->findWriteablePath();
        } else {
            $this->writeablePath = $writeablePath;
        }
    }

	function __destruct() {
		if (null!=$this->fileHandle){
			$this->releaseLock();
		}
	}

	public function lock($wait = false, $maxTime = 10){
        $locked = false;
        $timeAsleep = 0;
        do{
            $locked = $this->attemptLock();
            if(!$locked && $wait){
                sleep(1);
                $timeAsleep +=1;
            }
        } while(!$locked && $wait && $timeAsleep <= $maxTime);

        return $locked;
    }

    private function attemptLock(){
        $lockFilePath = $this->getLockFilePath();
        WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin:' .$lockFilePath);
        if (PHP_OS == 'WINNT'){
            if(file_exists($lockFilePath)){
                $unlinked = @unlink($lockFilePath);
                if(!$unlinked) return false; //locked
            }
        }

        $fileHandle = $this->getFileHandle();
	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'File Handle:' .var_export($fileHandle,true));

        if(!$fileHandle){
            return false;
        } else {
            return true;
        }
    }

    public function getFileHandle(){
        if(!$this->fileHandle){
            $this->fileHandle = @fopen($this->getLockFilePath(), 'a+');
            if($this->fileHandle){
                if (PHP_OS == 'WINNT'){
                    fwrite($this->fileHandle, "A");
                    if(flock($this->fileHandle, LOCK_SH)){
                        rewind($this->fileHandle);
                        $contents = fread($this->fileHandle, 2);
                        if($contents != 'A'){
                            flock($this->fileHandle, LOCK_UN);
                            fclose($this->fileHandle);
                            $this->fileHandle = null;
                            return false;
                        }
                    } else {
                        fclose($this->fileHandle);
                        return false;
                    }
                } else {
                    if(flock($this->fileHandle, LOCK_EX | LOCK_NB)){

                    } else {
                        fclose($this->fileHandle);
                        return false;
                    }
                }

            }
        }
        return $this->fileHandle;
    }

	/**
	 * release exclusive lock on file
	 *
	 * @return bool
	 */
	public function releaseLock(){
	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Begin');

        $close_rtn=true;

	    $fh = $this->fileHandle;
	    WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'File Handle:' .var_export($fh,true));

        if (null!=$fh ) {
            $flock_rtn = @flock($fh, LOCK_UN);
            WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Flock Unlock:' .var_export($flock_rtn,true));

            $close_rtn = @fclose($fh);
            WPBackItUp_Logger::log_info($this->log_name,__METHOD__,'Close File:' .var_export($close_rtn,true));
        }

	    //delete file & release reference
        $lock_file =$this->getLockFilePath();
        if (file_exists($lock_file)){
            unlink($lock_file);
        }

	    $this->fileHandle = null;

        return $close_rtn;
    }

    public function getLockFilePath(){
        return $this->writeablePath . DIRECTORY_SEPARATOR . $this->lockName . '.lock';
    }

    public function isLocked(){
        $lock = $this->attemptLock();

        if($lock){
            $this->releaseLock();
            return false;
        }else{
            return true;
        }
    }

    public function findWriteablePath(){
        $foundPath = false;

        //First try the temp directory...
        $path = $this->getTempDirPath();
        $fileName = $path . DIRECTORY_SEPARATOR . 'test_file';
        if($fileHandle = fopen($fileName, "w")){
            fclose($fileHandle);
            $foundPath = true;
        }


        //Now try the current directory
        if(!$foundPath){
            $path = '.';
            if($fileHandle = fopen($path . DIRECTORY_SEPARATOR . 'test_file', "w")){
                fclose($fileHandle);
                $this->writeablePath = $path;
            }
        }

        if(!$foundPath){
            throw new Exception("Cannot establish lock on temporary file.");
        }
        
        return $path;
    }

    public function getTempDirPath(){
        $fileName = tempnam("/tmp", "MUT");
        $path = dirname($fileName);
        if ($path == '/') $path = '/tmp';
        return $path;
    }
}