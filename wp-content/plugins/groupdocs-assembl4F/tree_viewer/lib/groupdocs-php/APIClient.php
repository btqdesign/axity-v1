<?php
/**
 * 	Wordnik.com's Swagger generic API client. This client handles the client-
 * 	server communication, and is invariant across implementations. Specifics of
 *	the methods and models for each application are generated from the Swagger
 *	templates.
 */


/* Autoload the model definition files */
/**
 *
 *
 * @param string $className the class to attempt to load
 */
function swagger_autoloader($className) {
	$currentDir = substr(__FILE__, 0, strrpos(__FILE__, DIRECTORY_SEPARATOR));
	if (file_exists($currentDir . DIRECTORY_SEPARATOR . $className . '.php')) {
		include $currentDir . DIRECTORY_SEPARATOR . $className . '.php';
	} elseif (file_exists($currentDir . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . $className . '.php')) {
		include $currentDir . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . $className . '.php';
	}
}
spl_autoload_register('swagger_autoloader');

interface RequestSigner {
    
    public function signUrl($url);
        
    public function signContent($requestBody, $headers);

}

class DefaultRequestSigner implements RequestSigner {
    
    public function signUrl($url){
        return $url;
	}
        
    public function signContent($requestBody, $headers){
        return $requestBody;
	}
}

class APIClient {
	
	private static $packageInfo;
	
	public static function getPackageInfo(){
		if(is_null(self::$packageInfo)){
			$filename = dirname(__FILE__)."/composer.json";
			if(!file_exists($filename)){
				$filename = dirname(__FILE__)."/../composer.json";
			}
			
			$json = file_get_contents($filename);
			$jsonArray = json_decode($json, true);
			self::$packageInfo = array();
			if(is_array($jsonArray)){
				self::$packageInfo['version'] = $jsonArray['version'];
				self::$packageInfo['name'] = $jsonArray['name'];
				$pos = strpos(self::$packageInfo['name'], "/");
				if($pos !== false){
					self::$packageInfo['name'] = substr(self::$packageInfo['name'], $pos + 1);
			  	}
			}
		}
		return self::$packageInfo;
	}

	public static $POST = "POST";
	public static $GET = "GET";
	public static $PUT = "PUT";
	public static $DELETE = "DELETE";

	/**
	 * @param RequestSigner $requestSigner RequestSigner implementation or omit to use DefaultRequestSigner which do nothing
	 */
	function __construct($requestSigner = null) {
		$this->signer = $requestSigner == null ? new DefaultRequestSigner() : $requestSigner;
		$info = self::getPackageInfo();
		$this->headers = array("Groupdocs-Referer" => $info["name"]."/".$info["version"]);
		$this->debug = false;
	}

	public function setDebug($flag, $curlLogFilepath="php://stderr") {
		$this->debug = $flag;
		$this->newline = "\n"; //$html ? "<br />" : "\n";
		$this->curlLogFilepath = $curlLogFilepath;
	}

	public function addHeaders(array $headers) {
		$this->headers = $headers;
	}

