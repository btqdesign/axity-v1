<?php 
/**
 *  Copyright 2012 GroupDocs.
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

class FileStream {
	
	private $fileName = null;
	private $contentType = null;
	private $size = null;
	private $inputStream = null;
	
	private function __construct($inputFilePath=null, $downloadDirectory=null){
		$this->filePath = $inputFilePath;
		$this->downloadDirectory = $downloadDirectory;
	}

	public static function fromFile($inFilePath){
		if(!file_exists($inFilePath)){
			throw new Exception("File $inFilePath doesn't exist.");
		}
		return new self($inFilePath);
	}
	
	/**
	 * by default filename is derived from content-disposition header or url
	 */
	public static function fromHttp($downloadDirectory, $outFileName=null){
		if(!file_exists($downloadDirectory)){
			throw new Exception("Directory $downloadDirectory doesn't exist.");
		}
		$instance = new self(null, $downloadDirectory);
		$instance->fileName = $outFileName;
		$instance->requestUrl = null;
		return $instance;
	}
	
	public function getFileName(){
        if ($this->fileName == null and $this->filePath != null){
            $this->fileName = basename($this->filePath);
		} else if ($this->fileName == null and $this->downloadDirectory != null){
			// guess from url
        	$this->fileName = basename(parse_url($this->requestUrl, PHP_URL_PATH));
		}
        return $this->fileName;
	}
	
	public function getContentType(){
        if ($this->contentType == null and $this->filePath != null){
            $this->contentType = APIClient::getMimeType($this->filePath);
		}
        return $this->contentType;
	}
	
	public function getSize(){
        if ($this->size == null and $this->filePath != null){
            $this->size = filesize($this->filePath);
		}
        return $this->size;
	}
	
	public function getInputStream(){
        if ($this->inputStream == null and $this->filePath != null){
            $this->inputStream = fopen($this->filePath, "rb");
		} else if ($this->inputStream == null and $this->downloadDirectory != null){
			$outFilePath = $this->downloadDirectory."/".$this->getFileName();
            $this->inputStream = fopen($outFilePath, "wb");
		}
        return $this->inputStream;
	}
	
	public function headerCallback($ch, $string){
		// this method is called multiple times
		
		$len = strlen($string);
        if( !strstr($string, ':') ) {
            return $len;
        }
        
        list($name, $value) = explode(':', $string, 2);
		
		if($this->fileName == null){
	        if( strcasecmp($name, 'Content-Disposition') == 0) {
	            $parts = explode(';', $value);
	            if( count($parts) > 1 ) {
	                foreach($parts AS $crumb) {
	                    if( strstr($crumb, '=') ) {
	                        list($pname, $pval) = explode('=', $crumb);
	                        $pname = trim($pname);
	                        if( strcasecmp($pname, 'filename') == 0 ) {
	                            // Using basename to prevent path injection in malicious headers.
	                            $this->fileName = basename($this->unquote(trim($pval)));
	                        }
	                    }
	                }
	            }
	        } else if($this->requestUrl == null){
	        	$this->requestUrl = $this->getCurlInfo($ch, CURLINFO_EFFECTIVE_URL);
	        }
		}
		
        $this->headers[$name] = trim($value);

		if( strcasecmp($name, 'Content-Type') == 0) {
			$this->contentType = $this->headers[$name];
		}

        return $len;
	}

	public function bodyCallback($ch, $string) {
		// this method is called multiple times
		$len = null;
		if(!empty($this->headers['jsonerror'])){
			$this->jsonError = $string;
			$len = strlen($string);
		} else {
        	$len = fwrite($this->getInputStream(), $string);
		}
        $this->size += $len;
        return $len;
    }
	
	public function getCurlInfo($ch, $name){
		return curl_getinfo($ch, $name);
	}

    private function unquote($string) {
        return str_replace(array("'", '"'), '', $string);
    }
	
}