    /**
	 * @param string $resourcePath path to method endpoint
	 * @param string $method method to call
	 * @param array $queryParams parameters to be place in query URL
	 * @param array $postData parameters to be placed in POST body
	 * @param array $headerParams parameters to be place in request header
	 * @return unknown
	 */
	public function callAPI($apiServer, $resourcePath, $method, $queryParams, $postData,
		$headerParams, FileStream $outFileStream=null) {

		$headers = array();
		foreach ($this->headers as $key => $val) {
			$headers[] = "$key: $val";
		}
		
		if ($headerParams != null) {
			foreach ($headerParams as $key => $val) {
				$headers[] = "$key: $val";
			}
		}
		
		$isFileUpload = false;
		if (empty($postData)){
			$headers[] = "Content-type: text/html";

		} else if ($postData instanceof FileStream) {
			$isFileUpload = true;
			$headers[] = "Content-type: application/octet-stream";
			$headers[] = "Content-Length: ". $postData->getSize();

		} else if (is_object($postData) or is_array($postData) or is_string($postData)) {
			$headers[] = "Content-type: application/json";
			$postData = json_encode(self::object_to_array($postData));
		}

		$url = $apiServer . $resourcePath;

		$timeoutSec = 0;
		$curl = curl_init();
		if($this->debug){
			// curl_setopt($curl, CURLOPT_HEADER, true); // Display headers; returns null response
 			curl_setopt($curl, CURLOPT_VERBOSE, true); // Display communication with server
 			curl_setopt($curl, CURLOPT_STDERR, $curl_log = fopen($this->curlLogFilepath, 'a+'));
		}
		curl_setopt($curl, CURLOPT_TIMEOUT, $timeoutSec);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		// return the result on success, rather than just TRUE
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		if (! empty($queryParams)) {
			$url = ($url . '?' . http_build_query($queryParams));
		}

		if ($method == self::$POST) {
			if($isFileUpload){
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($curl, CURLOPT_TIMEOUT, 0);
				curl_setopt($curl, CURLOPT_PUT, true);
				curl_setopt($curl, CURLOPT_INFILE, $postData->getInputStream());
				curl_setopt($curl, CURLOPT_INFILESIZE, $postData->getSize());
			} else {
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
			}
		} else if ($method == self::$PUT) {
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
			curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
		} else if ($method == self::$DELETE) {
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
			curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
		}

		$url = self::encodeURI($this->signer->signUrl($url));
		curl_setopt($curl, CURLOPT_URL, $url);
		
		if($outFileStream !== null){
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
			// curl_setopt($curl, CURLOPT_FILE, $outFileStream->getInputStream());
			curl_setopt($curl, CURLOPT_HEADERFUNCTION, array($outFileStream, 'headerCallback'));
			curl_setopt($curl, CURLOPT_WRITEFUNCTION, array($outFileStream, 'bodyCallback'));
		}
		
		if($this->debug){
			$body = "> Request Body: $this->newline";
			if($isFileUpload){
				fwrite($curl_log, "$body >>>stream info: size=".$postData->getSize()." content-type=".$postData->getContentType());
			} else {
				fwrite($curl_log, $body.$postData);
			}
			echo $this->newline;
		}
		
		// Make the request
		$response = curl_exec($curl);
		$response_info = curl_getinfo($curl);
		
		// Close curl
		curl_close($curl);
		
		if($this->debug){
			$body = "< Response Body: $this->newline";
			if($outFileStream !== null){
				fwrite($curl_log, "$body <<<stream info: size=".$outFileStream->getSize()." content-type=".
				$outFileStream->getContentType()." filename=".$outFileStream->getFileName());
			} else {
				fwrite($curl_log, $body.$response);
			}
			fwrite($curl_log, $this->newline);
			fclose($curl_log);
		}
		
		// Handle the response
		if ($response_info['http_code'] == 0) {
			throw new ApiException("TIMEOUT: api call to " . $url .
				" took more than " . $timeoutSec . "s to return" );
		} else if ($response_info['http_code'] == 200 || $response_info['http_code'] == 201 || $response_info['http_code'] == 202) {
			if($outFileStream !== null){
				if(in_array('Transfer-Encoding', $outFileStream->headers) or $outFileStream->getSize() > 0){
					fclose($outFileStream->getInputStream());
					return $outFileStream;
				} else {
					return null;
				}
			} else {
				return json_decode($response);
			}
		} else if ($response_info['http_code'] == 401) {
			throw new ApiException("Unauthorized API request to " . $url, 401);
		} else if ($response_info['http_code'] == 404) {
			return null;
		} else {
			$msg = $response;
			if($outFileStream !== null and !empty($outFileStream->jsonError)){
				$msg = $outFileStream->jsonError;
			}
			$jsonArray = json_decode($msg, true);
			if(is_array($jsonArray)){
				$msg = $jsonArray['error_message'];
			}
			throw new ApiException($msg, $response_info['http_code']); 
		}
	}



	/**
	 * Take value and turn it into a string suitable for inclusion in
	 * the path or the header
	 * @param object $object an object to be serialized to a string
	 * @return string the serialized object
	 */
	public static function toPathValue($object) {
        if (is_array($object)) {
            return implode(',', array_map(function($obj) {
            	return self::unquote(var_export($obj, true));
			}, $object));
        } else {
            return self::unquote(var_export($object, true));
        }
	}

	public static function unquote($str) {
		$to_return = $str;
		if(self::startsWith($to_return, "'") && self::endsWith($to_return, "'")){
			$to_return = str_replace("'", "", $to_return);
		}
		if(self::startsWith($to_return, '"') && self::endsWith($to_return, '"')){
			$to_return = str_replace('"', '', $to_return);
		}
        return $to_return;
	}

	/**
	 * Derialize a JSON string into an object
	 *
	 * @param object $object object or primitive to be deserialized
	 * @param string $class class name is passed as a string
	 * @return object an instance of $class
	 */
	public static function deserialize($object, $class) {

		if (gettype($object) == "NULL") {
	      	return $object;
	     }

    if (substr($class, 0, 6) == 'array[') {
      $sub_class = substr($class, 6, -1);
      $sub_objects = array();
      foreach ($object as $sub_object) {
        $sub_objects[] = self::deserialize($sub_object,
                                    			 $sub_class);
      }
      return $sub_objects;
    } elseif ($class == 'DateTime') {
      		return new DateTime($object);
		} elseif (in_array($class, array('string', 'int', 'float', 'bool'))) {
			settype($object, $class);
			return $object;
		} else {
			$instance = new $class(); // this instantiates class named $class
			$classVars = get_class_vars($class);
		}

		foreach ($object as $property => $value) {

			$true_property = $property;

			if (! property_exists($class, $true_property)) {
				if (property_exists($class, ucfirst($property))) {
					$true_property = ucfirst($property);
				} else if (property_exists($class, lcfirst($property))) {
					$true_property = lcfirst($property);
				} else {
					// trigger_error("class $class has no property $property", E_USER_WARNING);
					// ignore newly added attributes
					continue;
				}
			}

			$type = $classVars['swaggerTypes'][$true_property];
			if (in_array($type, array('string', 'int', 'float', 'bool'))) {
				settype($value, $type);
				$instance->{$true_property} = $value;
			} elseif (preg_match("/array<(.*)>/", $type, $matches)) {
				$sub_class = $matches[1];
				$instance->{$true_property} = array();
				foreach ($value as $sub_property => $sub_value) {
					$instance->{$true_property}[] = self::deserialize($sub_value,
						$sub_class);
				}
			} else {
				$instance->{$true_property} = self::deserialize($value, $type);
			}
		}
		return $instance;
	}

	public static function object_to_array($data) {
	    if (is_array($data) || is_object($data))
	    {
	        $result = array();
	        foreach ($data as $key => $value)
	        {
	            if(!is_null($value)){
	                $result[$key] = self::object_to_array($value);
	            }
	        }
	        return $result;
	    }
	    return $data;
	}

	public static function getMimeType($filePath){
		$cont_type = null;
		if (function_exists('finfo_file')) {
	        $finfo = finfo_open(FILEINFO_MIME_TYPE);
	        $cont_type = finfo_file($finfo, $filePath);
	        finfo_close($finfo);
	    }
		if($cont_type == null){
			$cont_type = "application/octet-stream";
		}
		return $cont_type;
	}

	public static function encodeURI($url) {
	    $reserved = array(
	        '%2D'=>'-','%5F'=>'_','%2E'=>'.','%21'=>'!', 
	        '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')'
	    );
	    $unescaped = array(
	        '%3B'=>';','%2C'=>',','%2F'=>'/','%3F'=>'?','%3A'=>':',
	        '%40'=>'@','%26'=>'&','%3D'=>'=',
	        //'%2B'=>'+',
	        '%24'=>'$', '%25'=>'%'
	    );
	    $score = array(
	        '%23'=>'#'
	    );
	    return strtr(rawurlencode($url), array_merge($reserved,$unescaped,$score));

	}
	
	public static function encodeURIComponent($str) {
	    $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
	    return strtr(rawurlencode($str), $revert);
	}
	
	public static function readAsDataURL($filePath) {
		return 'data:'.self::getMimeType($filePath).';base64,'.base64_encode(file_get_contents($filePath));
	}

	/**
	* Starts the $haystack string with the prefix $needle?
	* @param  string
	* @param  string
	* @return bool
	*/
	public static function startsWith($haystack, $needle) {
		return strncmp($haystack, $needle, strlen($needle)) === 0;
	}
	
	
	/**
	* Ends the $haystack string with the suffix $needle?
	* @param  string
	* @param  string
	* @return bool
	*/
	public static function endsWith($haystack, $needle) {
		return strlen($needle) === 0 || substr($haystack, -strlen($needle)) === $needle;
	}
}

class ApiException extends Exception {
	public function _construct($message, $code = 0, Exception $previous = null){
		parent::__construct($message, $code, $previous);
	}
	
	public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
